<?php
/**
 * Plugin Name: YITH Point of Sale for WooCommerce
 * Plugin URI: https://yithemes.com/themes/plugins/yith-point-of-sale-for-woocommerce
 * Description: <code><strong>YITH Point of Sale for WooCommerce</strong></code> allows you to turn your WooCommerce installation into an easy to use and powerful cash register for each type of store or business. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-point-of-sale-for-woocommerce
 * Domain Path: /languages/
 * Version: 1.0.2
 * Author URI: https://yithemes.com/
 * Requires at least: 5.0
 * Tested up to: 5.4.x
 * WC requires at least: 3.8.0
 * WC tested up to: 4.0.x
 *
 * @author  yithemes
 * @package YITH Point of Sale for WooCommerce
 * @version 1.0.2
 * =.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=.=
 * Copyright 2015 Your Inspiration Themes  (email : plugins@yithemes.com)
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( ! function_exists( 'yith_pos_install_woocommerce_admin_notice' ) ) {
	/**
	 * Print a notice if WooCommerce is not installed.
	 *
	 * @since  1.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function yith_pos_install_woocommerce_admin_notice() {
		?>
        <div class="error">
            <p><?php echo sprintf( __( '%s is enabled but not effective. It requires WooCommerce in order to work.', 'yith-point-of-sale-for-woocommerce' ), YITH_POS_PLUGIN_NAME ); ?></p>
        </div>
		<?php
	}
}
! defined( 'YITH_POS' ) && define( 'YITH_POS', true );
! defined( 'YITH_POS_VERSION' ) && define( 'YITH_POS_VERSION', '1.0.2' );
! defined( 'YITH_POS_INIT' ) && define( 'YITH_POS_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_POS_FILE' ) && define( 'YITH_POS_FILE', __FILE__ );
! defined( 'YITH_POS_URL' ) && define( 'YITH_POS_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_POS_DIR' ) && define( 'YITH_POS_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_POS_ASSETS_URL' ) && define( 'YITH_POS_ASSETS_URL', YITH_POS_URL . 'assets' );
! defined( 'YITH_POS_REACT_URL' ) && define( 'YITH_POS_REACT_URL', YITH_POS_URL . 'dist' );
! defined( 'YITH_POS_ASSETS_PATH' ) && define( 'YITH_POS_ASSETS_PATH', YITH_POS_DIR . 'assets' );
! defined( 'YITH_POS_TEMPLATE_PATH' ) && define( 'YITH_POS_TEMPLATE_PATH', YITH_POS_DIR . 'templates/' );
! defined( 'YITH_POS_LANGUAGES_PATH' ) && define( 'YITH_POS_LANGUAGES_PATH', YITH_POS_DIR . 'languages/' );
! defined( 'YITH_POS_VIEWS_PATH' ) && define( 'YITH_POS_VIEWS_PATH', YITH_POS_DIR . 'views/' );
! defined( 'YITH_POS_INCLUDES_PATH' ) && define( 'YITH_POS_INCLUDES_PATH', YITH_POS_DIR . '/includes/' );
! defined( 'YITH_POS_SLUG' ) && define( 'YITH_POS_SLUG', 'yith-point-of-sale-for-woocommerce' );
! defined( 'YITH_POS_SECRET_KEY' ) && define( 'YITH_POS_SECRET_KEY', '1415b451be1a13c283ba771ea52d38bb' );
! defined( 'YITH_POS_PLUGIN_NAME' ) && define( 'YITH_POS_PLUGIN_NAME', 'YITH Point of Sale for WooCommerce' );
if ( ! defined( 'YITH_POS_COOKIEHASH' ) ) {
	$site_url = get_site_option( 'siteurl' );
	$hash     = ! ! $site_url ? md5( $site_url ) : '';
	define( 'YITH_POS_COOKIEHASH', defined( 'COOKIEHASH' ) ? COOKIEHASH : $hash );
}
! defined( 'YITH_POS_REGISTER_COOKIE' ) && define( 'YITH_POS_REGISTER_COOKIE', 'yith_pos_register_' . YITH_POS_COOKIEHASH );

require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-post-types.php' );
require_once( YITH_POS_INCLUDES_PATH . 'functions.yith-pos.php' );
register_activation_hook( __FILE__, array( 'YITH_POS_Post_Types', 'handle_roles_and_capabilities' ) );
register_activation_hook( __FILE__, array( 'YITH_POS_Post_Types', 'create_default_receipt' ) );

if ( ! function_exists( 'yith_pos_install' ) ) {
	/**
	 * Check WC installation
	 *
	 * @since  1.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function yith_pos_install() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_pos_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_pos_init' );
			require_once( 'includes/abstract.yith-pos-db.php' );
			YITH_POS_DB::install();
		}
	}
}
add_action( 'plugins_loaded', 'yith_pos_install', 11 );

if ( ! function_exists( 'yith_pos_init' ) ) {
	/**
	 * Let's start the game
	 *
	 * @since  1.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function yith_pos_init() {
		load_plugin_textdomain( 'yith-point-of-sale-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos.php' );
		// Let's start the game!
		YITH_POS();
	}
}
add_action( 'yith_pos_init', 'yith_pos_init' );


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_POS_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_POS_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_POS_DIR );