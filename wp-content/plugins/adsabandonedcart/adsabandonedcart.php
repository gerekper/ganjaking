<?php
/**
 * Plugin Name: Ads Abandoned Cart AliDropship Plugin
 * Plugin URI: https://alidropship.com/addons/abandoned-cart/
 * Description: Decrease your abandoned cart rate with by sending customizable emails.
 * Version: 2.0.17
 * Text Domain: abd
 * Requires at least: WP 5.3.0
 * Author: Yaroslav Nevskiy & Pavel Shishkin
 * Author URI: https://yellowduck.me/
 * License: SHAREWARE
 */


if( !defined( 'ADSABANDONED_VERSION' ) ) define( 'ADSABANDONED_VERSION', '2.0.17' );
if( !defined( 'ADSABANDONED_PATH' ) ) define( 'ADSABANDONED_PATH', plugin_dir_path( __FILE__ ) );
if( !defined( 'ADSABANDONED_URL' ) ) define( 'ADSABANDONED_URL', str_replace( [ 'https:', 'http:' ], '', plugins_url( 'adsabandonedcart' ) ) );
if( !defined( 'ADSABANDONED_CODE' ) ) define( 'ADSABANDONED_CODE', 'ion72' );
if( !defined( 'ADSABANDONED_ERROR' ) ) define( 'ADSABANDONED_ERROR', abandoned_check_server() );

/**
 * Localization
 */
function abandoned_lang_init()
{
    
    load_plugin_textdomain( 'abd' );
}

add_action( 'init', 'abandoned_lang_init' );


function abandoned_instance_plugins(){
    $plugins_local = apply_filters('active_plugins', (array)get_option('active_plugins', []));
    if (!is_multisite() &&  in_array('alidswoo/alidswoo.php', $plugins_local)
    ) {
        if( ! defined('INSTANCE_WOO') )   define('INSTANCE_WOO', true);
    }
    
    $plugins_global = (array)get_site_option('active_sitewide_plugins', []);
    if (is_multisite() && array_key_exists('alidswoo/alidswoo.php', $plugins_global)) {
        if( ! defined('INSTANCE_WOO') ) define('INSTANCE_WOO', true);
    }
    
    if( ! defined('INSTANCE_WOO') ) define('INSTANCE_WOO', false);
}

abandoned_instance_plugins();

/**
 * abandoned_check_server
 *
 * @return string|bool
 */
function abandoned_check_server()
{
    
    if( version_compare( '7.1', PHP_VERSION, '>' ) ) {
        return sprintf( 'PHP Version is not suitable. You need version 7.1+. %s',
            '<a href="https://alidropship.com/codex/6-install-ioncube-loader-hosting/" target="_blank">Learn more</a>.'
        );
    }
    
    $ion_args = [ 'ion71' => '7.1', 'ion72' => '7.2' ];
    $ver      = explode( '.', PHP_VERSION );
    $ion_pref = 'ion' . $ver[0] . $ver[1];
    
    if( $ion_pref != ADSABANDONED_CODE && $ver[0] . $ver[1] < 73 ) {
        return sprintf(
            'You installed Abandoned Cart plugin for PHP %1$s, but your version of PHP is %2$s.' . ' ' .
            'Please <a href="%3$s" target="_blank">download</a> and install Abandoned Cart plugin for PHP %2$s.',
            isset( $ion_args[ ADSABANDONED_CODE ] ) ? $ion_args[ ADSABANDONED_CODE ] : 'Unknown',
            PHP_VERSION,
            'https://alidropship.com/addons/ads_abandoned/#updateaddon'
        );
    }
    
    $extensions = get_loaded_extensions();
    $key        = 'ionCube Loader';
    
    if( !in_array( $key, $extensions ) ) {
        return sprintf( __( '%s Not found' ), $key ) .
            '. <a href="https://alidropship.com/codex/6-install-ioncube-loader-hosting/" target="_blank">
            Please check instructions
        </a>.';
    }
    
    $plugins_local  = apply_filters( 'active_plugins', (array) get_option( 'active_plugins', [] ) );
    $plugins_global = (array) get_site_option( 'active_sitewide_plugins', [] );
    
    $path = dirname( __FILE__ );
    require_once( $path . '/../../../wp-admin/includes/plugin.php' );
    if( !is_multisite()
        && !in_array( 'alids/alids.php', $plugins_local )
        && !in_array( 'alidswoo/alidswoo.php', $plugins_local )
    ) {
        return __( 'Abandoned Cart add-on requires Alidropship plugin or AliDropship Woo plugin for its proper work' );
    }
    
    if( is_multisite()
        && !array_key_exists( 'alidswoo/alidswoo.php', $plugins_global )
        && !array_key_exists( 'alids/alids.php', $plugins_global )
    ) {
        return __( 'Abandoned Cart add-on requires Alidropship plugin or AliDropship Woo plugin for its proper work' );
    }
    
    return false;
}

/**
 * abandoned_admin_notice__error
 */
function abandoned_admin_notice__error()
{
    $check = abandoned_check_server();
    if( $check ) {
        $class   = 'notice notice-error';
        $message = __( 'Error!', 'abd' ) . ' ' . $check;
        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }
}

add_action( 'admin_notices', 'abandoned_admin_notice__error' );

if( !ADSABANDONED_ERROR ) {
    
    require( ADSABANDONED_PATH . 'install/functions.php' );
    require( ADSABANDONED_PATH . 'install/install.php' );
    require( ADSABANDONED_PATH . 'install/update.php' );
    require( ADSABANDONED_PATH . 'install/handlers.php' );
    
    require( ADSABANDONED_PATH . 'core/autoloader.php' );
    require( ADSABANDONED_PATH . 'core/cron.php' );
    
    \adsAbandoned\Option::init();
    
    new adsAbandoned\App();
    
    if( is_admin() ) {
        register_activation_hook( __FILE__, 'adsabandoned_install' );
        register_uninstall_hook( __FILE__, 'adsabandoned_uninstall' );
        register_activation_hook( __FILE__, 'adsabandoned_activate' );
        register_deactivation_hook( __FILE__, 'adsabandoned_deactivate' );
    }
    
}

