<?php

/*
Plugin Name: YITH Desktop Notifications for WooCommerce Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-desktop-notifications/
Description: <code><strong>YITH Desktop Notifications for WooCommerce Premium</strong></code> allows you to receive real time notifications right on your PC screen letting you know about orders and sales, a quick and easy way to be updated in real time even while you’re doing something else.  <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>.
Author: YITH
Text Domain: yith-desktop-notifications-for-woocommerce
Version: 1.2.13
Author URI: https://yithemes.com/
WC requires at least: 3.0.0
WC tested up to: 4.2
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! function_exists( 'yith_wcdn_install_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if WooCommerce is deactivated
     *
     * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function yith_wcdn_install_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php echo esc_html_x( 'YITH WooCommerce Desktop Notifications is enabled but not effective. It requires WooCommerce in order to work.', 'Alert Message: WooCommerce requires', 'yith-desktop-notifications-for-woocommerce' ); ?></p>
        </div>
        <?php
    }
}


/**
 * Check if WooCommerce is activated
 *
 * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
 * @since 1.0
 * @return void
 * @use admin_notices hooks
 */
function yith_wcdn_install() {

    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_wcdn_install_woocommerce_admin_notice' );
    } else {
        do_action( 'yith_wcdn_init' );
        YITH_WCDN_DB::install();
    }
}

add_action( 'plugins_loaded', 'yith_wcdn_install', 11 );

if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCDN_FREE_INIT', plugin_basename( __FILE__ ) );


/* === DEFINE === */
! defined( 'YITH_WCDN_VERSION' )            && define( 'YITH_WCDN_VERSION', '1.2.13' );
! defined( 'YITH_WCDN_PREMIUM' )            && define( 'YITH_WCDN_PREMIUM', true );
! defined( 'YITH_WCDN_INIT' )               && define( 'YITH_WCDN_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCDN_SLUG' )               && define( 'YITH_WCDN_SLUG', 'yith-desktop-notifications-for-woocommerce' );
! defined( 'YITH_WCDN_SECRETKEY' )          && define( 'YITH_WCDN_SECRETKEY', '12345' );
! defined( 'YITH_WCDN_FILE' )               && define( 'YITH_WCDN_FILE', __FILE__ );
! defined( 'YITH_WCDN_PATH' )               && define( 'YITH_WCDN_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCDN_URL' )                && define( 'YITH_WCDN_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCDN_ASSETS_URL' )         && define( 'YITH_WCDN_ASSETS_URL', YITH_WCDN_URL . 'assets/' );
! defined( 'YITH_WCDN_TEMPLATE_PATH' )      && define( 'YITH_WCDN_TEMPLATE_PATH', YITH_WCDN_PATH . 'templates/' );
! defined( 'YITH_WCDN_WC_TEMPLATE_PATH' )   && define( 'YITH_WCDN_WC_TEMPLATE_PATH', YITH_WCDN_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCDN_OPTIONS_PATH' )       && define( 'YITH_WCDN_OPTIONS_PATH', YITH_WCDN_PATH . 'plugin-options' );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCDN_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCDN_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCDN_PATH  );


function yith_wcdn_init() {
    load_plugin_textdomain( 'yith-desktop-notifications-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


    if ( ! function_exists( 'YITH_Desktop_Notifications' ) ) {
        /**
         * Unique access to instance of YITH_Desktop_Notifications class
         *
         * @return YITH_Desktop_Notifications
         * @since 1.0.0
         */
        function YITH_Desktop_Notifications() {
            // Load required classes and functions
            require_once(YITH_WCDN_PATH . 'includes/class.yith-wcdn-desktop-notifications.php' );


            if ( defined( 'YITH_WCDN_PREMIUM' ) && file_exists(YITH_WCDN_PATH . 'includes/class.yith-wcdn-desktop-notifications-premium.php' ) ) {
                require_once( YITH_WCDN_PATH . 'includes/class.yith-wcdn-desktop-notifications-premium.php' );
                return YITH_Desktop_Notifications_Premium::get_instance();
            }
            return YITH_Desktop_Notifications::get_instance();
        }
    }

    // Let's start the game!
    YITH_Desktop_Notifications();
}

add_action( 'yith_wcdn_init', 'yith_wcdn_init' );