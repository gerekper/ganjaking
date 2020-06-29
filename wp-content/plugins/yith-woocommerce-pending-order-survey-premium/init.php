<?php
/**
 * Plugin Name: YITH WooCommerce Pending Order Survey Premium
 * Plugin URI:  https://yithemes.com/themes/plugins/yith-woocommerce-pending-order-survey/
 * Description: <code><strong>YITH WooCommerce Pending Order Survey Premium</strong></code> allows you to send emails including surveys to users with pending orders. <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.0.17
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-pending-order-survey
 * Domain Path: /languages/
 * WC requires at least: 3.5
 * WC tested up to: 4.2
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Pending Order Survey
 * @version 1.0.17
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

function yith_wc_pending_order_surveys_premium_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Pending Order Survey is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-pending-order-survey' ); ?></p>
        </div>
    </div>
<?php
}


if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( !defined( 'YITH_WCPO_SURVEY_VERSION' ) ) {
    define( 'YITH_WCPO_SURVEY_VERSION', '1.0.17' );
}

if( !defined( 'YITH_WCPO_SURVEY_PREMIUM' ) )
    define( 'YITH_WCPO_SURVEY_PREMIUM', '1' );

if ( !defined( 'YITH_WCPO_SURVEY_INIT' ) ) {
    define( 'YITH_WCPO_SURVEY_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YITH_WCPO_SURVEY_FILE' ) ) {
    define( 'YITH_WCPO_SURVEY_FILE', __FILE__ );
}

if ( !defined( 'YITH_WCPO_SURVEY_DIR' ) ) {
    define( 'YITH_WCPO_SURVEY_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_WCPO_SURVEY_URL' ) ) {
    define( 'YITH_WCPO_SURVEY_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YITH_WCPO_SURVEY_ASSETS_URL' ) ) {
    define( 'YITH_WCPO_SURVEY_ASSETS_URL', YITH_WCPO_SURVEY_URL . 'assets/' );
}

if ( !defined( 'YITH_WCPO_SURVEY_ASSETS_PATH' ) ) {
    define( 'YITH_WCPO_SURVEY_ASSETS_PATH', YITH_WCPO_SURVEY_DIR . 'assets/' );
}

if ( !defined( 'YITH_WCPO_SURVEY_TEMPLATE_PATH' ) ) {
    define( 'YITH_WCPO_SURVEY_TEMPLATE_PATH', YITH_WCPO_SURVEY_DIR . 'templates/' );
}

if ( !defined( 'YITH_WCPO_SURVEY_INC' ) ) {
    define( 'YITH_WCPO_SURVEY_INC', YITH_WCPO_SURVEY_DIR . '/includes/' );
}
if( !defined('YITH_WCPO_SURVEY_SLUG' ) ){
    define( 'YITH_WCPO_SURVEY_SLUG', 'yith-woocommerce-pending-order-survey' );
}
if( !defined('YITH_WCPO_SURVEY_SECRET_KEY' ) ){
    define( 'YITH_WCPO_SURVEY_SECRET_KEY', 'TESbzQybbHp3dSGGQjqX' );
}

register_activation_hook( __FILE__, 'yith_create_pending_order_cron' );

if( !function_exists('yith_create_pending_order_cron')){
	function yith_create_pending_order_cron(){

		$check_after= get_option('ywcpos_include_pending_from', '30');//default 30 minutes

		if( $check_after!= '')
			wp_schedule_single_event( time()+absint( $check_after )*60, 'ywpos_check_pending_order' );
	}
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCPO_SURVEY_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCPO_SURVEY_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader( YITH_WCPO_SURVEY_DIR );

if ( ! function_exists( 'yith_wcpo_survey_premium_init' ) ) {
    /**
     * Unique access to instance of YITH_Pending_Order_Survey class
     *
     * @return YITH_WCOP_Survey
     * @since 1.0.0
     */
    function yith_wcpo_survey_premium_init() {

        load_plugin_textdomain( 'yith-woocommerce-pending-order-survey', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Load required classes and functions
        require_once( YITH_WCPO_SURVEY_INC.'functions.yith-wcpos-functions.php' );
        require_once( YITH_WCPO_SURVEY_INC.'post_type/class.yith-wcpos-survey-post-type.php' );
        require_once( YITH_WCPO_SURVEY_INC.'classes/class.yith-wcpos-survey-cron.php' );
        require_once( YITH_WCPO_SURVEY_INC.'classes/class.yith-wcpos-export.php' );
        require_once( YITH_WCPO_SURVEY_INC.'post_type/class.yith-wcpos-survey-email-type.php' );
        require_once( YITH_WCPO_SURVEY_INC.'tables/class.yith-wcpos-pending-survey-table.php' );
        require_once( YITH_WCPO_SURVEY_INC.'tables/class.yith-wcpos-pending-survey-email-table.php' );
        require_once( YITH_WCPO_SURVEY_INC.'tables/class.yith-wcpos-pending-order-table.php' );
        require_once( YITH_WCPO_SURVEY_INC.'tables/class.yith-wcpos-recovered-order-table.php' );
        require_once( YITH_WCPO_SURVEY_INC.'classes/class.yith-wcpos-survey.php' );


        global $YWCOP_Survey;
        $YWCOP_Survey = YITH_WCOP_Survey::get_instance();

        YITH_WC_POS_Cron();


        add_action( 'ywcpos_cron', array( YITH_Pending_Email_Type(),'send_pending_email_cron' ) );
    }
}

add_action( 'pending_order_survey_premium_init', 'yith_wcpo_survey_premium_init' );

if( !function_exists( 'yith_wcpo_survey_premium_install' ) ){

    function yith_wcpo_survey_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_wc_pending_order_surveys_premium_install_woocommerce_admin_notice' );
        }else
            do_action( 'pending_order_survey_premium_init' );
    }
}

add_action( 'plugins_loaded', 'yith_wcpo_survey_premium_install' ,11 );
