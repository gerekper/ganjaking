<?php

namespace WPMailSMTP\Pro\Tasks\Migrations;

use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\Tasks\Task;

/**
 * Class EmailLogMigration5.
 *
 * An async task for performing the Email Log migration #5.
 *
 * @since 2.2.0
 */
class EmailLogMigration5 extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 2.2.0
	 */
	const ACTION = 'wp_mail_smtp_process_email_logs_migration_5';

	/**
	 * Class constructor.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task.
	 *
	 * @since 2.2.0
	 */
	public function init() {

		// Register the action handler.
		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Process the needed changes for migration #5.
	 * - change the `subject` column type from VARCHAR(255) to VARCHAR(191),
	 *
	 * @since 2.2.0
	 */
	public function process() {

		// This migration could take longer for Email Log tables with a lot of entries.
		set_time_limit( 300 );

		global $wpdb;

		$table = Logs::get_table_name();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( "ALTER TABLE `$table` CHANGE COLUMN `subject` `subject` VARCHAR(191) NOT NULL AFTER `id`;" );
	}
}
