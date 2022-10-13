<?php

namespace WPMailSMTP\Pro;

use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachments;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Migration as AttachmentsMigration;
use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\Pro\Emails\Logs\Migration as EmailLogMigration;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Migration as TrackingMigration;
use WPMailSMTP\DBRepair as LiteDBRepair;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Tracking;

/**
 * Class DBRepair to fix the DB related issues.
 *
 * @since 3.6.0
 */
class DBRepair extends LiteDBRepair {

	/**
	 * Update the Migration option to fix the missing table.
	 *
	 * @since 3.6.0
	 *
	 * @param string $missing_table The name of the table.
	 */
	protected function fix_missing_db_table( $missing_table ) {

		parent::fix_missing_db_table( $missing_table );

		if ( $missing_table === Logs::get_table_name() ) {
			update_option( EmailLogMigration::OPTION_NAME, 0 );
		} elseif ( $missing_table === Tracking::get_events_table_name() ) {
			update_option( TrackingMigration::OPTION_NAME, 0 );
		} elseif ( $missing_table === Tracking::get_links_table_name() ) {
			update_option( TrackingMigration::OPTION_NAME, 0 );
		} elseif ( $missing_table === Attachments::get_attachment_files_table_name() ) {
			update_option( AttachmentsMigration::OPTION_NAME, 0 );
		} elseif ( $missing_table === Attachments::get_email_attachments_table_name() ) {
			update_option( AttachmentsMigration::OPTION_NAME, 0 );
		}
	}

	/**
	 * Get the error message (Reason) if the table is missing.
	 *
	 * @since 3.6.0
	 *
	 * @param string $missing_table The table name that we are checking.
	 * @param array  $reasons       The array that holds all the error messages or reason.
	 */
	protected function get_error_message_for_missing_table( $missing_table, &$reasons ) {

		parent::get_error_message_for_missing_table( $missing_table, $reasons );

		$reason = '';

		if ( $missing_table === Logs::get_table_name() ) {
			$reason .= $this->get_reason_output_message(
				$missing_table,
				get_option( EmailLogMigration::ERROR_OPTION_NAME, $this->get_missing_table_default_error_message() )
			);
		} elseif (
			$missing_table === Tracking::get_events_table_name() ||
			$missing_table === Tracking::get_links_table_name()
		) {
			$reason .= $this->get_reason_output_message(
				$missing_table,
				get_option( TrackingMigration::ERROR_OPTION_NAME, $this->get_missing_table_default_error_message() )
			);
		} elseif (
			$missing_table === Attachments::get_attachment_files_table_name() ||
			$missing_table === Attachments::get_email_attachments_table_name()
		) {
			$reason .= $this->get_reason_output_message(
				$missing_table,
				get_option( AttachmentsMigration::ERROR_OPTION_NAME, $this->get_missing_table_default_error_message() )
			);
		}

		$reasons[] = $reason;
	}
}
