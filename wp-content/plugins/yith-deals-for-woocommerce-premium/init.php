<?php
/*
Plugin Name: YITH Deals for WooCommerce Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-deals/
Description: <code><strong>YITH Deals for WooCommerce</strong></code> allows increasing the number of orders in your store through upsell promotions at the checkout. For example, you can offer all users buying a specific product to extend the product warranty at a small additional price. You can also create ad hoc rules to better manage your offers. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
Author: YITH
Text Domain: yith-deals-for-woocommerce
Version: 1.0.13
Author URI: http://yithemes.com/
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

if( ! function_exists( 'yith_wcdls_install_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if WooCommerce is deactivated
     *
     * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function yith_wcdls_install_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php echo esc_html_x( 'YITH Deals for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'Alert Message: WooCommerce requires', 'yith-deals-for-woocommerce' ); ?></p>
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
if( ! function_exists( 'yith_wcdls_install' ) ) {

    function yith_wcdls_install()
    {

        if (!function_exists('WC')) {
            add_action('admin_notices', 'yith_wcdls_install_woocommerce_admin_notice');
        } else {
            do_action('yith_wcdls_init');
        }
    }

    add_action( 'plugins_loaded', 'yith_wcdls_install', 11 );
}


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';                                      
}
yit_deactive_free_version( 'YITH_WCDLS_FREE_INIT', plugin_basename( __FILE__ ) );


/* === DEFINE === */
! defined( 'YITH_WCDLS_VERSION' )            && define( 'YITH_WCDLS_VERSION', '1.0.13' );
! defined( 'YITH_WCDLS_INIT' )               && define( 'YITH_WCDLS_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCDLS_SLUG' )               && define( 'YITH_WCDLS_SLUG', 'yith-deals-for-woocommerce' );
! defined( 'YITH_WCDLS_SECRETKEY' )          && define( 'YITH_WCDLS_SECRETKEY', '12345' );
! defined( 'YITH_WCDLS_FILE' )               && define( 'YITH_WCDLS_FILE', __FILE__ );
! defined( 'YITH_WCDLS_PATH' )               && define( 'YITH_WCDLS_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCDLS_URL' )                && define( 'YITH_WCDLS_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCDLS_ASSETS_URL' )         && define( 'YITH_WCDLS_ASSETS_URL', YITH_WCDLS_URL . 'assets/' );
! defined( 'YITH_WCDLS_TEMPLATE_PATH' )      && define( 'YITH_WCDLS_TEMPLATE_PATH', YITH_WCDLS_PATH . 'templates/' );
! defined( 'YITH_WCDLS_WC_TEMPLATE_PATH' )   && define( 'YITH_WCDLS_WC_TEMPLATE_PATH', YITH_WCDLS_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCDLS_OPTIONS_PATH' )       && define( 'YITH_WCDLS_OPTIONS_PATH', YITH_WCDLS_PATH . 'plugin-options' );
! defined( 'YITH_WCDLS_PREMIUM' )            && define( 'YITH_WCDLS_PREMIUM', true );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCDLS_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCDLS_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCDLS_PATH  );


function yith_wcdls_init_premium() {
    load_plugin_textdomain( 'yith-deals-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


    if ( ! function_exists( 'YITH_Deals' ) ) {
        /**
         * Unique access to instance of YITH_Deals class
         *
         * @return YITH_Deals
         * @since 1.0.0
         */
        function YITH_Deals() {

            require_once( YITH_WCDLS_PATH . 'includes/class.yith-wcdls-deals.php' );
            if ( defined( 'YITH_WCDLS_PREMIUM' ) && file_exists( YITH_WCDLS_PATH . 'includes/class.yith-wcdls-deals-premium.php' ) ) {

                require_once( YITH_WCDLS_PATH . 'includes/class.yith-wcdls-deals-premium.php' );
                return YITH_Deals_Premium::instance();
            }
            return YITH_Deals::instance();
        }
    }

   // Let's start the game!
   YITH_Deals();
}

add_action( 'yith_wcdls_init', 'yith_wcdls_init_premium' );