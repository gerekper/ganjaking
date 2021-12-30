<?php
/**
 * WC_PB_Admin_Post_Types class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add hooks to the edit posts view for the 'product' post type.
 *
 * @class    WC_PB_Admin_Post_Types
 * @version  6.10.0
 */
class WC_PB_Admin_Post_Types {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Add details to admin product stock info when the bundled stock is insufficient.
		add_filter( 'woocommerce_admin_stock_html', array( __CLASS__, 'admin_stock_html' ), 10, 2 );

		// Add support for bulk editing Bundle's Regular/Sale price.
		add_filter( 'woocommerce_bulk_edit_save_price_product_types', array( __CLASS__, 'bulk_edit_price' ), 10, 1 );
	}

	/**
	 * Add details to admin stock info when contents stock is insufficient.
	 *
	 * @param  string      $stock_status
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function admin_stock_html( $stock_status, $product ) {

		if ( 'bundle' === $product->get_type() ) {

			if ( $product->is_parent_in_stock() ) {

				if ( $product->contains( 'out_of_stock_strict' ) || 'outofstock' === $product->get_bundled_items_stock_status() ) {

					ob_start();

					?><mark class="outofstock insufficient_stock"><?php _e( 'Insufficient stock', 'woocommerce-product-bundles' ); ?></mark><?php

					if ( $product->contains( 'out_of_stock_strict' ) ) {

						$report_url = ! WC_PB_Admin_Analytics::is_enabled() ? 'admin.php?page=wc-reports&tab=stock&report=insufficient_stock&bundle_id=' . $product->get_id() : 'admin.php?page=wc-admin&path=%2Fanalytics%2Fbundles&section=stock&filter=single_product&products=' . $product->get_id();

						?><div class="row-actions">
							<span class="view"><a href="<?php echo admin_url( $report_url ); ?>" rel="bookmark" aria-label="<?php _e( 'View Report', 'woocommerce-product-bundles' ); ?>"><?php _e( 'View Report', 'woocommerce-product-bundles' ); ?></a></span>
						</div><?php
					}

					$stock_status = ob_get_clean();

				} else {

					$bundle_stock_quantity = $product->get_bundle_stock_quantity();

					if ( '' !== $bundle_stock_quantity ) {
						$stock_status = '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark> (' . wc_stock_amount( $bundle_stock_quantity ) . ')';
					}
				}
			}
		}

		return $stock_status;
	}

	/**
	 * Add support for bulk editing Bundle's Regular/Sale price.
	 *
	 * @param  array      $supported_product_types
	 * @return array
	 */
	public static function bulk_edit_price( $supported_product_types ) {

		$supported_product_types[] = 'bundle';

		return $supported_product_types;
	}
}

WC_PB_Admin_Post_Types::init();
