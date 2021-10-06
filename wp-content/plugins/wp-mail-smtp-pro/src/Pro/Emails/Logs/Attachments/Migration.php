<?php

namespace WPMailSMTP\Pro\Emails\Logs\Attachments;

use WPMailSMTP\MigrationAbstract;

/**
 * Email Log Attachment Migration Class
 *
 * @since 2.9.0
 * @since 3.0.0 Extends MigrationAbstract.
 */
class Migration extends MigrationAbstract {

	/**
	 * Version of the attachment database tables for the Email Logs functionality.
	 *
	 * @since 2.9.0
	 */
	const DB_VERSION = 2;

	/**
	 * Option key where we save the current attachments DB version for the Email Logs functionality.
	 *
	 * @since 2.9.0
	 */
	const OPTION_NAME = 'wp_mail_smtp_logs_attachments_db_version';

	/**
	 * Option key where we save any errors while creating the Email Log attachment DB tables.
	 *
	 * @since 2.9.0
	 */
	const ERROR_OPTION_NAME = 'wp_mail_smtp_logs_attachments_db_error';

	/**
	 * Whether migration is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_enabled() {

		return wp_mail_smtp()->get_pro()->get_logs()->is_enabled_save_attachments();
	}

	/**
	 * Create the attachment files DB table structure.
	 *
	 * @since 2.9.0
	 */
	protected function migrate_to_1() {

		global $wpdb;

		$table           = Attachments::get_attachment_files_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `$table` (
		    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		    `hash` VARCHAR(128) NOT NULL,
		    `folder` VARCHAR(64) NOT NULL,
		    `filename` TEXT NOT NULL,
		    PRIMARY KEY (id),
		    INDEX hash (hash)
		)
		ENGINE='InnoDB'
		{$charset_collate};";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		if ( ! empty( $wpdb->last_error ) ) {
			update_option( self::ERROR_OPTION_NAME, $wpdb->last_error, false );
		}

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 1 );
		}
	}

	/**
	 * Create the email attachments DB table structure.
	 *
	 * @since 2.9.0
	 */
	protected function migrate_to_2() {

		$this->maybe_required_older_migrations( 2 );

		global $wpdb;

		$table           = Attachments::get_email_attachments_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `$table` (
		    `email_log_id` INT UNSIGNED NOT NULL,
		    `attachment_id` INT UNSIGNED,
		    `filename` TEXT,
		    UNIQUE KEY uq_email_attachments (email_log_id, attachment_id, filename(190)),
		    INDEX email_log (email_log_id)
		)
		ENGINE='InnoDB'
		{$charset_collate};";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		if ( ! empty( $wpdb->last_error ) ) {
			update_option( self::ERROR_OPTION_NAME, $wpdb->last_error, false );
		}

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 2 );
		}
	}
}
