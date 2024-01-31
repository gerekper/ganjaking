<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Returns Users Memberships from a Subscription.
 *
 * Returns empty array if no User Memberships are found or Subscriptions is inactive.
 *
 * @since 1.5.4
 *
 * @param int|\WP_Post $subscription a Subscription post object or id
 * @return \WC_Memberships_User_Membership[] array of User Membership objects or empty array if none found
 */
function wc_memberships_get_memberships_from_subscription( $subscription ) {

	$integrations  = wc_memberships()->get_integrations_instance();
	$subscriptions = $integrations && $integrations->is_subscriptions_active() ? $integrations->get_subscriptions_instance() : null;

	return $subscriptions ? $subscriptions->get_memberships_from_subscription( $subscription ) : array();
}


/**
 * Checks if a user membership is tied to a subscription.
 *
 * @since 1.10.6
 *
 * @param int|\WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership the user membership
 * @return bool
 */
function wc_memberships_is_user_membership_linked_to_subscription( $user_membership ) {

	$integration   = wc_memberships()->get_integrations_instance();
	$subscriptions = $integration ? $integration->get_subscriptions_instance() : null;

	return $subscriptions && $subscriptions->is_membership_linked_to_subscription( $user_membership );
}


/**
 * Checks if the product that granted access to a membership is of a subscription type.
 *
 * @since 1.10.6
 *
 * @param int|\WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership the user membership
 * @return bool
 */
function wc_memberships_has_subscription_product_granted_access( $user_membership ) {

	$has_subscription_product = false;

	if ( is_numeric( $user_membership ) ) {
		$user_membership = wc_memberships_get_user_membership( $user_membership );
	}

	if ( $user_membership instanceof \WC_Memberships_User_Membership ) {
		$has_subscription_product = (bool) \WC_Subscriptions_Product::is_subscription( $user_membership->get_product() );
	}

	return $has_subscription_product;
}


/**
 * Returns a Subscription by order_id and product_id.
 *
 * @since 1.8.0
 *
 * @param int $order_id WC_Order ID
 * @param int $product_id WC_Product ID
 * @return null|\WC_Subscription Subscription object or null if not found
 */
function wc_memberships_get_order_subscription( $order_id, $product_id ) {

	$subscriptions = wcs_get_subscriptions_for_order( $order_id, array( 'product_id' => $product_id ) );
	$subscription  = is_array( $subscriptions ) ? reset( $subscriptions ) : null;

	// If undetermined it may be that the subscription was created directly in admin,
	// as there might be no attached order ($order_id is from a WC_Subscription).
	return $subscription instanceof \WC_Subscription ? $subscription : wcs_get_subscription( $order_id );
}
