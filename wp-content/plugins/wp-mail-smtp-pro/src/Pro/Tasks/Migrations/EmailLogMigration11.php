<?php

namespace WPMailSMTP\Pro\Tasks\Migrations;

use WPMailSMTP\Pro\Emails\Logs\Tracking\Cleanup as TrackingCleanup;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Cleanup as AttachmentsCleanup;
use WPMailSMTP\Tasks\Task;

/**
 * Class EmailLogMigration11.
 *
 * An async task for performing the Email Log migration #11.
 * This migration cleanups orphaned tracking data and attachments.
 *
 * @since 3.8.0
 */
class EmailLogMigration11 extends Task {

	/**
	 * Action name for this task.
	 *
	 * @var string
	 *
	 * @since 3.8.0
	 */
	const ACTION = 'wp_mail_smtp_process_email_logs_migration_11';

	/**
	 * Status option name.
	 *
	 * @var string
	 *
	 * @since 3.8.0
	 */
	const STATUS_OPTION_NAME = 'wp_mail_smtp_process_email_logs_migration_11_status';

	/**
	 * Cleanup "Tracking Events" status.
	 *
	 * @var string
	 *
	 * @since 3.8.0
	 */
	const CLEANUP_TRACKING_EVENTS_STATUS = 'tracking_events';

	/**
	 * Cleanup "Tracking Links" status.
	 *
	 * @var string
	 *
	 * @since 3.8.0
	 */
	const CLEANUP_TRACKING_LINKS_STATUS = 'tracking_links';

	/**
	 * Cleanup attachments status.
	 *
	 * @var string
	 *
	 * @since 3.8.0
	 */
	const CLEANUP_ATTACHMENT_STATUS = 'attachments';

	/**
	 * Cleanup completed status.
	 *
	 * @var string
	 *
	 * @since 3.8.0
	 */
	const CLEANUP_COMPLETE_STATUS = 'completed';

	/**
	 * Current migration status.
	 *
	 * @since 3.8.0
	 *
	 * @var string
	 */
	private $current_status = null;

	/**
	 * Tracking Cleanup object.
	 *
	 * @since 3.8.0
	 *
	 * @var TrackingCleanup
	 */
	private $tracking_cleanup = null;

	/**
	 * Constructor.
	 *
	 * @since 3.8.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task with all the proper checks.
	 *
	 * @since 3.8.0
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Hook our migration action.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function hooks() {

		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Start the cleanup process.
	 *
	 * The cleanup process goes like this:
	 * 1. Tracking Events.
	 * 2. Tracking Links.
	 * 3. Attachments.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function process() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		// This migration could take longer depending on the number of orphaned data.
		set_time_limit( 300 );

		switch ( $this->get_current_status() ) {
			case self::CLEANUP_TRACKING_LINKS_STATUS:
				$this->cleanup_tracking_links();
				break;

			case self::CLEANUP_ATTACHMENT_STATUS:
				$this->cleanup_attachments();
				break;

			case self::CLEANUP_COMPLETE_STATUS:
				add_action( 'shutdown', [ $this, 'after_migration_cycle' ] );
				break;

			default:
				$this->cleanup_tracking_events();
				break;
		}
	}

	/**
	 * Get the migration's current status.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_current_status() {

		if ( ! is_null( $this->current_status ) ) {
			return $this->current_status;
		}

		$this->current_status = get_option( self::STATUS_OPTION_NAME, self::CLEANUP_TRACKING_EVENTS_STATUS );

		return $this->current_status;
	}

	/**
	 * Clean up orphaned data in "Tracking Events" table.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function cleanup_tracking_events() {

		if ( $this->get_tracking_cleanup()->cleanup_tracking_events() ) {
			$this->update_status( self::CLEANUP_TRACKING_LINKS_STATUS );
		}
	}

	/**
	 * Clean up orphaned data in "Tracking Links" table.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function cleanup_tracking_links() {

		if ( $this->get_tracking_cleanup()->cleanup_tracking_links() ) {
			$this->update_status( self::CLEANUP_ATTACHMENT_STATUS );
		}
	}

	/**
	 * Update the migration's status.
	 *
	 * @since 3.8.0
	 *
	 * @param string $new_status The new status.
	 *
	 * @return void
	 */
	private function update_status( $new_status ) {

		$this->current_status = $new_status;

		update_option( self::STATUS_OPTION_NAME, $new_status );
	}

	/**
	 * Get Tracking Cleanup object.
	 *
	 * @since 3.8.0
	 *
	 * @return TrackingCleanup
	 */
	private function get_tracking_cleanup() {

		if ( is_null( $this->tracking_cleanup ) ) {
			$this->tracking_cleanup = new TrackingCleanup();
		}

		return $this->tracking_cleanup;
	}

	/**
	 * Cleanup orphaned attachments data in Email Attachments and
	 * Attachment Files DB tables. As well as the physical file.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function cleanup_attachments() {

		if ( ( new AttachmentsCleanup() )->cleanup_attachments() ) {
			$this->update_status( self::CLEANUP_COMPLETE_STATUS );
		}
	}

	/**
	 * Remove this recurring AS task.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function after_migration_cycle() {

		// Possibly just check if the task is no longer scheduled?
		if ( $this->get_current_status() !== self::CLEANUP_COMPLETE_STATUS ) {
			return;
		}

		$this->cancel();
	}
}
