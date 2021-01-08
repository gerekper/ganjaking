<?php
/**
 * Plugin Name: Shipping Product Aliexpress
 * Plugin URI: https://alidropship.com/addons/alishipping/
 * Description: Easily import and customize real shipping methods from AliExpress
 * Author: Pavel Shishkin
 * Version: 1.3.49
 * Author URI: https://yellowduck.me/
 */

if( ! defined('sSHIP_VERSION') ) define( 'sSHIP_VERSION', '1.3.49' );
if( ! defined('sSHIP_PATH') )    define( 'sSHIP_PATH', plugin_dir_path( __FILE__ ) );
if( ! defined('sSHIP_URL') )     define( 'sSHIP_URL', str_replace( [ 'https:', 'http:' ], '', plugins_url('alids-shipping-products') ) );
if( ! defined('sSHIP_CODE') )    define( 'sSHIP_CODE', 'ion72' );
if( ! defined('sSHIP_ERROR') )   define( 'sSHIP_ERROR', sship_check_server() );

/**
 * Localization
 */
function sship_lang_init() {

    load_plugin_textdomain( 'sship' );
}
add_action( 'init', 'sship_lang_init' );

function sship_check_server() {

    if( version_compare( '7.1', PHP_VERSION, '>' ) )
        return sprintf(
            'PHP Version is not suitable. You need version 7.1+. %s',
            '<a href="https://alidropship.com/codex/6-install-ioncube-loader-hosting/" target="_blank">Learn more</a>.'
        );

    $ion_args = [ 'ion71' => '7.1', 'ion72' => '7.2' ];

    $ver      = explode( '.', PHP_VERSION );
    $version  = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
    $ion_pref = 'ion' . $ver[0] . $ver[1];

    if( $ion_pref != sSHIP_CODE && $ver[0] . $ver[1] < 73 )
        return sprintf(
            'You installed AliDropship Shipping plugin for PHP %1$s, but your version of PHP is %2$s.' . ' ' .
            'Please <a href="%3$s" target="_blank">download</a> and install AliDropship Shipping plugin for PHP %2$s.',
            isset( $ion_args[ sSHIP_CODE ] ) ? $ion_args[ sSHIP_CODE ] : 'Unknown',
            $version,
            'https://alidropship.com/updates-plugin/'
        );

    $extensions = get_loaded_extensions();

    $key = 'ionCube Loader';

    if( ! in_array( $key, $extensions ) ) {

        return sprintf(
            '%s Not found. %s', $key,
            '<a href="https://alidropship.com/codex/6-install-ioncube-loader-hosting/" target="_blank">
            Please check instructions
        </a>.'
        );
    }

    $plugins_local  = apply_filters('active_plugins', (array)get_option('active_plugins', []));
    $plugins_global = (array) get_site_option('active_sitewide_plugins', []);

    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if( ! is_multisite() &&
        ! in_array( 'alids/alids.php', $plugins_local ) &&
        ! in_array('alidswoo/alidswoo.php', $plugins_local )
    ) {

        //deactivate_plugins( sSHIP_PATH . basename(__FILE__) );

        return __( 'Shipping Product Aliexpress add-on requires Alidropship plugin or AliDropship Woo plugin for its proper work', 'sship' );
    }

    if( is_multisite() &&
        ! array_key_exists( 'alids/alids.php', $plugins_global ) &&
        ! array_key_exists( 'alidswoo/alidswoo.php', $plugins_global )
    ) {

        //deactivate_plugins( sSHIP_PATH . basename(__FILE__) );

        return __( 'Shipping Product Aliexpress add-on requires Alidropship plugin or AliDropship Woo plugin for its proper work', 'sship' );
    }

    return false;
}

function sship_admin_notice__error() {

    if( sSHIP_ERROR ) {

        $class   = 'notice notice-error';
        $message = 'Ooops! ' . sSHIP_ERROR;

        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }
}

add_action( 'admin_notices', 'sship_admin_notice__error' );

if( is_admin() ) :

    require( sSHIP_PATH . 'core/setup.php' );

    register_activation_hook( __FILE__, 'sship_install' );
    register_activation_hook( __FILE__, 'sship_activate' );
    register_uninstall_hook( __FILE__, 'sship_uninstall' );

endif;


require( sSHIP_PATH . 'core/update.php' );
require( sSHIP_PATH . 'core/core.php' );
require( sSHIP_PATH . 'core/handler.php' );

\sship\Option::init();


function sship_check_plugin()  {

    $label = false;

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if( is_plugin_active( 'alidswoo/alidswoo.php' ) ) {

        $label = 'ADSW';
    }

    if( is_plugin_active( 'alids/alids.php' ) ) {

        $label = 'ADS';
    }

    return $label;
}

if( ! defined( 'SSHIP_PLUGIN' ) ) {
    define( 'SSHIP_PLUGIN', sship_check_plugin() );
}

if( SSHIP_PLUGIN == 'ADSW' ) {
    global $sshipApp;
    $sshipApp = new \sship\AppWoo( \sship\Option::params() );

    function sship_cart_css() {
        wp_enqueue_style( 'sship_style', sSHIP_URL . '/assets/css/cart.css', null, sSHIP_VERSION );
    }

    function sship_checkout() {
        wp_enqueue_style( 'sship_style', sSHIP_URL . '/assets/css/cart.css', null, sSHIP_VERSION );
        wp_enqueue_script( 'sship_js_checkout', sSHIP_URL . '/assets/js/checkout.js', ['jquery'], sSHIP_VERSION , true);
        wp_localize_script( 'sship_js_checkout', 'sship_checkout',
            [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'text_not_shipping' => __("This product/these products can't be shipped to the selected country", 'sship'),
            ]
        );
    }

    function redirect_visitor(){

        if( is_page('cart') || function_exists('is_cart') && is_cart() ) {
            sship_cart_css();
        }

        if ( is_page('checkout') || function_exists('is_checkout') && is_checkout() ) {
            sship_checkout();
        }
    }
    add_action( 'template_redirect','redirect_visitor' );

} else {

    add_action( 'wp_enqueue_scripts', 'ads_cart_script', 100 );

    global $sshipApp;

    $sshipApp = new \sship\App( \sship\Option::params() );
}


function ads_cart_script(){
    if('cart' === get_query_var( 'pagename' )){
        wp_enqueue_script( 'sship_js_checkout', sSHIP_URL . '/assets/js/checkout.js', ['jquery'], sSHIP_VERSION , true);
        wp_localize_script( 'sship_js_checkout', 'sship_checkout',
            [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'text_not_shipping' => __("This product/these products can't be shipped to the selected country", 'sship'),
            ]
        );
    }

}

add_action( 'admin_enqueue_scripts', 'sship_update_script', 10 );
add_action( 'wp_enqueue_scripts', 'sship_update_script', 10 );

function sship_update_script(){

    if( !current_user_can( 'level_9' ) ){
        return false;
    }

    if(isset($_GET['page']) && in_array( $_GET['page'], ['sshiplist'])){
        return false;
    }

    wp_enqueue_script( 'sship_update', sSHIP_URL . '/assets/js/update.js', 'jquery' );
    wp_localize_script( 'sship_update', 'sship_update',
        [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ]
    );
}
