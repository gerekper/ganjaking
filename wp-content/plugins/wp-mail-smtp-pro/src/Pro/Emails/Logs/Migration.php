<?php

namespace WPMailSMTP\Pro\Emails\Logs;

use WPMailSMTP\MigrationAbstract;
use WPMailSMTP\Pro\Tasks\Migrations\EmailLogMigration4;
use WPMailSMTP\Pro\Tasks\Migrations\EmailLogMigration5;
use WPMailSMTP\Tasks\Tasks;

/**
 * Class Migration
 *
 * @since 1.5.0
 * @since 3.0.0 Extends MigrationAbstract.
 */
class Migration extends MigrationAbstract {

	/**
	 * Version of the database table(s) for this Logs functionality.
	 *
	 * @since 1.5.0
	 */
	const DB_VERSION = 9;

	/**
	 * Option key where we save the current DB version for Logs functionality.
	 *
	 * @since 1.5.0
	 */
	const OPTION_NAME = 'wp_mail_smtp_logs_db_version';

	/**
	 * Option key where we save any errors while creating the Email Log DB table.
	 *
	 * @since 2.2.0
	 */
	const ERROR_OPTION_NAME = 'wp_mail_smtp_logs_error';

	/**
	 * Whether migration is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_enabled() {

		return wp_mail_smtp()->get_pro()->get_logs()->is_enabled();
	}

	/**
	 * Initial migration - create the table structure.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 Changed `date_sent` column type from DATETIME to TIMESTAMP to support MySQL 5.1+ for new clients.
	 * @since 2.1.0 Removed the specific DB table engine MyISAM (will now use the default MySQL engine).
	 * @since 2.1.2 Changed the `subject` type to varchar(255), removed the FULLTEXT indexes, removed `people` index,
	 *              set the Engine to InnoDB and made the `collate` parameter optional for the query.
	 * @since 2.2.0 Added error saving to the WP option, so it can be displayed on the Email Log page.
	 */
	protected function migrate_to_1() {

		global $wpdb;

		$table   = Logs::get_table_name();
		$collate = ! empty( $wpdb->collate ) ? "COLLATE='{$wpdb->collate}'" : '';

		/*
		 * Create the table.
		 */
		$sql = "
		CREATE TABLE `$table` (
		    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		    `subject` VARCHAR(191) NOT NULL,
		    `people` TEXT NOT NULL,
		    `headers` TEXT NOT NULL,
		    `content_plain` LONGTEXT NOT NULL,
		    `content_html` LONGTEXT NOT NULL,
		    `status` TINYINT UNSIGNED NOT NULL DEFAULT '0',
		    `date_sent` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		    `mailer` VARCHAR(255) NOT NULL,
		    `attachments` TINYINT UNSIGNED NOT NULL DEFAULT '0',
		    PRIMARY KEY (id),
		    INDEX subject (subject),
		    INDEX status (status)
		)
		ENGINE='InnoDB'
		{$collate};";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( ! empty( $wpdb->last_error ) ) {
			update_option( self::ERROR_OPTION_NAME, $wpdb->last_error, false );
		}

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 1 );
		}
	}

	/**
	 * Change the `date_sent` column type from DATETIME to TIMESTAMP to support MySQL 5.1+.
	 * Applied to older users, who initially created the table with the DATETIME type.
	 *
	 * @since 1.6.0
	 * @since 1.6.1 Included previous DB migration call for new users on 1.6.0.
	 */
	protected function migrate_to_2() {

		$this->maybe_required_older_migrations( 2 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` CHANGE COLUMN `date_sent` `date_sent` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`;";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 2 );
		}
	}

	/**
	 * Change the DB table engine for existing users to the default InnoDB.
	 *
	 * @since 2.1.0
	 */
	protected function migrate_to_3() {

		$this->maybe_required_older_migrations( 3 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` ENGINE=InnoDB;";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 3 );
		}
	}

	/**
	 * Make some Email Logs DB table changes:
	 * - change the `subject` column type from TEXT to VARCHAR(255),
	 * - change the `subject` index from FULLTEXT to normal index,
	 * - drop the index on `people` column,
	 * - at the end try to switch to the InnoDB engine.
	 *
	 * The check at the beginning (if subject type is `varchar(255)`) is needed, so that this migration can be skipped
	 * for new plugin installs, who should have the correct DB table setup in `migration_to_1`.
	 *
	 * @since 2.1.2
	 */
	protected function migrate_to_4() {

		$this->maybe_required_older_migrations( 4 );

		// Don't process if ActionScheduler is not usable.
		if ( ! Tasks::is_usable() ) {
			return;
		}

		global $wpdb;

		$table = Logs::get_table_name();

		$is_subject_varchar = false;

		// Check if subject column type is already set to varchar(255).
		$table_info = $wpdb->get_results( "DESCRIBE `$table`;", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		foreach ( $table_info as $row ) {
			if (
				( isset( $row['Field'] ) && $row['Field'] === 'subject' ) &&
				( isset( $row['Type'] ) && $row['Type'] === 'varchar(255)' )
			) {
				$is_subject_varchar = true;
				break;
			}
		}

		if ( ! $is_subject_varchar ) {
			( new EmailLogMigration4() )->async()->register();
		}

		// Save the current version to DB.
		$this->update_db_ver( 4 );
	}

	/**
	 * Change the `subject` DB table column length from 255 to 191.
	 *
	 * @since 2.2.0
	 */
	protected function migrate_to_5() {

		$this->maybe_required_older_migrations( 5 );

		// Don't process if ActionScheduler is not usable.
		if ( ! Tasks::is_usable() ) {
			return;
		}

		( new EmailLogMigration5() )->async()->register();

		// Save the current version to DB.
		$this->update_db_ver( 5 );
	}

	/**
	 * Add the `error_text` column to the DB table.
	 *
	 * @since 2.5.0
	 */
	protected function migrate_to_6() {

		$this->maybe_required_older_migrations( 6 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` ADD `error_text` TEXT NULL AFTER `headers`;";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 6 );
		}
	}

	/**
	 * Add the "initiator_name" and "initiator_file" columns to the DB table.
	 *
	 * @since 3.0.0
	 */
	protected function migrate_to_7() {

		$this->maybe_required_older_migrations( 7 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` ADD `initiator_name` VARCHAR(255) NULL AFTER `attachments`, ADD `initiator_file` TEXT NULL;";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 7 );
		}
	}

	/**
	 * Hide CC and BCC columns in email logs table for existing users.
	 *
	 * @since 3.1.0
	 */
	protected function migrate_to_8() {

		$this->maybe_required_older_migrations( 8 );

		global $wpdb;

		$meta_key = 'managewp-mail-smtp_page_wp-mail-smtp-logscolumnshidden';

		$rows = $wpdb->get_results( "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = '{$meta_key}'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		foreach ( $rows as $row ) {
			$value = maybe_unserialize( $row->meta_value );

			if ( empty( $value ) || ! is_array( $value ) ) {
				$value = [];
			}

			$value[] = 'cc';
			$value[] = 'bcc';

			update_user_meta( $row->user_id, $meta_key, array_unique( $value ) );
		}

		// Save the current version to DB.
		$this->update_db_ver( 8 );
	}

	/**
	 * Add the `message_id` column to the DB table.
	 *
	 * @since 3.3.0
	 */
	protected function migrate_to_9() {

		$this->maybe_required_older_migrations( 9 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` ADD `message_id` VARCHAR(255) NULL AFTER `id`;";

		$result = $wpdb->query( $sql ); // phpcs:ignore

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 9 );
		}
	}
}
