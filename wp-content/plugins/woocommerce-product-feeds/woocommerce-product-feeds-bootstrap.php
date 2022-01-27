<?php

use Pimple\Container;
use Ademti\DismissibleWpNotices\DismissibleWpNoticeManager;

defined( 'ABSPATH' ) || exit;

/**
 * Set up the DI container.
 *
 * NOTE: Most instances assigned into globals exist to support legacy use by
 * customer / third party code.
 */

global $woocommerce_gpf_di;
$woocommerce_gpf_di = new Container();

$woocommerce_gpf_di['WoocommerceGpfAdmin'] = function ( $c ) {
	global $woocommerce_gpf_admin;
	$woocommerce_gpf_admin = new WoocommerceGpfAdmin(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfTemplateLoader'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfCacheStatus'],
		$c['WoocommerceProductFeedsFeedImageManager'],
		$c['WoocommerceProductFeedsWoocommerceAdminIntegration'],
		$c['WoocommerceProductFeedsFeedConfigRepository'],
		$c['WoocommerceProductFeedsFeedManager']
	);

	return $woocommerce_gpf_admin;
};

$woocommerce_gpf_di['WoocommerceProductFeedsFeedManager'] = function ( $c ) {
	return new WoocommerceProductFeedsFeedManager(
		$c['WoocommerceProductFeedsFeedConfigRepository'],
		$c['WoocommerceGpfTemplateLoader'],
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceProductFeedsFeedManagerListTable']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsFeedManagerListTable'] = function ( $c ) {
	return new WoocommerceProductFeedsFeedManagerListTable(
		$c['WoocommerceProductFeedsFeedConfigRepository'],
		$c['WoocommerceGpfCommon']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsFeedImageManager'] = function ( $c ) {
	return new WoocommerceProductFeedsFeedImageManager(
		$c['WoocommerceGpfTemplateLoader'],
		$c['WoocommerceProductFeedsFeedItemFactory']
	);
};

$woocommerce_gpf_di['DismissibleWpNoticeManager'] = function ( $c ) {
	$uri_path = plugin_dir_url( __FILE__ ) . 'vendor/leewillis77/dismissible-wp-notices/';
	return DismissibleWpNoticeManager::get_instance( $uri_path );
};

$woocommerce_gpf_di['WoocommerceProductFeedsAdminNotices'] = function ( $c ) {
	return new WoocommerceProductFeedsAdminNotices(
		$c['DismissibleWpNoticeManager'],
		$c['WoocommerceGpfTemplateLoader']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsDbManager'] = function ( $c ) {
	return new WoocommerceProductFeedsDbManager(
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsFeedConfigRepository'],
		$c['WoocommerceGpfCommon']
	);
};

$woocommerce_gpf_di['WoocommerceGpfCache'] = function ( $c ) {
	return new WoocommerceGpfCache( $c['WoocommerceGpfDebugService'], $c );
};

$woocommerce_gpf_di['WoocommerceGpfDebugService'] = function ( $c ) {
	return new WoocommerceGpfDebugService();
};

$woocommerce_gpf_di['WoocommerceProductFeedsFeedConfigRepository'] = function ( $c ) {
	return new WoocommerceProductFeedsFeedConfigRepository();
};

$woocommerce_gpf_di['WoocommerceGpfCacheStatus'] = function ( $c ) {
	return new WoocommerceGpfCacheStatus(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfTemplateLoader'],
		$c['WoocommerceProductFeedsFeedConfigRepository']
	);
};

$woocommerce_gpf_di['WoocommerceGpfCacheInvalidator'] = function ( $c ) {
	return new WoocommerceGpfCacheInvalidator( $c['WoocommerceGpfCache'] );
};

$woocommerce_gpf_di['WoocommerceGpfCommon'] = function ( $c ) {
	global $woocommerce_gpf_common;
	$woocommerce_gpf_common = new WoocommerceGpfCommon( $c['WoocommerceProductFeedsTermDepthRepository'] );

	return $woocommerce_gpf_common;
};

$woocommerce_gpf_di['WoocommerceGpfFeedBing'] = function ( $c ) {
	return new WoocommerceGpfFeedBing(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
	);
};

$woocommerce_gpf_di['WoocommerceGpfFeedGoogle'] = function ( $c ) {
	return new WoocommerceGpfFeedGoogle(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
	);
};

$woocommerce_gpf_di['WoocommerceGpfFeedGoogleInventory'] = function ( $c ) {
	return new WoocommerceGpfFeedGoogleInventory(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
	);
};

$woocommerce_gpf_di['WoocommerceGpfFeedGoogleLocalProductInventory'] = function ( $c ) {
	return new WoocommerceGpfFeedGoogleLocalProductInventory(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
	);
};

$woocommerce_gpf_di['WoocommerceGpfFeedGoogleLocalProducts'] = function ( $c ) {
	return new WoocommerceGpfFeedGoogleLocalProducts(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsFeedConfigFactory'] = function ( $c ) {
	return new WoocommerceProductFeedsFeedConfigFactory(
		$c['WoocommerceProductFeedsFeedConfigRepository'],
		$c['WoocommerceGpfCommon']
	);
};

$woocommerce_gpf_di['WoocommerceGpfFrontend'] = function ( $c ) {
	global $woocommerce_gpf_frontend;
	$woocommerce_gpf_frontend = new WoocommerceGpfFrontend(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfDebugService'],
		$c['WoocommerceProductFeedsFeedItemFactory'],
		$c
	);

	return $woocommerce_gpf_frontend;
};

$woocommerce_gpf_di['WoocommerceGpfImportExportIntegration'] = function ( $c ) {
	global $woocommerce_gpf_import_export;
	$woocommerce_gpf_import_export = new WoocommerceGpfImportExportIntegration( $c['WoocommerceGpfCommon'] );

	return $woocommerce_gpf_import_export;
};

$woocommerce_gpf_di['WoocommerceGpfClearAllJob'] = function ( $c ) {
	return new WoocommerceGpfClearAllJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsFeedItemFactory'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfClearProductJob'] = function ( $c ) {
	return new WoocommerceGpfClearProductJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsFeedItemFactory'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfRebuildSimpleJob'] = function ( $c ) {
	return new WoocommerceGpfRebuildSimpleJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsFeedItemFactory'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfRebuildComplexJob'] = function ( $c ) {
	return new WoocommerceGpfRebuildComplexJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsFeedItemFactory'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfRebuildProductJob'] = function ( $c ) {
	return new WoocommerceGpfRebuildProductJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsFeedItemFactory'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfRestApi'] = function ( $c ) {
	global $woocommerce_gpf_rest_api;
	$woocommerce_gpf_rest_api = new WoocommerceGpfRestApi( $c['WoocommerceGpfCommon'] );

	return $woocommerce_gpf_rest_api;
};

$woocommerce_gpf_di['WoocommerceGpfStatusReport'] = function ( $c ) {
	global $woocommerce_gpf_status_report;
	$woocommerce_gpf_status_report = new WoocommerceGpfStatusReport(
		$c['WoocommerceGpfTemplateLoader'],
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceProductFeedsFeedConfigRepository']
	);

	return $woocommerce_gpf_status_report;
};

$woocommerce_gpf_di['WoocommerceGpfStructuredData'] = function ( $c ) {
	global $woocommerce_gpf_structured_data;
	$woocommerce_gpf_structured_data = new WoocommerceGpfStructuredData(
		$c['WoocommerceProductFeedsFeedItemFactory']
	);

	return $woocommerce_gpf_structured_data;
};

$woocommerce_gpf_di['WoocommerceProductFeedsExpandedStructuredData'] = function ( $c ) {
	return new WoocommerceProductFeedsExpandedStructuredData(
		$c['WoocommerceProductFeedsFeedItemFactory']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsFeedItemFactory'] = function ( $c ) {
	return new WoocommerceProductFeedsFeedItemFactory(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService'],
		$c['WoocommerceProductFeedsTermDepthRepository']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsExpandedStructuredDataCacheInvalidator'] = function ( $c ) {
	return new WoocommerceProductFeedsExpandedStructuredDataCacheInvalidator();
};

$woocommerce_gpf_di['WoocommerceGpfTemplateLoader'] = function ( $c ) {
	return new WoocommerceGpfTemplateLoader();
};

$woocommerce_gpf_di['WoocommerceGpfYoastWoocommerceSeo'] = function ( $c ) {
	return new WoocommerceGpfYoastWoocommerceSeo();
};

$woocommerce_gpf_di['WoocommercePrfAdmin'] = function ( $c ) {
	global $woocommerce_prf_admin;
	$woocommerce_prf_admin = new WoocommercePrfAdmin( $c['WoocommerceGpfTemplateLoader'] );

	return $woocommerce_prf_admin;
};

$woocommerce_gpf_di['WoocommercePrfGoogle'] = function ( $c ) {
	return new WoocommercePrfGoogle(
		$c['WoocommerceGpfTemplateLoader'],
		$c['WoocommerceGpfDebugService']
	);
};

$woocommerce_gpf_di['WoocommercePrfGoogleReviewFeed'] = function ( $c ) {
	global $woocommerce_prf_feed;
	$woocommerce_prf_feed = new WoocommercePrfGoogleReviewFeed(
		$c['WoocommerceGpfCache'],
		$c['WoocommercePrfGoogle'],
		$c['WoocommercePrfGoogleReviewProductInfo']
	);

	return $woocommerce_prf_feed;
};

$woocommerce_gpf_di['WoocommercePrfGoogleReviewProductInfo'] = function ( $c ) {
	return new WoocommercePrfGoogleReviewProductInfo(
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsFeedItemFactory']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsFacebookForWoocommerce'] = function ( $c ) {
	return new WoocommerceProductFeedsFacebookForWoocommerce();
};

$woocommerce_gpf_di['WoocommerceProductFeedsIntegrationManager'] = function ( $c ) {
	return new WoocommerceProductFeedsIntegrationManager( $c );
};

$woocommerce_gpf_di['WoocommerceProductFeedsMain'] = function ( $c ) {
	return new WoocommerceProductFeedsMain(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsIntegrationManager'],
		$c['WoocommerceProductFeedsFeedConfigFactory'],
		$c['WoocommerceProductFeedsJobManager'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsTermDepthRepository'] = function ( $c ) {
	return new WoocommerceProductFeedsTermDepthRepository();
};

$woocommerce_gpf_di['WoocommerceProductFeedsWoocommerceAdminIntegration'] = function ( $c ) {
	return new WoocommerceProductFeedsWoocommerceAdminIntegration();
};

/**
 * Jobs
 */

$woocommerce_gpf_di['WoocommerceProductFeedsJobManager'] = function ( $c ) {
	return new WoocommerceProductFeedsJobManager( $c );
};

$woocommerce_gpf_di['WoocommerceProductFeedsRefreshGoogleTaxonomyJob'] = function ( $c ) {
	return new WoocommerceProductFeedsRefreshGoogleTaxonomyJob();
};

$woocommerce_gpf_di['WoocommerceProductFeedsClearGoogleTaxonomyJob'] = function ( $c ) {
	return new WoocommerceProductFeedsClearGoogleTaxonomyJob();
};

/**
 * Integrations
 */
$woocommerce_gpf_di['WoocommerceGpfProductBrandsForWooCommerce']                = function ( $c ) {
	return new WoocommerceGpfProductBrandsForWooCommerce();
};
$woocommerce_gpf_di['WoocommerceCostOfGoods']                                   = function ( $c ) {
	global $woocommerce_gpf_cost_of_goods;
	$woocommerce_gpf_cost_of_goods = new WoocommerceCostOfGoods();

	return $woocommerce_gpf_cost_of_goods;
};
$woocommerce_gpf_di['WoocommerceGpfMulticurrency']                              = function ( $c ) {
	global $woocommerce_gpf_multicurrency;
	$woocommerce_gpf_multicurrency = new WoocommerceGpfMulticurrency(
		$c['WoocommerceProductFeedsFeedConfigFactory'],
		$c['WoocommerceGpfTemplateLoader']
	);

	return $woocommerce_gpf_multicurrency;
};
$woocommerce_gpf_di['WoocommerceMinMaxQuantities']                              = function ( $c ) {
	global $woocommerce_gpf_min_max_quantities;
	$woocommerce_gpf_min_max_quantities = new WoocommerceMinMaxQuantities();

	return $woocommerce_gpf_min_max_quantities;
};
$woocommerce_gpf_di['WoocommerceProductVendors']                                = function ( $c ) {
	global $woocommerce_gpf_product_vendors;
	$woocommerce_gpf_product_vendors = new WoocommerceProductVendors();

	return $woocommerce_gpf_product_vendors;
};
$woocommerce_gpf_di['WoocommerceGpfTheContentProtection']                       = function ( $c ) {
	global $woocommerce_gpf_the_content_protection;
	$woocommerce_gpf_the_content_protection = new WoocommerceGpfTheContentProtection();

	return $woocommerce_gpf_the_content_protection;
};
$woocommerce_gpf_di['WoocommerceGpfWoocommerceMixAndMatchProducts']             = function ( $c ) {
	return new WoocommerceGpfWoocommerceMixAndMatchProducts();
};
$woocommerce_gpf_di['WoocommerceGpfPriceByCountry']                             = function ( $c ) {
	return new WoocommerceGpfPriceByCountry();
};
$woocommerce_gpf_di['WoocommerceGpfCurrencySwitcherForWooCommerce']             = function ( $c ) {
	return new WoocommerceGpfCurrencySwitcherForWooCommerce(
		$c['WoocommerceProductFeedsFeedConfigFactory'],
		$c['WoocommerceGpfTemplateLoader']
	);
};
$woocommerce_gpf_di['WoocommerceGpfWoocommerceCompositeProducts']               = function ( $c ) {
	return new WoocommerceGpfWoocommerceCompositeProducts();
};
$woocommerce_gpf_di['WoocommerceGpfWoocommerceProductBundles']                  = function ( $c ) {
	return new WoocommerceGpfWoocommerceProductBundles();
};
$woocommerce_gpf_di['WoocommerceGpfWoocommerceMinMaxQuantityStepControlSingle'] = function ( $c ) {
	return new WoocommerceGpfWoocommerceMinMaxQuantityStepControlSingle();
};
$woocommerce_gpf_di['WoocommerceGpfWoocommerceMultilingual']                    = function ( $c ) {
	return new WoocommerceGpfWoocommerceMultilingual();
};
$woocommerce_gpf_di['WoocommerceGpfPwBulkEdit']                                 = function ( $c ) {
	return new WoocommerceGpfPwBulkEdit( $c['WoocommerceGpfCommon'] );
};
$woocommerce_gpf_di['WoocommerceProductFeedsAdvancedCustomFieldsFormatter']     = function ( $c ) {
	return new WoocommerceProductFeedsAdvancedCustomFieldsFormatter();
};
$woocommerce_gpf_di['WoocommerceProductFeedsAdvancedCustomFields']              = function ( $c ) {
	return new WoocommerceProductFeedsAdvancedCustomFields(
		$c['WoocommerceProductFeedsAdvancedCustomFieldsFormatter']
	);
};
$woocommerce_gpf_di['WoocommerceProductFeedsWoocommerceGermanized']             = function ( $c ) {
	return new WoocommerceProductFeedsWoocommerceGermanized();
};
