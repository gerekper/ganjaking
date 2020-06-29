<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Row_Total_Orders extends WC_SRE_Report_Row {

	/**
	 * The constructor
	 *
	 * @param $date_range
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $date_range ) {
		parent::__construct( $date_range, 'total-orders', __( 'Total Orders', 'woocommerce-sales-report-email' ) );
	}

	/**
	 * Prepare the data
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function prepare() {

		// Create a Report Manager object
		$report_manager = new WC_SRE_Report_Manager( $this->get_date_range() );

		// Set the default order types
		$order_types = array( 'shop_order' );

		// wc_get_order_types() is a 2.2+ function
		if ( function_exists( 'wc_get_order_types' ) ) {
			$order_types = wc_get_order_types( 'order-count' );
		}

		// Get the total orders count
		$total_orders = absint( $report_manager->get_order_report_data( array(
			'data'         => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders'
				)
			),
			'query_type'   => 'get_var',
			'filter_range' => true,
			'order_types'  => $order_types,
		) ) );

		$this->set_value( $total_orders );
	}

}