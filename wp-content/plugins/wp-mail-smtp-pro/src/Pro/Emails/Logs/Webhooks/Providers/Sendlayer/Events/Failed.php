<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Sendlayer\Events;

use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Failed as FailedBase;

/**
 * Class Failed.
 *
 * @since 3.4.0
 */
class Failed extends FailedBase {

	/**
	 * Get error message from event data.
	 *
	 * @since 3.4.0
	 *
	 * @param array $data Event data.
	 *
	 * @return string
	 */
	protected function get_error_message( $data ) {

		return ! empty( $data['Reason'] ) ? $data['Reason'] : parent::get_error_message( $data );
	}
}
