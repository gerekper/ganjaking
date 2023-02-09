<?php

if ( ! defined( 'ABSPATH' ) ) {
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
		$orders_ids = wc_get_orders(
			array(
				'type'         => 'shop_order',
				'return'       => 'ids',
				'limit'        => -1,
				'status'       => array( 'completed', 'processing', 'on-hold' ),
				'date_created' => $this->get_date_range()->get_start_date()->getTimestamp() . '...' . $this->get_date_range()->get_end_date()->getTimestamp(),
			)
		);

		$this->set_value( count( $orders_ids ) );
	}

}
