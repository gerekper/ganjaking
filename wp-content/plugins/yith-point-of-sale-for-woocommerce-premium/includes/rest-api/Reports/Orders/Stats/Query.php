<?php

namespace YITH\POS\RestApi\Reports\Orders\Stats;

defined( 'ABSPATH' ) || exit;

class Query extends \Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\Query {

	/**
	 * Get revenue data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args = apply_filters( 'woocommerce_reports_orders_stats_query_args', $this->get_query_vars() );

		$data_store = \WC_Data_Store::load( 'yith-pos-report-orders-stats' );
		$results    = $data_store->get_data( $args );
		return apply_filters( 'woocommerce_reports_orders_stats_select_query', $results, $args );
	}
}
