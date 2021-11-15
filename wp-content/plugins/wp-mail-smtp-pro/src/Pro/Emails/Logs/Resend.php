<?php

namespace WPMailSMTP\Pro\Emails\Logs;

/**
 * Class Resend (emails resend functionality).
 *
 * @since 2.9.0
 */
class Resend {

	/**
	 * Resend email trait.
	 *
	 * @since 2.9.0
	 */
	use CanResendEmailTrait;

	/**
	 * Register hooks.
	 *
	 * @since 2.9.0
	 */
	public function hooks() {

		add_action( 'wp_ajax_wp_mail_smtp_resend_email', [ $this, 'resend_email_ajax_handler' ] );
		add_action( 'wp_ajax_wp_mail_smtp_bulk_resend_emails', [ $this, 'bulk_resend_emails_ajax_handler' ] );
	}

	/**
	 * Resend single email ajax handler.
	 *
	 * @since 2.9.0
	 */
	public function resend_email_ajax_handler() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		if ( check_ajax_referer( 'wp-mail-smtp-admin', 'nonce', false ) === false ) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ) );
		}

		if ( ! current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ) );
		}

		$email_id = isset( $_POST['email_id'] ) ? intval( $_POST['email_id'] ) : false;

		if ( $email_id === false ) {
			wp_send_json_error( esc_html__( 'Email ID must be specified.', 'wp-mail-smtp-pro' ) );
		}

		$recipients = isset( $_POST['recipients'] ) && ! empty( trim( $_POST['recipients'] ) ) ? $this->parse_emails( $_POST['recipients'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		if ( $recipients === false ) {
			wp_send_json_error( esc_html__( 'Email recipients must be specified.', 'wp-mail-smtp-pro' ) );
		} elseif ( is_wp_error( $recipients ) ) {
			wp_send_json_error( esc_html__( 'Invalid recipients email addresses.', 'wp-mail-smtp-pro' ) );
		}

		$email = new Email( $email_id );

		if ( $email->get_id() === 0 ) {
			wp_send_json_error( esc_html__( 'Invalid email ID.', 'wp-mail-smtp-pro' ) );
		}

		$is_sent = $this->send_email( $email, $recipients );

		if ( ! is_wp_error( $is_sent ) ) {
			wp_send_json_success( esc_html__( 'Email was successfully sent!', 'wp-mail-smtp-pro' ) );
		} else {
			wp_send_json_error(
				sprintf(
					wp_kses( /* translators: %s - Error message. */
						__( 'Email sending error. <br>%s', 'wp-mail-smtp-pro' ),
						[
							'br' => [],
						]
					),
					str_replace( "\n", '<br>', $is_sent->get_error_message() )
				)
			);
		}
	}

	/**
	 * Emails bulk resend ajax handler.
	 *
	 * @since 2.9.0
	 */
	public function bulk_resend_emails_ajax_handler() {

		if ( check_ajax_referer( 'wp-mail-smtp-admin', 'nonce', false ) === false ) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ) );
		}

		if ( ! current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ) );
		}

		$email_ids = isset( $_POST['email_ids'] ) ? array_map( 'intval', $_POST['email_ids'] ) : false;

		if ( $email_ids === false ) {
			wp_send_json_error( esc_html__( 'Email ID\'s must be specified.', 'wp-mail-smtp-pro' ) );
		}

		$this->schedule_emails_send( $email_ids );

		wp_send_json_success( esc_html__( 'Emails were added to the send queue. If these selected emails have their email content, they will be resent shortly.', 'wp-mail-smtp-pro' ) );
	}

	/**
	 * Parse, sanitize and validate emails string.
	 *
	 * @since 2.9.0
	 *
	 * @param string $emails Email addresses separated by comma.
	 *
	 * @return array|\WP_Error Emails array or validation error.
	 */
	protected function parse_emails( $emails ) {

		$errors = new \WP_Error();

		$emails = explode( ',', $emails );
		$emails = array_map( 'trim', $emails );
		$emails = array_map( 'sanitize_email', $emails );

		foreach ( $emails as $email ) {
			if ( ! is_email( $email ) ) {
				$errors->add( 'invalid_email', $email );
			}
		}

		return ! empty( $errors->errors ) ? $errors : $emails;
	}

	/**
	 * The content of the "Resend email confirmation" modal window.
	 *
	 * @since 2.9.0
	 *
	 * @param Email $email Email instance.
	 *
	 * @return string
	 */
	public static function prepare_resend_confirmation_content( $email ) {

		ob_start();
		?>
		<div id="wp-mail-smtp-resend-confirmation-content">
			<p><?php esc_html_e( 'Are you sure you want to resend this email?', 'wp-mail-smtp-pro' ); ?></p>
			<p>
				<label for="wp-mail-smtp-resent-recipient-email">
					<?php esc_html_e( 'Email recipients (separate with a comma)', 'wp-mail-smtp-pro' ); ?>
				</label>
				<input type="text" name="email" value="<?php echo esc_attr( implode( ',', $email->get_people( 'to' ) ) ); ?>" id="wp-mail-smtp-resent-recipient-email">
			</p>
		</div>
		<?php
		return ob_get_clean();
	}
}
