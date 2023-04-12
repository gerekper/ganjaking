<?php
/**
 * Plugin Name: WooCommerce Coupon Campaigns & Tracking
 * Version: 1.2.1
 * Plugin URI: https://woocommerce.com/products/woocommerce-coupon-campaigns/.
 * Description: Provides the ability to group coupons into campaigns - also offers better tracking and reporting of coupons as well as a bulk coupon generation tool.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: wc_coupon_campaigns
 * Requires at least: 4.1
 * Tested up to: 6.2
 * WC tested up to: 7.6
 * WC requires at least: 5.0
 * Woo: 506329:0d1018512ffcfcca48a43da05de22647
 *
 * @package woocommerce-coupon-campaigns
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// HPOS compatibility declaration.
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
		}
	}
);

if ( ! function_exists( 'wc_coupon_campaigns_tracking' ) && ! function_exists( 'wc_coupon_campaigns_tracking_reports' ) ) {

	function wc_coupon_campaigns_tracking() {
		require_once 'includes/class-wc-coupon-campaigns.php';
		require_once 'includes/class-wc-coupon-campaigns-privacy.php';
		require_once 'includes/class-wc-coupon-campaign-filter.php';

		global $wc_coupon_campaigns;
		$wc_coupon_campaigns = new WC_Coupon_Campaigns( __FILE__ );

		// Load the coupon campaign filter instance.
		( new WC_Coupon_Campaign_Filter() )->load();
	}

	add_action( 'plugins_loaded', 'wc_coupon_campaigns_tracking', 0 );


	function wc_coupon_campaigns_tracking_reports( $reports ) {

		$reports['coupons'] = array(
			'title'   => __( 'Coupon Campaigns', 'wc_coupon_campaigns' ),
			'reports' => array(
				'campaigns' => array(
					'title'       => __( 'Coupon Campaigns', 'wc_coupon_campaigns' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
				),
			),
		);

		return $reports;
	}
	add_filter( 'woocommerce_admin_reports', 'wc_coupon_campaigns_tracking_reports' );


	function wc_coupon_campaigns_tracking_reports_path( $path, $name, $class ) {

		if ( 'WC_Report_campaigns' === $class ) {
			$dir  = plugin_dir_path( __FILE__ );
			$path = $dir . 'includes/class-wc-report-coupon-campaign.php';
		}

		return $path;
	}
	add_filter( 'wc_admin_reports_path', 'wc_coupon_campaigns_tracking_reports_path', 10, 3 );

}
