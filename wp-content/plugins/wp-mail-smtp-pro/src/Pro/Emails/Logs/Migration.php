<?php

namespace WPMailSMTP\Pro\Emails\Logs;

use WPMailSMTP\Pro\Tasks\Migrations\EmailLogMigration4;
use WPMailSMTP\Pro\Tasks\Migrations\EmailLogMigration5;
use WPMailSMTP\Tasks\Tasks;
use WPMailSMTP\WP;

/**
 * Class Migration
 *
 * @since 1.5.0
 */
class Migration {

	/**
	 * Version of the database table(s) for this Logs functionality.
	 *
	 * @since 1.5.0
	 */
	const DB_VERSION = 6;

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
	 * Current version, received from DB wp_options table.
	 *
	 * @since 1.5.0
	 *
	 * @var int
	 */
	protected $cur_ver;

	/**
	 * Migration constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->cur_ver = self::get_cur_version();

		$this->validate_db();
	}

	/**
	 * Static on purpose, to get current DB version without __construct() and validation.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public static function get_cur_version() {

		return (int) get_option( self::OPTION_NAME, 0 );
	}

	/**
	 * Check DB version and update to the latest one.
	 *
	 * @since 1.5.0
	 */
	protected function validate_db() {

		if ( $this->cur_ver < self::DB_VERSION ) {
			$this->run( self::DB_VERSION );
		}
	}

	/**
	 * Update DB version in options table.
	 *
	 * @since 1.5.0
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
	 * @since 1.5.0
	 *
	 * @param int $ver
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
	 * @since 1.5.0
	 *
	 * @param int $ver
	 */
	protected function run( $ver ) {

		$ver = (int) $ver;

		if ( method_exists( $this, 'migrate_to_' . $ver ) ) {
			$this->{'migrate_to_' . $ver}();
		} else {

			$message = sprintf( /* translators: %1$s - WP Mail SMTP, %2$s - error message. */
				esc_html__( 'There was an error while upgrading the database. Please contact %1$s support with this information: %2$s.', 'wp-mail-smtp-pro' ),
				'<strong>WP Mail SMTP</strong>',
				'<code>migration from v' . self::get_cur_version() . ' to v' . self::DB_VERSION . ' failed. Plugin version: v' . WPMS_PLUGIN_VER . '</code>'
			);

			WP::add_admin_notice( $message, WP::ADMIN_NOTICE_ERROR );
		}
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
	private function migrate_to_1() {

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

		$result = $wpdb->query( $sql ); // phpcs:ignore

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
	private function migrate_to_2() {

		$this->maybe_required_older_migrations( 2 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` CHANGE COLUMN `date_sent` `date_sent` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`;";

		$result = $wpdb->query( $sql ); // phpcs:ignore

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
	private function migrate_to_3() {

		$this->maybe_required_older_migrations( 3 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` ENGINE=InnoDB;";

		$result = $wpdb->query( $sql ); // phpcs:ignore

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
	private function migrate_to_4() {

		$this->maybe_required_older_migrations( 4 );

		// Don't process if ActionScheduler is not usable.
		if ( ! Tasks::is_usable() ) {
			return;
		}

		global $wpdb;

		$table = Logs::get_table_name();

		$is_subject_varchar = false;

		// Check if subject column type is already set to varchar(255).
		$table_info = $wpdb->get_results( "DESCRIBE `$table`;", ARRAY_A ); // phpcs:ignore

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

			// Initialize the AS task in 'init' hook, when AS is ready to process tasks.
			add_action(
				'init',
				function() {
					( new EmailLogMigration4() )->async()->register();
				},
				2
			);
		}

		// Save the current version to DB.
		$this->update_db_ver( 4 );
	}

	/**
	 * Change the `subject` DB table column length from 255 to 191.
	 *
	 * @since 2.2.0
	 */
	private function migrate_to_5() {

		$this->maybe_required_older_migrations( 5 );

		// Don't process if ActionScheduler is not usable.
		if ( ! Tasks::is_usable() ) {
			return;
		}

		// Initialize the AS task in 'init' hook, when AS is ready to process tasks.
		add_action(
			'init',
			function() {
				( new EmailLogMigration5() )->async()->register();
			},
			2
		);

		// Save the current version to DB.
		$this->update_db_ver( 5 );
	}

	/**
	 * Add the `error_text` column to the DB table.
	 *
	 * @since 2.5.0
	 */
	private function migrate_to_6() {

		$this->maybe_required_older_migrations( 6 );

		global $wpdb;

		$table = Logs::get_table_name();

		$sql = "ALTER TABLE `$table` ADD `error_text` TEXT NULL AFTER `headers`;";

		$result = $wpdb->query( $sql ); // phpcs:ignore

		// Save the current version to DB.
		if ( $result !== false ) {
			$this->update_db_ver( 6 );
		}
	}
}
