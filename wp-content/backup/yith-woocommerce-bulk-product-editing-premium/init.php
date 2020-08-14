<?php
/**
 * Plugin Name: YITH WooCommerce Bulk Product Editing Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-bulk-product-editing/
 * Description: <code><strong>YITH WooCommerce Bulk Product Editing</strong></code> allows you to edit multiple products at the same time. You can easily filter products and edit all fields you need in a massive, simple and fast way. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.2.24
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-bulk-product-editing
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.3.x
 *
 * @author yithemes
 * @package YITH WooCommerce Bulk Product Editing Premium
 * @version 1.2.24
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
yit_deactive_free_version( 'YITH_WCBEP_FREE_INIT', plugin_basename( __FILE__ ) );

function yith_wcbep_pr_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e( 'YITH WooCommerce Bulk Product Editing Premium Version is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-bulk-product-editing' ); ?></p>
    </div>
    <?php
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( !defined( 'YITH_WCBEP_VERSION' ) ) {
    define( 'YITH_WCBEP_VERSION', '1.2.24' );
}

if ( !defined( 'YITH_WCBEP_PREMIUM' ) ) {
    define( 'YITH_WCBEP_PREMIUM', '1' );
}

if ( !defined( 'YITH_WCBEP_INIT' ) ) {
    define( 'YITH_WCBEP_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YITH_WCBEP' ) ) {
    define( 'YITH_WCBEP', true );
}

if ( !defined( 'YITH_WCBEP_FILE' ) ) {
    define( 'YITH_WCBEP_FILE', __FILE__ );
}

if ( !defined( 'YITH_WCBEP_URL' ) ) {
    define( 'YITH_WCBEP_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'YITH_WCBEP_DIR' ) ) {
    define( 'YITH_WCBEP_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_WCBEP_TEMPLATE_PATH' ) ) {
    define( 'YITH_WCBEP_TEMPLATE_PATH', YITH_WCBEP_DIR . 'templates' );
}

if ( !defined( 'YITH_WCBEP_ASSETS_URL' ) ) {
    define( 'YITH_WCBEP_ASSETS_URL', YITH_WCBEP_URL . 'assets' );
}

if ( !defined( 'YITH_WCBEP_ASSETS_PATH' ) ) {
    define( 'YITH_WCBEP_ASSETS_PATH', YITH_WCBEP_DIR . 'assets' );
}

if ( !defined( 'YITH_WCBEP_INCLUDES_PATH' ) ) {
    define( 'YITH_WCBEP_INCLUDES_PATH', YITH_WCBEP_DIR . 'includes' );
}

if ( !defined( 'YITH_WCBEP_SLUG' ) ) {
    define( 'YITH_WCBEP_SLUG', 'yith-woocommerce-bulk-product-editing' );
}

if ( !defined( 'YITH_WCBEP_SECRET_KEY' ) ) {
    define( 'YITH_WCBEP_SECRET_KEY', 'sLeuS6GCDyhRQQtzN8Oz' );
}

function yith_wcbep_pr_init() {

    load_plugin_textdomain( 'yith-woocommerce-bulk-product-editing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    // Load required classes and functions
    require_once( 'includes/class.yith-wcbep-list-table.php' );
    require_once( 'includes/class.yith-wcbep-list-table-premium.php' );
    require_once( 'includes/class.yith-wcbep-custom-fields-manager.php' );
    require_once( 'includes/class.yith-wcbep-custom-taxonomies-manager.php' );
    require_once( 'includes/compatibility/class.yith-wcbep-compatibility.php' );
    require_once( 'includes/functions.yith-wcbep-premium.php' );
    require_once( 'includes/class.yith-wcbep-admin.php' );
    require_once( 'includes/class.yith-wcbep.php' );
    require_once( 'includes/class.yith-wcbep-admin-premium.php' );
    require_once( 'includes/class.yith-wcbep-premium.php' );

    // Let's start the game!
    YITH_WCBEP();
}

add_action( 'yith_wcbep_pr_init', 'yith_wcbep_pr_init' );


function yith_wcbep_pr_install() {

    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_wcbep_pr_install_woocommerce_admin_notice' );
    } else {
        do_action( 'yith_wcbep_pr_init' );
    }
}

add_action( 'plugins_loaded', 'yith_wcbep_pr_install', 11 );

/* Plugin Framework Version Check */
if ( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );