<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Events;

use WPMailSMTP\Pro\Emails\Logs\Email;

/**
 * Class Delivered. Delivered event.
 *
 * @since 3.3.0
 */
class Delivered implements EventInterface {

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
	public function handle( $email, $data ) {

		$email->set_status( Email::STATUS_DELIVERED );
		$email->save();

		return true;
	}
}
