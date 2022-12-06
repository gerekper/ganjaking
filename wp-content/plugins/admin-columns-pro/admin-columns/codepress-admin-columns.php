<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	return;
}

define( 'AC_FILE', __FILE__ );
define( 'AC_VERSION', '4.6' );

require_once __DIR__ . '/classes/Dependencies.php';

add_action( 'after_setup_theme', function () {
	$dependencies = new AC\Dependencies( plugin_basename( __FILE__ ), AC_VERSION );
	$dependencies->requires_php( '7.2' );

	if ( $dependencies->has_missing() ) {
		return;
	}

	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/api.php';

	/**
	 * For loading external resources, e.g. column settings.
	 * Can be called from plugins and themes.
	 */
	do_action( 'ac/ready', AC() );
}, 1 );