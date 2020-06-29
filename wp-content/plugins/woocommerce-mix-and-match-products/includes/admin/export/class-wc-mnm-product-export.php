<?php
/**
 * Product Export Class
 *
 * @author   SomewhereWarm
 * @category Admin
 * @package  WooCommerce Mix and Match Products/Admin/Export
 * @since    1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Product_Export Class.
 *
 * Add support for MNM products to WooCommerce product export.
 */
class WC_MNM_Product_Export {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Add CSV columns for exporting container data.
		add_filter( 'woocommerce_product_export_column_names', array( __CLASS__, 'add_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( __CLASS__, 'add_columns' ) );

		// "MnM Items" column data.
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_contents', array( __CLASS__, 'export_contents' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_min_container_size', array( __CLASS__, 'export_min_container_size' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_max_container_size', array( __CLASS__, 'export_max_container_size' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_priced_per_product', array( __CLASS__, 'export_priced_per_product' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_shipped_per_product', array( __CLASS__, 'export_shipped_per_product' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_discount', array( __CLASS__, 'export_discount_per_product' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_layout', array( __CLASS__, 'export_layout' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_add_to_cart_form_location', array( __CLASS__, 'export_add_to_cart_form_location' ), 10, 2 );
	}

	/**
	 * Add CSV columns for exporting container data.
	 *
	 * @param  array  $columns
	 * @return array  $columns
	 */
	public static function add_columns( $columns ) {

		$columns[ 'wc_mnm_contents' ]  		           = __( 'MnM Contents (JSON-encoded)', 'woocommerce-mix-and-match-products' );
		$columns[ 'wc_mnm_min_container_size' ]        = __( 'MnM Minimum Container Size', 'woocommerce-mix-and-match-products' );
		$columns[ 'wc_mnm_max_container_size' ]        = __( 'MnM Maximum Container Size', 'woocommerce-mix-and-match-products' );
		$columns[ 'wc_mnm_priced_per_product' ]        = __( 'MnM Per-Item Pricing', 'woocommerce-mix-and-match-products' );
		$columns[ 'wc_mnm_shipped_per_product' ] 	   = __( 'MnM Per-Item Shipping', 'woocommerce-mix-and-match-products' );
		$columns[ 'wc_mnm_discount' ] 	   = __( 'MnM Per-Item Discount', 'woocommerce-mix-and-match-products' );
		$columns[ 'wc_mnm_layout' ] 	   			   = __( 'MnM Layout', 'woocommerce-mix-and-match-products' );
		$columns[ 'wc_mnm_add_to_cart_form_location' ] = __( 'MnM Add to Cart Form Location', 'woocommerce-mix-and-match-products' );

		/**
		 * Mix and Match Export columns.
		 *
		 * @param  array $columns
		 */
		return apply_filters( 'woocommerce_mnm_export_column_names', $columns );
	}

	/**
	 * MnM contents data column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_contents( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {

			$mnm_contents = $product->get_contents( 'edit' );

			if ( ! empty( $mnm_contents ) ) {

				$data = array();

				foreach ( $mnm_contents as $mnm_item_id => $mnm_item_data ) {
					
					$mnm_item_data = array();

					$mnm_product    = wc_get_product( $mnm_item_id );
					
					if ( ! $mnm_product ) {
						return $value;
					}

					$mnm_product_id = $mnm_product->is_type( 'variation ' ) ? $mnm_product->get_parent_id( 'edit' ) : $mnm_product->get_id( 'edit' );

					$mnm_product_sku = $mnm_product->get_sku( 'edit' );

					// Refer to exported products by their SKU, if present.
					$mnm_item_data[ 'product_id' ] = $mnm_product_sku ? $mnm_product_sku : 'id:' . $mnm_product_id;

					$data[ $mnm_item_id ] = $mnm_item_data;
				}

				$value = json_encode( $data );

			}
		}

		return $value;
	}

	/**
	 * "Min Container Quantity" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_min_container_size( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->get_min_container_size( 'edit' );
		}

		return $value;
	}

	/**
	 * "max Container Quantity" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_max_container_size( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->get_max_container_size( 'edit' );
		}

		return $value;
	}

	/**
	 * "Container priced per product" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_priced_per_product( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->is_priced_per_product( 'edit' ) ? 1 : 0;
		}

		return $value;
	}

	/**
	 * "Container shipped per product" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_shipped_per_product( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->is_shipped_per_product( 'edit' ) ? 1 : 0;
		}

		return $value;
	}


	/**
	 * "Container Discount per product" column content.
	 *
	 * @since  1.4.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_discount_per_product( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->is_priced_per_product( 'edit' ) ? $product->get_discount( 'edit' ) : '';
		}

		return $value;
	}

	/**
	 * "Layout" column content.
	 *
	 * @since  1.4.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_layout( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->get_layout( 'edit' );
		}

		return $value;
	}

	/**
	 * "Add to cart form location" column content.
	 *
	 * @since  1.4.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_add_to_cart_form_location( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->get_add_to_cart_form_location( 'edit' );
		}

		return $value;
	}

}

WC_MNM_Product_Export::init();
