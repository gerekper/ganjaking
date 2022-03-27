<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks;

/**
 * Class AbstractSubscriber.
 *
 * @since 3.3.0
 */
abstract class AbstractSubscriber implements SubscriberInterface {

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
}
