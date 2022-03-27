<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks;

/**
 * Interface ProviderInterface.
 *
 * @since 3.3.0
 */
interface ProviderInterface {

	/**
	 * Get the webhook processor.
	 *
	 * @since 3.3.0
	 *
	 * @return AbstractProcessor
	 */
	public function get_processor();

	/**
	 * Get the webhook subscription manager.
	 *
	 * @since 3.3.0
	 *
	 * @return AbstractSubscriber
	 */
	public function get_subscriber();
}
