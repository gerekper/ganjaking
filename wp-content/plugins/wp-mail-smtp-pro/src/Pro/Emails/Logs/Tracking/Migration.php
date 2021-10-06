<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking;

use WPMailSMTP\MigrationAbstract;

/**
 * Email tracking Migration Class
 *
 * @since 2.9.0
 * @since 3.0.0 Extends MigrationAbstract.
 */
class Migration extends MigrationAbstract {

	/**
	 * Version of the email tracking database tables.
	 *
	 * @since 2.9.0
	 */
	const DB_VERSION = 2;

	/**
	 * Option key where we save the current email tracking DB version.
	 *
	 * @since 2.9.0
	 */
	const OPTION_NAME = 'wp_mail_smtp_email_tracking_db_version';

	/**
	 * Option key where we save any errors while creating the email tracking DB tables.
	 *
	 * @since 2.9.0
	 */
	const ERROR_OPTION_NAME = 'wp_mail_smtp_email_tracking_db_error';

	/**
	 * Whether migration is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_enabled() {

		return wp_mail_smtp()->get_pro()->get_logs()->is_enabled_tracking();
	}

	/**
	 * Create the email tracking events DB table structure.
	 *
	 * @since 2.9.0
	 */
	protected function migrate_to_1() {

		global $wpdb;

		$table           = Tracking::get_events_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `$table` (
		    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		    `email_log_id` INT UNSIGNED NOT NULL,
		    `event_type` VARCHAR(20) NOT NULL,
		    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		    `object_id` INT UNSIGNED,
		    PRIMARY KEY (id),
		    INDEX email_log (email_log_id),
		    INDEX event_type (event_type)
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
	 * Create the email tracking links DB table structure.
	 *
	 * @since 2.9.0
	 */
	protected function migrate_to_2() {

		$this->maybe_required_older_migrations( 2 );

		global $wpdb;

		$table           = Tracking::get_links_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `$table` (
		    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		    `email_log_id` INT UNSIGNED NOT NULL,
		    `url` TEXT,
		    PRIMARY KEY (id),
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
