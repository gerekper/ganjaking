<?php
/**
 *	Plugin Name: AliDropship Woo Plugin
 *	Plugin URI: https://alidropship.com/
 *	Description: AliDropship Woo is a WordPress plugin created for import AliExpress product to Woo Shop
 *	Version: 1.6.26
 *	Text Domain: adsw
 *	Requires at least: WP 5.4
 *	Author: Vitaly Kukin & Yaroslav Nevskiy & Pavel Shishkin & Denis Zharov
 *	Author URI: http://yellowduck.me/
 *	License: SHAREWARE
 *	WC requires at least: 4.5.0
 *	WC tested up to: 5.0.0
 */

if ( ! defined( 'ADSW_VERSION' ) ) define( 'ADSW_VERSION', '1.6.26' );

if ( ! defined( 'ADSW_PATH' ) ) define( 'ADSW_PATH', plugin_dir_path( __FILE__ ) );
if ( ! defined( 'ADSW_URL' ) ) define( 'ADSW_URL', str_replace( [ 'https:', 'http:' ], '', plugins_url( 'alidswoo' ) ) );
if ( ! defined( 'ADSW_CODE' ) ) define( 'ADSW_CODE', 'ion72' );
if ( ! defined( 'ADSW_ERROR' ) ) define( 'ADSW_ERROR', adsw_check_server() );
if ( ! defined( 'ADSW_MIN' ) ) define( 'ADSW_MIN', '.min' );
if ( ! defined( 'ADSW_ASSETS_PATH' ) ) define( 'ADSW_ASSETS_PATH', '/assets/' ); // /src/ - develop, /assets/ - production

function adsw_check_server() {

    if( version_compare( '7.1', PHP_VERSION, '>' ) )
		return sprintf(
			'PHP Version is not suitable. You need version 7.1+. %s',
			'<a href="https://alidropship.com/codex/6-install-ioncube-loader-hosting/" target="_blank">Learn more</a>.'
		);

    $ion_args = [ 'ion71' => '7.1', 'ion72' => '7.2' ];
    $ver      = explode( '.', PHP_VERSION );
    $version  = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
    $ion_pref = 'ion' . $ver[ 0 ] . $ver[ 1 ];

    if( $ion_pref != ADSW_CODE && $ver[ 0 ] . $ver[ 1 ] < 73 )
        return sprintf(
            'You installed AliDropship Woo plugin for PHP %1$s, but your version of PHP is %2$s.' . ' ' .
            'Please <a href="%3$s" target="_blank">download</a> and install AliDropship plugin for PHP %2$s.',
            isset( $ion_args[ ADSW_CODE ] ) ? $ion_args[ ADSW_CODE ] : 'Unknown',
            $version,
            'https://alidropship.com/updates-plugin/'
        );


	$extensions = get_loaded_extensions();

	$key = 'ionCube Loader';

	if ( ! in_array( $key, $extensions ) ) {

		return sprintf(
		    '%s Not found. %s', $key,
            '<a href="https://alidropship.com/codex/6-install-ioncube-loader-hosting/" target="_blank">
                Please check instructions
            </a>.'
        );
	}

	$plugins_local = apply_filters( 'active_plugins', (array) get_option( 'active_plugins', [] ) );
	$plugins_global = (array) get_site_option( 'active_sitewide_plugins', [] );

	if( in_array( 'alids/alids.php', $plugins_local ) ) {

		return __( 'You can\'t use AliDropship original and AliDropship Woo plugin at the same time. Please deactivate and delete AliDropship original plugin if you want to use AliDropship Woo plugin + WooCommerce.', 'adsw' );
	}

	if( ! in_array( 'woocommerce/woocommerce.php', $plugins_local ) && ! ( is_multisite() && array_key_exists( 'woocommerce/woocommerce.php' , $plugins_global ) ) ) {

		return __( 'Please install and activate WooCommerce to make AliDropship Woo plugin work properly.', 'adsw' );
	}

	return false;
}

function adsw_admin_notice__error() {

	if( ADSW_ERROR ) {

		$class = 'notice notice-error';
		$message = __( 'AliDropship Woo plugin alert: Ooops!', 'adsw' ) . ' ' . ADSW_ERROR;

		printf( '<div class="%1$s">
                            <div style="display: flex;">
                                <div style="margin-top:10px; margin-right: 10px; margin-bottom: 10px; width: 57px; min-width: 57px; height: 57px; background-color: #5f5f5f; background-image:  url(%2$s); background-position: center; background-size: 45px 45px; background-repeat:no-repeat; border-radius: 4px; }"></div>
                                <p>%3$s</p>
                            </div>
                        </div>',
            $class,
            ADSW_URL . ADSW_ASSETS_PATH . 'images/main/logo.svg',
            $message );
	}

    if( defined( 'DM_VERSION' ) ) {

        $class = 'notice notice-warning';
        $message = sprintf(
            '%s <strong>%s</strong> %s',
            __( 'AliDropship Woo plugin warning:', 'adsw' ),
            __( 'Duplicate function has been found: please deactivate and uninstall DropshipMe plugin.', 'adsw' ),
            __( 'You are trying to install AliDropship Woo and DropshipMe plugins together.', 'adsw' ) . ' ' .
            __( 'Note that AliDropship Woo includes DropshipMe database and functions.', 'adsw' )
        );

        printf( '<div class="%1$s">
                            <div style="display: flex;">
                                <div style="margin-top:10px; margin-right: 10px; margin-bottom: 10px; width: 57px; min-width: 57px; height: 57px; background-color: #5f5f5f; background-image:  url(%2$s); background-position: center; background-size: 45px 45px; background-repeat:no-repeat; border-radius: 4px; }"></div>
                                <p>%3$s</p>
                            </div>
                        </div>',
            $class,
            ADSW_URL . ADSW_ASSETS_PATH . 'images/main/logo.svg',
            $message );
    }
}
add_action( 'admin_notices', 'adsw_admin_notice__error' );

/**
 * Localization
 */
function adsw_lang_init() {

	load_plugin_textdomain( 'adsw' );
}
add_action( 'init', 'adsw_lang_init' );

require( ADSW_PATH . 'core/autoload.php');

if( is_admin() ) {

	require( ADSW_PATH . 'core/setup.php');

	register_activation_hook( __FILE__, 'adsw_lang_init' );
	register_activation_hook( __FILE__, 'adsw_install' );
	register_activation_hook( __FILE__, 'adsw_activate' );
}

if ( ! ADSW_ERROR ) {
	
	require( ADSW_PATH . 'core/core.php');
    require( ADSW_PATH . 'core/update.php');
	require( ADSW_PATH . 'core/filters.php' );
	require( ADSW_PATH . 'core/init.php' );
    require( ADSW_PATH . 'admin/cron.php' );
	
	if ( ! defined('ADSW_NICE') ) define( 'ADSW_NICE', serialize( adsw_nice_attributes_option() ) );

	if( is_admin() ) {
		require(ADSW_PATH . 'core/controller.php');
	} else {

		$status = adsw_get_nice_attr( 'status' );

		if( $status && $status == 1 )
            require( ADSW_PATH . 'core/nice_attributes_hooks.php' );

        require( ADSW_PATH . 'core/front_hooks.php' );
	}
}


