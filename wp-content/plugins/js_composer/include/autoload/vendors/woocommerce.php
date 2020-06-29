<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Add script for grid item add to card link
 *
 * @since 4.5
 */
function vc_woocommerce_add_to_cart_script() {
	if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
		wp_enqueue_script( 'vc_woocommerce-add-to-cart-js', vc_asset_url( 'js/vendors/woocommerce-add-to-cart.js' ), array( 'wc-add-to-cart' ), WPB_VC_VERSION );
	}
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize plugin WooCommerce vendor. (adds tons of WooCommerce shortcodes and some fixes)
 */
add_action( 'plugins_loaded', 'vc_init_vendor_woocommerce' );
function vc_init_vendor_woocommerce() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require class-vc-wxr-parser-plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) || class_exists( 'WooCommerce' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-woocommerce.php' );
		$vendor = new Vc_Vendor_Woocommerce();
		add_action( 'vc_before_init', array(
			$vendor,
			'load',
		), 0 );
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/woocommerce/grid-item-filters.php' );
		// Add 'add to card' link to the list of Add link.
		add_filter( 'vc_gitem_add_link_param', 'vc_gitem_add_link_param_woocommerce' );
		// Filter to add link attributes for grid element shortcode.
		add_filter( 'vc_gitem_post_data_get_link_link', 'vc_gitem_post_data_get_link_link_woocommerce', 10, 3 );
		add_filter( 'vc_gitem_post_data_get_link_target', 'vc_gitem_post_data_get_link_target_woocommerce', 12, 2 );
		add_filter( 'vc_gitem_post_data_get_link_real_link', 'vc_gitem_post_data_get_link_real_link_woocommerce', 10, 4 );
		add_filter( 'vc_gitem_post_data_get_link_real_target', 'vc_gitem_post_data_get_link_real_target_woocommerce', 12, 3 );
		add_filter( 'vc_gitem_zone_image_block_link', 'vc_gitem_zone_image_block_link_woocommerce', 10, 3 );
		add_action( 'wp_enqueue_scripts', 'vc_woocommerce_add_to_cart_script' );
	}
}
