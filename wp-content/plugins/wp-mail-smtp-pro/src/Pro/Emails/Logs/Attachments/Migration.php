<?php

namespace WPMailSMTP\Pro\Emails\Logs\Attachments;

use WPMailSMTP\WP;

/**
 * Email Log Attachment Migration Class
 *
 * @since 2.9.0
 */
class Migration {

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
	 * Current version, received from DB wp_options table.
	 *
	 * @since 2.9.0
	 *
	 * @var int
	 */
	protected $cur_ver;

	/**
	 * Migration constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {

		$this->cur_ver = self::get_current_version();

		$this->validate_db();
	}

	/**
	 * Static on purpose, to get current DB version without __construct() and validation.
	 *
	 * @since 2.9.0
	 *
	 * @return int
	 */
	public static function get_current_version() {

		return (int) get_option( self::OPTION_NAME, 0 );
	}

	/**
	 * Check DB version and update to the latest one.
	 *
	 * @since 2.9.0
	 */
	protected function validate_db() {

		if ( $this->cur_ver < self::DB_VERSION ) {
			$this->run( self::DB_VERSION );
		}
	}

	/**
	 * Update DB version in options table.
	 *
	 * @since 2.9.0
	 *
	 * @param int $ver Version number.
	 */
	protected function update_db_ver( $ver = 0 ) {

		$ver = (int) $ver;

		if ( empty( $ver ) ) {
			$ver = self::DB_VERSION;
		}

		// Autoload it, because this value is checked all the time
		// and no need to request it separately from all autoloaded options.
		update_option( self::OPTION_NAME, $ver, true );
	}

	/**
	 * Prevent running the same migration twice.
	 * Run migration only when required.
	 *
	 * @since 2.9.0
	 *
	 * @param int $ver The current migration version.
	 */
	protected function maybe_required_older_migrations( $ver ) {

		$ver = (int) $ver;

		if ( ( $ver - $this->cur_ver ) > 1 ) {
			$this->run( $ver - 1 );
		}
	}

	/**
	 * Actual migration launcher.
	 *
	 * @since 2.9.0
	 *
	 * @param int $ver The specified migration version to run.
	 */
	protected function run( $ver ) {

		$ver = (int) $ver;

		if ( method_exists( $this, 'migrate_to_' . $ver ) ) {
			$this->{'migrate_to_' . $ver}();
		} else {

			$message = sprintf( /* translators: %1$s - WP Mail SMTP, %2$s - error message. */
				esc_html__( 'There was an error while upgrading the Email Log Attachments database. Please contact %1$s support with this information: %2$s.', 'wp-mail-smtp-pro' ),
				'<strong>WP Mail SMTP</strong>',
				'<code>migration from v' . self::get_current_version() . ' to v' . self::DB_VERSION . ' failed. Plugin version: v' . WPMS_PLUGIN_VER . '</code>'
			);

			WP::add_admin_notice( $message, WP::ADMIN_NOTICE_ERROR );
		}
	}

	/**
	 * Create the attachment files DB table structure.
	 *
	 * @since 2.9.0
	 */
	private function migrate_to_1() {

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
	private function migrate_to_2() {

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
