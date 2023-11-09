<?php
namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification;

use Exception;
use WP_Error;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\WP;

/**
 * Class AbstractDeliveryVerifier.
 *
 * @since 3.9.0
 */
abstract class AbstractDeliveryVerifier {

	/**
	 * Mailer slug.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	private $mailer;

	/**
	 * Mailer plugin settings.
	 *
	 * @since 3.9.0
	 *
	 * @var array
	 */
	private $mailer_options = [];

	/**
	 * Email object.
	 *
	 * @since 3.9.0
	 *
	 * @var Email
	 */
	private $email;

	/**
	 * Any error that occur during the verification process.
	 *
	 * @since 3.9.0
	 *
	 * @var WP_Error
	 */
	protected $error;

	/**
	 * Array containing the events of the email.
	 *
	 * @since 3.9.0
	 *
	 * @var array
	 */
	protected $events = [];

	/**
	 * DeliveryStatus of the email.
	 *
	 * @since 3.9.0
	 *
	 * @var DeliveryStatus
	 */
	protected $delivery_status;

	/**
	 * Constructor.
	 *
	 * @since 3.9.0
	 *
	 * @param Email $email Email object.
	 *
	 * @throws Exception When unable to get connection, mailer or mailer options.
	 */
	public function __construct( $email ) {

		$connection = wp_mail_smtp()->get_pro()->get_logs()->get_email_connection( $email );

		if ( empty( $connection ) ) {
			throw new Exception( esc_html__( 'Unable to find email connection.', 'wp-mail-smtp-pro' ) );
		}

		$mailer         = $email->get_mailer();
		$mailer_options = $connection->get_options()->get_group( $mailer );

		if ( empty( $mailer_options ) ) {
			throw new Exception( esc_html__( 'Unable to find mailer options.', 'wp-mail-smtp-pro' ) );
		}

		$this->mailer         = $mailer;
		$this->mailer_options = $mailer_options;
		$this->email          = $email;
	}

	/**
	 * Get the DeliveryStatus of the email.
	 *
	 * @since 3.9.0
	 *
	 * @return DeliveryStatus
	 */
	abstract protected function get_delivery_status() : DeliveryStatus;

	/**
	 * Get events from the API response.
	 *
	 * @since 3.9.0
	 *
	 * @return mixed|WP_Error Returns `WP_Error` if unable to fetch a valid response from the API.
	 *                        Otherwise, returns an array containing the events.
	 */
	abstract protected function get_events();

	/**
	 * Verify the email delivery status.
	 *
	 * @since 3.9.0
	 * @since 3.10.0 Updated to return the delivery status, or error in case of failure.
	 *
	 * @return DeliveryStatus|WP_Error Returns `WP_Error` if unable to fetch a valid response from the API.
	 *                                 Otherwise, returns an instance of DeliveryStatus.
	 */
	public function verify() {

		$events = $this->get_events();

		if ( is_wp_error( $events ) ) {
			$this->unable_to_verify( $events );

			return $events;
		}

		$this->events          = $events;
		$this->delivery_status = $this->get_delivery_status();

		if ( $this->delivery_status->is_delivered() ) {
			$this->save_email_delivered();
		} elseif ( $this->delivery_status->is_failed() ) {
			$this->save_email_not_delivered( $this->delivery_status->get_fail_reason() );
		} else {
			$this->unable_to_verify(
				new WP_Error(
					$this->mailer . '_verification_error',
					esc_html__( 'Unable to verify email delivery status.', 'wp-mail-smtp-pro' )
				)
			);
		}

		return $this->delivery_status;
	}

	/**
	 * Set the email as not verified.
	 *
	 * @since 3.9.0
	 *
	 * @param WP_Error $error WP Error object.
	 *
	 * @return void
	 */
	protected function unable_to_verify( $error ) {

		$this->error = $error;
	}

	/**
	 * Save email status as delivered.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	protected function save_email_delivered() {

		$this->get_email()->set_status( Email::STATUS_DELIVERED );
		$this->get_email()->save();
	}

	/**
	 * Save email status as not delivered.
	 *
	 * @since 3.9.0
	 *
	 * @param string $fail_reason Fail reason.
	 *
	 * @return void
	 */
	protected function save_email_not_delivered( $fail_reason ) {

		$this->get_email()->set_status( Email::STATUS_UNSENT );

		$error_text = empty( $fail_reason ) ? esc_html__( 'The email failed to be delivered. No specific reason was provided by the API.', 'wp-mail-smtp-pro' ) : $fail_reason;

		$this->get_email()->set_error_text( $error_text );
		$this->get_email()->save();
	}

	/**
	 * Validate the response from the API.
	 *
	 * @since 3.9.0
	 *
	 * @param array|WP_Error $response Response from WP remote get.
	 *
	 * @return mixed|WP_Error Returns `WP_Error` if unable to fetch a valid response from the API.
	 *                        Otherwise, returns the JSON decoded response body.
	 */
	protected function validate_response( $response ) {

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return new WP_Error( $this->get_mailer() . '_delivery_verifier_invalid_response_code', WP::wp_remote_get_response_error_message( $response ) );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Get the mailer slug.
	 *
	 * @since 3.9.0
	 *
	 * @return string
	 */
	protected function get_mailer() {

		return $this->mailer;
	}

	/**
	 * Get the mailer plugin settings.
	 *
	 * @since 3.9.0
	 *
	 * @return array
	 */
	public function get_mailer_options() {

		return $this->mailer_options;
	}

	/**
	 * Get the Email object.
	 *
	 * @since 3.9.0
	 *
	 * @return Email
	 */
	public function get_email() {

		return $this->email;
	}

	/**
	 * Whether the email is verified or not.
	 *
	 * @since 3.9.0
	 *
	 * @return bool
	 */
	public function is_verified() {

		if ( ! $this->delivery_status instanceof DeliveryStatus ) {
			$this->delivery_status = new DeliveryStatus();
		}

		return $this->delivery_status->is_verified();
	}
}
