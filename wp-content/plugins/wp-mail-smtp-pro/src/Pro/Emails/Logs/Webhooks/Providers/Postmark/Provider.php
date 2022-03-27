<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Postmark;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProvider;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractSubscriber;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProcessor;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Webhooks;
use WPMailSMTP\Providers\MailerAbstract;

/**
 * Class Provider.
 *
 * @since 3.3.0
 */
class Provider extends AbstractProvider {

	/**
	 * Message stream.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	protected $message_stream;

	/**
	 * Constructor.
	 *
	 * @since 3.3.0
	 *
	 * @param string $mailer_name The plugin mailer name/slug.
	 */
	public function __construct( $mailer_name ) {

		parent::__construct( $mailer_name );

		$this->message_stream = $this->get_option( 'message_stream' );
	}

	/**
	 * Initialize provider.
	 *
	 * @since 3.3.0
	 */
	public function init() {

		parent::init();

		add_action( 'wp_mail_smtp_mailcatcher_send_before', [ $this, 'record_additional_message_stream' ] );
	}

	/**
	 * Get the webhook processor.
	 *
	 * @since 3.3.0
	 *
	 * @return AbstractProcessor
	 */
	public function get_processor() {

		if ( is_null( $this->processor ) ) {
			$this->processor = new Processor( $this );
		}

		return $this->processor;
	}

	/**
	 * Get the webhook subscription manager.
	 *
	 * @since 3.3.0
	 *
	 * @return AbstractSubscriber
	 */
	public function get_subscriber() {

		if ( is_null( $this->subscriber ) ) {
			$this->subscriber = new Subscriber( $this );
		}

		return $this->subscriber;
	}

	/**
	 * Remove subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function unsubscribe() {

		// Skip actual unsubscribe request if it was not setup properly.
		if ( $this->get_setup_status() !== Webhooks::SUCCESS_SETUP ) {
			$this->reset_setup();

			return true;
		}

		$errors          = new \WP_Error();
		$message_streams = array_merge( [ $this->message_stream ], $this->get_additional_message_streams() );

		foreach ( $message_streams as $message_stream ) {
			$this->set_message_stream( $message_stream );

			$result = $this->get_subscriber()->unsubscribe();

			if ( is_wp_error( $result ) ) {
				$errors->add( $result->get_error_code(), $result->get_error_message() );
			}
		}

		if ( ! empty( $errors->errors ) ) {
			$this->set_subscription_error( $errors, 'unsubscribe' );
		}

		$this->reset_setup();

		return ! empty( $errors->errors ) ? $errors : true;
	}

	/**
	 * Reset setup options.
	 *
	 * @since 3.3.0
	 */
	public function reset_setup() {

		$options = Options::init();
		$all_opt = $options->get_all_raw();

		$all_opt[ $this->mailer_name ]['webhooks_setup']             = '';
		$all_opt[ $this->mailer_name ]['additional_message_streams'] = [];
		$options->set( $all_opt );
	}

	/**
	 * Get message stream.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_message_stream() {

		return $this->message_stream;
	}

	/**
	 * Set message stream.
	 *
	 * @since 3.3.0
	 *
	 * @param string $message_stream Message stream.
	 */
	public function set_message_stream( $message_stream ) {

		$this->message_stream = $message_stream;
	}

	/**
	 * Get additional message streams.
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function get_additional_message_streams() {

		$additional_message_streams = $this->get_option( 'additional_message_streams' );

		return ! is_null( $additional_message_streams ) ? (array) $additional_message_streams : [];
	}

	/**
	 * Record additional message stream before send email and create subscription.
	 * Additional message stream can be set via filter.
	 * In most cases, there will not be additional message streams.
	 *
	 * @since 3.3.0
	 *
	 * @param MailerAbstract $mailer Mailer object.
	 */
	public function record_additional_message_stream( $mailer ) {

		if ( $mailer->get_mailer_name() !== $this->mailer_name ) {
			return;
		}

		if ( $this->get_setup_status() !== Webhooks::SUCCESS_SETUP ) {
			return;
		}

		$body = json_decode( $mailer->get_body(), true );

		if (
			isset( $body['MessageStream'] ) &&
			$body['MessageStream'] !== $this->message_stream &&
			! in_array( $body['MessageStream'], $this->get_additional_message_streams(), true )
		) {
			$this->set_message_stream( $body['MessageStream'] );
			$is_subscribed = $this->subscribe();

			if ( $is_subscribed === true ) {
				$updated_settings = [
					$this->mailer_name => [
						'additional_message_streams' => [ sanitize_key( $body['MessageStream'] ) ],
					],
				];

				Options::init()->set( $updated_settings, false, false );
			}
		}
	}
}
