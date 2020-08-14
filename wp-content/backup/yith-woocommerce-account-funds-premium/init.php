<?php
/**
 * Plugin Name: YITH WooCommerce Account Funds Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-account-funds/
 * Description: The plugin <code><strong>YITH WooCommerce Account Funds Premium</strong></code> gives your customers the possibility to deposit funds in your online store now and use them later at any time to proceed with the checkout more quickly. <a href="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.3.5
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-account-funds
 * Domain Path: /languages/
 * WC requires at least: 3.3.0
 * WC tested up to: 4.2
 * @author YITH
 * @package YITH WooCommerce Account Funds Premium
 * @version 1.3.5
 */
/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) )
    exit;

if ( !function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( !function_exists( 'yith_funds_premium_install_woocommerce_admin_notice' ) ) {
    function yith_funds_premium_install_woocommerce_admin_notice()
    {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Account Funds Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-account-funds' ); ?></p>
        </div>
        <?php
    }
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


//endregion

//region    ****    Define constants  ****
if ( !defined( 'YITH_FUNDS_VERSION' ) ) {
    define( 'YITH_FUNDS_VERSION', '1.3.5' );
}
if ( !defined( 'YITH_FUNDS_PREMIUM' ) ) {
    define( 'YITH_FUNDS_PREMIUM', '1' );
}
if ( !defined( 'YITH_FUNDS_INIT' ) ) {
    define( 'YITH_FUNDS_INIT', plugin_basename( __FILE__ ) );
}
if ( !defined( 'YITH_FUNDS_FILE' ) ) {
    define( 'YITH_FUNDS_FILE', __FILE__ );
}

if ( !defined( 'YITH_FUNDS_DIR' ) ) {
    define( 'YITH_FUNDS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_FUNDS_URL' ) ) {
    define( 'YITH_FUNDS_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YITH_FUNDS_ASSETS_URL' ) ) {
    define( 'YITH_FUNDS_ASSETS_URL', YITH_FUNDS_URL . 'assets/' );
}

if ( !defined( 'YITH_FUNDS_TEMPLATE_PATH' ) ) {
    define( 'YITH_FUNDS_TEMPLATE_PATH', YITH_FUNDS_DIR . 'templates/' );
}

if ( !defined( 'YITH_FUNDS_INC' ) ) {
    define( 'YITH_FUNDS_INC', YITH_FUNDS_DIR . 'includes/' );
}


if ( !defined( 'YITH_FUNDS_SLUG' ) ) {
    define( 'YITH_FUNDS_SLUG', 'yith-woocommerce-account-funds' );
}
if ( !defined( 'YITH_FUNDS_SECRET_KEY' ) ) {
    define( 'YITH_FUNDS_SECRET_KEY', '123456' );
}

if( !defined('YITH_FUNDS_DB_VERSION' ) ){
	define( 'YITH_FUNDS_DB_VERSION','1.0.1' );
}

//endregion

//create log user funds table

if( !class_exists('YITH_YWF_Log_Manager') ){
	require_once( YITH_FUNDS_INC.'/class.yith-ywf-log-manager.php');
	$log_manager = YWF_Log();
}
register_activation_hook( __FILE__, array( $log_manager,'install'));

/* Plugin Framework Version Check */
if ( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_FUNDS_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_FUNDS_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader( YITH_FUNDS_DIR );

if ( !function_exists( 'yith_funds_install' ) ) {

    function yith_funds_install()
    {

        if ( !function_exists( 'WC' ) ) {

            add_action( 'admin_notices', 'yith_funds_premium_install_woocommerce_admin_notice' );
        } else {

            do_action( 'yith_funds_init' );
        }
    }
}
add_action( 'plugins_loaded', 'yith_funds_install', 11 );

if ( !function_exists( 'yith_funds_init_plugin' ) ) {
    /**
     * @author YITHEMES
     */
    function yith_funds_init_plugin()
    {

        load_plugin_textdomain( 'yith-woocommerce-account-funds', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        require_once( YITH_FUNDS_DIR.'/class.yith-funds.php' );
        require_once( YITH_FUNDS_INC.'/functions.yith-ywf-functions.php');
        require_once( YITH_FUNDS_INC.'/class.yith-ywf-customer.php');
        require_once( YITH_FUNDS_INC.'/class.yith-ywf-cart-process.php' );
        require_once( YITH_FUNDS_INC.'/class.yith-ywf-deposit-fund-checkout.php');
        require_once( YITH_FUNDS_INC.'/class.wc-gateway-yith-funds.php');
        require_once( YITH_FUNDS_INC.'/class.yith-ywf-order.php');
        require_once( YITH_FUNDS_INC.'/class.yith-ywf-product-deposit.php');
        require_once( YITH_FUNDS_INC.'/shortcodes/class.yith-ywf-shortcodes.php' );
        require_once( YITH_FUNDS_INC.'/class.yith-ywf-reports.php');
        require_once( YITH_FUNDS_INC.'/compatibility/yith-woocommerce-multi-vendor/class.yith-funds-redeem-payouts-gateway.php');
        require_once( YITH_FUNDS_INC.'/compatibility/yith-woocommerce-multi-vendor/class.yith-funds-redeem-stripe-connect-gateway.php');
        require_once( YITH_FUNDS_INC.'/compatibility/class.yith-funds-compatibility.php');
        require_once( YITH_FUNDS_INC.'/class.yith-funds-endpoints.php');
	    global $YITH_FUNDS;




        $YITH_FUNDS = YITH_Funds::get_instance();
    }
}
add_action( 'yith_funds_init', 'yith_funds_init_plugin' );
