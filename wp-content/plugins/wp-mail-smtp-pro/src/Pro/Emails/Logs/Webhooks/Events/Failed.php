<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Events;

use WPMailSMTP\Pro\Alerts\Alerts;
use WPMailSMTP\Pro\Emails\Logs\Email;

/**
 * Class Failed. Failed event.
 *
 * @since 3.3.0
 */
class Failed implements EventInterface {

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

		$error_text = $this->get_error_message( $data );

		$email->set_status( Email::STATUS_UNSENT );
		$email->set_error_text( $error_text );
		$email->save();

		// Trigger alerts.
		( new Alerts() )->handle_hard_bounced_email( $error_text, $email );

		return true;
	}

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

		// Default error message, if this method is not overwritten on provider level.
		return esc_html__( 'The email failed to be delivered. No specific reason was provided by the API.', 'wp-mail-smtp-pro' );
	}
}
