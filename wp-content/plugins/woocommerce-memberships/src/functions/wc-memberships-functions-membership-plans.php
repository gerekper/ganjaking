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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Main function for returning a membership plan.
 *
 * @since 1.0.0
 *
 * @param int|string|\WP_Post|\WC_Memberships_Membership_Plan $membership_plan post object, ID or slug of the membership plan
 * @param int|\WC_Memberships_User_Membership|\null optional, used in filter hook
 * @return \WC_Memberships_Membership_Plan|\WC_Memberships_Integration_Subscriptions_Membership_Plan|false
 */
function wc_memberships_get_membership_plan( $membership_plan = null, $user_membership = null ) {
	return wc_memberships()->get_plans_instance()->get_membership_plan( $membership_plan, $user_membership );
}


/**
 * Main function for returning all available membership plans.
 *
 * @since 1.0.0
 *
 * @param array $args optional array of arguments, same as for `get_posts()`
 * @return \WC_Memberships_Membership_Plan[]|\WC_Memberships_Integration_Subscriptions_Membership_Plan[]
 */
function wc_memberships_get_membership_plans( $args = array() ) {
	return wc_memberships()->get_plans_instance()->get_membership_plans( $args );
}


/**
 * Main function for returning all available free membership plans.
 *
 * These are plans where access is granted upon user account registration.
 *
 * @since 1.7.0
 *
 * @param array $args optional array of arguments, same as for `get_posts()`
 * @return \WC_Memberships_Membership_Plan[]|\WC_Memberships_Integration_Subscriptions_Membership_Plan[]
 */
function wc_memberships_get_free_membership_plans( $args = array() ) {
	return wc_memberships()->get_plans_instance()->get_free_membership_plans( $args );
}


/**
 * Returns the members area sections.
 *
 * @since 1.4.0
 *
 * @param int|string $membership_plan optional: membership plan ID for filtering purposes
 * @return array associative array
 */
function wc_memberships_get_members_area_sections( $membership_plan = '' ) {

	/**
	 * Filters the available choices for the members area sections of a membership plan.
	 *
	 * @since 1.4.0
	 *
	 * @param array $members_area_sections associative array with members area id and label of each section
	 * @param int|string $membership_plan optional, the current membership plan, might be empty
	 */
	return apply_filters( 'wc_membership_plan_members_area_sections', array(
		'my-membership-content'   => __( 'Content', 'woocommerce-memberships' ),
		'my-membership-products'  => __( 'Products', 'woocommerce-memberships' ),
		'my-membership-discounts' => __( 'Discounts', 'woocommerce-memberships' ),
		'my-membership-notes'     => __( 'Notes', 'woocommerce-memberships' ),
		'my-membership-details'   => __( 'Manage', 'woocommerce-memberships' ),
	), $membership_plan );
}
