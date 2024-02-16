<?php

/**
 * Integration with importer/exporter in WooCommerce 3.1+
 */
class WoocommerceGpfImportExportIntegration {

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $woocommerce_gpf_common;

	/**
	 * WoocommerceGpfImportExportIntegration constructor.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 */
	public function __construct( WoocommerceGpfCommon $woocommerce_gpf_common ) {
		$this->woocommerce_gpf_common = $woocommerce_gpf_common;
	}

	/**
	 * Attach to the relevant hooks to integrate with the importer / exporter.
	 */
	public function initialise() {
		// Export filters.
		add_filter( 'woocommerce_product_export_column_names', array( $this, 'add_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_columns' ) );
		add_action( 'plugins_loaded', [ $this, 'attach_render_hooks' ], 11 );
		// Import filters.
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_columns' ) );
		add_filter(
			'woocommerce_csv_product_import_mapping_default_columns',
			array(
				$this,
				'add_default_mapping_columns',
			)
		);
		add_filter( 'woocommerce_product_import_pre_insert_product_object', array( $this, 'process_import' ), 10, 2 );
	}

	/**
	 * Register our columns with the importer/exporter.
	 *
	 * @param array $columns List of columns.
	 *
	 * @return array            Modified list of columns.
	 */
	public function add_columns( $columns ) {
		return array_merge( $columns, $this->generate_column_list() );
	}

	/**
	 * Attach all necessary hooks for rendering fields during export.
	 */
	public function attach_render_hooks() {

		$fields = $this->generate_column_list();
		foreach ( array_keys( $fields ) as $key ) {
			add_filter(
				'woocommerce_product_export_product_column_' . $key,
				array(
					$this,
					"render_column_$key",
				),
				10,
				2
			);
		}
	}

	/**
	 * Return list of default mappings.
	 *
	 * @param array $mappings The list of standard mappings.
	 *
	 * @return array             The extended list of mappings.
	 */
	public function add_default_mapping_columns( $mappings ) {
		$fields = $this->generate_column_list();
		foreach ( $fields as $k => $v ) {
			$mappings[ $v ]               = $k;
			$mappings[ strtolower( $v ) ] = $k;
		}

		return $mappings;
	}

	/**
	 * Generate a list of our columns from the common field class.
	 *
	 * @return array   Array of GPF columns with appropriate keys.
	 */
	private function generate_column_list() {
		$fields = wp_list_pluck( $this->woocommerce_gpf_common->product_fields, 'desc' );
		// Remove description from the list since it's not set-able against products.
		unset( $fields['description'] );
		foreach ( $fields as $key => $value ) {
			// Translators: Placeholder is the name of the feed field
			$fields[ 'gpf_' . $key ] = sprintf( __( 'Google product feed: %s', 'woocommerce_gpf' ), $value );
			unset( $fields[ $key ] );
		}
		$fields['gpf_exclude_product'] = __( 'Google product feed: Hide product from feed (Y/N)', 'woocommerce_gpf' );
		asort( $fields );

		return $fields;
	}

	/**
	 * Process a set of import data.
	 *
	 * @param WC_Product $product The product being imported.
	 * @param array $data The data processed from the CSV file and mapped.
	 *
	 * @return WC_Product          The product with updates applied.
	 */
	public function process_import( $product, $data ) {
		$fields       = $this->generate_column_list();
		$product_data = $product->get_meta( '_woocommerce_gpf_data' );
		if ( empty( $product_data ) ) {
			$product_data = [];
		}
		foreach ( array_keys( $fields ) as $key ) {
			$product_data_key = str_replace( 'gpf_', '', $key );
			$field_config     = $this->woocommerce_gpf_common->product_fields[ $product_data_key ] ?? [];

			// Let specific fields handle unpacking the data from the CSV themselves.
			$method_name = 'import_column_' . $key;
			if ( method_exists( $this, $method_name ) ) {
				$product_data[ $product_data_key ] = $this->{$method_name}( $data[ $key ] );
				continue;
			}
			// Standard behaviour for other fields.
			if ( 'gpf_exclude_product' === $key ) {
				if ( isset( $data[ $key ] ) && 'y' === strtolower( $data[ $key ] ) ) {
					$data[ $key ] = 'on';
				} else {
					$data[ $key ] = '';
				}
			}
			if ( isset( $data[ $key ] ) ) {
				$field_data = $data[ $key ];
				if ( isset( $field_config['import_as_array'] ) && $field_config['import_as_array'] ) {
					$field_data = explode( ',', $field_data );
				}
				$product_data[ $product_data_key ] = $field_data;
			}
		}
		$product->update_meta_data( '_woocommerce_gpf_data', $product_data );

		return $product;
	}

	/**
	 * Magic method to handle the export field rendering.
	 *
	 * Extracts the field name from the method name invoked, and uses
	 * get_product_gpf_value to retrieve the relevant field.
	 *
	 * @param string $method The method name attempted to be called.
	 * @param array $args The args passed to the method.
	 *
	 * @return string           The value for the relevant field.
	 * @throws Exception
	 */
	public function __call( $method, $args ) {
		if ( stripos( $method, 'render_column_gpf_' ) !== 0 ) {
			throw new RuntimeException( esc_html( 'Invalid method on ' . __CLASS__ . ' - ' . $method ) );
		}
		$field = str_replace( 'render_column_gpf_', '', $method );

		return $this->get_product_gpf_value( $field, $args[1] );
	}

	/**
	 * Bespoke renderer for the exclude_product field to make user-friendly
	 * values.
	 */
	public function render_column_gpf_exclude_product( $value, $product ) {
		$value = $this->get_product_gpf_value( 'exclude_product', $product );

		return 'on' === $value ? 'Y' : '';
	}

	/**
	 * Custom renderer for the certification field.
	 *
	 * Format is authority:name:code,authority:name:code,authority:name:code...
	 *
	 * @param mixed $value Unused
	 * @param WC_Product $product The product being exported.
	 *
	 * @return string
	 */
	public function render_column_gpf_certification( $value, $product ) {
		return $this->generic_array_pack(
			$this->get_product_gpf_value( 'certification', $product, false ),
			[
				'certification_authority',
				'certification_name',
				'certification_code',
			]
		);
	}

	/**
	 * Custom rendered for the installment field.
	 *
	 * Format is: number-of-installments:amount,number-of-installments:amount...
	 *
	 * @see render_column_gpf_certification()
	 */
	public function render_column_gpf_installment( $value, $product ) {
		return $this->generic_array_pack(
			$this->get_product_gpf_value( 'installment', $product, false ),
			[
				'months',
				'amount',
			]
		);
	}

	/**
	 * Custom rendered for the product detail field.
	 *
	 * Format is: section:attribute-name:attribute-value,section:attribute-name:attribute-value...
	 *
	 * @see render_column_gpf_certification()
	 */
	public function render_column_gpf_product_detail( $value, $product ) {
		return $this->generic_array_pack(
			$this->get_product_gpf_value( 'product_detail', $product, false ),
			[
				'section_name',
				'attribute_name',
				'attribute_value',
			]
		);
	}

	/**
	 * Custom rendered for the product detail field.
	 *
	 * Format is: section:attribute-name:attribute-value,section:attribute-name:attribute-value...
	 *
	 * @see render_column_gpf_certification()
	 */
	public function render_column_gpf_product_fee( $value, $product ) {
		return $this->generic_array_pack(
			$this->get_product_gpf_value( 'product_fee', $product, false ),
			[
				'type',
				'amount',
			]
		);
	}

	/**
	 * Custom import for the certification field.
	 *
	 * @param string $data The raw data in format a1:a2:a3,b1:b2:b3...
	 *
	 * @return array
	 */
	public function import_column_gpf_certification( $data ) {
		return $this->generic_string_unpack(
			$data,
			[
				'certification_authority',
				'certification_name',
				'certification_code',
			]
		);
	}

	/**
	 * Custom import for the installment field.
	 *
	 * @see import_column_gpf_certification()
	 */
	public function import_column_gpf_installment( $data ) {
		return $this->generic_string_unpack(
			$data,
			[
				'months',
				'amount',
			]
		);
	}

	/**
	 * Custom import for the product_detail field.
	 *
	 * @see import_column_gpf_certification()
	 */
	public function import_column_gpf_product_detail( $data ) {
		return $this->generic_string_unpack(
			$data,
			[
				'section_name',
				'attribute_name',
				'attribute_value',
			]
		);
	}

	/**
	 * Custom import for the product_fee field.
	 *
	 * @see import_column_gpf_certification()
	 */
	public function import_column_gpf_product_fee( $data ) {
		return $this->generic_string_unpack(
			$data,
			[
				'type',
				'amount',
			]
		);
	}

	/**
	 * Get the value of a GPF field for a product.
	 *
	 * @param string $key The key that we want to retrieve.
	 * @param WC_Product $product The product we're enquiring about.
	 * @param bool $array_to_string If true, array entries will be turned into CSVs
	 *
	 * @return string                The value of the key for this product, or
	 *                               empty string.
	 */
	private function get_product_gpf_value( $key, $product, $array_to_string = true ) {
		$product_settings = get_post_meta( $product->get_id(), '_woocommerce_gpf_data', true );
		if ( ! isset( $product_settings[ $key ] ) ) {
			return '';
		}
		if ( is_array( $product_settings[ $key ] ) && $array_to_string ) {
			return implode( ',', $product_settings[ $key ] );
		}

		return $product_settings[ $key ];
	}

	/**
	 *  Pack an array with defined keys into a string in the format a:a:a,b:b:b,c:c:c.
	 *
	 *  Used for exporting complex fields.
	 *
	 * @param $data
	 * @param $keys
	 *
	 * @return string
	 */
	private function generic_array_pack( $data, $keys ) {
		if ( ! is_array( $data ) || empty( $data ) ) {
			return '';
		}
		$instances = array_map(
			function ( $instance ) use ( $keys ) {
				// Check we have the expected keys. If not, ignore the record.
				$valid_keys = array_intersect( $keys, array_keys( $instance ) );
				if ( count( $valid_keys ) !== count( $keys ) ) {
						return '';
				}
				$values    = [];
				$all_empty = true;
				foreach ( $keys as $key ) {
					if ( ! empty( $instance[ $key ] ) ) {
						$all_empty = false;
					}
					$values[ $key ] = $instance[ $key ];
				}

				// If all attributes are empty, ignore it.
				if ( $all_empty ) {
					return '';
				}

				return implode( ':', $values );
			},
			$data
		);

		$instances = array_filter( $instances );

		return implode( ',', $instances );
	}

	/**
	 * Unpack a string in the format a:a:a,b:b:b,c:c:c into nested array with defined keys.
	 *
	 * Used for importing complex fields.
	 *
	 * @param $data
	 * @param $keys
	 *
	 * @return string[]
	 */
	private function generic_string_unpack( $data, $keys ) {
		$instances = explode( ',', $data );
		if ( ! $instances ) {
			return [];
		}
		$instances = array_map(
			function ( $instance ) use ( $keys ) {
				$values = explode( ':', $instance );
				// Ignore the instance if the count of values doesn't match the number of keys.
				if ( count( $values ) !== count( $keys ) ) {
						return null;
				}
				// Map the values into an associative array.
				$return = [];
				foreach ( $keys as $key ) {
					$return[ $key ] = array_shift( $values );
				}
				return $return;
			},
			$instances
		);

		return array_filter( $instances );
	}
}
