<?php

defined( 'ABSPATH' ) || exit;

/**
 * Porto Theme Options
 */

require_once( PORTO_ADMIN . '/functions.php' );

// include redux framework core functions
require_once( PORTO_ADMIN . '/ReduxCore/framework.php' );
// porto theme settings options
require_once( PORTO_ADMIN . '/theme_options/settings.php' );

require_once( PORTO_ADMIN . '/theme_options/save_settings.php' );

if ( ! get_theme_mod( 'theme_options_saved', false ) ) {
	// set search layout and minicart type for old versions
	porto_restore_default_options_for_old_versions();

	porto_check_theme_options();
}

// regenerate default css, skin css files after update theme
$porto_cur_version = get_option( 'porto_version', '1.0' );
if ( ! porto_is_ajax() && version_compare( PORTO_VERSION, $porto_cur_version, '!=' ) ) {

	if ( version_compare( phpversion(), '5.3', '>=' ) ) {

		// set search layout and minicart type for old versions
		porto_restore_default_options_for_old_versions( true );

		// regenerate skin css
		porto_save_theme_settings();

		// regenerate default css
		if ( is_rtl() ) {
			porto_compile_css( 'bootstrap_rtl' );
		} else {
			porto_compile_css( 'bootstrap' );
		}

		// regenerate shortcodes css
		if ( '1.0' != $porto_cur_version ) {
			porto_compile_css( 'shortcodes' );
		}
	}

	update_option( 'porto_version', PORTO_VERSION );
}
