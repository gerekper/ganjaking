<?php
/**
 * REST API Reports bundles query
 *
 * Class for parameter-based Bundles Stock Report querying
 *
 * @package  WooCommerce Product Bundles
 * @since    6.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\Query as ReportsQuery;

/**
 * WC_PB_REST_Reports_Stock_Query class.
 *
 * @version 6.9.0
 */
class WC_PB_Analytics_Stock_Query extends ReportsQuery {

	/**
	 * Valid fields for Products report.
	 *
	 * @return array
	 */
	protected function get_default_query_vars() {
		return array();
	}

	/**
	 * Get product data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args = apply_filters( 'woocommerce_analytics_bundles_stock_query_args', $this->get_query_vars() );

		$data_store = WC_Data_Store::load( 'report-bundles-stock' );
		$results    = $data_store->get_data( $args );
		return apply_filters( 'woocommerce_analytics_bundles_stock_select_query', $results, $args );
	}

}
