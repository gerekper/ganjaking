<?php

namespace WPMailSMTP\Pro\Tasks;

use DateTime;
use Exception;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Cleanup as TrackingCleanup;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Cleanup as AttachmentsCleanup;
use WPMailSMTP\Tasks\Meta;
use WPMailSMTP\Tasks\Task;
use WPMailSMTP\Tasks\Tasks;
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
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		// Register the action handler.
		add_action( self::ACTION, [ $this, 'process' ] );

		// Get the retention period value from the Log settings.
		$retention_period = Options::init()->get( 'logs', 'log_retention_period' );

		// Exit if the retention period is not defined (set to "forever") or this task is already scheduled.
		if ( empty( $retention_period ) || Tasks::is_scheduled( self::ACTION ) !== false ) {
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
	 * @since 3.8.0 Cleanup orphaned tracking data and attachments.
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 *
	 * @throws Exception Exception will be logged in the Action Scheduler logs table.
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

		// Bail if DB tables was not created.
		if ( ! wp_mail_smtp()->get_pro()->get_logs()->is_valid_db() ) {
			return;
		}

		// This cleanup could take longer depending on the number of orphaned data.
		set_time_limit( 300 );

		$wpdb = WP::wpdb();
		$date = ( new DateTime( "- $retention_period seconds" ) )->format( WP::datetime_mysql_format() );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				'DELETE FROM `%1$s` WHERE date_sent < "%2$s"', // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				Logs::get_table_name(),
				$date
			)
		);

		$this->cleanup_tracking();
		$this->cleanup_attachments();
	}

	/**
	 * Cleanup orphaned tracking data.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function cleanup_tracking() {

		$tracking_cleanup = new TrackingCleanup();

		$tracking_cleanup->cleanup_tracking_events();
		$tracking_cleanup->cleanup_tracking_links();
	}

	/**
	 * Cleanup orphaned attachments.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function cleanup_attachments() {

		( new AttachmentsCleanup() )->cleanup_attachments();
	}
}
