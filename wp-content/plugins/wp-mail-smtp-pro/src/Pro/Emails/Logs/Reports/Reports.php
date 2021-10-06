<?php

namespace WPMailSMTP\Pro\Emails\Logs\Reports;

use WPMailSMTP\WP;
use WPMailSMTP\Pro\Emails\Logs\Reports\Emails\Summary as SummaryReportEmail;
use WPMailSMTP\Reports\Reports as ReportsLite;

/**
 * Class Reports. Emails stats reports.
 *
 * @since 3.0.0
 */
class Reports extends ReportsLite {

	/**
	 * Get emails stats weekly summary report.
	 *
	 * @since 3.0.0
	 *
	 * @return Report
	 */
	public function get_summary_report() {

		return new Report(
			[
				'date'    => [
					( new \DateTime( 'now', WP::wp_timezone() ) )->modify( '- 7 days' )->format( 'Y-m-d' ),
					( new \DateTime( 'now', WP::wp_timezone() ) )->modify( '- 1 day' )->format( 'Y-m-d' ),
				],
				'order'   => 'desc',
				'orderby' => 'total',
			]
		);
	}

	/**
	 * Get emails stats weekly summary report email.
	 *
	 * @since 3.0.0
	 *
	 * @return SummaryReportEmail
	 */
	public function get_summary_report_email() {

		return new SummaryReportEmail( $this->get_summary_report() );
	}
}
