<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\Options;
use WPMailSMTP\Providers\MailerAbstract;

/**
 * Class AbstractProvider.
 *
 * @since 3.3.0
 */
abstract class AbstractProvider implements ProviderInterface {

	/**
	 * The plugin mailer name/slug.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	protected $mailer_name;

	/**
	 * The plugin mailer object.
	 *
	 * @since 3.3.0
	 *
	 * @var MailerAbstract
	 */
	private $mailer = null;

	/**
	 * Mailer DB options.
	 *
	 * @since 3.3.0
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Webhook processor.
	 *
	 * @since 3.3.0
	 *
	 * @var AbstractProcessor
	 */
	protected $processor = null;

	/**
	 * Webhook subscription manager.
	 *
	 * @since 3.3.0
	 *
	 * @var AbstractSubscriber
	 */
	protected $subscriber = null;

	/**
	 * Constructor.
	 *
	 * @since 3.3.0
	 *
	 * @param string $mailer_name The plugin mailer name/slug.
	 */
	public function __construct( $mailer_name ) {

		$this->mailer_name = $mailer_name;
		$this->options     = Options::init()->get_group( $mailer_name );
	}

	/**
	 * Initialize provider if necessary.
	 *
	 * @since 3.3.0
	 */
	public function init() { }

	/**
	 * Get the name/slug of the mailer.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_mailer_name() {

		return $this->mailer_name;
	}

	/**
	 * Get the plugin mailer object.
	 *
	 * @since 3.3.0
	 *
	 * @return MailerAbstract
	 */
	public function get_mailer() {

		if ( is_null( $this->mailer ) ) {
			$phpmailer    = wp_mail_smtp()->get_processor()->get_phpmailer();
			$this->mailer = wp_mail_smtp()->get_providers()->get_mailer( $this->mailer_name, $phpmailer );
		}

		return $this->mailer;
	}

	/**
	 * Get the mailed DB option.
	 *
	 * @since 3.3.0
	 *
	 * @param string $key Option key.
	 *
	 * @return mixed|null
	 */
	public function get_option( $key ) {

		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
	}

	/**
	 * Get webhook url.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_url() {

		return get_rest_url( null, 'wp-mail-smtp/v1/webhooks/' . $this->mailer_name );
	}

	/**
	 * Create subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function subscribe() {

		$result = $this->get_subscriber()->subscribe();

		if ( ! is_wp_error( $result ) ) {
			$this->set_setup_status( Webhooks::SUCCESS_SETUP );
		} else {
			$this->set_setup_status( Webhooks::FAILED_SETUP );
			$this->set_subscription_error( $result, 'subscribe' );
		}

		return $result;
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

		$result = $this->get_subscriber()->unsubscribe();

		if ( is_wp_error( $result ) ) {
			$this->set_subscription_error( $result, 'unsubscribe' );
		}

		$this->reset_setup();

		return $result;
	}

	/**
	 * Check subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return bool|\WP_Error
	 */
	public function is_subscribed() {

		return $this->get_subscriber()->is_subscribed();
	}

	/**
	 * Verify subscription and set appropriate status.
	 *
	 * @since 3.3.0
	 */
	public function verify_subscription() {

		$is_subscribed = $this->is_subscribed();

		if ( $this->get_setup_status() === Webhooks::SUCCESS_SETUP && ! $is_subscribed ) {
			$this->set_setup_status( Webhooks::BROKEN_SETUP );
		} else if ( $this->get_setup_status() !== Webhooks::SUCCESS_SETUP && $is_subscribed ) {
			$this->set_setup_status( Webhooks::SUCCESS_SETUP );
		}
	}

	/**
	 * Set setup status.
	 *
	 * @since 3.3.0
	 *
	 * @param string $status Status.
	 */
	public function set_setup_status( $status ) {

		$updated_settings = [
			$this->mailer_name => [
				'webhooks_setup' => $status,
			],
		];

		Options::init()->set( $updated_settings, false, false );
	}

	/**
	 * Get setup status.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_setup_status() {

		return Options::init()->get( $this->mailer_name, 'webhooks_setup' );
	}

	/**
	 * Reset setup options.
	 *
	 * @since 3.3.0
	 */
	public function reset_setup() {

		$updated_settings = [
			$this->mailer_name => [
				'webhooks_setup' => '',
			],
		];

		Options::init()->set( $updated_settings, false, false );
	}

	/**
	 * Set subscription error.
	 *
	 * @since 3.3.0
	 *
	 * @param \WP_Error $error  WP Error object.
	 * @param string    $action Error action.
	 */
	protected function set_subscription_error( $error, $action ) {

		$error_message = implode( "\r\n", array_unique( $error->get_error_messages() ) );

		DebugEvents::add(
			sprintf( /* translators: %1$s - mailer name; %2$s - action; %3$s - error message. */
				esc_html__( 'Webhooks subscription. Mailer: %1$s. Action: %2$s. Error: %3$s.', 'wp-mail-smtp-pro' ),
				$this->get_mailer_name(),
				$action,
				$error_message
			)
		);

		update_option( Webhooks::SUBSCRIPTION_ERROR_OPTION_NAME, $error_message );
	}
}
