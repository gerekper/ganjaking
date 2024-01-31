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
 * Checks if the product (or current product) has any member discounts.
 *
 * @since 1.0.0
 *
 * @param int|\WP_Post|\WC_Product|null $the_product product ID: optional, defaults to current product
 * @return bool
 */
function wc_memberships_product_has_member_discount( $the_product = null ) {
	global $product;

	if ( is_numeric( $the_product ) || $the_product instanceof \WP_Post ) {
		$the_product = wc_get_product( $the_product );
	}elseif ( null === $the_product ) {
		$the_product = $product;
	}

	if ( ! $the_product instanceof \WC_Product || wc_memberships_is_product_excluded_from_member_discounts( $the_product ) ) {
		$has_member_discount = false;
	} else {
		$has_member_discount = wc_memberships()->get_rules_instance()->product_has_purchasing_discount_rules( $the_product->get_id() );
	}

	return $has_member_discount;
}


/**
 * Checks if a product is set to be excluded from member discount rules.
 *
 * @since 1.7.0
 *
 * @param int|\WC_Product $product the product object or ID
 * @return bool if false, discounts may still apply depending on the rules and member status
 */
function wc_memberships_is_product_excluded_from_member_discounts( $product ) {
	return wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $product );
}


/**
 * Checks if a user is eligible for member discount for the current product.
 *
 * @since 1.0.0
 *
 * @param int|\WC_Product|null $product optional, product id or object, if not set will attempt to get the current one
 * @param int|\WP_User|null $member optional, user to check for (defaults to current logged in user)
 * @return bool
 */
function wc_memberships_user_has_member_discount( $product = null, $member = null ) {
	return wc_memberships()->get_member_discounts_instance()->user_has_member_discount( $product, $member );
}



/**
 * Echoes the member discount badge for the loop.
 *
 * @since 1.0.0
 */
function wc_memberships_show_product_loop_member_discount_badge() {
	wc_get_template( 'loop/member-discount-badge.php' );
}


/**
 * Echoes the member discount badge for the single product page.
 *
 * @since 1.0.0
 */
function wc_memberships_show_product_member_discount_badge() {
	wc_get_template( 'single-product/member-discount-badge.php' );
}


/**
 * Returns the member discount badge HTML content.
 *
 * @since 1.6.4
 *
 * @param \WC_Product $product the product object to output a badge for (passed to filter)
 * @param bool $variation whether to output a discount badge for a product variation (default false)
 * @return string HTML
 */
function wc_memberships_get_member_discount_badge( $product, $variation = false ) {
	return wc_memberships()->get_member_discounts_instance()->get_member_discount_badge( $product, $variation );
}


/**
 * Returns the product discount for a member.
 *
 * @since 1.4.0
 *
 * @param \WC_Memberships_User_Membership $user_membership the user membership object
 * @param int|\WC_Product $product the product object or id to get discount for
 * @param bool $formatted whether to return a formatted amount or a numerical string (default false, return a discount as a fixed amount or a percentage amount)
 * @return string HTML or empty string
 */
function wc_memberships_get_member_product_discount( $user_membership, $product, $formatted = false ) {

	$plan     = $user_membership->get_plan();
	$discount = '';

	if ( $plan ) {
		$discount = $formatted ? $plan->get_formatted_product_discount( $product ) : $plan->get_product_discount( $product );
	}

	return $discount;
}
