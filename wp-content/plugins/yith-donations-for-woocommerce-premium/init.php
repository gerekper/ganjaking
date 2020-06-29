<?php
/**
 * Plugin Name: YITH Donations for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-donations-for-woocommerce/
 * Description: <strong><code>YITH Donations for WooCommerce Premium</code></strong> allows your customers to donate an amount of their choice through a dedicated form to purchase your products.The administrator can set a minimum threshold for the payment. <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.1.11
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-donations-for-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 3.3.0
 * WC tested up to: 4.2
 * @author Your Inspiration Themes
 * @package YITH Donations for WooCommerce Premium
 * @version 1.1.11
 */

/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

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

    if ( !defined( 'ABSPATH' ) ){
        exit;
    }

    if( !function_exists( 'is_plugin_active' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }



        function yith_ywcds_premium_install_woocommerce_admin_notice() {
            ?>
            <div class="error">
                <p><?php _e( 'YITH Donations for WooCommerce Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-donations-for-woocommerce' ); ?></p>
            </div>
        <?php
        }


    if( ! function_exists( 'yit_deactive_free_version' ) ) {
        require_once 'plugin-fw/yit-deactive-plugin.php';
    }
    yit_deactive_free_version( 'YWCDS_FREE_INIT', plugin_basename( __FILE__ ) );


    if ( !defined( 'YWCDS_VERSION' ) ) {
        define( 'YWCDS_VERSION', '1.1.11' );
    }

    if( !defined( 'YWCDS_PREMIUM' ) ){
        define( 'YWCDS_PREMIUM', '1' );
    }

    if ( !defined( 'YWCDS_INIT' ) ) {
        define( 'YWCDS_INIT', plugin_basename( __FILE__ ) );
    }

    if ( !defined( 'YWCDS_FILE' ) ) {
        define( 'YWCDS_FILE', __FILE__ );
    }

    if ( !defined( 'YWCDS_DIR' ) ) {
        define( 'YWCDS_DIR', plugin_dir_path( __FILE__ ) );
    }

    if ( !defined( 'YWCDS_URL' ) ) {
        define( 'YWCDS_URL', plugins_url( '/', __FILE__ ) );
    }

    if ( !defined( 'YWCDS_ASSETS_URL' ) ) {
        define( 'YWCDS_ASSETS_URL', YWCDS_URL . 'assets/' );
    }

    if ( !defined( 'YWCDS_TEMPLATE_PATH' ) ) {
        define( 'YWCDS_TEMPLATE_PATH', YWCDS_DIR . 'templates/' );
    }

    if ( !defined( 'YWCDS_INC' ) ) {
        define( 'YWCDS_INC', YWCDS_DIR . 'includes/' );
    }

    if( !defined('YWCDS_SLUG' ) ){
        define( 'YWCDS_SLUG', 'yith-donations-for-woocommerce' );
    }

    if ( ! defined( 'YWCDS_SECRET_KEY' ) ) {
        define('YWCDS_SECRET_KEY', '12345');

    }


    if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
        require_once 'plugin-fw/yit-plugin-registration-hook.php';
    }
    register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCDS_DIR . 'plugin-fw/init.php' ) )
    require_once( YWCDS_DIR . 'plugin-fw/init.php' );

yit_maybe_plugin_fw_loader( YWCDS_DIR  );


    if( !function_exists( 'YITH_Donations_Premium_Init' ) ) {
        /**
         * Unique access to instance of YITH_WC_Donations_Premium class
         *
         * @return  YITH_WC_Donations_Premium
         * @since 1.0.3
         */
        function YITH_Donations_Premium_Init()
        {
            load_plugin_textdomain( 'yith-donations-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

            require_once( YWCDS_INC . 'functions.yith-wc-donations.php' );
            require_once( YWCDS_INC .'widgets/class.yith-wc-donations-form-widget.php' );
            require_once( YWCDS_INC . 'classes/class.yith-woocommerce-donations.php' );
            require_once( YWCDS_INC . 'functions.yith-wc-donations-premium.php' );
            require_once( YWCDS_INC . 'widgets/class.yith-wc-donations-summary-widget.php' );
            require_once( YWCDS_INC . 'shortcodes/class.yith-wc-donations-shortcode.php' );
            require_once( YWCDS_INC . 'classes/class.yith-custom-table.php' );
            require_once( YWCDS_INC . 'classes/class.yith-woocommerce-donations-premium.php' );

	        if (  !empty( $GLOBALS['woocommerce-aelia-currencyswitcher'] )  ) {
		        require_once( YWCDS_INC . 'classes/modules/class.yith-wc-aeliacs-module.php' );
	        }

	        require_once (YWCDS_INC.'classes/modules/class.yith-subscription-module.php');

            global $YITH_Donations;
            $YITH_Donations = YITH_WC_Donations_Premium::get_instance();

        }
    }
add_action('yith_wc_donations_premium_init', 'YITH_Donations_Premium_Init' );

if( !function_exists( 'yith_donations_premium_install' ) ){
    /**
     * install donations
     * @author YIThemes
     * @since 1.0.3
     */
    function yith_donations_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_ywcds_premium_install_woocommerce_admin_notice' );
        }
        else
            do_action( 'yith_wc_donations_premium_init' );

    }
}

add_action( 'plugins_loaded', 'yith_donations_premium_install', 11 );
