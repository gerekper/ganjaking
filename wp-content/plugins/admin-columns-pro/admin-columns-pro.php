<?php
/*
Plugin Name: Admin Columns Pro
Version: 6.0.3
Description: Customize columns on the administration screens for post(types), users and other content. Filter and sort content, and edit posts directly from the posts overview. All via an intuitive, easy-to-use drag-and-drop interface.
Author: AdminColumns.com
Author URI: https://www.admincolumns.com
Plugin URI: https://www.admincolumns.com
Requires PHP: 7.2
Requires at least: 5.3
Text Domain: codepress-admin-columns
Domain Path: /languages/
*/

use AC\Asset\Location\Absolute;
use ACA\ACF\AdvancedCustomFields;
use ACA\BbPress\BbPress;
use ACA\BeaverBuilder\BeaverBuilder;
use ACA\BP\BuddyPress;
use ACA\EC\EventsCalendar;
use ACA\GravityForms\GravityForms;
use ACA\JetEngine\JetEngine;
use ACA\MetaBox\MetaBox;
use ACA\MLA\MediaLibraryAssistant;
use ACA\Pods\Pods;
use ACA\Polylang\Polylang;
use ACA\Types\Types;
use ACA\WC\WooCommerce;
use ACA\YoastSeo\YoastSeo;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	return;
}

define( 'ACP_FILE', __FILE__ );
define( 'ACP_VERSION', '6.0.3' );

require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Deactivate Admin Columns
 */
deactivate_plugins( 'codepress-admin-columns/codepress-admin-columns.php' );

/**
 * Load Admin Columns
 */
add_action( 'plugins_loaded', function () {
	require_once 'admin-columns/codepress-admin-columns.php';
} );

/**
 * Load Admin Columns Pro
 */
add_action( 'after_setup_theme', function () {
	$dependencies = new AC\Dependencies( plugin_basename( ACP_FILE ), ACP_VERSION );
	$dependencies->requires_php( '7.2' );

	if ( $dependencies->has_missing() ) {
		return;
	}

	$deactivate = [];

	$addons = [
		'acf'                     => AdvancedCustomFields::class,
		'beaver-builder'          => BeaverBuilder::class,
		'bbpress'                 => BbPress::class,
		'buddypress'              => BuddyPress::class,
		'events-calendar'         => EventsCalendar::class,
		'gravityforms'            => GravityForms::class,
		'jetengine'               => JetEngine::class,
		'metabox'                 => MetaBox::class,
		'media-library-assistant' => MediaLibraryAssistant::class,
		'pods'                    => Pods::class,
		'polylang'                => Polylang::class,
		'types'                   => Types::class,
		'woocommerce'             => WooCommerce::class,
		'yoast-seo'               => YoastSeo::class,
	];

	foreach ( $addons as $key => $addon ) {
		$filename = sprintf( '%1$s%2$s/%1$s%2$s.php', 'ac-addon-', $key );

		if ( is_plugin_active( $filename ) ) {
			$deactivate[] = $filename;
		}
	}

	// Reload to prevent duplicate loading of functions and classes
	if ( $deactivate ) {
		deactivate_plugins( $deactivate );

		$protocol = is_ssl() ? 'https' : 'http';
		$url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		wp_redirect( $url );
		exit;
	}

	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/api.php';

	/**
	 * For loading external resources like column settings.
	 * Can be called from plugins and themes.
	 */
	do_action( 'acp/ready', ACP() );

	foreach ( $addons as $key => $addon ) {
		if ( ! apply_filters( 'acp/addon/' . $key . '/active', true ) ) {
			continue;
		}

		$path = 'addons/' . $key;
		$location = new Absolute(
			plugin_dir_url( __FILE__ ) . $path,
			plugin_dir_path( __FILE__ ) . $path
		);

		( new $addon( $location ) )->register();
	}
}, 5 );