<?php
/**
 * A class for rendering a product catalog in an CSV format.
 *
 * @package WC_Instagram/Product_Catalog/Formats
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
	 * @return string
	 */
	public function get_output() {
		ob_start();

		$output = fopen( 'php://output', 'w' );

		fputcsv( $output, $this->get_output_heading() );

		$product_items = $this->get_product_items();

		foreach ( $product_items as $product_item ) {
			fputcsv( $output, $this->get_formatted_item( $product_item ) );
		}

		fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

		return ob_get_clean();
	}

	/**
	 * Gets the starting content of the formatted product catalog.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_output_start() {
		return $this->format_csv( $this->get_output_heading() );
	}

	/**
	 * Gets the content of the formatted item.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Set method visibility to public.
	 *                  Returns a string instead of an array.
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item A catalog item.
	 * @return string
	 */
	public function get_output_item( $product_item ) {
		return $this->format_csv( $this->get_formatted_item( $product_item ) );
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
	 * Loads the product item properties.
	 *
	 * @since 4.0.0
	 */
	protected function load_item_props() {
		parent::load_item_props();

		/*
		 * Replace the function 'esc_url' by 'esc_url_raw'.
		 * Don't scape the character ampersand.
		 */
		foreach ( $this->item_props as $key => $sanitize_callback ) {
			if ( 'esc_url' === $sanitize_callback ) {
				$this->item_props[ $key ] = 'esc_url_raw';
			}
		}
	}

	/**
	 * Gets the formatted catalog item.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item The catalog item.
	 * @return array
	 */
	protected function get_formatted_item( $product_item ) {
		$item   = parent::get_formatted_item( $product_item );
		$props  = $this->get_item_props();
		$values = array();

		// Iterate over the properties to preserve the number and order of the columns.
		foreach ( $props as $prop => $callback ) {
			if ( ! isset( $item[ $prop ] ) ) {
				$values[ $prop ] = '';
			} elseif ( is_array( $item[ $prop ] ) ) {
				$values[ $prop ] = implode( ',', $item[ $prop ] );
			} else {
				$values[ $prop ] = $item[ $prop ];
			}
		}

		return $values;
	}

	/**
	 * Formats an array of fields to a CSV string.
	 *
	 * @since 4.0.0
	 *
	 * @param array $fields An array of strings.
	 * @return string
	 */
	protected function format_csv( $fields ) {
		$handle = fopen( 'php://temp', 'r+b' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

		fputcsv( $handle, $fields );
		rewind( $handle );

		$string = stream_get_contents( $handle );

		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

		return $string;
	}
}
