<?php

namespace WPMailSMTP\Pro\Emails\Logs\Attachments;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use WPMailSMTP\Uploads;

/**
 * Email Log Attachments class.
 *
 * @since 2.9.0
 */
class Attachments {

	/**
	 * The base name of the DB table for the attachment files, without the DB prefix.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	const BASE_ATTACHMENT_FILES_DB_NAME = 'wpmailsmtp_attachment_files';

	/**
	 * The base name of the DB table for the email attachments (connecting email logs with attachment files),
	 * without the DB prefix.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	const BASE_EMAIL_ATTACHMENTS_DB_NAME = 'wpmailsmtp_email_attachments';

	/**
	 * Get all attachments for provided email log.
	 *
	 * @since 2.9.0
	 *
	 * @param int $email_log_id The Email Log ID.
	 *
	 * @return array
	 */
	public function get_attachments( $email_log_id ) {

		global $wpdb;

		if ( ! $this->is_valid_db() ) {
			return [];
		}

		$email_attachments_db_table = self::get_email_attachments_table_name();
		$attachment_files_db_table  = self::get_attachment_files_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		$attachments_data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT attachment_files.id AS id, attachment_files.folder AS folder, attachment_files.filename AS filename, email_attachments.filename AS original_filename
					FROM {$email_attachments_db_table} email_attachments
					INNER JOIN {$attachment_files_db_table} attachment_files ON email_attachments.attachment_id = attachment_files.id
					WHERE email_attachments.email_log_id = %d;",
				$email_log_id
			),
			ARRAY_A
		);
		// phpcs:enable

		if ( empty( $attachments_data ) ) {
			return [];
		}

		$attachments = [];

		foreach ( $attachments_data as $attachment_args ) {
			$attachments[] = new Attachment( $attachment_args );
		}

		return $attachments;
	}

	/**
	 * Delete attachments connected to the provided email log id list.
	 * And also delete any attachment files from FS and DB if they are no longer connected to at least one email log.
	 *
	 * @since 2.9.0
	 *
	 * @param string $email_log_ids A string of Email Log IDs to be deleted.
	 */
	public function delete_attachments( $email_log_ids ) {

		global $wpdb;

		if ( ! $this->is_valid_db() ) {
			return;
		}

		$log_ids_array = explode( ',', (string) $email_log_ids );
		$log_ids_array = array_map( 'intval', $log_ids_array );
		$placeholders  = implode( ', ', array_fill( 0, count( $log_ids_array ), '%d' ) );

		$email_attachments_db_table = self::get_email_attachments_table_name();

		// Delete the DB rows of attachments connected to the provided email log ids list.
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		$number_deleted_rows = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$email_attachments_db_table} WHERE email_log_id IN ( {$placeholders} )",
				$log_ids_array
			)
		);
		// phpcs:enable

		if ( ! empty( $number_deleted_rows ) ) {
			$this->delete_unconnected_attachments();
		}
	}

	/**
	 * Process the attachments of the sending email.
	 *
	 * @since 2.9.0
	 *
	 * @param int   $email_log_id The Email Log ID.
	 * @param array $attachments  The array of attachments and their data (the attachment file path is in index 0).
	 *
	 * @return void|false
	 */
	public function process_attachments( $email_log_id, $attachments ) {

		if ( empty( $email_log_id ) || empty( $attachments ) ) {
			return false;
		}

		if ( ! wp_mail_smtp()->pro->get_logs()->is_enabled_save_attachments() || ! $this->is_valid_db() ) {
			return false;
		}

		foreach ( $attachments as $attachment_item ) {

			if ( empty( $attachment_item[0] ) ) {
				continue;
			}

			$attachment = new Attachment();
			$attachment->add( $attachment_item[0], $email_log_id, $attachment_item[2], $attachment_item[5] );
		}
	}

	/**
	 * Delete all attachments from the DB and FS.
	 *
	 * @since 2.9.0
	 */
	public function delete_all_attachments() {

		global $wpdb;

		// Truncate attachment DB tables.
		if ( $this->is_valid_db() ) {
			$attachment_files_table  = self::get_attachment_files_table_name();
			$email_attachments_table = self::get_email_attachments_table_name();

			$wpdb->query( "TRUNCATE TABLE `$attachment_files_table`;" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "TRUNCATE TABLE `$email_attachments_table`;" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		// Remove all attachment files from the FS.
		$this->remove_all_attachment_files();
	}

	/**
	 * Whether the attachments DB tables exist.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_valid_db() {

		global $wpdb;

		$attachment_files_table  = self::get_attachment_files_table_name();
		$email_attachments_table = self::get_email_attachments_table_name();

		$files_exists  = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s;', $attachment_files_table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$attach_exists = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s;', $email_attachments_table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching

		return $files_exists && $attach_exists;
	}

	/**
	 * Get the root uploads directory.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public static function get_root_uploads_directory() {

		$upload_directory = Uploads::upload_dir();

		return trailingslashit( trailingslashit( $upload_directory['path'] ) . 'attachments' );
	}

	/**
	 * Get the root uploads URL.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public static function get_root_uploads_url() {

		$upload_directory = Uploads::upload_dir();

		return trailingslashit( trailingslashit( $upload_directory['url'] ) . 'attachments' );
	}

	/**
	 * Get the attachment files DB table name.
	 *
	 * @since 2.9.0
	 *
	 * @return string Attachment files DB table name, prefixed.
	 */
	public static function get_attachment_files_table_name() {

		global $wpdb;

		return $wpdb->prefix . self::BASE_ATTACHMENT_FILES_DB_NAME;
	}

	/**
	 * Get the email attachments (connecting email logs with attachment files) DB table name.
	 *
	 * @since 2.9.0
	 *
	 * @return string Email attachments DB table name, prefixed.
	 */
	public static function get_email_attachments_table_name() {

		global $wpdb;

		return $wpdb->prefix . self::BASE_EMAIL_ATTACHMENTS_DB_NAME;
	}

	/**
	 * Delete any attachment files from FS and DB if they are no longer connected to at least one email log.
	 *
	 * @since 2.9.0
	 */
	private function delete_unconnected_attachments() {

		global $wpdb;

		$email_attachments_db_table = self::get_email_attachments_table_name();
		$attachment_files_db_table  = self::get_attachment_files_table_name();

		// Get the list of attachment files without connected email logs.
		$query = "SELECT attachment_files.id, attachment_files.folder, attachment_files.filename
		 			FROM {$attachment_files_db_table} attachment_files
					LEFT JOIN {$email_attachments_db_table} email_attachments ON email_attachments.attachment_id = attachment_files.id
					WHERE email_attachments.attachment_id IS NULL";

		$attachments = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $attachments ) ) {
			return;
		}

		// Delete attachment files from the FS and DB, without connected email logs.
		foreach ( $attachments as $attachment_args ) {
			$attachment = new Attachment( $attachment_args );
			$file       = $attachment->get_path();
			$deleted    = false;

			if ( file_exists( $file ) ) {
				$deleted = unlink( $file );
			}

			if ( $deleted ) {
				rmdir( dirname( $file ) );

				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query(
					$wpdb->prepare( "DELETE FROM {$attachment_files_db_table} WHERE id = %d", $attachment->get_id() )
				);
				// phpcs:enable
			}
		}
	}

	/**
	 * Remove all attachment files and folders in our attachments folder.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	private function remove_all_attachment_files() {

		$attachments_directory = self::get_root_uploads_directory();

		if ( false === file_exists( $attachments_directory ) ) {
			return false;
		}

		/** Array of file objects. @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $attachments_directory, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ( $files as $fileinfo ) {
			if ( $fileinfo->isDir() ) {
				rmdir( $fileinfo->getRealPath() );
			} else {
				unlink( $fileinfo->getRealPath() );
			}
		}

		return rmdir( $attachments_directory );
	}
}
