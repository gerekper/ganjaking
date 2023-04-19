<?php
/**
 * Plugin Name: WooCommerce Multilingual & Multicurrency
 * Plugin URI: https://wpml.org/documentation/related-projects/woocommerce-multilingual/?utm_source=plugin&utm_medium=gui&utm_campaign=wcml
 * Description: Make your store multilingual and enable multiple currencies | <a href="https://wpml.org/documentation/related-projects/woocommerce-multilingual/?utm_source=plugin&utm_medium=gui&utm_campaign=wcml">Documentation</a>
 * Author: OnTheGoSystems
 * Author URI: http://www.onthegosystems.com/
 * Text Domain: woocommerce-multilingual
 * Requires at least: 4.7
 * Tested up to: 6.2
 * Version: 5.1.3
 * Plugin Slug: woocommerce-multilingual
 * WC requires at least: 3.9
 * WC tested up to: 7.6
 *
 * @package WCML
 * @author  OnTheGoSystems
 */

if (
	defined( 'WCML_VERSION' )
	/* phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected */
	|| ( isset( $_SERVER['REQUEST_URI'] ) && '/favicon.ico' === $_SERVER['REQUEST_URI'] )
) {
	return;
}

require_once 'vendor/wpml-shared/wpml-lib-dependencies/src/dependencies/class-wpml-php-version-check.php'; // We cannot use composer here.

$wpml_php_version_check = new WPML_PHP_Version_Check(
	'5.6',
	'WooCommerce Multilingual & Multicurrency',
	__FILE__,
	'woocommerce-multilingual'
);
if ( ! $wpml_php_version_check->is_ok() ) {
	return;
}

define( 'WCML_VERSION', '5.1.3' );
define( 'WCML_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'WCML_PLUGIN_FOLDER', basename( WCML_PLUGIN_PATH ) );
define( 'WCML_LOCALE_PATH', WCML_PLUGIN_PATH . '/locale' );
define( 'WPML_LOAD_API_SUPPORT', true );
define( 'WCML_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

require WCML_PLUGIN_PATH . '/inc/constants.php';
require WCML_PLUGIN_PATH . '/inc/missing-php-functions.php';
require WCML_PLUGIN_PATH . '/inc/installer-loader.php';
require WCML_PLUGIN_PATH . '/inc/functions.php';
require WCML_PLUGIN_PATH . '/inc/wcml-core-functions.php';

require WCML_PLUGIN_PATH . '/vendor/autoload.php';

require_once WCML_PLUGIN_PATH . '/vendor/otgs/ui/loader.php';
otgs_ui_initialize( WCML_PLUGIN_PATH . '/vendor/otgs/ui', WCML_PLUGIN_URL . '/vendor/otgs/ui' ); // @phpstan-ignore-line

$vendor_root_url = WCML_PLUGIN_URL . '/vendor';
require_once WCML_PLUGIN_PATH . '/vendor/otgs/icons/loader.php';

if ( WPML_Core_Version_Check::is_ok( WCML_PLUGIN_PATH . '/wpml-dependencies.json' ) ) {
	global $woocommerce_wpml;

	if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
		( new WPML_Action_Filter_Loader() )->load( [
			WCML_Switch_Lang_Request::class,
			WCML_Cart_Switch_Lang_Functions::class,
			WCML\AdminTexts\Hooks::class,
		] );
	}

	$woocommerce_wpml = new woocommerce_wpml();
	$woocommerce_wpml->add_hooks();

	add_action( 'wpml_loaded', 'wcml_loader' );
}

/**
 * Load WooCommerce Multilingual after WPML is loaded
 */
function wcml_loader() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	\WPML\Container\share( \WCML\Container\Config::getSharedInstances() );
	\WPML\Container\share( \WCML\Container\Config::getSharedClasses() );
	\WPML\Container\alias( \WCML\Container\Config::getAliases() );
	\WPML\Container\delegate( \WCML\Container\Config::getDelegated() );

	$loaders = [
		WCML_xDomain_Data::class,
		'WCML_Privacy_Content_Factory',
		'WCML_ATE_Activate_Synchronization',
		\WCML\RewriteRules\Hooks::class,
		\WCML\Email\Settings\Hooks::class,
		\WCML\Block\Convert\Hooks::class,
		\WCML\MO\Hooks::class,
		\WCML\Multicurrency\Shipping\ShippingHooksFactory::class,
		\WCML\Reviews\Backend\Hooks::class,
		\WCML\Reviews\Translations\Factory::class,
		\WCML\Tax\Strings\Hooks::class,
		\WCML\AdminDashboard\Hooks::class,
		\WCML\AdminNotices\Review::class,
		\WCML\Multicurrency\UI\Factory::class,
		\WCML\PaymentGateways\Hooks::class,
		\WCML\CLI\Hooks::class,
		\WCML\Reports\Hooks::class,
		\WCML\Reports\Products\Query::class,
		\WCML\MultiCurrency\GeolocationFrontendHooks::class,
		\WCML\MultiCurrency\GeolocationBackendHooks::class,
		\WCML\Reports\Categories\Query::class,
		\WCML\Reports\Orders\Hooks::class,
		\WCML\Multicurrency\Analytics\Factory::class,
		\WCML\Multicurrency\Analytics\Export::class,
		\WCML\Setup\BeforeHooks::class,
		\WCML\AdminNotices\MultiCurrencyMissing::class,
		\WCML\Products\Hooks::class,
		\WCML\API\VendorAddon\Hooks::class,
		\WCML\Attributes\LookupTableFactory::class,
		\WCML\Attributes\LookupFiltersFactory::class,
		\WCML\HomeScreen\Factory::class,
		\WCML\Terms\Count\Hooks::class,
		\WCML\Rest\Store\HooksFactory::class,
		\WCML\Importer\Products::class,
	];

	if (
		( defined( 'ICL_SITEPRESS_VERSION' ) && defined( 'WPML_MEDIA_VERSION' ) )
		|| ( defined( 'ICL_SITEPRESS_VERSION' )
			 && version_compare( ICL_SITEPRESS_VERSION, '4.0.0', '>=' )
			 && version_compare( ICL_SITEPRESS_VERSION, '4.0.4', '<' )
			 && ! defined( 'WPML_MEDIA_VERSION' )
		)
	) {
		$loaders[] = 'WCML_Product_Image_Filter_Factory';
		$loaders[] = 'WCML_Product_Gallery_Filter_Factory';
		$loaders[] = 'WCML_Update_Product_Gallery_Translation_Factory';
		$loaders[] = 'WCML_Append_Gallery_To_Post_Media_Ids_Factory';
	}

	$action_filter_loader = new \WCML\StandAlone\ActionFilterLoader();
	$action_filter_loader->load( $loaders );
}

if ( WCML\Rest\Functions::isRestApiRequest() ) {
	add_action( 'wpml_before_init', [ WCML\Rest\Generic::class, 'removeHomeUrlFilterOnRestAuthentication' ] );
}

/**
 * Load WooCommerce Multilingual when WPML is NOT active.
 */
function load_wcml_without_wpml() {
	if ( ! did_action( 'wpml_loaded' ) ) {
		require_once WCML_PLUGIN_PATH . '/addons/load-standalone-dependencies.php';

		global $woocommerce_wpml;
		$woocommerce_wpml = new woocommerce_wpml();
		wcml_loader();
	}
}

add_action( 'plugins_loaded', 'load_wcml_without_wpml', 10000 );
