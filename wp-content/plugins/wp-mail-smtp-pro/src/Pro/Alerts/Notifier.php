<?php

namespace WPMailSMTP\Pro\Alerts;

use WPMailSMTP\Pro\Alerts\Handlers\HandlerInterface;

/**
 * Class Notifier.
 *
 * @since 3.5.0
 */
class Notifier {

	/**
	 * Registered handlers.
	 *
	 * @since 3.5.0
	 *
	 * @var HandlerInterface[]
	 */
	private $handlers = [];

	/**
	 * Register handler.
	 *
	 * @since 3.5.0
	 *
	 * @param HandlerInterface $handler Handler object.
	 */
	public function push_handler( HandlerInterface $handler ) {

		$this->handlers[] = $handler;
	}

	/**
	 * Send notification via registered handlers.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 */
	public function notify( Alert $alert ) {

		foreach ( $this->handlers as $handler ) {
			if ( ! $handler->can_handle( $alert ) ) {
				continue;
			}

			$handler->handle( $alert );
		}
	}
}
