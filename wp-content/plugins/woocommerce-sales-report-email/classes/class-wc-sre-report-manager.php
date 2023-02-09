<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// WC_Admin_Report is not autoloaded
require_once WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php';

/**
 * Report manager class.
 *
 * @deprecated 1.2.0
 */
class WC_SRE_Report_Manager extends WC_Admin_Report {

	/**
	 * The constructor creates a WC_Admin_Reports object sets the start and end date
	 *
	 * @since 1.0.0
	 *
	 * @param WC_SRE_Date_Range $date_range
	 */
	public function __construct( $date_range ) {
		wc_deprecated_function( __FUNCTION__, '1.2.0' );

		$this->start_date = (int) $date_range->get_start_date()->format( 'U' );
		$this->end_date   = (int) $date_range->get_end_date()->format( 'U' );
	}

}
