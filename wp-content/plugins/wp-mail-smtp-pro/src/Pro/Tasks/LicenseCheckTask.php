<?php

namespace WPMailSMTP\Pro\Tasks;

use ActionScheduler;
use Exception;
use WPMailSMTP\Tasks\Task;
use WPMailSMTP\Tasks\Tasks;

/**
 * Class LicenseCheckTask.
 *
 * @since 3.9.0
 */
class LicenseCheckTask extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 3.9.0
	 */
	const ACTION = 'wp_mail_smtp_process_license_check_task';

	/**
	 * Constructor.
	 *
	 * @since 3.9.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		// Register the action handler.
		add_action( self::ACTION, [ $this, 'process' ] );

		if ( Tasks::is_scheduled( self::ACTION ) !== false ) {
			return;
		}

		$this->recurring(
			strtotime( '+1 day' ),
			$this->get_license_check_interval()
		)->register();
	}

	/**
	 * Get the license check interval.
	 *
	 * @since 3.9.0
	 *
	 * @return int
	 */
	private function get_license_check_interval() {

		/**
		 * Filters the interval for the license check task.
		 *
		 * @since 3.9.0
		 *
		 * @param int $interval The interval in seconds. Default to a day (in seconds).
		 */
		return (int) apply_filters( 'wp_mail_smtp_pro_tasks_license_check_task_get_license_check_interval', DAY_IN_SECONDS );
	}

	/**
	 * Check license status.
	 *
	 * @since 3.9.0
	 *
	 * @throws Exception Exception will be logged in the Action Scheduler logs table.
	 */
	public function process() {

		// Delete license check task duplicates.
		try {
			$this->delete_pending_tasks();
		} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Do nothing.
		}

		$license_key = wp_mail_smtp()->get_license_key();

		if ( empty( $license_key ) ) {
			return;
		}

		wp_mail_smtp()->get_pro()->get_license()->validate_key( $license_key );
	}

	/**
	 * Delete license check task duplicates.
	 *
	 * @since 3.10.1
	 */
	private function delete_pending_tasks() {

		// Make sure that all used functions, classes, and methods exist.
		if (
			! function_exists( 'as_get_scheduled_actions' ) ||
			! class_exists( 'ActionScheduler' ) ||
			! method_exists( 'ActionScheduler', 'store' ) ||
			! class_exists( 'ActionScheduler_Store' ) ||
			! method_exists( 'ActionScheduler_Store', 'delete_action' )
		) {
			return;
		}

		// Get all pending license check actions.
		$action_ids = as_get_scheduled_actions(
			[
				'hook'     => self::ACTION,
				'status'   => 'pending',
				'per_page' => 1000,
			],
			'ids'
		);

		if ( empty( $action_ids ) ) {
			return;
		}

		// Delete all pending license check actions.
		foreach ( $action_ids as $action_id ) {
			ActionScheduler::store()->delete_action( $action_id );
		}
	}
}
