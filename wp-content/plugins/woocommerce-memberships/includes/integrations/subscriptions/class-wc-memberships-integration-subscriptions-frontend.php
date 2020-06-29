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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Frontend integration class for WooCommerce Subscriptions.
 *
 * @since 1.6.0
 */
class WC_Memberships_Integration_Subscriptions_Frontend {


	/**
	 * Adds frontend hooks.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// restrict subscription product or variation purchase if rules/dripping apply
		add_filter( 'woocommerce_subscription_is_purchasable',           array( $this, 'subscription_is_purchasable' ), 10, 2 );
		add_filter( 'woocommerce_subscription_variation_is_purchasable', array( $this, 'subscription_is_purchasable' ), 10, 2 );

		// frontend UI hooks
		add_filter( 'wc_memberships_members_area_my-memberships_actions',           array( $this, 'my_membership_actions' ), 10, 2 );
		add_filter( 'wc_memberships_members_area_my-membership-details_actions',    array( $this, 'my_membership_actions' ), 10, 2 );
		add_filter( 'wc_memberships_members_area_my_membership_settings',           array( $this, 'my_membership_details' ), 10, 2 );
		add_filter( 'wc_memberships_my_memberships_column_names',                   array( $this, 'my_memberships_subscriptions_columns' ), 20 );
		add_action( 'wc_memberships_my_memberships_column_membership-next-bill-on', array( $this, 'output_subscription_columns' ), 20 );
		add_filter( 'wc_memberships_members_area_my_membership_details',            array( $this, 'add_members_area_details' ), 10, 2 );
	}


	/**
	 * Restricts product purchasing based on restriction rules.
	 *
	 * @see \WC_Memberships_Restrictions::product_is_purchasable()
	 *
	 * @internal
	 *
	 * @since 1.6.5
	 *
	 * @param bool $purchasable whether the subscription product is purchasable
	 * @param \WC_Product_Subscription|\WC_Product_Subscription_Variation $subscription_product the subscription product
	 * @return bool
	 */
	public function subscription_is_purchasable( $purchasable, $subscription_product ) {
		return wc_memberships()->get_restrictions_instance()->get_products_restrictions_instance()->product_is_purchasable( $purchasable, $subscription_product );
	}


	/**
	 * Removes the cancel action from memberships tied to a subscription.
	 * Adds a link to "View Billing" if tied to a subscription.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $actions
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership post object
	 * @return array
	 */
	public function my_membership_actions( $actions, \WC_Memberships_User_Membership $user_membership ) {

		$integration = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();

		if ( $integration && wc_memberships_is_user_membership_linked_to_subscription( $user_membership ) ) {

			$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );
			$subscription    = $integration->get_subscription_from_membership( $user_membership->get_id() );

			if ( $subscription instanceof \WC_Subscription ) {

				// a Membership tied to a Subscription can only be cancelled by cancelling the associated Subscription
				unset( $actions['cancel'] );

				if ( $user_membership->can_be_renewed_early() ) {
					// allow a renew button if the Subscription-tied Membership can be renewed early
					$actions['renew'] = [
						'url'  => $user_membership->get_renew_membership_url(),
						'name' => __( 'Renew', 'woocommerce-memberships' ),
					];
				} elseif ( ! $user_membership->can_be_renewed() ) {
					// normally, otherwise we want to make sure that no renew button is displayed, as subscribers can renew from the Subscriptions dashboard section
					unset( $actions['renew'] );
				}

				// finally, add an action to view the billing record
				$actions['view-subscription'] = [
					'url'  => $subscription->get_view_order_url(),
					'name' => __( 'View Billing', 'woocommerce-memberships' ),
				];
			}
		}

		return $actions;
	}


	/**
	 * Adds subscriptions details to memberships settings members area section.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $settings associative array of settings and user membership details data
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership
	 * @return array
	 */
	public function my_membership_details( $settings, $user_membership ) {

		$integration  = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();

		if (    $integration
		     && wc_memberships_is_user_membership_linked_to_subscription( $user_membership )
		     && ( $subscription = $integration->get_subscription_from_membership( $user_membership->get_id() ) ) ) {

			$next_payment = $subscription ? $subscription->get_time( 'next_payment' ) : null;

			if ( $subscription && ! empty( $next_payment ) ) {
				$next_payment = date_i18n( wc_date_format(), $next_payment );
			} else {
				$next_payment = esc_html__( 'N/A', 'woocommerce-memberships' );
			}

			$subscription_item_key = 'next-payment-date';
			$subscription_data     = array(
				'label'   => __( 'Next Payment Date', 'woocommerce-memberships' ),
				'content' => $next_payment,
				'class'   => 'my-membership-setting-user-membership-next-payment-date',
			);

			if ( array_key_exists( 'expires', $settings ) ) {
				$settings = Framework\SV_WC_Helper::array_insert_after( $settings, 'expires', array( $subscription_item_key => $subscription_data ) );
			} else {
				$settings[ $subscription_item_key ] = $subscription_data;
			}
		}

		return $settings;
	}


	/**
	 * Adds subscription column headers in My Memberships on My Account page.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $columns
	 * @return array
	 */
	public function my_memberships_subscriptions_columns( $columns ) {

		return Framework\SV_WC_Helper::array_insert_after( $columns, 'membership-status', array( 'membership-next-bill-on' => __( 'Next Bill On', 'woocommerce-memberships' ) ) );
	}


	/**
	 * Displays subscription columns in My Memberships section.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership a membership that may be tied to a subscription
	 */
	public function output_subscription_columns( $user_membership ) {

		$next_bill_on = null;

		if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

			$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );

			if ( $user_membership->has_subscription() ) {
				$next_bill_on = $user_membership->get_next_bill_on_local_date( wc_date_format() );
			}
		}

		echo null === $next_bill_on ? esc_html__( 'N/A', 'woocommerce-memberships' ) : $next_bill_on;
	}


	/**
	 * Adds a "next bill on" information in the members area membership details section.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $details associative array of membership details
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership a user membership that may be tied to a Subscription
	 * @return array associative array
	 */
	public function add_members_area_details( $details, $user_membership ) {

		if ( $next_bill_on = $user_membership instanceof \WC_Memberships_Integration_Subscriptions_User_Membership ? $user_membership->get_next_bill_on_local_date( wc_date_format() ) : null ) {

			$insert_after = null;
			$next_bill_on = array(
				'next-bill-on' => array(
					'label'   => __( 'Next Bill On', 'woocommerce-memberships' ),
					'content' => $next_bill_on,
					'class'   => 'my-membership-detail-user-membership-next-bill-on',
				),
			);

			if ( array_key_exists( 'expires', $details ) ) {
				$insert_after = 'expires';
			} elseif ( array_key_exists( 'start-date', $details ) ) {
				$insert_after = 'start-date';
			} elseif ( array_key_exists( 'status', $details ) ) {
				$insert_after = 'status';
			}

			if ( $insert_after ) {
				$details = Framework\SV_WC_Helper::array_insert_after( $details, $insert_after, $next_bill_on );
			} else {
				$details = array_merge( $next_bill_on, $details );
			}
		}

		return $details;
	}


}
