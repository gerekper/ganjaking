<?php

namespace WPMailSMTP\Pro\Alerts\Handlers;

use WPMailSMTP\Pro\Alerts\Alert;

/**
 * Interface HandlerInterface.
 *
 * @since 3.5.0
 */
interface HandlerInterface {

	/**
	 * Whether current handler can handle provided alert.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return bool
	 */
	public function can_handle( Alert $alert );

	/**
	 * Handle alert.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return bool
	 */
	public function handle( Alert $alert );
}
