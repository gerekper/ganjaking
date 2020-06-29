<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// WC_Admin_Report is not autoloaded
include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

class WC_SRE_Report_Manager extends WC_Admin_Report {

	/**
	 * The constructor creates a WC_Admin_Reports object sets the start and end date
	 *
	 * @param WC_SRE_Date_Range $date_range
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $date_range ) {
		$this->start_date = (int) $date_range->get_start_date()->format( 'U' );
		$this->end_date   = (int) $date_range->get_end_date()->format( 'U' );
	}

}