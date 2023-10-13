<?php

use WPML\FP\Lst;
use WPML\FP\Logic;
use WPML\FP\Relation;
use function WCML\functions\getWooCommerceWpml;

class WCML_Compatibility {

	/**
	 * Initialize compatibility classes that need to run before multi-currency.
	 */
	public function init_before_multicurrency() {
		$loaders = wpml_collect( [
			\WCML\Compatibility\WpSuperCache\Factory::class => function_exists( 'wp_cache_is_enabled' ) && wp_cache_is_enabled(),
		] )->filter( Logic::isTruthy() )
			->keys()
			->toArray();

		// This one needs to run after all caching classes.
		$loaders = Lst::append( \WCML\AdminNotices\CachePlugins::class, $loaders );

		( new \WCML\StandAlone\ActionFilterLoader() )->load( $loaders );
	}

	/**
	 * Initialize class
	 */
	public function init() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$woocommerce_wpml = getWooCommerceWpml();

		// $isActiveTheme :: string -> bool
		$isActiveTheme = Relation::equals( wp_get_theme()->get( 'Name' ) );

		$loaders = wpml_collect( [
			\WCML\Compatibility\WcTabManager\Factory::class           => class_exists( 'WC_Tab_Manager' ),
			\WCML\Compatibility\TableRateShipping\Factory::class      => defined( 'TABLE_RATE_SHIPPING_VERSION' ),
			\WCML\Compatibility\WcSubscriptions\Factory::class        => class_exists( 'WC_Subscriptions' ),
			\WCML\Compatibility\WcNameYourPrice\Factory::class        => class_exists( 'WC_Name_Your_Price' ),
			\WCML\Compatibility\WcProductBundles\Factory::class       => class_exists( 'WC_Product_Bundle' ) && function_exists( 'WC_PB' ),
			\WCML\Compatibility\WcSwatches\Factory::class             => class_exists( 'WC_SwatchesPlugin' ),
			\WCML\Compatibility\WcProductAddons\Factory::class        => defined( 'WC_PRODUCT_ADDONS_VERSION' ) || class_exists( 'Product_Addon_Display' ),
			\WCML\Compatibility\PerProductShipping\Factory::class     => defined( 'PER_PRODUCT_SHIPPING_VERSION' ),
			\WCML\Compatibility\WcExporter\Factory::class             => defined( 'WOO_CE_PATH' ),
			\WCML\Compatibility\GravityForms\Factory::class           => class_exists( 'GFForms' ),
			\WCML\Compatibility\Sensei\Factory::class                 => class_exists( 'WooThemes_Sensei' ),
			\WCML\Compatibility\TmExtraProductOptions\Factory::class  => class_exists( 'TM_Extra_Product_Options' ),
			\WCML\Compatibility\WcDynamicPricing\Factory::class       => class_exists( 'WC_Dynamic_Pricing' ),
			\WCML\Compatibility\WcBookings\Factory::class             => defined( 'WC_BOOKINGS_VERSION' ) && version_compare( WC_BOOKINGS_VERSION, '1.7.8', '>=' ),
			\WCML\Compatibility\WoobeBulkEditor\Factory::class        => defined( 'WOOBE_PATH' ),
			\WCML\Compatibility\WcCheckoutFieldEditor\Factory::class  => function_exists( 'woocommerce_init_checkout_field_editor' ),
			\WCML\Compatibility\WcBulkStockManagement\Factory::class  => class_exists( 'WC_Bulk_Stock_Management' ),
			\WCML\Compatibility\WcAjaxLayeredNav\Factory::class       => is_plugin_active( 'woocommerce-ajax-layered-nav/ajax_layered_nav-widget.php' ),
			\WCML\Compatibility\WcCompositeProducts\Factory::class    => isset( $GLOBALS['woocommerce_composite_products'] ),
			\WCML\Compatibility\WcCheckoutAddons\Factory::class       => class_exists( 'WC_Checkout_Add_Ons_Loader' ),
			\WCML\Compatibility\WcMixAndMatch\Factory::class          => class_exists( 'WC_Mix_and_Match' ),
			\WCML\Compatibility\WpSeo\Factory::class                  => defined( 'WPSEO_VERSION' ),
			\WCML\Compatibility\AdventureTours\Factory::class         => function_exists( 'adventure_tours_check' ),
			\WCML\Compatibility\Flatsome\Factory::class               => $isActiveTheme( 'Flatsome' ),
			\WCML\Compatibility\Aurum\Factory::class                  => $isActiveTheme( 'Aurum' ),
			\WCML\Compatibility\WcShowSingleVariations\Factory::class => defined( 'JCK_WSSV_PATH' ),
			\WCML\Compatibility\WcPip\Factory::class                  => class_exists( 'WC_PIP' ),
			\WCML\Compatibility\TheEventsCalendar\Factory::class      => class_exists( 'Tribe__Events__Main' ),
			\WCML\Compatibility\KlarnaPayments\Factory::class         => class_exists( 'WC_Gateway_Klarna' ),
			\WCML\Compatibility\StripePayments\Factory::class         => class_exists( 'WC_Gateway_Stripe' ) && isset( $woocommerce_wpml->multi_currency->orders ),
			\WCML\Compatibility\YithWcQuickView\Factory::class        => class_exists( 'YITH_WCQV' ),
			\WCML\Compatibility\WcMemberships\Factory::class          => class_exists( 'WC_Memberships' ),
			\WCML\Compatibility\MaxStorePro\Factory::class            => function_exists( 'maxstore_pro_setup' ),
			\WCML\Compatibility\WpBakery\Factory::class               => defined( 'WPB_VC_VERSION' ),
			\WCML\Compatibility\WoofWcProductFilter\Factory::class    => defined( 'WOOF_PLUGIN_NAME' ),
			\WCML\Compatibility\Relevanssi\Factory::class             => function_exists( 'relevanssi_insert_edit' ),
			\WCML\Compatibility\WooVariationsTable\Factory::class     => defined( 'WOO_VARIATIONS_TABLE_VERSION' ),
			\WCML\Compatibility\WpFastestCache\Factory::class         => class_exists( 'WpFastestCache' ),
			\WCML\Compatibility\WcProductTypeColumn\Factory::class    => class_exists( 'WC_Product_Type_Column' ),
			\WCML\Compatibility\YikesCustomProductTabs\Factory::class => class_exists( 'YIKES_Custom_Product_Tabs' ),
			\WCML\Compatibility\WcOrderStatusManager\Factory::class   => class_exists( 'WC_Order_Status_Manager' ),
		] )->filter( Logic::isTruthy() )
			->keys()
			->toArray();

		( new \WCML\StandAlone\ActionFilterLoader() )->load( $loaders );
	}
}
