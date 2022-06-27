<?php
/**
 * Product Export Class
 *
 * @package  WooCommerce Mix and Match Products/Admin/Export
 * @since    1.3.0
 * @version  2.0.0
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
	 * var WC_Product_CSV_Exporter Class.
	 * @since 2.0.0
	 */
	private $exporter = false;

	/**
	 * Hook in.
	 */
	public static function init() {

		// Add CSV columns for exporting container data.
		add_filter( 'woocommerce_product_export_column_names', array( __CLASS__, 'add_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( __CLASS__, 'add_columns' ) );

		// "MnM Items" column data.
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_content_source', array( __CLASS__, 'export_content_source' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_child_category_ids', array( __CLASS__, 'export_child_category_ids' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_child_items', array( __CLASS__, 'export_child_items' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_min_container_size', array( __CLASS__, 'export_min_container_size' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_max_container_size', array( __CLASS__, 'export_max_container_size' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_priced_per_product', array( __CLASS__, 'export_priced_per_product' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_packing_mode', array( __CLASS__, 'export_packing_mode' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_mnm_weight_cumulative', array( __CLASS__, 'export_weight_cumulative' ), 10, 2 );
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

		$columns['wc_mnm_wc_mnm_content_source']     = __( 'MnM Content Source', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_child_category_ids']        = __( 'MnM Child Category Ids', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_child_items']               = __( 'MnM Child Items (JSON-encoded)', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_min_container_size']        = __( 'MnM Minimum Container Size', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_max_container_size']        = __( 'MnM Maximum Container Size', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_priced_per_product']        = __( 'MnM Per-Item Pricing', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_packing_mode']              = __( 'MnM Packing Mode', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_mnm_weight_cumulative']     = __( 'MnM Weight Cumulative', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_discount']                  = __( 'MnM Per-Item Discount', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_layout_override']           = __( 'MnM Layout Override', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_layout']                    = __( 'MnM Layout', 'woocommerce-mix-and-match-products' );
		$columns['wc_mnm_add_to_cart_form_location'] = __( 'MnM Add to Cart Form Location', 'woocommerce-mix-and-match-products' );

		/**
		 * Mix and Match Export columns.
		 *
		 * @param  array $columns
		 */
		return apply_filters( 'wc_mnm_export_column_names', $columns );
	}

	/**
	 * "Contents source" column content.
	 * 
	 * @since 2.0.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_content_source( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->get_content_source( 'edit' );
		}

		return $value;
	}

	/**
	 * "Child Category Ids" column content.
	 * 
	 * @since 2.0.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_child_category_ids( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			// Use the WC_Product_CSV_Exporter formatting for term IDs.
			$exporter = new WC_Product_CSV_Exporter();
			$value = $exporter->format_term_ids( $product->get_child_category_ids( 'edit' ), 'product_cat' );
		}

		return $value;
	}


	/**
	 * MnM child items data column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_child_items( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {

			$child_items = $product->get_child_items( 'edit' );

			if ( ! empty( $child_items ) ) {

				$data = array();

				foreach ( $child_items as $item_id => $child_item ) {

					if ( ! $child_item->exists() ) {
						continue;
					}

					$child_item_data = array();
					$child_product   = $child_item->get_product();
					$child_sku       = $child_product->get_sku( 'edit' );

					// Refer to exported products by their SKU, if present.
					if ( $child_item->get_variation_id() ) {

						$parent_data = $child_product->get_parent_data();
						$parent_sku  = isset( $parent_data['sku'] ) ? $parent_data['sku'] : '';

						$data[] = array( 
                            'product_id'   => $parent_sku ? $parent_sku : 'id:' . $child_item->get_parent_id(),
                            'variation_id' => $child_sku ? $child_sku : 'id:' . $child_item->get_variation_id(),
                        );

					} else {
						$data[] = array( 
                            'product_id'   => $child_sku ? $child_sku : 'id:' . $child_item->get_product_id(),
                            'variation_id' => 0,
                        );

					}

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
	 * "Container packing mode" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_packing_mode( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->get_packing_mode( 'edit' );
		}

		return $value;
	}

	/**
	 * "Container Weight Cumulative" column content.
	 * 
	 * @since 2.0.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_weight_cumulative( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->get_weight_cumulative( 'edit' ) ? 1 : 0;
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
	 * "Layout override" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_layout_override( $value, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = $product->has_layout_override( 'edit' ) ? 1 : 0;
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

	/**
	 * MnM contents data column content.
	 * 
	 * @deprecated 2.0.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_contents( $value, $product ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::export_child_items()' );
		return self::export_child_items( $value, $product );
	}

	/**
	 * "Container shipped per product" column content.
	 * 
	 * @deprecated 2.0.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_shipped_per_product( $value, $product ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::export_packing_method()' );

		if ( $product->is_type( 'mix-and-match' ) ) {
			$value = ! $product->is_packed_together( 'edit' ) ? 1 : 0;
		}

		return $value;
	}
}

WC_MNM_Product_Export::init();
