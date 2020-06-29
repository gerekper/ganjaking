<?php

/**
 * Integration with importer/exporter in WooCommerce 3.1+
 */
class WoocommerceMsrpImportExport {

	/**
	 * Constructor.
	 *
	 * Attach to the relevant hooks to integrate with the importer / exporter.
	 */
	public function __construct() {
		// Export filters.
		add_filter( 'woocommerce_product_export_column_names', array( $this, 'add_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_columns' ) );
		$this->attach_render_hooks();

		// Import filters.
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_columns' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'add_default_mapping_columns' ) );
		add_filter( 'woocommerce_product_import_pre_insert_product_object', array( $this, 'process_import' ), 10, 2 );
	}

	/**
	 * Register our columns with the importer/exporter.
	 *
	 * @param  array  $columns  List of columns.
	 *
	 * @return array            Modified list of columns.
	 */
	public function add_columns( $columns ) {
		return array_merge( $columns, $this->generate_column_list() );
	}

	/**
	 * Attach all necessary hooks for rendering fields during export.
	 */
	private function attach_render_hooks() {
		$fields = $this->generate_column_list();
		foreach ( array_keys( $fields ) as $key ) {
			add_filter( 'woocommerce_product_export_product_column_' . $key, array( $this, "render_column_$key" ), 10, 2 );
		}
	}

	/**
	 * Return list of default mappings.
	 *
	 * @param  array   $mappings  The list of standard mappings.
	 *
	 * @return array             The extended list of mappings.
	 */
	public function add_default_mapping_columns( $mappings ) {
		$fields = $this->generate_column_list();
		foreach ( $fields as $k => $v ) {
			$mappings[ $v ] = $k;
			$mappings[ strtolower( $v ) ] = $k;
		}
		return $mappings;
	}

	/**
	 * Generate a list of columns for import / export.
	 *
	 * @return array   Array of columns with appropriate keys.
	 */
	private function generate_column_list() {
		return array(
			'msrp_price' => __( 'MSRP price', 'woocommerce_msrp' ),
		);
	}

	/**
	 * Process a set of import data.
	 *
	 * @param  WC_Product $object  The product being imported.
	 * @param  array      $data    The data processed from the CSV file and mapped.
	 *
	 * @return WC_Product          The product with updates applied.
	 */
	public function process_import( $object, $data ) {
		$fields       = $this->generate_column_list();
		foreach ( array_keys( $fields ) as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				continue;
			}
			if ( 'variation' === $object->get_type() ) {
				$object->update_meta_data( '_msrp', (float) $data[ $key ] );
			} else {
				$object->update_meta_data( '_msrp_price', (float) $data[ $key ] );
			}
		}
		return $object;
	}

	/**
	 * Get the value of a GPF field for a product.
	 *
	 * @param  string      $key      The key that we want to retrieve.
	 * @param  WC_Product  $product  The product we're enquiring about.
	 *
	 * @return string                The value of the key for this product, or
	 *                               empty string.
	 */
	public function render_column_msrp_price( $key, $product ) {
		if ( 'variation' === $product->get_type() ) {
			$msrp_price = $product->get_meta( '_msrp', true );
		} else {
			$msrp_price = $product->get_meta( '_msrp_price', true );
		}
		return $msrp_price;
	}
}
