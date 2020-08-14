<?php
/**
 * Plugin Name: YITH Easy Login & Register Popup For WooCommerce
 * Plugin URI: https://yithemes.com/themes/plugins/yith-easy-login-register-popup-for-woocommerce/
 * Description: The <code><strong>YITH Easy Login & Register Popup For WooCommerce</strong></code> plugin lets you make the login, registration and password reset processes easier during the checkout and reducs the cart abandonment rate. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.5.1
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-easy-login-register-popup-for-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 3.4.0
 * WC tested up to: 4.2
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.5.1
 */
/*  Copyright 2020  Your Inspiration Solutions  ( email: plugins@yithemes.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

defined( 'YITH_WELRP' ) || define( 'YITH_WELRP', true );
defined( 'YITH_WELRP_VERSION' ) || define( 'YITH_WELRP_VERSION', '1.5.1' );
defined( 'YITH_WELRP_INIT' ) || define( 'YITH_WELRP_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_WELRP_FILE' ) || define( 'YITH_WELRP_FILE', __FILE__ );
defined( 'YITH_WELRP_URL' ) || define( 'YITH_WELRP_URL', plugin_dir_url( __FILE__ ) );
defined( 'YITH_WELRP_PATH' ) || define( 'YITH_WELRP_PATH', plugin_dir_path( __FILE__ ) );
defined( 'YITH_WELRP_TEMPLATE_PATH' ) || define( 'YITH_WELRP_TEMPLATE_PATH', YITH_WELRP_PATH . 'templates/' );
defined( 'YITH_WELRP_ASSETS_URL' ) || define( 'YITH_WELRP_ASSETS_URL', YITH_WELRP_URL . 'assets/' );
defined( 'YITH_WELRP_SLUG' ) || define( 'YITH_WELRP_SLUG', 'yith-easy-login-register-popup-for-woocommerce' );
defined( 'YITH_WELRP_SECRET_KEY' ) || define( 'YITH_WELRP_SECRET_KEY', 'OQqviMFTug6qJiFvuPyS' );


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WELRP_PATH . 'plugin-fw/init.php' ) ) {
	require_once YITH_WELRP_PATH . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_WELRP_PATH );

function yith_welrp_init() {

	load_plugin_textdomain( 'yith-easy-login-register-popup-for-woocommerce', false, dirname( YITH_WELRP_INIT ) . '/languages/' );

	// Load required classes and functions
	require_once 'includes/functions.yith-easy-login-register.php';
	require_once 'includes/class.yith-easy-login-register.php';
	YITH_Easy_Login_Register();
}

add_action( 'plugins_loaded', 'yith_welrp_init', 11 );
