<?php

namespace WPMailSMTP\Pro\Emails\Logs\Attachments;

use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\WP;

/**
 * Email Logs Attachments Cleanup Class.
 *
 * @since 3.8.0
 */
class Cleanup {

	/**
	 * Default cleanup attachment files batch size.
	 *
	 * @var int
	 *
	 * @since 3.8.0
	 */
	const CLEANUP_ATTACHMENT_FILES_BATCH_SIZE = 50;

	/**
	 * Cleanup orphaned attachments data in Email Attachments and
	 * Attachment Files DB tables. As well as the physical files.
	 *
	 * @since 3.8.0
	 *
	 * @return bool Returns `true` if all orphaned attachments data and physical files are deleted data are removed or
	 *              if attachments DB is not valid.
	 */
	public function cleanup_attachments() {

		if ( ! ( new Attachments() )->is_valid_db() ) {
			return true;
		}

		$wpdb = WP::wpdb();

		// Delete orphaned email attachments.
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		$delete_email_attachment_query = $wpdb->prepare(
			'DELETE email_attachments
			FROM `%1$s` email_attachments
				LEFT JOIN `%2$s` email_logs
					ON email_logs.id = email_attachments.email_log_id
			WHERE email_logs.id IS NULL',
			Attachments::get_email_attachments_table_name(),
			Logs::get_table_name()
		);
		// phpcs:enable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( $delete_email_attachment_query );

		return $this->cleanup_attachment_files();
	}

	/**
	 * Cleanup all orphaned attachment files. Both the DB records and physical files.
	 *
	 * @since 3.8.0
	 *
	 * @return bool Returns `true` if all orphaned attachments files are deleted data are removed or
	 *              if attachments DB is not valid.
	 */
	private function cleanup_attachment_files() {

		if ( ! ( new Attachments() )->is_valid_db() ) {
			return true;
		}

		$wpdb                   = WP::wpdb();
		$attachment_files_table = Attachments::get_attachment_files_table_name();

		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		$get_attachments_query = $wpdb->prepare(
			'SELECT attachment_files.id, attachment_files.folder, attachment_files.filename
			FROM `%1$s` attachment_files
				LEFT JOIN `%2$s` email_attachments
					ON email_attachments.attachment_id = attachment_files.id
			WHERE email_attachments.attachment_id IS NULL
			LIMIT %3$d',
			$attachment_files_table,
			Attachments::get_email_attachments_table_name(),
			$this->get_batch_size()
		);
		// phpcs:enable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$attachments_results = $wpdb->get_results( $get_attachments_query, ARRAY_A );

		if ( empty( $attachments_results ) ) {
			return true;
		}

		foreach ( $attachments_results as $att ) {
			$attachment              = new Attachment( $att );
			$file                    = $attachment->get_path();
			$should_delete_db_record = false;

			// If the file no longer exist, we can safely delete it's DB record.
			if ( ! file_exists( $file ) ) {
				$should_delete_db_record = true;
			} elseif ( unlink( $file ) ) {
				// Delete the file folder.
				rmdir( dirname( $file ) );

				// If the file exist, try to delete it.
				// If we successfully delete it, we can remove the DB record.
				$should_delete_db_record = true;
			}

			if ( $should_delete_db_record ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$wpdb->query(
					$wpdb->prepare(
						'DELETE FROM `%1$s` WHERE id = %2$d',
						$attachment_files_table,
						$attachment->get_id()
					)
				);
				// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			}
		}

		return false;
	}

	/**
	 * Returns the number of attachment files to cleanup per batch.
	 *
	 * @since 3.8.0
	 *
	 * @return int
	 */
	private function get_batch_size() {

		/**
		 * Filter the number of attachment files to cleanup per batch.
		 *
		 * @since 3.8.0
		 *
		 * @param int $batch_size Number of attachment files to cleanup per batch.
		 */
		$batch_size = absint( apply_filters( 'wp_mail_smtp_pro_emails_logs_attachments_cleanup_get_batch_size',  self::CLEANUP_ATTACHMENT_FILES_BATCH_SIZE ) );

		return empty( $batch_size ) ? self::CLEANUP_ATTACHMENT_FILES_BATCH_SIZE : $batch_size;
	}
}
