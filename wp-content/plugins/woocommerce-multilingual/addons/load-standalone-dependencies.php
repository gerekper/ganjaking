<?php
/**
 * This bootstrap file is loaded only when WCML is running in the Standalone mode.
 * It's executed on `plugins_loaded` priority 10000.
 *
 * @see load_wcml_without_wpml
 */

/**
 * This constant `WCML_WPML_DEPENDENCY_URL` is used in several places inside
 * the dependency files (it replaces hard-coded URL base).
 */
define( 'WCML_WPML_DEPENDENCY_URL', WCML_PLUGIN_URL . '/addons/wpml-dependencies/lib' );

require_once WCML_PLUGIN_PATH . '/addons/wpml-dependencies/vendor/autoload.php';
require_once WCML_PLUGIN_PATH . '/addons/wpml-dependencies/lib/missing-functions.php';

$setPartialDicConfigFromCore = function() {
	\WPML\Container\share( \WCML\StandAlone\Container\Config::getSharedInstances() );
	\WPML\Container\share( \WCML\StandAlone\Container\Config::getSharedClasses() );
	\WPML\Container\alias( \WCML\StandAlone\Container\Config::getAliases() );
	\WPML\Container\delegate( \WCML\StandAlone\Container\Config::getDelegated() );
};

$setPartialDicConfigFromCore();

if ( is_admin() ) {
	require_once WCML_PLUGIN_PATH . '/addons/wpml-dependencies/lib/inc/icl-admin-notifier.php';

	$loadOtgsIconsStyles = function() {
		$vendor_root_url = WCML_PLUGIN_URL . '/vendor';
		require_once WCML_PLUGIN_PATH . '/vendor/otgs/icons/loader.php';

		add_action( 'admin_init',
			function() {
				wp_enqueue_style( OTGS_ASSETS_ICONS_STYLES );
			},
			PHP_INT_MAX
		);
	};

	$loadOtgsIconsStyles();

	( new \WCML\StandAlone\DependencyAssets( WCML_WPML_DEPENDENCY_URL ) )->add_hooks();

	wcml_wpml_get_admin_notices(); // Initialize the notices hooks.

	( new WPML_Action_Filter_Loader() )->load( [
		\WPML\Notices\DismissNotices::class,
	] );
}
