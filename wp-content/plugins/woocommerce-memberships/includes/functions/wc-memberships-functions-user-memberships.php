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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;


/**
 * Main function for returning a user membership.
 *
 * Supports getting user membership by membership ID, Post object or a combination of the user ID and membership plan id/slug/Post object.
 * If no $id is provided, defaults to getting the membership for the current user.
 *
 * @since 1.0.0
 *
 * @param int|\WP_Post $id optional post object or post ID of the user membership, or user ID
 * @param string|int|\WP_Post $plan optional Membership Plan slug, post object or related post ID
 * @return \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership|null The User Membership or null if not found
 */
function wc_memberships_get_user_membership( $id = null, $plan = null ) {
	return wc_memberships()->get_user_memberships_instance()->get_user_membership( $id, $plan );
}


/**
 * Returns all memberships for a user.
 *
 * @since 1.0.0
 *
 * @param int|\WP_User $user_id optional, defaults to current user
 * @param array $args optional arguments
 * @return \WC_Memberships_User_Membership[]|\WC_Memberships_Integration_Subscriptions_User_Membership[] array of user memberships
 */
function wc_memberships_get_user_memberships( $user_id = null, $args = array() ) {
	return wc_memberships()->get_user_memberships_instance()->get_user_memberships( $user_id, $args );
}


/**
 * Returns all active memberships for a user.
 *
 * Note: does not include just memberships in purely 'active' status, but memberships that give active access to a plan.
 *
 * @since 1.7.0
 *
 * @param int $user_id optional, defaults to current user
 * @param array $args optional arguments
 * @return \WC_Memberships_User_Membership[]|\\WC_Memberships_Integration_Subscriptions_User_Membership[] array of user memberships
 */
function wc_memberships_get_user_active_memberships( $user_id = null, $args = array() ) {

	$args['status'] = wc_memberships()->get_user_memberships_instance()->get_active_access_membership_statuses();

	return wc_memberships()->get_user_memberships_instance()->get_user_memberships( $user_id, $args );
}


/**
 * Returns all user membership statuses.
 *
 * @since 1.0.0
 *
 * @param bool $with_labels whether to output status keys only (false) or associative array with label data (true, default)
 * @param bool $prefixed whether the status keys should be prefixed (default, true) or use shorthand slugs (false)
 * @return array
 */
function wc_memberships_get_user_membership_statuses( $with_labels = true, $prefixed = true ) {
	return wc_memberships()->get_user_memberships_instance()->get_user_membership_statuses( $with_labels, $prefixed );
}


/**
 * Returns the nice name for a user membership status.
 *
 * @since 1.0.0
 *
 * @param array|string $status one or more statuses
 * @return string
 */
function wc_memberships_get_user_membership_status_name( $status ) {

	$statuses = wc_memberships_get_user_membership_statuses();
	$status   = 0 === strpos( $status, 'wcm-' ) ? substr( $status, 4 ) : $status;
	$status   = isset( $statuses[ 'wcm-' . $status ] ) ? $statuses[ 'wcm-' . $status ] : $status;

	return is_array( $status ) && isset( $status['label'] ) ? $status['label'] : $status;
}


/**
 * Determines if user is a member of either any or a particular membership plan, with any status.
 *
 * @since 1.0.0
 *
 * @param int|\WP_User|null $user_id optional, defaults to current user
 * @param int|string|\WC_Memberships_Membership_Plan|null $membership_plan slug, ID or object, if null (default) checks membership of any plan
 * @param bool $cache whether to use cached results (default true)
 * @return bool
 */
function wc_memberships_is_user_member( $user_id = null, $membership_plan = null, $cache = true ) {
	return wc_memberships()->get_user_memberships_instance()->is_user_member( $user_id, $membership_plan, false, $cache );
}


/**
 * Determines if user is an active member of either any or a particular membership plan.
 *
 * @since 1.0.0
 *
 * @param int|\WP_User|null $user optional, defaults to current user
 * @param int|string|\WC_Memberships_Membership_Plan|null $plan membership plan slug, ID or object, if null (default) checks membership of any plan
 * @param bool $cache whether to use cached results (default true)
 * @return bool
 */
function wc_memberships_is_user_active_member( $user = null, $plan = null, $cache = true ) {
	return wc_memberships()->get_user_memberships_instance()->is_user_active_member( $user, $plan, $cache );
}


/**
 * Determines if user is a delayed member of either any or a particular membership plan.
 *
 * @since 1.7.0
 *
 * @param int|\WP_User|null $user optional, defaults to current user
 * @param int|string|\WC_Memberships_Membership_Plan|null $plan membership plan slug, ID or object, if null (default) checks membership of any plan
 * @param bool $cache whether to use cached results (default true)
 * @return bool
 */
function wc_memberships_is_user_delayed_member( $user = null, $plan = null, $cache = true ) {
	return wc_memberships()->get_user_memberships_instance()->is_user_delayed_member( $user, $plan, $cache );
}


/**
 * Determines if user is a member with either active or delayed status of either a particular or any membership plan.
 *
 * @since 1.8.0
 *
 * @param int|\WP_User|null $user optional, defaults to current user
 * @param int|string|\WC_Memberships_Membership_Plan|null $plan membership plan slug, ID or object, if null (default) checks membership of any plan
 * @param bool $cache whether to use cached results (default true)
 * @return bool
 */
function wc_memberships_is_user_active_or_delayed_member( $user = null, $plan = null, $cache = true ) {
	return wc_memberships()->get_user_memberships_instance()->is_user_active_or_delayed_member( $user, $plan, $cache );
}


/**
 * Checks if a product is accessible (viewable or purchasable).
 *
 * TODO for now `$target` only supports a simple array like  'post' => id  or  'product' => id  - in future we could extend this to take arrays or different/multiple args {FN 2016-04-26}
 *
 * @since 1.4.0
 *
 * @param int $user_id user to check if has access
 * @param string|array type of capabilities: 'view', 'purchase' (products only)
 * @param array $target associative array of content type and content id to access to
 * @param int|string UTC timestamp to compare for content access (optional, defaults to now)
 * @return bool|null
 */
function wc_memberships_user_can( $user_id, $action, $target, $when = '' ) {
	return wc_memberships()->get_capabilities_instance()->user_can( $user_id, $action, $target, $when );
}


/**
 * Creates a new user membership.
 *
 * Returns a new user membership object on success which can then be used to add additional data.
 * Throws an exception on failure.
 *
 * @since 1.3.0
 *
 * @param array $args array of arguments
 * @param string $action either 'create' or 'renew' -- when in doubt, use 'create'
 * @throws \SkyVerge\WooCommerce\PluginFramework\v5_7_1\SV_WC_Plugin_Exception may create an exception on errors
 * @return \WC_Memberships_User_Membership
 */
function wc_memberships_create_user_membership( $args = array(), $action = 'create' ) {
	return wc_memberships()->get_user_memberships_instance()->create_user_membership( $args, $action );
}
