<?php

namespace WPMailSMTP\Pro\Alerts;

use Exception;
use ReflectionClass;
use WPMailSMTP\Pro\Alerts\Handlers\HandlerInterface;

/**
 * Class Loader.
 *
 * @since 3.5.0
 */
class Loader {

	/**
	 * Key is the provider slug, value is the path to its classes.
	 *
	 * @since 3.5.0
	 *
	 * @var array
	 */
	protected $providers = [
		'email'          => 'WPMailSMTP\Pro\Alerts\Providers\Email\\',
		'slack_webhook'  => 'WPMailSMTP\Pro\Alerts\Providers\SlackWebhook\\',
		'twilio_sms'     => 'WPMailSMTP\Pro\Alerts\Providers\TwilioSMS\\',
		'custom_webhook' => 'WPMailSMTP\Pro\Alerts\Providers\CustomWebhook\\',
	];

	/**
	 * Get all the supported providers.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	public function get_providers() {

		/**
		 * Filters supported providers.
		 *
		 * @since 3.5.0
		 *
		 * @param array $providers Supported providers.
		 */
		return apply_filters( 'wp_mail_smtp_pro_alerts_loader_get_providers', $this->providers );
	}

	/**
	 * Get the provider handler, if exists.
	 *
	 * @since 3.5.0
	 *
	 * @param string $provider Provider slug.
	 *
	 * @return HandlerInterface|null
	 */
	public function get_handler( $provider ) {

		return $this->get_entity( $provider, 'Handler' );
	}

	/**
	 * Get the provider options, if exists.
	 *
	 * @since 3.5.0
	 *
	 * @param string $provider Provider slug.
	 *
	 * @return AbstractOptions|null
	 */
	public function get_options( $provider ) {

		return $this->get_entity( $provider, 'Options' );
	}

	/**
	 * Get handlers of all providers.
	 *
	 * @since 3.5.0
	 *
	 * @return HandlerInterface[]
	 */
	public function get_handlers_all() {

		$handlers = [];

		foreach ( $this->get_providers() as $provider => $path ) {
			$handler = $this->get_handler( $provider );

			if ( ! $handler instanceof HandlerInterface ) {
				continue;
			}

			$handlers[ $provider ] = $handler;
		}

		return $handlers;
	}

	/**
	 * Get options of all providers.
	 *
	 * @since 3.5.0
	 *
	 * @return AbstractOptions[]
	 */
	public function get_options_all() {

		$options = [];

		foreach ( $this->get_providers() as $provider => $path ) {
			$option = $this->get_options( $provider );

			if ( ! $option instanceof AbstractOptions ) {
				continue;
			}

			$options[ $provider ] = $option;
		}

		return $options;
	}

	/**
	 * Get a single provider FQN-path based on its name.
	 *
	 * @since 3.5.0
	 *
	 * @param string $provider Provider slug.
	 *
	 * @return string|null
	 */
	private function get_provider_path( $provider ) {

		$providers = $this->get_providers();

		return isset( $providers[ $provider ] ) ? $providers[ $provider ] : null;
	}

	/**
	 * Get a generic entity based on the request.
	 *
	 * @since 3.5.0
	 *
	 * @param string $provider Provider slug.
	 * @param string $request  Entity class name.
	 * @param array  $args     Entity instantiation arguments.
	 *
	 * @return AbstractOptions|HandlerInterface|null
	 */
	private function get_entity( $provider, $request, $args = [] ) {

		$path   = $this->get_provider_path( $provider );
		$entity = null;

		if ( empty( $path ) ) {
			return $entity;
		}

		try {
			$reflection = new ReflectionClass( $path . $request );

			if ( file_exists( $reflection->getFileName() ) ) {
				$class  = $path . $request;
				$entity = new $class( ...$args );
			}
		} catch ( Exception $e ) {
			$entity = null;
		}

		return $entity;
	}
}
