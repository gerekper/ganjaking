<?php

namespace WPMailSMTP\Pro\Tasks;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\Tasks\Meta;
use WPMailSMTP\Tasks\Task;
use WPMailSMTP\WP;

/**
 * Class EmailLogCleanupTask.
 *
 * @since 2.1.0
 */
class EmailLogCleanupTask extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 2.1.0
	 */
	const ACTION = 'wp_mail_smtp_process_email_log_cleanup';

	/**
	 * Class constructor.
	 *
	 * @since 2.1.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task with all the proper checks.
	 *
	 * @since 2.1.0
	 */
	public function init() {

		// Register the action handler.
		add_action( self::ACTION, array( $this, 'process' ) );

		// Exit if AS function does not exist.
		if ( ! function_exists( 'as_next_scheduled_action' ) ) {
			return;
		}

		// Get the retention period value from the Log settings.
		$retention_period = Options::init()->get( 'logs', 'log_retention_period' );

		// Exit if the retention period is not defined (set to "forever") or this task is already scheduled.
		if ( empty( $retention_period ) || as_next_scheduled_action( self::ACTION ) !== false ) {
			return;
		}

		// Schedule the task.
		$this->recurring(
			strtotime( 'tomorrow' ),
			(int) apply_filters( 'wp_mail_smtp_tasks_email_log_cleanup_interval', DAY_IN_SECONDS )
		)
			->params( $retention_period )
			->register();
	}

	/**
	 * Perform the cleanup action: remove outdated email logs.
	 *
	 * @since 2.1.0
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 *
	 * @throws \Exception Exception will be logged in the Action Scheduler logs table.
	 */
	public function process( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive the passed parameter.
		if ( empty( $meta ) || empty( $meta->data ) || count( $meta->data ) !== 1 ) {
			return;
		}

		/**
		 * Date in seconds (examples: 86400, 100500).
		 * Email logs older than this period will be deleted.
		 *
		 * @var int $retention_period
		 */
		$retention_period = (int) $meta->data[0];

		if ( empty( $retention_period ) ) {
			return;
		}

		$wpdb  = WP::wpdb();
		$table = Logs::get_table_name();
		$date  = ( new \DateTime( "- $retention_period seconds" ) )->format( WP::datetime_mysql_format() );

		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare( "DELETE FROM `$table` WHERE date_sent < %s", $date ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);
	}
}
