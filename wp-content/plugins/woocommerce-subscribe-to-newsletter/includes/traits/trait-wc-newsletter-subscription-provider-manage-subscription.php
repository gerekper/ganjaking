<?php
/**
 * Implements the provider manage subscription feature.
 *
 * @package WC_Newsletter_Subscription/Traits
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait WC_Newsletter_Subscription_Provider_Manage_Subscription.
 */
trait WC_Newsletter_Subscription_Provider_Manage_Subscription {

	/**
	 * Checks if the subscriber belongs to the specified list.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed                                 $list       The list identifier.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return bool|WP_Error A boolean with the subscription status. WP_Error on failure.
	 */
	abstract public function is_subscribed( $list, $subscriber );

	/**
	 * Removes the subscriber from the specified list.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed                                 $list       The list identifier.
	 * @param WC_Newsletter_Subscription_Subscriber $subscriber Subscriber object.
	 * @return WC_Newsletter_Subscription_Subscriber|WP_Error Subscriber object on success. WP_Error on failure.
	 */
	abstract public function unsubscribe( $list, $subscriber );
}
