<?php
/**
 * Plugin Name: YITH WooCommerce Custom Order Status Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-custom-order-status/
 * Description: <code><strong>YITH WooCommerce Custom Order Status</strong></code> allows you to create and manage new custom order statuses. For example, you can create "in shipping" or "shipped" before setting orders with those statuses to completed. A big advantage for your internal management. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.2.1
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-custom-order-status
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2.x
 *
 * @author  yithemes
 * @package YITH WooCommerce Custom Order Status Premium
 * @version 1.2.1
 */
/*  Copyright 2015  Your Inspiration Themes  (email : plugins@yithemes.com)

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

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Free version deactivation if installed __________________

if ( !function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCCOS_FREE_INIT', plugin_basename( __FILE__ ) );

function yith_wccos_pr_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e( 'YITH WooCommerce Custom Order Status Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yit' ); ?></p>
    </div>
    <?php
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( !defined( 'YITH_WCCOS_VERSION' ) ) {
    define( 'YITH_WCCOS_VERSION', '1.2.1' );
}

if ( !defined( 'YITH_WCCOS_PREMIUM' ) ) {
    define( 'YITH_WCCOS_PREMIUM', '1' );
}

if ( !defined( 'YITH_WCCOS_INIT' ) ) {
    define( 'YITH_WCCOS_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YITH_WCCOS' ) ) {
    define( 'YITH_WCCOS', true );
}

if ( !defined( 'YITH_WCCOS_FILE' ) ) {
    define( 'YITH_WCCOS_FILE', __FILE__ );
}

if ( !defined( 'YITH_WCCOS_URL' ) ) {
    define( 'YITH_WCCOS_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'YITH_WCCOS_DIR' ) ) {
    define( 'YITH_WCCOS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_WCCOS_TEMPLATE_PATH' ) ) {
    define( 'YITH_WCCOS_TEMPLATE_PATH', YITH_WCCOS_DIR . 'templates' );
}

if ( !defined( 'YITH_WCCOS_ASSETS_URL' ) ) {
    define( 'YITH_WCCOS_ASSETS_URL', YITH_WCCOS_URL . 'assets' );
}

if ( !defined( 'YITH_WCCOS_ASSETS_PATH' ) ) {
    define( 'YITH_WCCOS_ASSETS_PATH', YITH_WCCOS_DIR . 'assets' );
}

if ( !defined( 'YITH_WCCOS_SLUG' ) ) {
    define( 'YITH_WCCOS_SLUG', 'yith-woocommerce-custom-order-status' );
}

if ( !defined( 'YITH_WCCOS_SECRET_KEY' ) ) {
    define( 'YITH_WCCOS_SECRET_KEY', '4yiQOGGPmRNLese2qz0I' );
}

function yith_wccos_pr_init() {

    load_plugin_textdomain( 'yith-woocommerce-custom-order-status', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    // Load required classes and functions
    require_once 'includes/class.yith-wccos-admin.php' ;
    require_once 'includes/class.yith-wccos-frontend.php' ;
    require_once 'includes/class.yith-wccos.php' ;
    require_once 'includes/class.yith-wccos-admin-premium.php' ;
    require_once 'includes/class.yith-wccos-frontend-premium.php' ;
    require_once 'includes/class.yith-wccos-premium.php' ;
    require_once 'includes/class.yith-wccos-updates.php' ;
    require_once 'includes/integrations/class.yith-wccos-integrations.php' ;
    require_once 'includes/functions.yith-wccos.php' ;
    require_once 'includes/functions.yith-wccos-colors.php' ;

    // Let's start the game!
    YITH_WCCOS();
}

add_action( 'yith_wccos_pr_init', 'yith_wccos_pr_init' );


function yith_wccos_pr_install() {

    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_wccos_pr_install_woocommerce_admin_notice' );
    } else {
        do_action( 'yith_wccos_pr_init' );
    }
}

add_action( 'plugins_loaded', 'yith_wccos_pr_install', 11 );

/* Plugin Framework Version Check */
if ( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );
