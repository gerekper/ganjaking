<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Mailgun\Events;

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

		$text = [];

		if ( ! empty( $data['delivery-status']['description'] ) ) {
			$text[] = $data['delivery-status']['description'];
		}

		if ( ! empty( $data['reason'] ) ) {
			$text[] = $data['reason'];
		}

		return ! empty( $text ) ? implode( ' - ', $text ) : parent::get_error_message( $data );
	}
}
