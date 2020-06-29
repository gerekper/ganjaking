<?php
/**
 * A class for rendering a product catalog in an CSV format.
 *
 * @package WC_Instagram/Product Catalog/Formats
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_Product_Catalog_Format', false ) ) {
	include_once 'abstract-class-wc-instagram-product-catalog-format.php';
}

/**
 * WC_Instagram_Product_Catalog_Format_CSV class.
 */
class WC_Instagram_Product_Catalog_Format_CSV extends WC_Instagram_Product_Catalog_Format {

	/**
	 * The format used to render the product catalog.
	 *
	 * @var string
	 */
	protected $format = 'csv';

	/**
	 * Gets the formatted product catalog.
	 *
	 * @since 3.0.0
	 *
	 * return string
	 */
	public function get_output() {
		ob_start();

		$output = fopen( 'php://output', 'w' );

		fputcsv( $output, $this->get_output_heading() );

		foreach ( $this->get_product_items() as $product_item ) {
			fputcsv( $output, $this->get_output_item( $product_item ) );
		}

		fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

		return ob_get_clean();
	}

	/**
	 * Gets the heading row.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_output_heading() {
		return array_keys( $this->get_item_props() );
	}

	/**
	 * Gets the formatted row for the catalog item.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item A catalog item.
	 * @return array
	 */
	protected function get_output_item( $product_item ) {
		$values = array_merge(
			array_fill_keys( array_keys( $this->get_item_props() ), '' ),
			$this->get_formatted_item( $product_item )
		);

		foreach ( $values as $key => $value ) {
			if ( ! is_array( $value ) ) {
				continue;
			}

			$values[ $key ] = implode( ',', $value );
		}

		return $values;
	}

	/**
	 * Gets the properties of the catalog item.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item Optional. The catalog item. Default null.
	 * @return array
	 */
	protected function get_item_props( $product_item = null ) {
		$props = parent::get_item_props( $product_item );

		/*
		 * Replace the function 'esc_url' by 'esc_url_raw'.
		 * Don't scape the character ampersand.
		 */
		foreach ( $props as $key => $sanitize_callback ) {
			if ( 'esc_url' === $sanitize_callback ) {
				$props[ $key ] = 'esc_url_raw';
			}
		}

		return $props;
	}
}
