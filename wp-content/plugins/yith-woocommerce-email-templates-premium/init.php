<?php
/**
 * Plugin Name: YITH WooCommerce Email Templates Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-email-templates/
 * Description: <code><strong>YITH WooCommerce Email Templates</strong></code> allows you to easily stylize WooCommerce email templates. You can customize colors, set a logo, social icons, custom links and so on to make them look more professional. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.3.27
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-email-templates
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.3.x
 *
 * @author  YITH
 * @package YITH WooCommerce Email Templates Premium
 * @version 1.3.27
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
yit_deactive_free_version( 'YITH_WCET_FREE_INIT', plugin_basename( __FILE__ ) );

function yith_wcet_pr_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e( 'YITH WooCommerce Email Templates Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-email-templates' ); ?></p>
    </div>
    <?php
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( !defined( 'YITH_WCET_VERSION' ) ) {
    define( 'YITH_WCET_VERSION', '1.3.27' );
}

if ( !defined( 'YITH_WCET_PREMIUM' ) ) {
    define( 'YITH_WCET_PREMIUM', '1' );
}

if ( !defined( 'YITH_WCET_INIT' ) ) {
    define( 'YITH_WCET_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YITH_WCET' ) ) {
    define( 'YITH_WCET', true );
}

if ( !defined( 'YITH_WCET_FILE' ) ) {
    define( 'YITH_WCET_FILE', __FILE__ );
}

if ( !defined( 'YITH_WCET_URL' ) ) {
    define( 'YITH_WCET_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'YITH_WCET_DIR' ) ) {
    define( 'YITH_WCET_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_WCET_TEMPLATE_PATH' ) ) {
    define( 'YITH_WCET_TEMPLATE_PATH', YITH_WCET_DIR . 'templates' );
}

if ( !defined( 'YITH_WCET_INCLUDES_PATH' ) ) {
    define( 'YITH_WCET_INCLUDES_PATH', YITH_WCET_DIR . 'includes' );
}

if ( !defined( 'YITH_WCET_ASSETS_URL' ) ) {
    define( 'YITH_WCET_ASSETS_URL', YITH_WCET_URL . 'assets' );
}

if ( !defined( 'YITH_WCET_SLUG' ) ) {
    define( 'YITH_WCET_SLUG', 'yith-woocommerce-email-templates' );
}

if ( !defined( 'YITH_WCET_SECRET_KEY' ) ) {
    define( 'YITH_WCET_SECRET_KEY', '5dylgkpXWkuILKJVSFAv' );
}

if ( !defined( 'YITH_WCET_TEMPLATE_EMAIL_PATH' ) ) {
    define( 'YITH_WCET_TEMPLATE_EMAIL_PATH', YITH_WCET_TEMPLATE_PATH . '/emails/woocommerce2.5' );
}

function yith_wcet_pr_init() {

    load_plugin_textdomain( 'yith-woocommerce-email-templates', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    // Load required classes and functions
    require_once( 'includes/functions.yith-wcet.php' );
    require_once( 'includes/functions.yith-wcet-premium.php' );
    require_once( 'includes/class.yith-wcet-wc-compatibility.php' );
    require_once( 'includes/class.yith-wcet-email-template-helper.php' );
    require_once( 'includes/class.yith-wcet-email-template-helper-premium.php' );
    require_once( 'includes/class.yith-wcet-admin.php' );
    require_once( 'includes/class.yith-wcet.php' );
    require_once( 'includes/class.yith-wcet-admin-premium.php' );

    // Let's start the game!
    YITH_WCET();
}

add_action( 'yith_wcet_pr_init', 'yith_wcet_pr_init' );


function yith_wcet_pr_install() {

    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_wcet_pr_install_woocommerce_admin_notice' );
    } else {
        do_action( 'yith_wcet_pr_init' );
    }
}

add_action( 'plugins_loaded', 'yith_wcet_pr_install', 11 );

/* Plugin Framework Version Check */
if ( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );