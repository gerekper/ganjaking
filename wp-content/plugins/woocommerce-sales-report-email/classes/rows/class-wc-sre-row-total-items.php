<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Row_Total_Items extends WC_SRE_Report_Row {

	/**
	 * The constructor
	 *
	 * @param $date_range
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $date_range ) {
		parent::__construct( $date_range, 'total-items', __( 'Total Items', 'woocommerce-sales-report-email' ) );
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
		$total_items = absint( $report_manager->get_order_report_data( array(
			'data'         => array(
				'_qty' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_qty'
				)
			),
			'query_type'   => 'get_var',
			'order_types'  => $order_types,
			'filter_range' => true,
		) ) );

		$this->set_value( $total_items );
	}

}