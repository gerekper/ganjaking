<?php

class WoocommerceGpfPwBulkEdit {

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;

	/**
	 * @var array
	 */
	private $gpf_fields;

	/**
	 * @var array
	 */
	private $gpf_settings;

	/**
	 * WoocommerceGpfPwBulkEdit constructor.
	 *
	 * @param WoocommerceGpfCommon $common
	 */
	public function __construct( WoocommerceGpfCommon $common ) {
		$this->common = $common;
	}

	/**
	 * Add the filters we need to register our columns.
	 */
	public function run() {
		// Note:
		// Our autoloader register the dummy PWBE_WooCommerce_GPF class.
		// This causes PWBE's built in integration (which would otherwise
		// interfere with this) not to load

		// Load config if this is an admin page.
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		// Register our columns.
		add_filter( 'pwbe_product_columns', [ $this, 'register_columns' ] );
		// Register the available options for the SELECT style columns.
		add_filter( 'pwbe_select_options', [ $this, 'register_select_options' ] );
		// Make sure fields that should be unset are unset.
		add_filter( 'pwbe_save_array_value', [ $this, 'save_array_value' ], 10, 3 );
		// Add a custom view for our fields
		add_filter( 'pwbe_views', [ $this, 'register_view' ] );
	}

	/**
	 * Load up config required by the other methods.
	 */
	public function admin_init() {
		$this->gpf_fields   = apply_filters( 'woocommerce_gpf_product_fields', $this->common->product_fields );
		$this->gpf_settings = get_option( 'woocommerce_gpf_config', array() );
	}

	/**
	 * Register all applicable fields as columns for PW Bulk Edit
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function register_columns( $columns ) {
		// Start off with the "hide" checkbox as it can't be pulled form the common field array.
		$pwbe_gpf_columns = [
			[
				'name'       => __( 'GPF: Hide from the feed', 'woocommerce_gpf' ),
				'type'       => 'select',
				'table'      => 'meta',
				'field'      => '_woocommerce_gpf_data___exclude_product',
				'readonly'   => 'false',
				'visibility' => 'both',
				'sortable'   => 'true',
				'views'      => [ 'all' ],
			],
		];

		// Pull in the remaining fields from the WoocommerceGpfCommon class.
		foreach ( $this->gpf_fields as $key => $field_info ) {
			if ( $this->field_excluded( $key ) ) {
				continue;
			}
			$pwbe_gpf_columns[] = $this->generate_pwbe_column_for_field( $key, $field_info );
		}
		// Order them sensibly
		usort( $pwbe_gpf_columns, [ $this, 'usort_callback' ] );

		// Find the right place for us to insert
		// After _sale_price_dates_to if found, or at the end if not.
		$start_index = 0;
		foreach ( $columns as $index => $column ) {
			$start_index = $index + 1;
			if ( '_sale_price_dates_to' === $column['field'] ) {
				break;
			}
		}

		// Add the columns in the correct place.
		array_splice( $columns, $start_index, 0, $pwbe_gpf_columns );

		return $columns;
	}

	/**
	 * Register the values for the dropdowns for fields that are rendered as dropdowns.
	 *
	 * @param $select_options
	 *
	 * @return mixed
	 */
	public function register_select_options( $select_options ) {
		// Include select options for exclude dropdown.
		$select_options['_woocommerce_gpf_data___exclude_product'] = [
			''   => [
				'name'       => _x( 'No', 'Option for whether to hide product from feed', 'woocommerce_gpf' ),
				'visibility' => 'both',
			],
			'on' => [
				'name'       => _x( 'Yes', 'Option for whether to hide product from feed', 'woocommerce_gpf' ),
				'visibility' => 'both',
			],
		];
		// Generate select options for any other fields that have an options callback defined.
		foreach ( $this->gpf_fields as $key => $field_info ) {
			if ( $this->field_excluded( $key ) ) {
				continue;
			}
			if ( isset( $field_info['options_callback'] ) &&
				 is_callable( $field_info['options_callback'] ) ) {
				$select_options[ '_woocommerce_gpf_data___' . $key ] = array_merge(
					[
						'' => [
							'name'       => __( 'Use default', 'woocommerce_gpf' ),
							'visibility' => 'both',
						],
					],
					array_map(
						[ $this, 'enrich_options' ],
						$field_info['options_callback']()
					)
				);
			}
		}

		return $select_options;
	}

	/**
	 * Register a custom view, and show only the Google Product Feed fields
	 *
	 * Note: PWBE the list of fields against a view here is the list of fields to *hide*.
	 *
	 * @param $views
	 *
	 * @return mixed
	 */
	public function register_view( $views ) {
		$columns = PWBE_Columns::get();
		$hidden  = [];
		foreach ( $columns as $column ) {
			if ( stripos( $column['field'], '_woocommerce_gpf_data___' ) !== 0 ) {
				$hidden[] = $column['field'];
			}
		}
		$views['WooCommerce Product Feed fields'] = $hidden;

		return $views;
	}

	/**
	 * Ensures that "no" options get cleared down as expected.
	 *
	 * @param $array_value
	 * @param $meta_key
	 * @param $field
	 *
	 * @return mixed
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function save_array_value( $array_value, $meta_key, $field ) {
		// Only applies to a specific fields.
		$relevant_fields = [
			'_woocommerce_gpf_data___adult',
			'_woocommerce_gpf_data___age_group',
			'_woocommerce_gpf_data___availability',
			'_woocommerce_gpf_data___condition',
			'_woocommerce_gpf_data___energy_efficiency_class',
			'_woocommerce_gpf_data___exclude_product',
			'_woocommerce_gpf_data___gender',
			'_woocommerce_gpf_data___google_funded_promotion_eligibility',
			'_woocommerce_gpf_data___is_bundle',
			'_woocommerce_gpf_data___min_energy_efficiency_class',
			'_woocommerce_gpf_data___max_energy_efficiency_class',
			'_woocommerce_gpf_data___pickup_method',
			'_woocommerce_gpf_data___pickup_sla',
			'_woocommerce_gpf_data___size_system',
			'_woocommerce_gpf_data___size_type',
		];
		if ( ! in_array( $field['field'], $relevant_fields, true ) ) {
			// Do nothing if it's not a field we're interested in.
			return $array_value;
		}
		$field_id     = $field['field'];
		$internal_key = str_replace( '_woocommerce_gpf_data___', '', $field_id );
		if ( isset( $array_value[ $internal_key ] ) && empty( $array_value[ $internal_key ] ) ) {
			unset( $array_value[ $internal_key ] );
		}

		return $array_value;
	}

	/**
	 * Generate the PWBE column data for a given GPF field.
	 *
	 * @param string $key
	 * @param array $field_info
	 *
	 * @return array
	 */
	private function generate_pwbe_column_for_field( $key, $field_info ) {
		return [
			'name'       => sprintf(
			// Translators: %s is the name of the Google Product Feed field
				__( 'GPF: %s', 'woocommerce_gpf' ),
				$field_info['desc']
			),
			'type'       => $this->get_pwbe_type_from_field_info(
				! empty( $field_info['callback'] ) ? $field_info['callback'] : 'render_textfield'
			),
			'table'      => 'meta',
			'field'      => '_woocommerce_gpf_data___' . $key,
			'readonly'   => 'false',
			'visibility' => 'both',
			'sortable'   => 'true',
			'views'      => [ 'all' ],
		];
	}

	/**
	 * Generate the PWBE field type for a given GPF callback.
	 *
	 * @param string $callback
	 *
	 * @return string
	 */
	private function get_pwbe_type_from_field_info( $callback ) {
		switch ( $callback ) {
			case 'render_is_bundle':
			case 'render_generic_select':
				return 'select';
				break;
			case 'render_availability_date':
			case 'render_b_category':
			case 'render_product_type':
			case 'render_textfield':
			case 'render_title':
			default:
				return 'text';
				break;
		}
	}

	/**
	 * Determine if a field should be excluded from the bulk edit screens.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	private function field_excluded( $key ) {
		// Skip if not enabled & not mandatory.
		if ( ! isset( $this->gpf_settings['product_fields'][ $key ] ) &&
			 ( ! isset( $this->gpf_fields[ $key ]['mandatory'] ) || ! $this->gpf_fields[ $key ]['mandatory'] )
		) {
			return true;
		}
		// Skip if not to be shown on product pages.
		if ( isset( $this->gpf_fields[ $key ]['skip_on_product_pages'] ) &&
			 $this->gpf_fields[ $key ]['skip_on_product_pages']
		) {
			return true;
		}
		// Skip if not bulk editable
		if ( isset( $this->gpf_fields[ $key ]['skip_on_bulk_edit'] ) &&
			 $this->gpf_fields[ $key ]['skip_on_bulk_edit'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Enrich the option format used by the main plugin into the format PWBE expects.
	 *
	 * @param $option_array
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function enrich_options( $option_array ) {
		return [
			'name'       => $option_array,
			'visibility' => 'both',
		];
	}

	/**
	 * usort() callback for sorting the options array.
	 *
	 * @param $item_a
	 * @param $item_b
	 *
	 * @return int|lt
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function usort_callback( $item_a, $item_b ) {
		return strcmp( $item_a['name'], $item_b['name'] );
	}
}
