<?php

/**
 * Discover ACF custom fields, and make them available as pre-population options.
 *
 * Class WoocommerceProductFeedsAdvancedCustomFields
 */
class WoocommerceProductFeedsAdvancedCustomFields {
	/**
	 * @var WoocommerceProductFeedsAdvancedCustomFieldsFormatter
	 */
	protected $formatter;

	/**
	 * Array of ACF field groups.
	 *
	 * @var array
	 */
	private $field_groups = [];

	/**
	 * Array of ACF field objects.
	 *
	 * @var array
	 */
	private $fields = [];

	/**
	 * WoocommerceProductFeedsAdvancedCustomFields constructor.
	 *
	 * @param WoocommerceProductFeedsAdvancedCustomFieldsFormatter $formatter
	 */
	public function __construct( WoocommerceProductFeedsAdvancedCustomFieldsFormatter $formatter ) {
		$this->formatter = $formatter;
	}

	/**
	 * Run the integration.
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_prepopulate_options', [ $this, 'register_options' ] );
		add_filter( 'woocommerce_gpf_prepopulation_description', [ $this, 'describe_acf_prepopulation' ], 10, 2 );
		add_filter( 'woocommerce_gpf_prepopulate_value_for_product', [ $this, 'prepopulate' ], 10, 5 );
		add_filter( 'woocommerce_gpf_prepopulate_label', [ $this, 'prepopulate_label' ], 10, 2 );
	}

	/**
	 * Removes raw meta keys, and adds ACF replacements to the prepopulation list.
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function register_options( $options ) {
		$this->discover_field_groups();
		$this->discover_fields();
		// Bail if there are no fields to handle.
		if ( ! count( $this->fields ) ) {
			return $options;
		}
		$acf_options = $this->get_acf_options();

		// Splice them in above custom fields.
		$idx     = array_search( 'disabled:meta', array_keys( $options ), true );
		$options = array_slice( $options, 0, $idx, true ) +
				   $acf_options +
				   array_slice( $options, $idx, null, true );
		return $options;
	}

	/**
	 * Create a description for the option when displayed in the status report.
	 *
	 * @param $description
	 * @param $key
	 *
	 * @return string
	 */
	public function describe_acf_prepopulation( $description, $key ) {
		list( $type, $acf_key ) = explode( ':', $key );
		if ( 'acf' !== $type ) {
			return $description;
		}

		// Try and load the ACF field details.
		$field = get_field_object( $acf_key );
		if ( ! $field ) {
			return sprintf(
			// Translators: %s is the name of the ACF field
				__( 'ACF. Unknown field (%s)', 'woocommerce_gpf' ),
				$acf_key
			);
		}

		$field_group       = get_post( $field['parent'] );
		$field_group_title = isset( $field_group->post_title ) ?
			$field_group->post_title :
			__( 'Unknown group', 'woocommerce_gpf' );

		return sprintf(
		// Translators: %1$s is field group title, %2$s is field name, %3$s is the internal field ID
			__( 'ACF. %1$s, %2$s (%3$s)', 'woocommerce_gpf' ),
			$field_group_title,
			$field['label'],
			$acf_key
		);
	}

	/**
	 * Create a description for the option when used to explain prepopulations against field entry boxes.
	 *
	 * @param $description
	 * @param $key
	 *
	 * @return string
	 */
	public function prepopulate_label( $description, $key ) {
		list( $type, $acf_key ) = explode( ':', $key );
		if ( 'acf' !== $type ) {
			return $description;
		}
		// Try and load the ACF field details.
		$field = get_field_object( $acf_key );
		if ( ! $field ) {
			return sprintf(
			// Translators: %s is the name of the ACF field
				__( 'ACF field (%s)', 'woocommerce_gpf' ),
				$acf_key
			);
		}
		$field_group       = get_post( $field['parent'] );
		$field_group_title = isset( $field_group->post_title ) ?
			$field_group->post_title :
			__( 'Unknown group', 'woocommerce_gpf' );

		return sprintf(
		// Translators: %1$s is field group title, %2$s is field name, %3$s is the internal field ID
			__( 'ACF field %1$s: %2$s (%3$s)', 'woocommerce_gpf' ),
			$field_group_title,
			$field['label'],
			$acf_key
		);
	}

	/**
	 * Calculate the value for a field.
	 *
	 * @param $result
	 * @param $prepopulate
	 * @param $which_product
	 * @param $specific_product
	 * @param $general_product
	 *
	 * @return array
	 */
	public function prepopulate( $result, $prepopulate, $which_product, $specific_product, $general_product ) {
		list( $type, $acf_key ) = explode( ':', $prepopulate );
		if ( 'acf' !== $type ) {
			return $result;
		}
		$product      = ( 'general' === $which_product ) ?
			$general_product :
			$specific_product;
		$field_object = get_field_object( $acf_key, $product->get_id() );

		return $this->formatter->get_value( $field_object, $result, $prepopulate );
	}

	/**
	 * Return an array of all of the configured ACF Fields.
	 *
	 * @return array
	 */
	private function discover_field_groups() {
		$field_groups = get_posts(
			[
				'post_type'   => 'acf-field-group',
				'post_status' => 'publish',
				'numberposts' => - 1,
			]
		);

		foreach ( $field_groups as $field_group ) {
			$this->field_groups[ $field_group->ID ] = $field_group;
		}
	}

	/**
	 * Return an array of all of the configured ACF Fields.
	 *
	 * @return array
	 */
	private function discover_fields() {
		$this->fields = get_posts(
			[
				'post_type'   => 'acf-field',
				'post_status' => 'publish',
				'numberposts' => - 1,
			]
		);

		$this->fields = array_map( [ $this, 'load_field_object_from_post' ], $this->fields );
		usort( $this->fields, [ $this, 'sort_field_objects' ] );
	}

	/**
	 * Sort field groups by title, ID.
	 *
	 * @param $group_a
	 * @param $group_b
	 *
	 * @return int
	 */
	private function sort_field_groups( $group_a, $group_b ) {
		if ( $group_a->post_title === $group_b->post_title ) {
			return ( $group_a->ID > $group_b->ID ) ? 1 : - 1;
		}

		return strcmp( $group_a->post_title, $group_b->post_title );
	}

	/**
	 * usort() callback for sorting fields by field group, label, ID.
	 *
	 * @param $field_a
	 * @param $field_b
	 *
	 * @return int
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function sort_field_objects( $field_a, $field_b ) {
		if ( $field_a['parent'] !== $field_b['parent'] ) {
			$group_a = isset( $this->field_groups[ $field_a['parent'] ] ) ?
				$this->field_groups[ $field_a['parent'] ] :
				(object) [
					'post_title' => 'Unknown group',
					'ID'         => 0,
				];
			$group_b = isset( $this->field_groups[ $field_b['parent'] ] ) ?
				$this->field_groups[ $field_b['parent'] ] :
				(object) [
					'post_title' => 'Unknown group',
					'ID'         => 0,
				];

			return $this->sort_field_groups( $group_a, $group_b );
		}
		if ( $field_a['label'] === $field_b['label'] ) {
			return ( $field_a['ID'] > $field_b['ID'] ) ? 1 : - 1;
		}

		return strcmp( $field_a['label'], $field_b['label'] );
	}

	/**
	 * Callback that maps an acf-field WP_Post to the corresponding ACF field object.
	 *
	 * @param $post
	 *
	 * @return array|false|mixed|void|null
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function load_field_object_from_post( $post ) {
		return get_field_object( $post->post_name );
	}

	/**
	 * Get the ACF fields to be merged into the available options, organised by field group.
	 *
	 * @return array
	 */
	private function get_acf_options() {
		$options           = [];
		$doing_field_group = null;
		$supported_types   = [
			'button_group',
			'date_picker',
			'date_time_picker',
			'file',
			'image',
			'link',
			'number',
			'page_link',
			'radio',
			'range',
			'select',
			'taxonomy',
			'text',
			'textarea',
			'true_false',
			'url',
			'wysiwyg',
		];

		foreach ( $this->fields as $field ) {
			// Skip the field if it isn't a supported type.
			if ( ! in_array( $field['type'], $supported_types, true ) ) {
				continue;
			}
			// Add a header if this is a new field group.
			if ( $field['parent'] !== $doing_field_group ) {
				$doing_field_group                             = $field['parent'];
				$options[ 'disabled:acf_' . $field['parent'] ] = sprintf(
				// Translators: This is a field group heading in the list of available prepopulations. %s is the field group title
					__( '- %s (ACF) -', 'woocommerce_gpf' ),
					isset( $this->field_groups[ $field['parent'] ]->post_title ) ?
						$this->field_groups[ $field['parent'] ]->post_title :
						__( 'Unknown group', 'woocommerce_gpf' )
				);
			}
			// Add the options for the field.
			if ( in_array( $field['type'], [ 'file', 'image' ], true ) ) {

				$options[ 'acf:' . $field['key'] . ':name' ] = sprintf(
				// Translators: %s is the field name.
					__( '%s (filename)', 'woocommerce_gpf' ),
					$field['label']
				);

				$options[ 'acf:' . $field['key'] . ':url' ] = sprintf(
				// Translators: %s is the field name.
					__( '%s (URL)', 'woocommerce_gpf' ),
					$field['label']
				);
			} else {
				$options[ 'acf:' . $field['key'] ] = $field['label'];
			}
		}

		return $options;
	}
}
