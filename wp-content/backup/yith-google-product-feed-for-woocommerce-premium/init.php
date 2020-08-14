<?php

/*
Plugin Name: YITH Google Product Feed for WooCommerce Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-google-product-feed-for-woocommerce/
Description: <code><strong>YITH Google Product Feed for WooCommerce Premium</strong></code> allows you to generate product feed to sync your products with your Google Shopping merchant center. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>.
Author: YITH
Text Domain: yith-google-product-feed-for-woocommerce
Version: 1.1.12
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

if( ! function_exists( 'yith_wcgpf_install_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if WooCommerce is deactivated
     *
     * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function yith_wcgpf_install_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php echo esc_html_x( 'YITH Google Product Feed for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'Alert Message: WooCommerce requires', 'yith-google-product-feed-for-woocommerce' ); ?></p>
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
if(!function_exists('yith_wcgpf_install')){
    function yith_wcgpf_install() {

        if ( !function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'yith_wcgpf_install_woocommerce_admin_notice' );
        } else {
            do_action( 'yith_wcgpf_init' );
        }
    }
}
add_action( 'plugins_loaded', 'yith_wcgpf_install', 11 );


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCGPF_FREE_INIT', plugin_basename( __FILE__ ) );


/* === DEFINE === */
! defined( 'YITH_WCGPF_VERSION' ) && define( 'YITH_WCGPF_VERSION', '1.1.12' );
! defined( 'YITH_WCGPF_PREMIUM' ) && define( 'YITH_WCGPF_PREMIUM', true );
! defined( 'YITH_WCGPF_INIT' ) && define( 'YITH_WCGPF_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCGPF_SLUG' ) && define( 'YITH_WCGPF_SLUG', 'yith-google-product-feed-for-woocommerce' );
! defined( 'YITH_WCGPF_SECRETKEY' ) && define( 'YITH_WCGPF_SECRETKEY', '12345' );
! defined( 'YITH_WCGPF_FILE' ) && define( 'YITH_WCGPF_FILE', __FILE__ );
! defined( 'YITH_WCGPF_PATH' ) && define( 'YITH_WCGPF_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCGPF_URL' ) && define( 'YITH_WCGPF_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCGPF_ASSETS_URL' ) && define( 'YITH_WCGPF_ASSETS_URL', YITH_WCGPF_URL . 'assets/' );
! defined( 'YITH_WCGPF_TEMPLATE_PATH' ) && define( 'YITH_WCGPF_TEMPLATE_PATH', YITH_WCGPF_PATH . 'templates/' );
! defined( 'YITH_WCGPF_WC_TEMPLATE_PATH' ) && define( 'YITH_WCGPF_WC_TEMPLATE_PATH', YITH_WCGPF_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCGPF_OPTIONS_PATH' ) && define( 'YITH_WCGPF_OPTIONS_PATH', YITH_WCGPF_PATH . 'plugin-options' );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCGPF_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCGPF_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCGPF_PATH  );

function yith_wcgpf_init_premium() {
    load_plugin_textdomain( 'yith-google-product-feed-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


    if ( ! function_exists( 'YITH_Google_Product_Feed' ) ) {
        /**
         * Unique access to instance of YITH_Google_Product_Feed class
         *
         * @return YITH_Google_Product_Feed
         * @since 1.0.0
         */
        function YITH_Google_Product_Feed() {
            // Load required classes and functions

            require_once(YITH_WCGPF_PATH . 'includes/class.yith-wcgpf-google-product-feed.php' );

            if ( defined( 'YITH_WCGPF_PREMIUM' ) && file_exists(YITH_WCGPF_PATH . 'includes/class.yith-wcgpf-google-product-feed-premium.php' ) ) {
                require_once( YITH_WCGPF_PATH . 'includes/class.yith-wcgpf-google-product-feed-premium.php' );
                return YITH_WCGPF_Google_Product_Feed_Premium::instance();
            }
            return YITH_WCGPF_Google_Product_Feed::instance();
        }
    }

    // Let's start the game!
    YITH_Google_Product_Feed();
}

add_action( 'yith_wcgpf_init', 'yith_wcgpf_init_premium' );
