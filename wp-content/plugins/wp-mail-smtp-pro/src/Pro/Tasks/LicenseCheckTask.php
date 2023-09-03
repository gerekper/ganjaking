<?php

namespace WPMailSMTP\Pro\Tasks;

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

		if ( Tasks::is_scheduled( self::ACTION ) ) {
			return;
		}

		$this->recurring(
			strtotime( '+1 minute' ),
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

		$license_key = wp_mail_smtp()->get_license_key();

		if ( empty( $license_key ) ) {
			return;
		}

		wp_mail_smtp()->get_pro()->get_license()->validate_key( $license_key );
	}
}
