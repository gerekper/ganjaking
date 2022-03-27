<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Postmark\Events;

use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Failed as FailedBase;

/**
 * Class Failed.
 *
 * @since 3.3.0
 */
class Failed extends FailedBase {

	/**
	 * Get error message from event data.
	 *
	 * @since 3.3.0
	 *
	 * @param array $data Event data.
	 *
	 * @return string
	 */
	protected function get_error_message( $data ) {

		return isset( $data['Description'] ) ? $data['Description'] : parent::get_error_message( $data );
	}
}
