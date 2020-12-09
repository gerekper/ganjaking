<?php

use Pimple\Container;

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
		$c['WoocommerceProductFeedsFeedImageManager']
	);

	return $woocommerce_gpf_admin;
};

$woocommerce_gpf_di['WoocommerceProductFeedsFeedImageManager'] = function ( $c ) {
	return new WoocommerceProductFeedsFeedImageManager(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService'],
		$c['WoocommerceGpfTemplateLoader']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsDbManager'] = function ( $c ) {
	return new WoocommerceProductFeedsDbManager( $c['WoocommerceGpfCache'] );
};

$woocommerce_gpf_di['WoocommerceGpfCache'] = function ( $c ) {
	return new WoocommerceGpfCache( $c );
};

$woocommerce_gpf_di['WoocommerceGpfDebugService'] = function ( $c ) {
	return new WoocommerceGpfDebugService();
};

$woocommerce_gpf_di['WoocommerceGpfCacheStatus'] = function ( $c ) {
	return new WoocommerceGpfCacheStatus(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfTemplateLoader']
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

$woocommerce_gpf_di['WoocommerceGpfFrontend'] = function ( $c ) {
	global $woocommerce_gpf_frontend;
	$woocommerce_gpf_frontend = new WoocommerceGpfFrontend(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfDebugService'],
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
		$c['WoocommerceGpfDebugService'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfClearProductJob'] = function ( $c ) {
	return new WoocommerceGpfClearProductJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfDebugService'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfRebuildSimpleJob'] = function ( $c ) {
	return new WoocommerceGpfRebuildSimpleJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfDebugService'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfRebuildComplexJob'] = function ( $c ) {
	return new WoocommerceGpfRebuildComplexJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfDebugService'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceGpfRebuildProductJob'] = function ( $c ) {
	return new WoocommerceGpfRebuildProductJob(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceGpfDebugService'],
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
	$woocommerce_gpf_status_report = new WoocommerceGpfStatusReport( $c['WoocommerceGpfTemplateLoader'], $c['WoocommerceGpfCommon'] );

	return $woocommerce_gpf_status_report;
};

$woocommerce_gpf_di['WoocommerceGpfStructuredData'] = function ( $c ) {
	global $woocommerce_gpf_structured_data;
	$woocommerce_gpf_structured_data = new WoocommerceGpfStructuredData(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
	);

	return $woocommerce_gpf_structured_data;
};

$woocommerce_gpf_di['WoocommerceProductFeedsExpandedStructuredData'] = function ( $c ) {
	return new WoocommerceProductFeedsExpandedStructuredData(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
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
	return new WoocommercePrfGoogle( $c['WoocommerceGpfTemplateLoader'] );
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
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfDebugService']
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsIntegrationManager'] = function ( $c ) {
	return new WoocommerceProductFeedsIntegrationManager( $c );
};

$woocommerce_gpf_di['WoocommerceProductFeedsMain'] = function ( $c ) {
	return new WoocommerceProductFeedsMain(
		$c['WoocommerceGpfCommon'],
		$c['WoocommerceGpfCache'],
		$c['WoocommerceProductFeedsIntegrationManager'],
		$c
	);
};

$woocommerce_gpf_di['WoocommerceProductFeedsTermDepthRepository'] = function ( $c ) {
	return new WoocommerceProductFeedsTermDepthRepository();
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
	$woocommerce_gpf_multicurrency = new WoocommerceGpfMulticurrency();

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
	return new WoocommerceGpfCurrencySwitcherForWooCommerce();
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
