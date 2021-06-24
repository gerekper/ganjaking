<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable;

/**
 * Email tracking open email event class.
 *
 * @since 2.9.0
 */
class OpenEmailEvent extends AbstractInjectableEvent {

	/**
	 * Get the event type.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public static function get_type() {

		return 'open-email';
	}

	/**
	 * Whether the tracking event is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_active() {

		return wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking();
	}

	/**
	 * Inject tracking pixel to content.
	 *
	 * @since 2.9.0
	 *
	 * @param string $email_content Email content.
	 *
	 * @return string Email content with injected tracking code.
	 */
	public function inject( $email_content ) {

		$email_content .= sprintf( '<img src="%s" alt=""/>', $this->get_tracking_url() );

		return $email_content;
	}

	/**
	 * Return pixel image.
	 *
	 * @since 2.9.0
	 *
	 * @param array $event_data Event data from request.
	 */
	public function get_response( $event_data ) {

		header( 'Cache-Control: must-revalidate, no-cache, no-store, max-age=0, no-transform' );
		header( 'Pragma: no-cache' );
		header( 'Content-Type: image/gif' );
		echo "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x90\x00\x00\xff\x00\x00\x00\x00\x00\x21\xf9\x04\x05\x10\x00\x00\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x04\x01\x00\x3b";
		exit;
	}
}
