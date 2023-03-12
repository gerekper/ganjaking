<?php
/**
 * Plugin Name: Gravity Forms Multilingual
 * Plugin URI: https://wpml.org/documentation/related-projects/gravity-forms-multilingual/?utm_source=plugin&utm_medium=gui&utm_campaign=gfml
 * Description: Add multilingual support for Gravity Forms
 * Author: OnTheGoSystems
 * Author URI: http://www.onthegosystems.com/
 * Version: 1.7.2
 * Plugin Slug: gravityforms-multilingual
 *
 * @package WPML\gfml
 */

if ( defined( 'GRAVITYFORMS_MULTILINGUAL_VERSION' ) ) {
	return;
}

define( 'GRAVITYFORMS_MULTILINGUAL_VERSION', '1.7.2' );
define( 'GRAVITYFORMS_MULTILINGUAL_PATH', dirname( __FILE__ ) );

require_once GRAVITYFORMS_MULTILINGUAL_PATH . '/classes/class-wpml-gfml-plugin-activation.php';
( new WPML_GFML_Plugin_Activation() )->register_callback();

add_action( 'wpml_loaded', 'gfml_init' );

function gfml_init() {
	if ( ! class_exists( 'WPML_Core_Version_Check' ) ) {
		require_once GRAVITYFORMS_MULTILINGUAL_PATH . '/vendor/wpml-shared/wpml-lib-dependencies/src/dependencies/class-wpml-core-version-check.php';
	}

	if ( ! WPML_Core_Version_Check::is_ok( GRAVITYFORMS_MULTILINGUAL_PATH . '/wpml-dependencies.json' ) ) {
		return;
	}

	require_once GRAVITYFORMS_MULTILINGUAL_PATH . '/vendor/autoload.php';

	add_action( 'wpml_gfml_has_requirements', 'load_gfml' );

	new WPML_GFML_Requirements();
}

/**
 * Load the plugin if WPML-Core is installed
 */
function load_gfml() {
	/* @var GFML_TM_API|null $wpml_gfml_tm_api */
	global $wpml_gfml_tm_api;

	if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
		\WPML\Container\share( \GFML\Container\Config::getSharedClasses() );

		$wpml_gfml_tm_api = \WPML\Container\make( GFML_TM_API::class );

		\GFML\Loader::init();

		do_action( 'wpml_gfml_tm_api_loaded', $wpml_gfml_tm_api );
	}
}

/**
 * Disable the normal wpml admin language switcher for gravity forms.
 *
 * @param string $state
 *
 * @return bool
 */
function gfml_disable_wpml_admin_lang_switcher( $state ) {
	global $pagenow;

	if ( 'admin.php' === $pagenow && 'gf_edit_forms' === filter_input( INPUT_GET, 'page' ) ) {
		$state = false;
	}

	return $state;
}

add_filter( 'wpml_show_admin_language_switcher', 'gfml_disable_wpml_admin_lang_switcher' );
