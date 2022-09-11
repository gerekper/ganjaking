<?php
/**
 * @package Polylang-WC
 */

/**
 * A class to export languages and translations of products in CSV files.
 *
 * @since 0.8
 */
class PLLWC_Export {

	/**
	 * Constructor.
	 * Setups filters.
	 *
	 * @since 0.8
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'default_columns' ) );
		add_filter( 'woocommerce_product_export_row_data', array( $this, 'row_data' ), 10, 2 );
	}

	/**
	 * Adds the language and translation group to default columns.
	 * Hooked to the filter 'woocommerce_product_export_product_default_columns'.
	 *
	 * @since 0.8
	 *
	 * @param string[] $columns Columns to export.
	 * @return string[]
	 */
	public function default_columns( $columns ) {
		return array_merge(
			$columns,
			array(
				'language'     => __( 'Language', 'polylang-wc' ),
				'translations' => __( 'Translation group', 'polylang-wc' ),
			)
		);
	}

	/**
	 * Exports the product language and translation group.
	 * Hooked to the filter 'woocommerce_product_export_row_data'.
	 *
	 * @since 0.8
	 *
	 * @param array      $row     Data exported in a CSV row.
	 * @param WC_Product $product Product.
	 * @return array
	 */
	public function row_data( $row, $product ) {
		/** @var PLLWC_Product_Language_CPT */
		$data_store = PLLWC_Data_Store::load( 'product_language' );

		$id = $product->get_id();

		if ( isset( $row['language'] ) ) {
			$row['language'] = $data_store->get_language( $id );
		}

		if ( isset( $row['translations'] ) ) {
			$row['translations'] = $data_store->get_translation_group_name( $id );
		}

		return $row;
	}
}
