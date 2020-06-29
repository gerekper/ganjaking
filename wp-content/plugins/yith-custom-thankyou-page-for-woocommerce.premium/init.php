<?php
/*
* Plugin Name: YITH Custom ThankYou Page for Woocommerce Premium
* Plugin URI: https://yithemes.com/themes/plugins/yith-custom-thank-you-page-for-woocommerce
* Description: The <code><strong>YITH Custom ThankYou Page for Woocommerce</strong></code> shows a Thank You page to users buying in your shop. The page can be customized also per single product and allows your customers to share their purchases on social networks. Excellent to loyalize your customers and show ad-hoc marketing campaigns! <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
* Author: YITH
* Text Domain: yith-custom-thankyou-page-for-woocommerce
* Version: 1.2.4
* Author URI: https://yithemes.com/
* WC requires at least: 3.6
* WC tested up to: 4.2
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( ! function_exists( 'yith_ctpw_woocommerce_admin_notice' ) ) {
    /**
     * Show notice if WooCommerce is not active
     *
     * @author Armando Liccardo <armando.liccardo@yithemes.com>
     * @since  1.0.0
     * @return void
     */
    function yith_ctpw_woocommerce_admin_notice() {
        ?>
        <div class="error">

            <p><?php _e( 'YITH Custom ThankYou page for Woocommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-custom-thankyou-page-for-woocommerce' ); ?></p>
        </div>
    <?php
    }
}

if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_CTPW_FREE_INIT', plugin_basename( __FILE__ ) );

/* === DEFINE === */
! defined( 'YITH_CTPW_VERSION' )            && define( 'YITH_CTPW_VERSION', '1.2.4' );
! defined( 'YITH_CTPW_INIT' )               && define( 'YITH_CTPW_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_CTPW_SLUG' )               && define( 'YITH_CTPW_SLUG', 'yith-custom-thank-you-page-for-woocommerce' );
! defined( 'YITH_CTPW_SECRETKEY' )          && define( 'YITH_CTPW_SECRETKEY', '12345' );
! defined( 'YITH_CTPW_FILE' )               && define( 'YITH_CTPW_FILE', __FILE__ );
! defined( 'YITH_CTPW_PATH' )               && define( 'YITH_CTPW_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_CTPW_URL' )                && define( 'YITH_CTPW_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_CTPW_ASSETS_URL' )         && define( 'YITH_CTPW_ASSETS_URL', YITH_CTPW_URL . 'assets/' );
! defined( 'YITH_CTPW_TEMPLATE_PATH' )      && define( 'YITH_CTPW_TEMPLATE_PATH', YITH_CTPW_PATH . 'templates/' );
! defined( 'YITH_CTPW_WC_TEMPLATE_PATH' )   && define( 'YITH_CTPW_WC_TEMPLATE_PATH', YITH_CTPW_PATH . 'templates/woocommerce/' );
! defined( 'YITH_CTPW_OPTIONS_PATH' )       && define( 'YITH_CTPW_OPTIONS_PATH', YITH_CTPW_PATH . 'plugin-options' );
! defined( 'YITH_CTPW_PREMIUM' )            && define( 'YITH_CTPW_PREMIUM', '1' );
! defined( 'YITH_CTPW_LIB_DIR' )            && define( 'YITH_CTPW_LIB_DIR', YITH_CTPW_PATH . 'lib/' );
! defined( 'YITH_CTPW_PDF_TEMPLATE_PATH' )  && define( 'YITH_CTPW_PDF_TEMPLATE_PATH', YITH_CTPW_TEMPLATE_PATH . 'ctpw_pdf/');

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_CTPW_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_CTPW_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_CTPW_PATH  );

/* Load text domain */
load_plugin_textdomain( 'yith-custom-thankyou-page-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


if ( ! function_exists( 'YITH_Custom_Thankyou_page' ) ) {
    /**
     * Unique access to instance of YITH_Custom_Thankyou_Page
     *
     * @return YITH_Custom_Thankyou_Page|YITH_Custom_Thankyou_Page_Premium
     * @author Armando Liccardo <armando.liccardo@yithemes.com>
     * @since 1.0.0
     */
    function YITH_Custom_Thankyou_Page() {
        // Load required classes and functions
        require_once( YITH_CTPW_PATH . 'includes/class.yith-custom-thankyou-page.php' );
        if ( defined( 'YITH_CTPW_PREMIUM' ) && file_exists( YITH_CTPW_PATH . 'includes/class.yith-custom-thankyou-page-premium.php' ) ) {
            require_once( YITH_CTPW_PATH . 'includes/class.yith-custom-thankyou-page-premium.php' );
            return YITH_Custom_Thankyou_Page_Premium::instance();
        }

        return YITH_Custom_Thankyou_Page::instance();
    }
}

if( ! function_exists( 'yith_ctpw_start' ) ){
    /**
     * Initialize the plugin
     *
     * @since 1.0.0
     * @author Armando Liccardo <armando.liccardo@yithemes.com>
     */
    function yith_ctpw_start() {

        if ( ! function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'yith_ctpw_woocommerce_admin_notice' );
        }

        else {
            /**
             * Instance main plugin class
             */
            YITH_Custom_Thankyou_Page();
        }
    }
}
add_action( 'plugins_loaded', 'yith_ctpw_start', 11 );



