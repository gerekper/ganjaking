<?php

namespace WPMailSMTP\Pro\Tasks\Migrations;

use WPMailSMTP\Helpers\DB;
use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\Tasks\Task;

/**
 * Class EmailLogMigration4.
 *
 * An async task for performing the Email Log migration #4.
 *
 * @since 2.1.2
 */
class EmailLogMigration4 extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 2.1.2
	 */
	const ACTION = 'wp_mail_smtp_process_email_logs_migration_4';

	/**
	 * Class constructor.
	 *
	 * @since 2.1.2
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task with all the proper checks.
	 *
	 * @since 2.1.2
	 */
	public function init() {

		// Register the action handler.
		add_action( self::ACTION, array( $this, 'process' ) );
	}

	/**
	 * Process the needed changes for migration #4.
	 * - change the `subject` column type from TEXT to VARCHAR(255),
	 * - change the `subject` index from FULLTEXT to normal index,
	 * - drop the index on `people` column,
	 * - at the end try to switch to the InnoDB engine.
	 *
	 * @since 2.1.2
	 *
	 * @throws \Exception Exception will be logged in the Action Scheduler logs table.
	 */
	public function process() {

		// This migration could take longer for Email Log tables with a lot of entries.
		set_time_limit( 300 );

		global $wpdb;

		$table = Logs::get_table_name();

		$queries = [];

		if ( DB::index_exists( $table, 'subject' ) ) {
			$queries[] = "DROP INDEX `subject` ON `$table`;";
		}

		if ( DB::index_exists( $table, 'people' ) ) {
			$queries[] = "DROP INDEX `people` ON `$table`;";
		}

		$queries[] = "ALTER TABLE `$table` CHANGE COLUMN `subject` `subject` VARCHAR(255) NOT NULL AFTER `id`;";
		$queries[] = "CREATE INDEX `subject` ON `$table` (`subject`);";

		// Try to change the DB table engine again since all restrictions should have been lifted.
		$queries[] = "ALTER TABLE `$table` ENGINE=InnoDB;";

		foreach ( $queries as $query ) {
			$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		}
	}
}
