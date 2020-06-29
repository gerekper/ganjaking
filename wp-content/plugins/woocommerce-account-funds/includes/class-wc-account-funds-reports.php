<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Reports
 */
class WC_Account_Funds_Reports {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_reports_charts', array( $this, 'reports_charts' ) );
	}

	/**
	 * Add charts to WC
	 */
	public function reports_charts( $charts ) {
		$charts['deposits'] = array(
			'title'     => __( 'Deposits', 'woocommerce-account-funds' ),
			'charts'    => array(
				'deposits_by_date' => array(
					'title'       => __( 'Overview', 'woocommerce-account-funds' ),
					'description' => '',
					'hide_title'  => true,
					'function'    => array( $this, 'get_report' )
				)
			)
		);
		return $charts;
	}

	/**
	 * Get the report
	 */
	public function get_report() {
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
		include_once( 'class-wc-account-funds-deposits-by-date.php' );

		$report = new WC_Report_Deposits_By_Date();
		$report->output_report();
	}
}

new WC_Account_Funds_Reports();