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

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * AJAX integration class for WooCommerce Subscriptions.
 *
 * @since 1.6.0
 */
class WC_Memberships_Integration_Subscriptions_Ajax {


	/**
	 * Adds AJAX callbacks.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// admin-only callbacks:
		add_action( 'wp_ajax_wc_memberships_membership_plan_has_subscription_product', array( $this, 'ajax_plan_has_subscription' ) );
		add_action( 'wp_ajax_wc_memberships_delete_membership_and_subscription',       array( $this, 'delete_membership_with_subscription' ) );
		add_action( 'wp_ajax_wc_memberships_edit_membership_subscription_link',        array( $this, 'search_subscriptions_by_id_or_customers' ) );
	}


	/**
	 * Checks if a plan has a subscription product.
	 *
	 * Responds with an array of subscription products, if any.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function ajax_plan_has_subscription() {

		check_ajax_referer( 'check-subscriptions', 'security' );

		$product_ids = isset( $_REQUEST['product_ids'] ) && is_array( $_REQUEST['product_ids'] ) ? array_map( 'absint', $_REQUEST['product_ids'] ) : null;

		if ( empty( $product_ids ) ) {
			die();
		}

		$subscription_products = array();

		foreach ( $product_ids as $product_id ) {

			if ( \WC_Subscriptions_Product::is_subscription( $product_id ) ) {
				$subscription_products[] = (int) $product_id;
			}
		}

		wp_send_json( $subscription_products );
	}


	/**
	 * Deletes a membership with its associated subscription.
	 *
	 * Ajax callback to delete both a membership and a subscription from the user memberships admin edit screen.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function delete_membership_with_subscription() {

		check_ajax_referer( 'delete-user-membership-with-subscription', 'security' );

		if ( isset( $_POST['user_membership_id'], $_POST['subscription_id'] ) ) {

			$subscription_id    = (int) $_POST['subscription_id'];
			$user_membership_id = (int) $_POST['user_membership_id'];

			if ( $user_membership = wc_memberships_get_user_membership( $user_membership_id ) ) {

				$integration  = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
				$subscription = $integration ? $integration->get_subscription_from_membership( $user_membership->get_id() ) : null;

				if ( $subscription instanceof \WC_Subscription && $subscription_id === (int) $subscription->get_id() ) {

					wp_send_json_success( [
						'delete-subscription'    => wp_delete_post( $subscription_id ),
						'delete-user-membership' => wp_delete_post( $user_membership_id ),
					] );
				}
			}
		}

		die();
	}


	/**
	 * Returns Subscriptions by looking at the Subscription ID or at the Subscription's holder name.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 */
	public function search_subscriptions_by_id_or_customers() {

		// security check
		check_ajax_referer( 'edit-membership-subscription-link', 'security' );

		// grab the search term
		$keyword = isset( $_GET['term'] ) ? urldecode( stripslashes( strip_tags( $_GET['term'] ) ) ) : '';

		// abort if void
		if ( empty( $keyword ) ) {
			die;
		}

		if ( is_numeric( $keyword ) ) {

			// query for subscription id
			$query_args = array(
				'p' => (int) $keyword,
			);

		} else {

			// query for subscription holder name
			$query_args = array(
				'meta_query' => array(
					array(
						'key'     => '_billing_first_name',
						'value'   => $keyword,
						'compare' => 'LIKE',
					),
					array(
						'key'     => '_billing_last_name',
						'value'   => $keyword,
						'compare' => 'LIKE',
					),
					'relation' => 'OR',
				),
			);
		}

		$integration   = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
		$results       = $integration->get_subscriptions_ids( $query_args );
		$subscriptions = array();

		if ( ! empty( $results ) ) {

			foreach ( $results as $subscription_id ) {

				if ( $subscription = wcs_get_subscription( $subscription_id ) ) {

					$subscriptions[ $subscription_id ] = $integration->get_formatted_subscription_id_holder_name( $subscription );
				}
			}
		}

		wp_send_json( $subscriptions );
	}


}
