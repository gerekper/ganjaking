<?php
/**
 * Plugin Name: WooCommerce Boost Sales Premium
 * Plugin URI: http://villatheme.com
 * Description: Increases sales from every order by using Up-sell and Cross-sell techniques for your online store.
 * Version: 1.4.2
 * Author: Andy Ha (villatheme.com)
 * Author URI: http://villatheme.com
 * Copyright 2017-2020 VillaTheme.com. All rights reserved.
 * Tested up to: 5.4
 * WC tested up to: 4.2
**/
define( 'VI_WBOOSTSALES_VERSION', '1.4.2' );
/**
 * Detect plugin. For use on Front End only.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woocommerce-boost-sales" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "define.php";
	require_once $init_file;
}


/**
 * Class VI_WBOOSTSALES
 */
class VI_WBOOSTSALES {
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		add_action( 'admin_notices', array( $this, 'global_note' ) );
	}

	function global_note() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			deactivate_plugins( 'woocommerce-boost-sales/woocommerce-boost-sales.php' );
			unset( $_GET['activate'] );
			?>
			<div id="message" class="error">
				<p><?php _e( 'Please install and activate WooCommerce to use WooCommerce Boost Sales.', 'woocommerce-boost-sales' ); ?></p>
			</div>
			<?php
		}
	}


	/**
	 * When active plugin Function will be call
	 */
	public function install() {
		global $wp_version;
		if ( version_compare( $wp_version, "2.9", "<" ) ) {
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
			wp_die( "This plugin requires WordPress version 2.9 or higher." );
		}
		$json_data = '{"enable_mobile":"1","enable_upsell":"1","show_with_category":"1","sort_product":"4","crosssell_enable":"1","crosssell_display_on":"0","enable_cart_page":"1","cart_page_option":"1","enable_checkout_page":"1","checkout_page_option":"1","crosssell_description":"Hang on! We have this offer just for you!","coupon_desc":"SWEET! Add more products and get {discount_amount} off on your entire order!","enable_thankyou":"1","message_congrats":"You have successfully reached the goal, and a {discount_amount} discount will be applied to your order.","text_btn_checkout":"Checkout now","button_color":"#111111","button_bg_color":"#bdbdbd","init_delay":"3,10","enable_cross_sell_open":"1","icon":"0","custom_gift_image":"0","icon_color":"#555555","icon_bg_color":"#ffffff","icon_position":"0","bg_color_cross_sell":"#ffffff","bg_image_cross_sell":"0","text_color_cross_sell":"#9e9e9e","price_text_color_cross_sell":"#111111","save_price_text_color_cross_sell":"#111111","item_per_row":"4","limit":"8","select_template":"1","message_bought":"Frequently bought with {name_product}","coupon_position":"0","text_color_discount":"#111111","process_color":"#111111","process_background_color":"#bdbdbd","custom_css":"","key":""}';
		if ( ! get_option( '_woocommerce_boost_sales', '' ) ) {
			update_option( '_woocommerce_boost_sales', json_decode( $json_data, true ) );
		}
	}

	/**
	 * When deactive function will be call
	 */
	public function uninstall() {

	}
}

new VI_WBOOSTSALES();