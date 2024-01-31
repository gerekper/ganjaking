<?php
/**
 * REST API Reports Stats Query
 * Handles requests to the '/reports/composites/stats' endpoint.
 *
 * @package  Woo Composite Products
 * @since    8.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\Query as ReportsQuery;

/**
 * WC_CP_Analytics_Revenue_Stats_Query class.
 *
 * @version 8.3.0
 */
class WC_CP_Analytics_Revenue_Stats_Query extends ReportsQuery {

	/**
	 * Valid fields for Composites report.
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
		$args = apply_filters( 'woocommerce_analytics_composites_stats_query_args', $this->get_query_vars() );

		$data_store = WC_Data_Store::load( 'report-composites-revenue-stats' );
		$results    = $data_store->get_data( $args );
		return apply_filters( 'woocommerce_analytics_composites_stats_select_query', $results, $args );
	}
}
