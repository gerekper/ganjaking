<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks;

/**
 * Class AbstractProcessor.
 *
 * @since 3.3.0
 */
abstract class AbstractProcessor implements ProcessorInterface {

	/**
	 * Provider object.
	 *
	 * @since 3.3.0
	 *
	 * @var AbstractProvider
	 */
	protected $provider;

	/**
	 * Constructor.
	 *
	 * @since 3.3.0
	 *
	 * @param AbstractProvider $provider Provider object.
	 */
	public function __construct( $provider ) {

		$this->provider = $provider;
	}

	/**
	 * Validate webhook incoming request.
	 *
	 * @since 3.3.0
	 *
	 * @param \WP_REST_Request $request Webhook request.
	 *
	 * @return bool
	 */
	public function validate( \WP_REST_Request $request ) {

		//  Skip validation. Validation must be configured on the server level if needed.
		return true;
	}
}
