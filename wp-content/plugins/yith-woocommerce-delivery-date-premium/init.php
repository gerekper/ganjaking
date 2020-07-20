<?php
/**
 * Plugin Name: YITH WooCommerce Delivery Date Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-delivery-date/
 * Description: With <code><strong>YITH WooCommerce Delivery Date Premium</strong></code> you will allow your customers to choose the delivery date for their orders by specifying time slots and possible carriers! <a href="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 2.1.15
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-delivery-date
 * Domain Path: /languages/
 * WC requires at least: 3.3.0
 * WC tested up to: 4.2
 * @author YITH
 * @package YITH WooCommerce Delivery Date Premium
 * @version 2.1.15
 */

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if( !defined( 'ABSPATH' ) ) {
    exit;
}

if( !function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if( !function_exists( 'yith_delivery_date_premium_install_woocommerce_admin_notice' ) ) {
    function yith_delivery_date_premium_install_woocommerce_admin_notice()
    {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Delivery Date Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-delivery-date' ); ?></p>
        </div>
        <?php
    }
}

if( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


//endregion

//region    ****    Define constants  ****
if( !defined( 'YITH_DELIVERY_DATE_VERSION' ) ) {
    define( 'YITH_DELIVERY_DATE_VERSION', '2.1.15' );
}
if( !defined( 'YITH_DELIVERY_DATE_PREMIUM' ) ) {
    define( 'YITH_DELIVERY_DATE_PREMIUM', '1' );
}
if( !defined( 'YITH_DELIVERY_DATE_INIT' ) ) {
    define( 'YITH_DELIVERY_DATE_INIT', plugin_basename( __FILE__ ) );
}
if( !defined( 'YITH_DELIVERY_DATE_FILE' ) ) {
    define( 'YITH_DELIVERY_DATE_FILE', __FILE__ );
}

if( !defined( 'YITH_DELIVERY_DATE_DIR' ) ) {
    define( 'YITH_DELIVERY_DATE_DIR', plugin_dir_path( __FILE__ ) );
}

if( !defined( 'YITH_DELIVERY_DATE_URL' ) ) {
    define( 'YITH_DELIVERY_DATE_URL', plugins_url( '/', __FILE__ ) );
}

if( !defined( 'YITH_DELIVERY_DATE_ASSETS_URL' ) ) {
    define( 'YITH_DELIVERY_DATE_ASSETS_URL', YITH_DELIVERY_DATE_URL . 'assets/' );
}

if( !defined( 'YITH_DELIVERY_DATE_TEMPLATE_PATH' ) ) {
    define( 'YITH_DELIVERY_DATE_TEMPLATE_PATH', YITH_DELIVERY_DATE_DIR . 'templates/' );
}

if( !defined( 'YITH_DELIVERY_DATE_INC' ) ) {
    define( 'YITH_DELIVERY_DATE_INC', YITH_DELIVERY_DATE_DIR . 'includes/' );
}


if( !defined( 'YITH_DELIVERY_DATE_SLUG' ) ) {
    define( 'YITH_DELIVERY_DATE_SLUG', 'yith-woocommerce-delivery-date' );
}
if( !defined( 'YITH_DELIVERY_DATE_SECRET_KEY' ) ) {
    define( 'YITH_DELIVERY_DATE_SECRET_KEY', 'w5PhD7VGXngCNkMH4OUn' );
}

if( !defined( 'YITH_DELIVERY_DATE_DB_VERSION' ) ) {
    define( 'YITH_DELIVERY_DATE_DB_VERSION', '2.0.2' );
}

//endregion

if( !class_exists( 'YITH_Delivery_Date_Calendar' ) ) {
    require_once( YITH_DELIVERY_DATE_INC . 'class.yith-delivery-date-calendar.php' );
    $calendar = YITH_Delivery_Date_Calendar();
}
register_activation_hook( __FILE__, array( $calendar, 'install' ) );

/* Plugin Framework Version Check */
if( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_DELIVERY_DATE_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_DELIVERY_DATE_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader( YITH_DELIVERY_DATE_DIR );

if( !function_exists( 'yith_delivery_date_install' ) ) {

    function yith_delivery_date_install()
    {

        if( !function_exists( 'WC' ) ) {

            add_action( 'admin_notices', 'yith_delivery_date_premium_install_woocommerce_admin_notice' );
        }
        else {

            do_action( 'yith_delivery_date_init' );
        }
    }
}
add_action( 'plugins_loaded', 'yith_delivery_date_install', 11 );

if( !function_exists( 'yith_delivery_date_init_plugin' ) ) {
    /**
     * @author YITHEMES
     */
    function yith_delivery_date_init_plugin()
    {

        load_plugin_textdomain( 'yith-woocommerce-delivery-date', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        require_once( 'class.yith-delivery-date.php' );
        require_once( YITH_DELIVERY_DATE_INC.'class.yith-delivery-date-integrations.php' );
        /**
         * @var YITH_Delivery_Date
         */
        global $YITH_DELIVERY_DATE;

        $YITH_DELIVERY_DATE = YITH_Delivery_Date::get_instance();


    }
}
add_action( 'yith_delivery_date_init', 'yith_delivery_date_init_plugin' );
