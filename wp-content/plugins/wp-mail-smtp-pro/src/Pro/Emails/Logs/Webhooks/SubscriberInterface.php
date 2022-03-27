<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks;

/**
 * Interface SubscriberInterface.
 *
 * @since 3.3.0
 */
interface SubscriberInterface {

	/**
	 * Create subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function subscribe();

	/**
	 * Remove subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function unsubscribe();

	/**
	 * Whether subscription exists.
	 *
	 * @since 3.3.0
	 *
	 * @return bool|\WP_Error
	 */
	public function is_subscribed();
}
