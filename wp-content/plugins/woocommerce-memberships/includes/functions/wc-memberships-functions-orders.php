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
 * Returns memberships granted access meta from order.
 *
 * @since 1.7.0
 *
 * @param int|\WC_Order|\WC_Order_Refund $order order object
 * @return false|array false if order didn't grant access to a membership or associative array of membership ids and granting access details
 */
function wc_memberships_get_order_access_granted_memberships( $order ) {

	if ( is_numeric( $order ) ) {
		$order = wc_get_order( (int) $order );
	}

	$meta = $order instanceof \WC_Order || $order instanceof \WC_Order_Refund ? $order->get_meta( '_wc_memberships_access_granted' ) : false;

	return ! is_array( $meta ) ? []: $meta;
}


/**
 * Checks whether an order has granted access to a plan or a user membership.
 *
 * @since 1.7.0
 *
 * @param int|\WC_Order $order the order id or object
 * @param array $args an associative array either with an user membership or a membership plan id or object: or example: array( 'user_membership' => 123 ) or array( 'membership_plan' => $plan_object )
 * @return bool
 */
function wc_memberships_has_order_granted_access( $order, $args ) {

	$has_granted                = false;
	$access_granted_memberships = wc_memberships_get_order_access_granted_memberships( $order );

	if ( ! empty( $access_granted_memberships ) ) {

		if ( isset( $args['user_membership'] ) ) {

			$user_membership = $args['user_membership'];

			if ( is_numeric( $user_membership ) ) {
				$user_membership = wc_memberships_get_user_membership( (int) $user_membership );
			}

			if ( $user_membership instanceof \WC_Memberships_User_Membership ) {
				$has_granted = array_key_exists( $user_membership->get_id(), $access_granted_memberships );
			}

		} elseif ( isset( $args['membership_plan'] ) ) {

			$membership_plan    = $args['membership_plan'];
			$membership_plan_id = null;

			if ( is_numeric( $membership_plan ) ) {
				$membership_plan_id = (int) $membership_plan;
			} elseif ( $membership_plan instanceof \WC_Memberships_Membership_Plan ) {
				$membership_plan_id = $membership_plan->get_id();
			}

			if ( $membership_plan_id && ( $user_membership_ids = array_keys( $access_granted_memberships ) ) ) {

				foreach ( $user_membership_ids as $user_membership_id ) {

					$user_membership = wc_memberships_get_user_membership( $user_membership_id );

					if ( $user_membership && $membership_plan_id === $user_membership->get_plan_id() ) {
						$has_granted = true;
						break;
					}
				}
			}
		}
	}

	return $has_granted;
}


/**
 * Sets memberships an order granted access to meta.
 *
 * @since 1.7.0
 *
 * @param int|\WC_Order $order the order to set the flag for
 * @param int|\WC_Memberships_User_Membership $user_membership user membership being granted access from order
 * @param array $args optional: additional details to record in the same array meta
 */
function wc_memberships_set_order_access_granted_membership( $order, $user_membership, $args = array() ) {

	if ( is_numeric( $order ) ) {
		$order = wc_get_order( (int) $order );
	}

	if ( $order instanceof \WC_Order ) {

		if ( is_numeric( $user_membership ) ) {
			$user_membership = wc_memberships_get_user_membership( (int) $user_membership );
		}

		if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

			$user_membership_id = $user_membership->get_id();
			$meta               = wc_memberships_get_order_access_granted_memberships( $order );
			$details            = wp_parse_args( $args, [
				'already_granted'       => 'yes',
				'granting_order_status' => $order->get_status(),
			] );

			$meta[ $user_membership_id ] = $details;

			$order->update_meta_data('_wc_memberships_access_granted', $meta );
			$order->save_meta_data();
		}
	}
}


/**
 * Checks if purchasing products that grant access to a membership in the same order allow to extend the length of the membership.
 *
 * @since 1.6.0
 *
 * @return bool
 */
function wc_memberships_cumulative_granting_access_orders_allowed() {
	return 'yes' === get_option( 'wc_memberships_allow_cumulative_access_granting_orders' );
}


/**
 * Returns order products that grant access to a membership plan.
 *
 * @since 1.7.0
 *
 * @param \WC_Memberships_Membership_Plan $plan Membership plan to check for access
 * @param int|\WC_Order|string $order WC_Order instance or ID, can be empty string if $order_items are provided
 * @param array $order_items array of order items, if empty will try to get those from $order
 * @return array
 */
function wc_memberships_get_order_access_granting_product_ids( $plan, $order, $order_items = array() ) {

	$access_granting_product_ids = array();

	if ( empty( $order_items ) ) {

		$order = is_numeric( $order ) ? wc_get_order( (int) $order ) : $order;

		if ( $order instanceof \WC_Order ) {
			$order_items = $order->get_items();
		}
	}

	if ( ! empty( $order_items ) ) {

		// loop over items to collect products and their quantity
		foreach ( $order_items as $key => $item ) {

			// product grants access
			if ( $plan->has_product( $item['product_id'] ) ) {
				$access_granting_product_ids[ $item['product_id'] ]   = max( 1, (int) $item['qty'] );
			}

			// variation grants access
			if ( isset( $item['variation_id'] ) && $item['variation_id'] && $plan->has_product( $item['variation_id'] ) ) {
				$access_granting_product_ids[ $item['variation_id'] ] = max( 1, (int) $item['qty'] );
			}
		}

		// check if we have access granting products in order
		if ( ! empty( $access_granting_product_ids ) ) {

			// by default we get the first product that grant access,
			// unless an option is set, which might trigger a Memberships
			// access length extension
			if ( wc_memberships_cumulative_granting_access_orders_allowed() ) {

				$product_ids = array();

				foreach ( $access_granting_product_ids as $product_id => $product_quantity ) {

					// get any product id that may grant access
					// but consider also the quantity, which we add up
					// as extra id of the same value
					for ( $i = 1; $i <= $product_quantity; $i++ ) {

						$product_ids[] = $product_id;
					}
				}

			} else {

				// standard behavior, get the first id, disregard quantity
				reset( $access_granting_product_ids );
				$product_ids = key( $access_granting_product_ids );
			}

			/**
			 * Filter the product ID that grants access to the membership plan via purchase
			 *
			 * Multiple products from a single order can grant access to a membership plan
			 * Default behavior is to use the first product that grants access,
			 * unless overridden by option in settings and/or using this filter
			 *
			 * @since 1.0.0
			 * @param int|int[] $product_ids Product id or array of product ids that grant access (may be non unique)
			 * @param int[] $access_granting_product_ids Array of product ids that can grant access to this plan
			 * @param \WC_Memberships_Membership_Plan $plan Membership plan access will be granted to
			 */
			$access_granting_product_ids = (array) apply_filters( 'wc_memberships_access_granting_purchased_product_id', $product_ids, array_keys( $access_granting_product_ids ), $plan );
		}
	}

	return $access_granting_product_ids;
}


/**
 * Returns HTML for the thank you page / order emails to display links to relevant members areas.
 *
 * @since 1.8.4
 *
 * @param int|\WC_order $order_id the order ID or order object that granted access
 * @return string $message the HTML for the thank you message
 */
function wc_memberships_get_order_thank_you_links( $order_id ) {

	$message = '';

	if ( $order_id instanceof WC_Order ) {
		$order_id = $order_id->get_id();
	}

	if ( is_numeric( $order_id ) ) {

		$memberships = wc_memberships_get_order_access_granted_memberships( $order_id );

		if ( ! empty( $memberships ) ) {

			$memberships_with_members_area = [];

			foreach ( $memberships as $membership_id => $data ) {

				if ( 'yes' === $data['already_granted'] ) {

					$user_membership       = wc_memberships_get_user_membership( (int) $membership_id );
					$membership_plan       = $user_membership ? $user_membership->get_plan() : null;
					$members_area_sections = $membership_plan ? $membership_plan->get_members_area_sections() : array();

					if ( ! empty( $members_area_sections ) ) {
						$memberships_with_members_area[ $user_membership->get_plan_id() ] = $user_membership->get_plan()->get_name();
					}
				}
			}

			if ( ! empty( $memberships_with_members_area ) ) {

				$message = '<p>' . __( 'Thanks for purchasing a membership!', 'woocommerce-memberships' );

				if ( 1 === count( $memberships_with_members_area ) ) {

					/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
					$message .= ' ' . sprintf( __( 'You can view more details about your membership from %1$syour account%2$s.', 'woocommerce-memberships' ), '<a href="' . esc_url( wc_memberships_get_members_area_url( key( $memberships_with_members_area ) ) ) . '">', '</a>' );

				} else {

					$message .= ' ' . __( 'You can view details for each membership in your account:', 'woocommerce-memberships' );
					$message .= '<ul>';

					foreach( $memberships_with_members_area as $plan_id => $plan_name ) {
						$message .= '<li><a href="' . esc_url( wc_memberships_get_members_area_url( $plan_id ) ) . '">' . esc_html( $plan_name ) . '</a></li>';
					}

					$message .= '</ul>';
				}

				$message .= '</p>';
			}

			/**
			 * Filters the thank you page / email message when a membership with members area sections is purchased.
			 *
			 * @since 1.8.4
			 * @since 1.17.3 added the $memberships argument
			 *
			 * @param string $message the thank you message HTML
			 * @param int $order_id the order ID from the purchase
			 * @param array $memberships associative array of membership IDs and granting access details found for the given order
			 */
			return (string) apply_filters( 'woocommerce_memberships_thank_you_message', $message, $order_id, $memberships );
		}
	}

	return $message;
}

