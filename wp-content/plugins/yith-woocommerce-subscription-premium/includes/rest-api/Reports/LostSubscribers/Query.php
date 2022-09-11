<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Query class
 *
 * @class   \YITH\Subscription\RestApi\Reports\LostSubscribers\Query
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */


namespace YITH\Subscription\RestApi\Reports\LostSubscribers;

defined( 'ABSPATH' ) || exit;
use \Automattic\WooCommerce\Admin\API\Reports\Query as ReportsQuery;

/**
 * Class Query
 */
class Query extends ReportsQuery {

	const REPORT_NAME = 'yith-ywsbs-report-lost-subscribers';

	/**
	 * Valid fields for Customers report.
	 *
	 * @return array
	 */
	protected function get_default_query_vars() {
		return array();
	}

	/**
	 * Get categories data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args    = apply_filters( 'yith_ywsbs_reports_lost_subscribers_query_args', $this->get_query_vars() );
		$results = \WC_Data_Store::load( self::REPORT_NAME )->get_data( $args );
		return apply_filters( 'yith_ywsbs_reports_lost_subscribers_select_query', $results, $args );
	}
}
