<?php
/**
 * Plugin Name: YITH WooCommerce Save for Later Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-save-for-later/
 * Description: <code><strong>YITH WooCommerce Save for Later Premium</strong></code> allows your customers to add products to a save-list in the cart page just like Amazon. <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.1.4
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-save-for-later
 * Domain Path: /languages/
 * WC requires at least: 3.3.0
 * WC tested up to: 4.2
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Save for Later Premium
 * @version 1.1.4
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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

    function ywsfl_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Save for Later Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-save-for-later' ); ?></p>
        </div>
    <?php
    }


if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YWSFL_FREE_INIT', plugin_basename( __FILE__ ) );


if ( !defined( 'YWSFL_VERSION' ) ) {
    define( 'YWSFL_VERSION', '1.1.4' );
}

if ( ! defined( 'YWSFL_PREMIUM' ) ) {
    define( 'YWSFL_PREMIUM', '1' );
}

if ( !defined( 'YWSFL_INIT' ) ) {
    define( 'YWSFL_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YWSFL_FILE' ) ) {
    define( 'YWSFL_FILE', __FILE__ );
}

if ( !defined( 'YWSFL_DIR' ) ) {
    define( 'YWSFL_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YWSFL_URL' ) ) {
    define( 'YWSFL_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YWSFL_ASSETS_URL' ) ) {
    define( 'YWSFL_ASSETS_URL', YWSFL_URL . 'assets/' );
}

if ( !defined( 'YWSFL_ASSETS_PATH' ) ) {
    define( 'YWSFL_ASSETS_PATH', YWSFL_DIR . 'assets/' );
}

if ( !defined( 'YWSFL_TEMPLATE_PATH' ) ) {
    define( 'YWSFL_TEMPLATE_PATH', YWSFL_DIR . 'templates/' );
}

if ( !defined( 'YWSFL_INC' ) ) {
    define( 'YWSFL_INC', YWSFL_DIR . 'includes/' );
}

if( !defined('YWSFL_SLUG' ) ){
    define( 'YWSFL_SLUG', 'yith-woocommerce-save-for-later' );
}

if ( ! defined( 'YWSFL_SECRET_KEY' ) ) {
    define( 'YWSFL_SECRET_KEY', 'NXmo9j5caLqWdWoaf50t' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWSFL_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YWSFL_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader(YWSFL_DIR);


if (! function_exists( 'YITH_Save_For_Later_Premium_Init' ) ){
    /**
     * Unique access to instance of YITH_WC_Save_For_Later_Premium class
     *
     * @return  YITH_WC_Save_For_Later_Premium
     * @since 1.0.2
     */
     function YITH_Save_For_Later_Premium_Init() {

         load_plugin_textdomain( 'yith-woocommerce-save-for-later', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

         require_once( YWSFL_INC . 'functions.yith-wsfl.php');
         require_once( YWSFL_INC . 'class.yith-wsfl-install.php' );
         require_once( YWSFL_INC . 'class.yith-wsfl.php' );
         require_once( YWSFL_INC . 'class.yith-wsfl-shortcode.php');
         require_once( YWSFL_INC . 'class.yith-wsfl-premium.php' );


         global $YIT_Save_For_Later;

         $YIT_Save_For_Later = YITH_WC_Save_For_Later_Premium::get_instance();

    }

}



add_action('yith_wc_save_for_later_premium_init', 'YITH_Save_For_Later_Premium_Init' );

if( !function_exists( 'yith_save_for_later_premium_install' ) ){
    /**
     * install category accordion
     * @author YIThemes
     * @since 1.0.2
     */
    function yith_save_for_later_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'ywsfl_install_woocommerce_admin_notice' );
        }
        else
            do_action( 'yith_wc_save_for_later_premium_init' );

    }
}

add_action( 'plugins_loaded', 'yith_save_for_later_premium_install', 11 );
