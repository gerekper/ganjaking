<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Row_Active_Subscribers extends WC_SRE_Report_Row {

	/**
	 * The constructor
	 *
	 * @param $date_range
	 *
	 * @access public
	 * @since  1.1.0
	 */
	public function __construct( $date_range ) {
		parent::__construct( $date_range, 'active-subscribers', __( 'Active Subscribers', 'woocommerce-sales-report-email' ) );
	}

	/**
	 * Prepare the data
	 *
	 * @access public
	 * @since  1.1.0
	 */
	public function prepare() {

		$subscriptions = wcs_get_subscriptions( array( 'subscription_status' => 'active' ) );

		$active_subscription_count = count( $subscriptions );

		$this->set_value( $active_subscription_count );
	}

}
