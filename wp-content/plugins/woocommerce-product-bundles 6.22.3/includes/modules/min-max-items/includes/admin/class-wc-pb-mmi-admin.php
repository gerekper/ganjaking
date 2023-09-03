<?php
/**
 * WC_PB_MMI_Admin class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin functions and filters.
 *
 * @class    WC_PB_MMI_Admin
 * @version  6.6.0
 */
class WC_PB_MMI_Admin {

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Display min/max qty settings in "Bundled Products" tab.
		add_action( 'woocommerce_bundled_products_admin_config', array( __CLASS__, 'display_options' ), 16 );

		// Save min/max qty settings.
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'save_meta' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Admin min/max settings.
	 */
	public static function display_options() {

		global $product_bundle_object;

		woocommerce_wp_text_input( array(
			'id'            => '_wcpb_min_qty_limit',
			'value'         => $product_bundle_object->get_min_bundle_size( 'edit' ),
			'wrapper_class' => 'bundled_product_data_field',
			'type'          => 'number',
			'label'         => __( 'Min Bundle Size', 'woocommerce-product-bundles' ),
			'desc_tip'      => true,
			'description'   => __( 'Minimum combined quantity of bundled items.', 'woocommerce-product-bundles' )
		) );

		woocommerce_wp_text_input( array(
			'id'            => '_wcpb_max_qty_limit',
			'value'         => $product_bundle_object->get_max_bundle_size( 'edit' ),
			'wrapper_class' => 'bundled_product_data_field',
			'type'          => 'number',
			'label'         => __( 'Max Bundle Size', 'woocommerce-product-bundles' ),
			'desc_tip'      => true,
			'description'   => __( 'Maximum combined quantity of bundled items.', 'woocommerce-product-bundles' )
		) );
	}

	/**
	 * Save meta.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function save_meta( $product ) {

		if ( $product->is_type( 'bundle' ) ) {

			$props = array(
				'min_bundle_size' => '',
				'max_bundle_size' => ''
			);

			if ( ! empty( $_POST[ '_wcpb_min_qty_limit' ] ) && is_numeric( $_POST[ '_wcpb_min_qty_limit' ] ) ) {
				$props[ 'min_bundle_size' ] = stripslashes( wc_clean( $_POST[ '_wcpb_min_qty_limit' ] ) );
			}

			if ( ! empty( $_POST[ '_wcpb_max_qty_limit' ] ) && is_numeric( $_POST[ '_wcpb_max_qty_limit' ] ) ) {
				$props[ 'max_bundle_size' ] = stripslashes( wc_clean( $_POST[ '_wcpb_max_qty_limit' ] ) );
			}

			if ( ! $props[ 'min_bundle_size' ] && ! $props[ 'max_bundle_size' ] ) {
				$props = array(
					'min_bundle_size' => '',
					'max_bundle_size' => ''
				);
			}

			$product->set( $props );
		}
	}

}

WC_PB_MMI_Admin::init();
