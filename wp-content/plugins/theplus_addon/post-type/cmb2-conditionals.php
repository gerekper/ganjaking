<?php
if ( ! class_exists( 'CMB2_Conditionals' ) ) {

	/**
	 * CMB2_Conditionals Plugin.
	 */
	class CMB2_Conditionals {

		/**
		 * Priority on which our actions are hooked in.
		 *
		 * @const int
		 */
		const PRIORITY = 99999;

		/**
		 * Version number of the plugin.
		 *
		 * @const string
		 */
		const VERSION = '1.0.4';

		/**
		 * CMB2 Form elements which can be set to "required".
		 *
		 * @var array
		 */
		protected $maybe_required_form_elms = array(
			'list_input',
			'input',
			'textarea',
			'input',
			'select',
			'checkbox',
			'radio',
			'radio_inline',
			'taxonomy_radio',
			'taxonomy_multicheck',
			'multicheck_inline',
		);


		/**
		 * Constructor - Set up the actions for the plugin.
		 */
		public function __construct() {
			if ( ! defined( 'CMB2_LOADED' ) || false === CMB2_LOADED ) {
				return;
			}

			add_action( 'admin_init', array( $this, 'admin_init' ), self::PRIORITY );
			
			foreach ( $this->maybe_required_form_elms as $element ) {
				add_filter( "cmb2_{$element}_attributes", array( $this, 'maybe_set_required_attribute' ), self::PRIORITY );
			}
		}



		/**
		 * Ensure valid html for the required attribute.
		 *
		 * @param array $args Array of HTML attributes.
		 *
		 * @return array
		 */
		public function maybe_set_required_attribute( $args ) {
			if ( ! isset( $args['required'] ) ) {
				return $args;
			}

			// Comply with HTML specs.
			if ( true === $args['required'] ) {
				$args['required'] = 'required';
			}

			return $args;
		}


		/**
		 * Hook in the filtering of the data being saved.
		 */
		public function admin_init() {
			$cmb2_boxes = CMB2_Boxes::get_all();

			foreach ( $cmb2_boxes as $cmb_id => $cmb2_box ) {
				add_action(
					"cmb2_{$cmb2_box->object_type()}_process_fields_{$cmb_id}",
					array( $this, 'filter_data_to_save' ),
					self::PRIORITY,
					2
				);
			}
		}


		/**
		 * Filter the data received from the form in order to remove those values
		 * which are not suppose to be enabled to edit according to the declared conditionals.
		 *
		 * @param \CMB2 $cmb2      An instance of the CMB2 class.
		 * @param int   $object_id The id of the object being saved, could post_id, comment_id, user_id.
		 *
		 * The potentially adjusted array is returned via reference $cmb2.
		 */
		public function filter_data_to_save( CMB2 $cmb2, $object_id ) {
			foreach ( $cmb2->prop( 'fields' ) as $field_args ) {
				if ( ! ( 'group' === $field_args['type'] || ( array_key_exists( 'attributes', $field_args ) && array_key_exists( 'data-conditional-id', $field_args['attributes'] ) ) ) ) {
					continue;
				}

				if ( 'group' === $field_args['type'] ) {
					foreach ( $field_args['fields'] as $group_field ) {
						if ( ! ( array_key_exists( 'attributes', $group_field ) && array_key_exists( 'data-conditional-id', $group_field['attributes'] ) ) ) {
							continue;
						}

						$field_id               = $group_field['id'];
						$conditional_id         = $group_field['attributes']['data-conditional-id'];
						$decoded_conditional_id = @json_decode( $conditional_id );
						if ( $decoded_conditional_id ) {
							$conditional_id = $decoded_conditional_id;
						}

						if ( is_array( $conditional_id ) && ! empty( $conditional_id ) && ! empty( $cmb2->data_to_save[ $conditional_id[0] ] ) ) {
							foreach ( $cmb2->data_to_save[ $conditional_id[0] ] as $key => $group_data ) {
								$cmb2->data_to_save[ $conditional_id[0] ][ $key ] = $this->filter_field_data_to_save( $group_data, $field_id, $conditional_id[1], $group_field['attributes'] );
							}
						}
						continue;
					}
				} else {
					$field_id       = $field_args['id'];
					$conditional_id = $field_args['attributes']['data-conditional-id'];

					$cmb2->data_to_save = $this->filter_field_data_to_save( $cmb2->data_to_save, $field_id, $conditional_id, $field_args['attributes'] );
				}
			}
		}


		/**
		 * Determine if the data for one individual field should be saved or not.
		 *
		 * @param array  $data_to_save   The received $_POST data.
		 * @param string $field_id       The CMB2 id of this field.
		 * @param string $conditional_id The CMB2 id of the field this field is conditional on.
		 * @param array  $attributes     The CMB2 field attributes.
		 *
		 * @return array Array of data to save.
		 */
		protected function filter_field_data_to_save( $data_to_save, $field_id, $conditional_id, $attributes ) {
			if ( array_key_exists( 'data-conditional-value', $attributes ) ) {

				$conditional_value         = $attributes['data-conditional-value'];
				$decoded_conditional_value = @json_decode( $conditional_value );
				if ( $decoded_conditional_value ) {
					$conditional_value = $decoded_conditional_value;
				}

				if ( ! isset( $data_to_save[ $conditional_id ] ) ) {
					if ( 'off' !== $conditional_value ) {
						unset( $data_to_save[ $field_id ] );
					}
					return $data_to_save;
				}

				if ( ( ! is_array( $conditional_value ) && ! is_array( $data_to_save[ $conditional_id ] ) ) && $data_to_save[ $conditional_id ] != $conditional_value ) {
					unset( $data_to_save[ $field_id ] );
					return $data_to_save;
				}

				if ( is_array( $conditional_value ) || is_array( $data_to_save[ $conditional_id ] ) ) {
					$match = array_intersect( (array) $conditional_value, (array) $data_to_save[ $conditional_id ] );
					if ( empty( $match ) ) {
						unset( $data_to_save[ $field_id ] );
						return $data_to_save;
					}
				}
			}

			if ( ! isset( $data_to_save[ $conditional_id ] ) || ! $data_to_save[ $conditional_id ] ) {
				unset( $data_to_save[ $field_id ] );
			}

			return $data_to_save;
		}
	} /* End of class. */


	/**
	 * Instantiate our class.
	 *
	 * {@internal wp_installing() function was introduced in WP 4.4. The function exists and constant
	 * check can be removed once the min version for this plugin has been upped to 4.4.}}
	 */
	if ( ( function_exists( 'wp_installing' ) && wp_installing() === false ) || ( ! function_exists( 'wp_installing' ) && ( ! defined( 'WP_INSTALLING' ) || WP_INSTALLING === false ) ) ) {
		add_action( 'plugins_loaded', 'cmb2_conditionals_init' );
	}

	if ( ! function_exists( 'cmb2_conditionals_init' ) ) {
		/**
		 * Initialize the class.
		 */
		function cmb2_conditionals_init() {
			$cmb2_conditionals = new CMB2_Conditionals();
		}
	}
} /* End of class-exists wrapper. */
