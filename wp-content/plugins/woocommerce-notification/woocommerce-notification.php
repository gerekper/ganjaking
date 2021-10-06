<?php
/**
 * Plugin Name: WooCommerce Notification Premium
 * Plugin URI: http://villatheme.com
 * Description: Increase conversion rate by highlighting other customers that have bought products.
 * Version: 1.4.2.5
 * Author: Andy Ha (villatheme.com)
 * Author URI: http://villatheme.com
 * Copyright 2016-2021 VillaTheme.com. All rights reserved.
 * Requires PHP: 7.0
 * Requires at least: 5.0
 * Tested up to: 5.8
 * WC requires at least: 4.0
 * WC tested up to: 5.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_WNOTIFICATION_VERSION', '1.4.2.5' );
/**
 * Detect plugin. For use on Front End only.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woocommerce-notification" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "define.php";
	require_once $init_file;
}

/**
 * Class VI_WNOTIFICATION
 */
class VI_WNOTIFICATION {
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		add_action( 'admin_notices', array( $this, 'global_note' ) );
	}

	/**
	 *
	 */
	function global_note() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			?>
			<div id="message" class="error">
				<p><?php _e( 'Please install WooCommerce and active. WooCommerce Notification is going to working.', 'woocommerce-notification' ); ?></p>
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
		$json_data = '{"enable":"1","enable_mobile":"1","archive_page":"2","limit_product":"50","order_threshold_num":"60","order_threshold_time":"2","order_statuses":["wc-processing","wc-completed"],"virtual_name":"Oliver\r\nJack\r\nHarry\r\nJacob\r\nCharlie","virtual_time":"10","country":"1","virtual_city":"New York City, New York, USA\r\nEkwok, Alaska, USA\r\nLondon, England\r\nAldergrove, British Columbia, Canada\r\nURRAWEEN, Queensland, Australia\r\nBernau, Freistaat Bayern, Germany","virtual_country":"","ipfind_auth_key":"","product_sizes":"shop_thumbnail","non_ajax":"1","notification_product_show_type":"1","highlight_color":"#000000","text_color":"#000000","background_color":"#ffffff","background_image":"0","image_position":"0","position":"0","border_radius":"0","message_display_effect":"fade-in","message_hidden_effect":"fade-out","loop":"1","next_time":"30","notification_per_page":"60","initial_delay_random":"1","initial_delay_min":"0","initial_delay":"5","display_time":"5","sound":"cool.mp3","message_purchased":["Someone in {city} purchased a {product_with_link} {time_ago}","{product_with_link} {custom}"],"custom_shortcode":"{number} people seeing this product right now","min_number":"100","max_number":"200","conditional_tags":"","history_time":"30","key":""}';
		if ( ! get_option( 'wnotification_params', '' ) ) {
			update_option( 'wnotification_params', json_decode( $json_data, true ) );
		}
	}

	/**
	 * When deactive function will be call
	 */
	public function uninstall() {

	}
}

new VI_WNOTIFICATION();