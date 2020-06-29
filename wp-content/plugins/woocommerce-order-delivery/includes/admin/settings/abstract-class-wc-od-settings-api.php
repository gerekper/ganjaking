<?php
/**
 * Abstract Settings API Class
 *
 * @package WC_OD/Admin/Settings
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Settings_API', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/abstract-wc-settings-api.php';
}

if ( ! class_exists( 'WC_OD_Settings_API', false ) ) {
	/**
	 * Class WC_OD_Settings_API
	 */
	abstract class WC_OD_Settings_API extends WC_Settings_API {

		/**
		 * The plugin ID. Used for option names.
		 *
		 * @var string
		 */
		public $plugin_id = 'wc_od_';

		/**
		 * Constructor.
		 *
		 * @since 1.5.0
		 */
		public function __construct() {
			add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitized_fields' ) );
		}

		/**
		 * Return the name of the option in the WP DB.
		 *
		 * @since 1.5.0
		 *
		 * @return string
		 */
		public function get_option_key() {
			return $this->plugin_id . $this->id;
		}

		/**
		 * Prefix key for settings.
		 *
		 * @since 1.5.0
		 *
		 * @param  string $key Field key.
		 * @return string
		 */
		public function get_field_key( $key ) {
			return $key;
		}

		/**
		 * Gets the default values for the form fields.
		 *
		 * @since 1.5.0
		 *
		 * @return array
		 */
		public function get_form_fields_defaults() {
			$form_fields = $this->get_form_fields();

			return array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
		}

		/**
		 * Initialise Settings.
		 *
		 * @since 1.5.0
		 */
		public function init_settings() {
			$this->settings = WC_OD()->settings()->get_setting( $this->get_option_key() );
		}

		/**
		 * Enqueues the settings scripts.
		 *
		 * @since 1.6.0
		 */
		public function enqueue_scripts() {}

		/**
		 * Outputs the settings screen heading.
		 *
		 * @since 1.6.0
		 */
		public function output_heading() {}

		/**
		 * Output the settings screen.
		 *
		 * @since 1.6.0
		 */
		public function admin_options() {
			$this->init_form_fields();
			$this->init_settings();

			$this->enqueue_scripts();
			$this->output_heading();

			parent::admin_options();
		}

		/**
		 * Processes and saves options.
		 *
		 * @since 1.6.0
		 *
		 * @return bool was anything saved?
		 */
		public function process_admin_options() {
			$this->init_form_fields();

			$saved = parent::process_admin_options();

			$this->after_save( $saved );

			return $saved;
		}

		/**
		 * After saving the form.
		 *
		 * @since 1.6.0
		 *
		 * @param bool $saved Was anything saved?.
		 */
		public function after_save( $saved ) {}

		/**
		 * Gets if there are error messages to display or not.
		 *
		 * @since 1.6.0
		 *
		 * @return bool
		 */
		public function has_errors() {
			return ( ! empty( $this->errors ) );
		}

		/**
		 * Sanitize the settings before save the option.
		 *
		 * @since 1.5.0
		 *
		 * @param array $settings The settings to sanitize.
		 * @return array
		 */
		public function sanitized_fields( $settings ) {
			$settings = $this->sanitize_shipping_methods_fields( $settings );

			return $settings;
		}

		/**
		 * Generate the HTML for a table field.
		 *
		 * @since 1.5.0
		 *
		 * @param string $field_id Field ID.
		 * @param array  $field    Field data.
		 * @return string
		 */
		public function generate_wc_od_table_html( $field_id, $field ) {
			$field['id']    = $field_id;
			$field['value'] = $this->get_option( $field_id );

			ob_start();
			wc_od_field_wrapper( $field );
			return ob_get_clean();
		}

		/**
		 * Generates the HTML for a 'shipping_methods' field.
		 *
		 * @since 1.6.0
		 *
		 * @param string $key  The field key.
		 * @param mixed  $data The field data.
		 * @return string
		 */
		public function generate_shipping_methods_html( $key, $data ) {
			$defaults = array(
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select-nostd',
				'css'               => 'width: 400px;',
				'desc_tip'          => true,
				'options'           => wc_od_get_shipping_methods_choices(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select shipping methods', 'woocommerce-order-delivery' ),
				),
			);

			$data = wp_parse_args( $data, $defaults );

			return $this->generate_multiselect_html( $key, $data );
		}

		/**
		 * Validates a field with an array as value.
		 *
		 * @since 1.6.0
		 *
		 * @param string $key   Field key.
		 * @param mixed  $value Posted Value.
		 * @return array An array with the tags.
		 */
		public function validate_array_field( $key, $value ) {
			if ( $value && ! is_array( $value ) ) {
				$value = array_map( 'trim', explode( ',', $value ) );
			} elseif ( empty( $value ) ) {
				$value = array();
			}

			return array_map( 'wc_clean', array_map( 'wp_unslash', $value ) );
		}

		/**
		 * Validates a 'shipping_methods' field.
		 *
		 * @since 1.6.0
		 *
		 * @param string $key   Field key.
		 * @param mixed  $value Posted Value.
		 * @return array An array with the shipping methods.
		 */
		public function validate_shipping_methods_field( $key, $value ) {
			return $this->validate_array_field( $key, $value );
		}

		/**
		 * Validates a table field.
		 *
		 * @since 1.5.0
		 *
		 * @param string $field_id Field ID.
		 * @param mixed  $value    Field value.
		 * @return mixed
		 */
		public function validate_wc_od_table_field( $field_id, $value ) {
			$field          = $this->form_fields[ $field_id ];
			$field['id']    = $field_id;
			$field['value'] = $this->get_option( $field_id );

			$instance = wc_od_get_table_field( $field );

			if ( $instance ) {
				$value = $instance->sanitize_field( $value );
			}

			return $value;
		}

		/**
		 * Gets the 'shipping methods' fields ready to be registered in the form fields.
		 *
		 * @since 1.6.0
		 *
		 * @return array
		 */
		protected function get_shipping_methods_fields() {
			return array(
				'shipping_methods_option'     => array(
					'title'    => __( 'Shipping methods', 'woocommerce-order-delivery' ),
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'width: 400px;',
					'desc_tip' => true,
					'options'  => array(
						''           => __( 'All shipping methods', 'woocommerce-order-delivery' ),
						'all_except' => __( 'All shipping methods, except&hellip;', 'woocommerce-order-delivery' ),
						'specific'   => __( 'Only specific shipping methods', 'woocommerce-order-delivery' ),
					),
				),
				'all_except_shipping_methods' => array(
					'title' => __( 'All shipping methods, except&hellip;', 'woocommerce-order-delivery' ),
					'type'  => 'shipping_methods',
				),
				'specific_shipping_methods'   => array(
					'title' => __( 'Only specific shipping methods', 'woocommerce-order-delivery' ),
					'type'  => 'shipping_methods',
				),
			);
		}

		/**
		 * Sanitizes the 'shipping methods' fields.
		 *
		 * @since 1.6.0
		 *
		 * @param array $settings The settings to sanitize.
		 * @return array
		 */
		protected function sanitize_shipping_methods_fields( $settings ) {
			if ( ! isset( $settings['shipping_methods_option'] ) ) {
				return $settings;
			}

			$settings['shipping_methods'] = array();

			if ( ! empty( $settings['shipping_methods_option'] ) ) {
				$setting_key = "{$settings['shipping_methods_option']}_shipping_methods";

				$settings['shipping_methods'] = ( ! empty( $settings[ $setting_key ] ) ? $settings[ $setting_key ] : array() );
			}

			unset( $settings['specific_shipping_methods'], $settings['all_except_shipping_methods'] );

			return $settings;
		}
	}
}
