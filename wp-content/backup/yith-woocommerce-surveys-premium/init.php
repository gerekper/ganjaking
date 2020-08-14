<?php
/**
 * Plugin Name: YITH WooCommerce Surveys Premium
 * Plugin URI:  https://yithemes.com/themes/plugins/yith-woocommerce-surveys/
 * Description: <code><strong>YITH WooCommerce Surveys Premium</strong></code> allows adding a survey to your checkout page to learn more about your customers' habits! <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.2.3
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-surveys
 * Domain Path: /languages/
 * WC requires at least: 3.3.0
 * WC tested up to: 4.1
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Surveys Premium
 * @version 1.2.3
 */

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
*/
if( !defined( 'ABSPATH' ) ){
    exit;
}
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_wc_surveys_premium_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Surveys Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-surveys' ); ?></p>
        </div>
    </div>
<?php
}

if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WC_SURVEYS_FREE_INIT', plugin_basename( __FILE__ ) );


if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );



if ( !defined( 'YITH_WC_SURVEYS_VERSION' ) ) {
    define( 'YITH_WC_SURVEYS_VERSION', '1.2.3' );
}

if( !defined( 'YITH_WC_SURVEYS_DB_VERSION' ) ){
	define( 'YITH_WC_SURVEYS_DB_VERSION', '1.1.1' );
}
if( !defined( 'YITH_WC_SURVEYS_PREMIUM' ) )
    define( 'YITH_WC_SURVEYS_PREMIUM', '1' );

if ( !defined( 'YITH_WC_SURVEYS_INIT' ) ) {
    define( 'YITH_WC_SURVEYS_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YITH_WC_SURVEYS_FILE' ) ) {
    define( 'YITH_WC_SURVEYS_FILE', __FILE__ );
}

if ( !defined( 'YITH_WC_SURVEYS_DIR' ) ) {
    define( 'YITH_WC_SURVEYS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_WC_SURVEYS_URL' ) ) {
    define( 'YITH_WC_SURVEYS_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YITH_WC_SURVEYS_ASSETS_URL' ) ) {
    define( 'YITH_WC_SURVEYS_ASSETS_URL', YITH_WC_SURVEYS_URL . 'assets/' );
}

if ( !defined( 'YITH_WC_SURVEYS_ASSETS_PATH' ) ) {
    define( 'YITH_WC_SURVEYS_ASSETS_PATH', YITH_WC_SURVEYS_DIR . 'assets/' );
}

if ( !defined( 'YITH_WC_SURVEYS_TEMPLATE_PATH' ) ) {
    define( 'YITH_WC_SURVEYS_TEMPLATE_PATH', YITH_WC_SURVEYS_DIR . 'templates/' );

}

if ( !defined( 'YITH_WC_SURVEYS_INC' ) ) {
    define( 'YITH_WC_SURVEYS_INC', YITH_WC_SURVEYS_DIR . '/includes/' );
}
if( !defined('YITH_WC_SURVEYS_SLUG' ) ){
    define( 'YITH_WC_SURVEYS_SLUG', 'yith-woocommerce-surveys' );
}
if( !defined('YITH_WC_SURVEYS_SECRET_KEY' ) ){
    define( 'YITH_WC_SURVEYS_SECRET_KEY', 'B5FAkGFVx9W6GQOlawvg' );
}

$wp_upload_dir = wp_upload_dir();

if ( ! defined( 'YITH_WC_SURVEYS_SAVE_DIR' ) ) {
    define( 'YITH_WC_SURVEYS_SAVE_DIR', $wp_upload_dir['basedir'] . '/yith-surveys-export/' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WC_SURVEYS_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WC_SURVEYS_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader( YITH_WC_SURVEYS_DIR );

if ( ! function_exists( 'yith_surveys_premium_init' ) ) {
    /**
     * Unique access to instance of YITH_Surveys class
     *
     * @return YITH_WC_Surveys_Premium
     * @since 1.0.0
     */
    function yith_surveys_premium_init() {

        load_plugin_textdomain( 'yith-woocommerce-surveys', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Load required classes and functions
        require_once( YITH_WC_SURVEYS_INC.'post-type/class.yith-surveys-post-type.php' );
        require_once( YITH_WC_SURVEYS_INC.'functions.yith-surveys-function.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys-admin.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys-frontend.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/compatibility/class.yith-wc-surveys-compatibility.php' );
        require_once( YITH_WC_SURVEYS_INC.'functions.yith-surveys-premium-function.php' );
        require_once( YITH_WC_SURVEYS_TEMPLATE_PATH.'admin/yith-wc-surveys-report.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys-utility.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys-premium.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys-admin-premium.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys-frontend-premium.php' );
        require_once( YITH_WC_SURVEYS_INC.'post-type/class.yith-surveys-post-type-premium.php' );
        require_once( YITH_WC_SURVEYS_INC.'shortcodes/class.yith-wc-surveys-shortcode.php' );
        require_once( YITH_WC_SURVEYS_INC.'widgets/class.yith-wc-surveys-widget.php' );
        require_once( YITH_WC_SURVEYS_INC.'classes/class.yith-wc-surveys-export.php' );


        if( !file_exists( YITH_WC_SURVEYS_SAVE_DIR ) ) {
            wp_mkdir_p( YITH_WC_SURVEYS_SAVE_DIR );
        }

        global $YWC_Surveys;
        $YWC_Surveys = YITH_WC_Surveys_Premium::get_instance();
    }
}

add_action( 'ywcsurveys_premium_init', 'yith_surveys_premium_init' );

if( !function_exists( 'yith_surveys_premium_install' ) ){

    function yith_surveys_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_wc_surveys_premium_install_woocommerce_admin_notice' );
        }else
            do_action( 'ywcsurveys_premium_init' );
    }
}

add_action( 'plugins_loaded', 'yith_surveys_premium_install' ,11 );
