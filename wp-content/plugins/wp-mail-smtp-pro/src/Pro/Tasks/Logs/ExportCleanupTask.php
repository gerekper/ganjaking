<?php

namespace WPMailSMTP\Pro\Tasks\Logs;

use WPMailSMTP\Tasks\Task;
use WPMailSMTP\Tasks\Meta;
use WPMailSMTP\Pro\Emails\Logs\Export\CanRemoveExportFileTrait;

/**
 * Class ExportCleanupTask.
 *
 * @since 2.8.0
 */
class ExportCleanupTask extends Task {

	/**
	 * Remove export file trait.
	 *
	 * @since 2.8.0
	 */
	use CanRemoveExportFileTrait;

	/**
	 * Action name for this task.
	 *
	 * @since 2.8.0
	 */
	const ACTION = 'wp_mail_smtp_process_old_export_logs_files_cleanup';

	/**
	 * Class constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task.
	 *
	 * @since 2.8.0
	 */
	public function init() {

		// Register the action handler.
		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Schedule export file cleanup.
	 *
	 * @since 2.8.0
	 *
	 * @param string $request_id       Request id.
	 * @param int    $request_data_ttl Request time to live.
	 */
	public function schedule( $request_id, $request_data_ttl ) {

		// Exit if AS function does not exist.
		if ( ! function_exists( 'as_next_scheduled_action' ) ) {
			return;
		}

		// Schedule the task.
		$this->once( time() + $request_data_ttl )
				 ->params( $request_id )
				 ->register();
	}

	/**
	 * Perform the cleanup action: remove export file.
	 *
	 * @since 2.8.0
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 */
	public function process( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive the passed parameter.
		if ( empty( $meta ) || empty( $meta->data ) || count( $meta->data ) !== 1 ) {
			return;
		}

		$request_id = $meta->data[0];

		if ( empty( $request_id ) ) {
			return;
		}

		$this->remove_export_file( $request_id );
	}
}
