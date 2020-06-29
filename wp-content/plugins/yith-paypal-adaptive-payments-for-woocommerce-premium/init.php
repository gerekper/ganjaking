<?php
/**
 * Plugin Name: YITH Paypal Adaptive Payments for WooCommerce Premium
 * Description: With <strong><code>YITH PayPal Adaptive Payments for WooCommerce Premium</code></strong>, you can manage payments between a sender and one or more receivers, thanks to paypal adaptive payment service. You can set a commission for each receiver, so to split the payments during the checkout process! <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.0.22
 * Author: YITH
 * Author URI: https://yithemes.com/
 * WC requires at least: 3.4.0
 * WC tested up to: 4.0
 * Text Domain: yith-paypal-adaptive-payments-for-woocommerce
 * Domain Path: /languages/
 *
 * @author YITH
 * @package YITH Paypal Adaptive Payments for WooCommerce - Premium
 * @version 1.0.22
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

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if( !function_exists( 'yith_adaptive_payments_premium_install_woocommerce_admin_notice' ) ) {
    function yith_adaptive_payments_premium_install_woocommerce_admin_notice()
    {
        ?>
        <div class="error">
            <p><?php _e( 'YITH PayPal Adaptive Payments Premium for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-paypal-adaptive-payments-for-woocommerce' ); ?></p>
        </div>
        <?php
    }
}

if( !function_exists( 'yith_plugin_registration_hook' ) ) {
    
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if( !function_exists( 'yith_padp_reset_rewrite_option' ) ){
    function yith_padp_reset_rewrite_option(){

        update_option( 'ywpadp_rewrite', true );
    }
}

register_deactivation_hook( __FILE__, 'yith_padp_reset_rewrite_option' );


// DEFINE CONSTANTS

if( !defined( 'YITH_PAYPAL_ADAPTIVE_VERSION' ) ){

    define( 'YITH_PAYPAL_ADAPTIVE_VERSION', '1.0.22' );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_DB_VERSION' ) ){

    define( 'YITH_PAYPAL_ADAPTIVE_DB_VERSION', '1.0.0' );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_PREMIUM' ) ) {

    define( 'YITH_PAYPAL_ADAPTIVE_PREMIUM', '1' );

}
if( !defined( 'YITH_PAYPAL_ADAPTIVE_INIT' ) ) {
    define( 'YITH_PAYPAL_ADAPTIVE_INIT', plugin_basename( __FILE__ ) );
}
if( !defined( 'YITH_PAYPAL_ADAPTIVE_FILE' ) ) {
    define( 'YITH_PAYPAL_ADAPTIVE_FILE', __FILE__ );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_DIR' ) ) {
    define( 'YITH_PAYPAL_ADAPTIVE_DIR', plugin_dir_path( __FILE__ ) );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_URL' ) ) {
    define( 'YITH_PAYPAL_ADAPTIVE_URL', plugins_url( '/', __FILE__ ) );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_ASSETS_URL' ) ) {
    define( 'YITH_PAYPAL_ADAPTIVE_ASSETS_URL', YITH_PAYPAL_ADAPTIVE_URL . 'assets/' );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH' ) ) {
    define( 'YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH', YITH_PAYPAL_ADAPTIVE_DIR . 'templates/' );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_INC' ) ) {
    define( 'YITH_PAYPAL_ADAPTIVE_INC', YITH_PAYPAL_ADAPTIVE_DIR . 'includes/' );
}

if( !defined( 'YITH_PAYPAL_ADAPTIVE_SLUG' ) ) {

    define( 'YITH_PAYPAL_ADAPTIVE_SLUG', 'yith-paypal-adaptive-payments-for-woocommerce' );

}
if( !defined( 'YITH_PAYPAL_ADAPTIVE_SECRET_KEY' ) ) {

    define( 'YITH_PAYPAL_ADAPTIVE_SECRET_KEY', '12345' );

}
//END DEFINE CONSTANTS

/* Plugin Framework Version Check */
if ( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_PAYPAL_ADAPTIVE_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_PAYPAL_ADAPTIVE_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader( YITH_PAYPAL_ADAPTIVE_DIR );

if ( !function_exists( 'yith_paypal_adaptive_install' ) ) {

    function yith_paypal_adaptive_install()
    {

        if ( !function_exists( 'WC' ) ) {

            add_action( 'admin_notices', 'yith_adaptive_payments_premium_install_woocommerce_admin_notice' );
        } else {

            do_action( 'yith_paypal_adaptive_init' );
        }
    }
}
add_action( 'plugins_loaded', 'yith_paypal_adaptive_install', 11 );

if ( !function_exists( 'yith_paypal_adaptive_init_plugin' ) ) {
    /**
     * @author YITHEMES
     */
    function yith_paypal_adaptive_init_plugin()
    {

        load_plugin_textdomain( 'yith-paypal-adaptive-payments-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        require_once( YITH_PAYPAL_ADAPTIVE_INC.'functions.yith-padp-functions.php' );
        require_once( 'class.yith-paypal-adaptive-payments.php' );
        require_once( YITH_PAYPAL_ADAPTIVE_INC.'class.yith-paypal-adaptive-payments-admin.php' ); 
        require_once( YITH_PAYPAL_ADAPTIVE_INC.'class.wc-gateway-yith-paypal-adaptive-payments.php' );
        require_once( YITH_PAYPAL_ADAPTIVE_INC.'class.yith-paypal-adaptive-payments-receiver-commission.php' );
        require_once( YITH_PAYPAL_ADAPTIVE_INC.'class.yith-paypal-adaptive-payments-receivers.php');
        require_once( YITH_PAYPAL_ADAPTIVE_INC.'class.yith-paypal-adaptive-payments-integrations.php');


        global $YITH_Adaptive_Payments;

        $YITH_Adaptive_Payments= YITH_Paypal_Adaptive_Payments::get_instance();



    }
}
add_action( 'yith_paypal_adaptive_init', 'yith_paypal_adaptive_init_plugin' );

if( !class_exists( 'YITH_PADP_Receiver_Commission' ) ){
    require_once( YITH_PAYPAL_ADAPTIVE_INC.'class.yith-paypal-adaptive-payments-receiver-commission.php' );
}
register_activation_hook( YITH_PAYPAL_ADAPTIVE_FILE, 'YITH_PADP_Receiver_Commission::install' );
register_deactivation_hook( YITH_PAYPAL_ADAPTIVE_FILE, 'yith_padp_deactivation' );

if( ! function_exists( 'yith_padp_deactivation' ) ){
    function yith_padp_deactivation(){
        class_exists( 'YITH_Vendors' ) && update_option( 'payment_gateway', 'masspay' );
    }
}
