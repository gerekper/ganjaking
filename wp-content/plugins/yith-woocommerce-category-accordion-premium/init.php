<?php
/**
 * Plugin Name: YITH WooCommerce Category Accordion Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-category-accordion/
 * Description: With <code><strong>YITH WooCommerce Category Accordion Premium</strong></code> you can add an accordion menu to your sidebars in a few clicks to view product or post categories! <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.0.33
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-category-accordion
 * Domain Path: /languages/
 * WC requires at least: 3.3
 * WC tested up to: 4.2
 * @author YITH
 * @package YITH WooCommerce Category Accordion Premium
 * @version 1.0.33
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



    function yith_ywcca_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Category Accordion Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-category-accordion' ); ?></p>
        </div>
    <?php
    }


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}

yit_deactive_free_version( 'YWCCA_FREE_INIT', plugin_basename( __FILE__ ) );

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( !defined( 'YWCCA_VERSION' ) ) {
    define( 'YWCCA_VERSION', '1.0.33' );
}

if ( !defined( 'YWCCA_PREMIUM' ) ) {
    define( 'YWCCA_PREMIUM', '1' );
}

if ( !defined( 'YWCCA_INIT' ) ) {
    define( 'YWCCA_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YWCCA_FILE' ) ) {
    define( 'YWCCA_FILE', __FILE__ );
}

if ( !defined( 'YWCCA_DIR' ) ) {
    define( 'YWCCA_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YWCCA_URL' ) ) {
    define( 'YWCCA_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YWCCA_ASSETS_URL' ) ) {
    define( 'YWCCA_ASSETS_URL', YWCCA_URL . 'assets/' );
}

if ( !defined( 'YWCCA_TEMPLATE_PATH' ) ) {
    define( 'YWCCA_TEMPLATE_PATH', YWCCA_DIR . 'templates/' );
}

if ( !defined( 'YWCCA_INC' ) ) {
    define( 'YWCCA_INC', YWCCA_DIR . 'includes/' );
}

if( !defined('YWCCA_SLUG' ) ){
    define( 'YWCCA_SLUG', 'yith-woocommerce-category-accordion' );
}

if ( ! defined( 'YWCCA_SECRET_KEY' ) ) {
    define('YWCCA_SECRET_KEY', '12345');

}

if( ! defined( 'FS_CHMOD_FILE' ) ){
    define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCCA_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YWCCA_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader( YWCCA_DIR  );


if( !function_exists( 'YITH_Category_Accordion_Premium_Init' ) ){
    /**
     * Unique access to instance of YITH_Category_Accordion class
     *
     * @return  YITH_WC_Category_Accordion_Premium
     * @since 1.0.4
     */
    function YITH_Category_Accordion_Premium_Init()
    {
        load_plugin_textdomain( 'yith-woocommerce-category-accordion', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        require_once(YWCCA_INC . 'class.yith-category-accordion-widget.php');
        require_once(YWCCA_INC . 'class.yith-woocommerce-category-accordion.php');
        require_once(YWCCA_INC . 'functions.yith-category-accordion.php');
        require_once(YWCCA_INC . 'class.yith-category-accordion-shortcode.php');
        require_once(YWCCA_INC . 'class.yith-woocommerce-category-accordion-premium.php');

        global $YIT_Category_Accordion;
        $YIT_Category_Accordion = YITH_WC_Category_Accordion_Premium::get_instance();

    }
}

add_action('yith_wc_category_accordion_premium_init', 'YITH_Category_Accordion_Premium_Init' );

if( !function_exists( 'yith_category_accordion_premium_install' ) ){
    /**
     * install category accordion
     * @author YITHEMES
     * @since 1.0.4
     */
    function yith_category_accordion_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_ywcca_install_woocommerce_admin_notice' );
        }
        else
            do_action( 'yith_wc_category_accordion_premium_init' );

    }
}

add_action( 'plugins_loaded', 'yith_category_accordion_premium_install', 11 );
