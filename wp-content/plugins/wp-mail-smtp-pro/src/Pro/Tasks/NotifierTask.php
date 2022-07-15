<?php

namespace WPMailSMTP\Pro\Tasks;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\Alert;
use WPMailSMTP\Pro\Alerts\Loader;
use WPMailSMTP\Pro\Alerts\Notifier;
use WPMailSMTP\Tasks\Task;
use WPMailSMTP\Tasks\Meta;
use Exception;

/**
 * Class NotifierTask.
 *
 * @since 3.5.0
 */
class NotifierTask extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 3.5.0
	 */
	const ACTION = 'wp_mail_smtp_process_alert_notifier_task';

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task.
	 *
	 * @since 3.5.0
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		// Register the action handler.
		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Send notification.
	 *
	 * @since 3.5.0
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 *
	 * @throws Exception Exception will be logged in the Action Scheduler logs table.
	 */
	public function process( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive the passed parameters.
		if ( empty( $meta->data ) || count( $meta->data ) !== 2 ) {
			return;
		}

		$notifier = new Notifier();
		$loader   = new Loader();
		$options  = Options::init();

		// Register all enabled handlers.
		foreach ( $loader->get_handlers_all() as $slug => $handler ) {
			if ( $options->get( 'alert_' . $slug, 'enabled' ) ) {
				$notifier->push_handler( $handler );
			}
		}

		$alert_type = $meta->data[0];
		$alert_data = $meta->data[1];

		$alert = new Alert( $alert_type, $alert_data );

		// Send notifications.
		$notifier->notify( $alert );
	}
}
