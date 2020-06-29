<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Row_Total_Sales extends WC_SRE_Report_Row {

	/**
	 * The constructor
	 *
	 * @param $date_range
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $date_range ) {
		parent::__construct( $date_range, 'total-sales', __( 'Sales in this period', 'woocommerce-sales-report-email' ) );
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

		// Get the total sales
		$total_sales = $report_manager->get_order_report_data( array(
			'data'         => array(
				'_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),
			),
			'query_type'   => 'get_var',
			'filter_range' => true
		) );

		// Set the value
		$this->set_value( wc_price( $total_sales ) );
	}

}