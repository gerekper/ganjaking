<?php
/**
 * Plugin Name: WooCommerce Google Product Feed
 * Plugin URI: https://woocommerce.com/products/google-product-feed/
 * Description: WooCommerce extension that allows you to more easily populate advanced attributes into the Google Merchant Centre feed
 * Author: Ademti Software Ltd.
 * Version: 10.10.0
 * Woo: 18619:d55b4f852872025741312839f142447e
 * WC requires at least: 7.2
 * WC tested up to: 7.6
 * Requires PHP: 7.4.0
 * Author URI: https://www.ademti-software.co.uk/
 * License: GPLv3
 *
 * @package woocommerce-gpf
 */

defined( 'ABSPATH' ) || exit;

// The current DB schema version.
define( 'WOOCOMMERCE_GPF_DB_VERSION', 16 );

// The current version.
define( 'WOOCOMMERCE_GPF_VERSION', '10.10.0' );

$woocommerce_gpf_dirname = dirname( __FILE__ ) . '/';
require_once $woocommerce_gpf_dirname . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
require_once $woocommerce_gpf_dirname . 'vendor/autoload.php';
require_once $woocommerce_gpf_dirname . 'woocommerce-product-feeds-install.php';
require_once $woocommerce_gpf_dirname . 'src/gpf/woocommerce-gpf-template-functions.php';
require_once $woocommerce_gpf_dirname . 'woocommerce-product-feeds-bootstrap.php';

register_activation_hook( __FILE__, 'woocommerce_gpf_install' );

/**
 * Run the plugin.
 */
global $woocommerce_product_feeds_main;
$woocommerce_product_feeds_main = $woocommerce_gpf_di['WoocommerceProductFeedsMain'];
$woocommerce_product_feeds_main->run();


/**
 * Declare support for High Performance Order Storage.
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
