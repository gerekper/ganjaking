<?php
/**
 * Plugin Name: YITH WooCommerce Sequential Order Number Premium
 * Plugin URI:https://yithemes.com/themes/plugins/yith-woocommerce-sequential-order-number
 * Description: <code><strong>YITH WooCommerce Sequential Order Number Premium</strong></code> allows you to create sequential numbers for your orders! You can add a prefix and suffix to your orders! <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.2.3
 * Author: YITH
 * Author URI: https://yithemes.com/
 * WC requires at least: 4.0.0
 * WC tested up to: 4.3
 * Text Domain: yith-woocommerce-sequential-order-number
 * Domain Path: /languages/
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Sequential Order Number
 * @version 1.2.3
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


    function yith_ywson_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Sequential Order Numbers is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-sequential-order-number' ); ?></p>
        </div>
    <?php

    }


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YWSON_FREE_INIT', plugin_basename( __FILE__ ) );


if ( !defined( 'YWSON_VERSION' ) ) {
    define( 'YWSON_VERSION', '1.2.3' );
}

if ( ! defined( 'YWSON_PREMIUM' ) ) {
    define( 'YWSON_PREMIUM', '1' );
}

if ( !defined( 'YWSON_INIT' ) ) {
    define( 'YWSON_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YWSON_FILE' ) ) {
    define( 'YWSON_FILE', __FILE__ );
}

if ( !defined( 'YWSON_DIR' ) ) {
    define( 'YWSON_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YWSON_URL' ) ) {
    define( 'YWSON_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YWSON_ASSETS_URL' ) ) {
    define( 'YWSON_ASSETS_URL', YWSON_URL . 'assets/' );
}

if ( !defined( 'YWSON_ASSETS_PATH' ) ) {
    define( 'YWSON_ASSETS_PATH', YWSON_DIR . 'assets/' );
}

if ( !defined( 'YWSON_TEMPLATE_PATH' ) ) {
    define( 'YWSON_TEMPLATE_PATH', YWSON_DIR . 'templates/' );
}

if ( !defined( 'YWSON_INC' ) ) {
    define( 'YWSON_INC', YWSON_DIR . 'includes/' );
}

if( !defined(' YWSON_SLUG' ) ){
    define( 'YWSON_SLUG', 'yith-woocommerce-sequential-order-number' );
}

if ( ! defined( 'YWSON_SECRET_KEY' ) ) {
    define( 'YWSON_SECRET_KEY', 'y19QENi2B7JVe5T4pEBI' );
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWSON_DIR . 'plugin-fw/init.php' ) )
    require_once( YWSON_DIR . 'plugin-fw/init.php' );

yit_maybe_plugin_fw_loader( YWSON_DIR  );


if ( ! function_exists( 'YITH_Sequential_Order_Number_Premium_Init' ) ) {
    /**
     * Unique access to instance of YITH_Sequential_Order_Number class
     *
     *@since 1.0.3
     */
    function YITH_Sequential_Order_Number_Premium_Init() {

        load_plugin_textdomain( 'yith-woocommerce-sequential-order-number', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        // Load required classes and functions
        include_once( 'functions.ywson_functions.php');
        include_once(YWSON_INC.'class.yith-sequential-order-number-manager.php');
        include_once(YWSON_INC.'class.yith-woocommerce-sequential-order-number.php');
        include_once(YWSON_INC.'class.yith-sequential-order-number-admin.php');

	    YITH_Sequential_Order_Number();

    }
}

add_action('yith_wc_sequential_order_number_premium_init', 'YITH_Sequential_Order_Number_Premium_Init' );

if( !function_exists( 'yith_sequential_order_number_premium_install' ) ){
    /**
     * install sequential order number
     * @author YIThemes
     * @since 1.0.3
     */
    function yith_sequential_order_number_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_ywson_install_woocommerce_admin_notice' );
        }
        else
            do_action( 'yith_wc_sequential_order_number_premium_init' );

    }
}

add_action( 'plugins_loaded', 'yith_sequential_order_number_premium_install', 11 );
