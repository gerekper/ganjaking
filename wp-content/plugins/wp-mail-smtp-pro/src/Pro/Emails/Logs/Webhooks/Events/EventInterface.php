<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Events;

use WPMailSMTP\Pro\Emails\Logs\Email;

/**
 * Interface EventInterface.
 *
 * @since 3.3.0
 */
interface EventInterface {

	/**
	 * Handle event.
	 *
	 * @since 3.3.0
	 *
	 * @param Email $email Email object.
	 * @param array $data  Event data.
	 *
	 * @return bool
	 */
	public function handle( $email, $data );
}
