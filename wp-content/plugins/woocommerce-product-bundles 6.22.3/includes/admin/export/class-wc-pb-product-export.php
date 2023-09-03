<?php
/**
 * WC_PB_Product_Export class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Exporter support.
 *
 * @class    WC_PB_Product_Export
 * @version  6.17.4
 */
class WC_PB_Product_Export {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Add CSV columns for exporting bundle data.
		add_filter( 'woocommerce_product_export_column_names', array( __CLASS__, 'add_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( __CLASS__, 'add_columns' ) );

		// "Bundled Items" column data.
		add_filter( 'woocommerce_product_export_product_column_wc_pb_bundled_items', array( __CLASS__, 'export_bundled_items' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_min_bundle_size', array( __CLASS__, 'export_min_bundle_size' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_max_bundle_size', array( __CLASS__, 'export_max_bundle_size' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_virtual_bundle', array( __CLASS__, 'export_virtual_bundle' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_aggregate_weight', array( __CLASS__, 'export_aggregate_weight' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_layout', array( __CLASS__, 'export_layout' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_group_mode', array( __CLASS__, 'export_group_mode' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_editable_in_cart', array( __CLASS__, 'export_editable_in_cart' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_sold_individually_context', array( __CLASS__, 'export_sold_individually_context' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_add_to_cart_form_location', array( __CLASS__, 'export_add_to_cart_form_location' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_bundle_sells', array( __CLASS__, 'export_bundle_sells' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_bundle_sells_title', array( __CLASS__, 'export_bundle_sells_title' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_pb_bundle_sells_discount', array( __CLASS__, 'export_bundle_sells_discount' ), 10, 2 );
	}

	/**
	 * Add CSV columns for exporting bundle data.
	 *
	 * @param  array  $columns
	 * @return array  $columns
	 */
	public static function add_columns( $columns ) {

		$columns[ 'wc_pb_bundled_items' ]             = __( 'Bundled Items (JSON-encoded)', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_min_bundle_size' ]           = __( 'Min Bundle Size', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_max_bundle_size' ]           = __( 'Max Bundle Size', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_virtual_bundle' ]            = __( 'Bundle Contents Virtual', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_aggregate_weight' ]          = __( 'Bundle Aggregate Weight', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_layout' ]                    = __( 'Bundle Layout', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_group_mode' ]                = __( 'Bundle Group Mode', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_editable_in_cart' ]          = __( 'Bundle Cart Editing', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_sold_individually_context' ] = __( 'Bundle Sold Individually', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_add_to_cart_form_location' ] = __( 'Bundle Form Location', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_bundle_sells' ]              = __( 'Bundle Sells', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_bundle_sells_title' ]        = __( 'Bundle Sells Title', 'woocommerce-product-bundles' );
		$columns[ 'wc_pb_bundle_sells_discount' ]     = __( 'Bundle Sells Discount', 'woocommerce-product-bundles' );

		return $columns;
	}

	/**
	 * Bundle data column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_bundled_items( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {

			$bundled_items = $product->get_bundled_data_items( 'edit' );

			if ( ! empty( $bundled_items ) ) {

				$data = array();

				foreach ( $bundled_items as $bundled_item ) {

					$bundled_item_id    = $bundled_item->get_id();
					$bundled_item_data  = $bundled_item->get_data();

					// Bundled item stock information not needed.
					unset( $bundled_item_data[ 'meta_data' ][ 'stock_status' ] );
					unset( $bundled_item_data[ 'meta_data' ][ 'max_stock' ] );

					$bundled_product_id = $bundled_item->get_product_id();
					$bundled_product    = wc_get_product( $bundled_product_id );

					if ( ! $bundled_product ) {
						return $value;
					}

					// Not needed as we will be re-creating all bundled items during import.
					unset( $bundled_item_data[ 'bundled_item_id' ] );
					unset( $bundled_item_data[ 'bundle_id' ] );

					$bundled_product_sku = $bundled_product->get_sku( 'edit' );

					// Refer to exported products by their SKU, if present.
					$bundled_item_data[ 'product_id' ] = $bundled_product_sku ? $bundled_product_sku : 'id:' . $bundled_product_id;

					$data[ $bundled_item_id ] = $bundled_item_data;
				}

				$value = json_encode( $data );
			}
		}

		return $value;
	}

	/**
	 * "Min Bundle Size" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_min_bundle_size( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_min_bundle_size( 'edit' );
		}

		return $value;
	}

	/**
	 * "Max Bundle Size" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_max_bundle_size( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_max_bundle_size( 'edit' );
		}

		return $value;
	}

	/**
	 * "Bundle Contents Virtual" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_virtual_bundle( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_virtual_bundle( 'edit' ) ? 1 : 0;
		}

		return $value;
	}

	/**
	 * "Bundle Aggregate Weight" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_aggregate_weight( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_aggregate_weight() ? 1 : 0;
		}

		return $value;
	}

	/**
	 * "Bundle Layout" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_layout( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_layout( 'edit' );
		}

		return $value;
	}

	/**
	 * "Bundle Group Mode" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_group_mode( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_group_mode( 'edit' );
		}

		return $value;
	}

	/**
	 * "Bundle Cart Editing" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_editable_in_cart( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_editable_in_cart( 'edit' ) ? 1 : 0;
		}

		return $value;
	}

	/**
	 * "Bundle Sold Individually" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_sold_individually_context( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_sold_individually_context( 'edit' );
		}

		return $value;
	}

	/**
	 * "Bundle Form Location" column content.
	 *
	 * @since  5.8.1
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_add_to_cart_form_location( $value, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$value = $product->get_add_to_cart_form_location( 'edit' );
		}

		return $value;
	}

	/**
	 * "Bundle Sells" field content.
	 *
	 * @since  6.1.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_bundle_sells( $value, $product ) {

		if ( ! $product->is_type( 'bundle' ) ) {

			$bundle_sells = $product->get_meta( '_wc_pb_bundle_sell_ids', true );

			if ( ! empty( $bundle_sells ) ) {

				$product_list = array();

				foreach ( $bundle_sells as $bundle_sell ) {

					if ( $linked_product = wc_get_product( $bundle_sell ) ) {

						if ( $linked_product->get_sku() ) {
							$product_list[] = str_replace( ',', '\\,', $linked_product->get_sku() );
						} else {
							$product_list[] = 'id:' . $linked_product->get_id();
						}
					}
				}

				$value = implode( ', ', $product_list );
			}
		}

		return $value;
	}

	/**
	 * "Bundle Sells Title" field content.
	 *
	 * @since  6.1.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_bundle_sells_title( $value, $product ) {

		if ( ! $product->is_type( 'bundle' ) ) {

			$bundle_sells_title = $product->get_meta( '_wc_pb_bundle_sells_title', true );

			if ( ! empty( $bundle_sells_title ) ) {
				$value = $bundle_sells_title;
			}
		}

		return $value;
	}

	/**
	 * "Bundle Sells Discount" field content.
	 *
	 * @since  6.1.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_bundle_sells_discount( $value, $product ) {

		if ( ! $product->is_type( 'bundle' ) ) {

			$bundle_sells_discount = $product->get_meta( '_wc_pb_bundle_sells_discount', true );

			if ( ! empty( $bundle_sells_discount ) ) {
				$value = $bundle_sells_discount;
			}
		}

		return $value;
	}
}

WC_PB_Product_Export::init();
