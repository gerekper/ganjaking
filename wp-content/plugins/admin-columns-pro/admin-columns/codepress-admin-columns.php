<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	return;
}

define( 'AC_FILE', __FILE__ );
define( 'AC_VERSION', '4.1.9' );

require_once __DIR__ . '/classes/Dependencies.php';

add_action( 'after_setup_theme', function () {
	$dependencies = new AC\Dependencies( plugin_basename( AC_FILE ), AC_VERSION );
	$dependencies->requires_php( '5.6.20' );

	if ( $dependencies->has_missing() ) {
		return;
	}

	require_once __DIR__ . '/api.php';
	require_once __DIR__ . '/classes/Autoloader.php';

	AC\Autoloader::instance()->register_prefix( 'AC', __DIR__ . '/classes' );
	AC\Autoloader\Underscore::instance()
	                        ->add_alias( 'AC\ListScreen', 'AC_ListScreen' )
	                        ->add_alias( 'AC\Settings\FormatValue', 'AC_Settings_FormatValueInterface' )
	                        ->add_alias( 'AC\Column\Media\MediaParent', 'AC_Column_Media_Parent' )
	                        ->add_alias( 'AC\Column\Post\PostParent', 'AC_Column_Post_Parent' );

	/**
	 * For loading external resources, e.g. column settings.
	 * Can be called from plugins and themes.
	 */
	do_action( 'ac/ready', AC() );
}, 1 );
