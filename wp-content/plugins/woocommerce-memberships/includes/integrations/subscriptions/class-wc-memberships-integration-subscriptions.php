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
 * Integration class for WooCommerce Subscriptions 2.0+.
 *
 * @since 1.6.0
 */
class WC_Memberships_Integration_Subscriptions {


	/** @var null|\WC_Memberships_Integration_Subscriptions_Lifecycle instance */
	protected $lifecycle;

	/** @var \WC_Memberships_Integration_Subscriptions_Membership_Plans instance */
	protected $membership_plans;

	/** @var \WC_Memberships_Integration_Subscriptions_User_Memberships instance */
	protected $user_memberships;

	/** @var null|\WC_Memberships_Integration_Subscriptions_Admin instance */
	protected $admin;

	/** @var null|\WC_Memberships_Integration_Subscriptions_Frontend instance */
	protected $frontend;

	/** @var null|\WC_Memberships_Integration_Subscriptions_Ajax instance */
	protected $ajax;

	/** @var null|\WC_Memberships_Integration_Subscriptions_Free_Trial instance */
	protected $free_trial;

	/** @var null|\WC_Memberships_Integration_Subscriptions_Discounts instance */
	protected $discounts;

	/** @var \WC_Memberships_Integration_Subscriptions_Utilities instance */
	private $utilities;

	/** @var \WC_Memberships_Integration_Subscriptions_CLI instance */
	protected $cli;

	/** @var array memoization of membership plans that have a subscription */
	private $has_membership_plan_subscription = array();

	/** @var array memoization of user memberships that are linked to a subscription */
	private $has_user_membership_subscription = array();


	/**
	 * Loads Subscriptions integration components.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// load integration files
		$this->includes();

		// handle Subscription switches
		add_action( 'woocommerce_subscriptions_switched_item', array( $this, 'handle_subscription_switches' ), 10, 3 );
		// handle Subscription removals
		add_action( 'wcs_user_removed_item',                   array( $this, 'handle_subscription_removal' ), 10, 2 );
		add_action( 'wcs_user_readded_item',                   array( $this, 'handle_subscription_removal' ), 10, 2 );

		// handle Membership status changes
		add_action( 'wc_memberships_user_membership_status_changed', array( $this, 'handle_user_membership_status_change' ), 10, 3 );

		// handle Subscriptions events
		add_action( 'woocommerce_subscription_status_updated', array( $this, 'handle_subscription_status_change' ), 10, 3 );
		add_action( 'woocommerce_subscription_date_updated',   array( $this, 'update_related_membership_dates' ), 10, 3 );
		add_action( 'trashed_post',                            array( $this, 'cancel_related_membership' ) );
		add_action( 'delete_post',                             array( $this, 'cancel_related_membership' ) );

		// handle linking/unlinking: clear cached value when a new link is set or an existing one is removed
		add_action( 'wc_memberships_user_membership_linked_to_subscription',     array( $this, 'prune_membership_link_cache' ), 1 );
		add_action( 'wc_memberships_user_membership_unlinked_from_subscription', array( $this, 'prune_membership_link_cache' ), 1 );

		// export additional data upon request
		add_filter( 'wc_memberships_privacy_export_user_membership_personal_data', array( $this, 'export_user_membership_personal_data' ), 1, 2 );
	}


	/**
	 * Loads integration files and init object instances.
	 *
	 * @since 1.7.0
	 */
	private function includes() {

		// load helper functions
		require_once( wc_memberships()->get_plugin_path() . '/includes/integrations/subscriptions/functions/wc-memberships-integration-subscriptions-functions.php' );

		// handler of Membership Plans tied to Subscriptions
		$this->membership_plans = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-membership-plans.php', 'WC_Memberships_Integration_Subscriptions_Membership_Plans' );

		// handler of User Memberships tied to Subscriptions
		$this->user_memberships = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-user-memberships.php', 'WC_Memberships_Integration_Subscriptions_User_Memberships' );

		// handle free trials for Memberships
		$this->free_trial = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-free-trial.php', 'WC_Memberships_Integration_Subscriptions_Free_Trial' );

		// handle discounts
		$this->discounts = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-discounts.php', 'WC_Memberships_Integration_Subscriptions_Discounts' );

		if ( is_admin() ) {
			// admin methods and hooks
			$this->admin = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-admin.php', 'WC_Memberships_Integration_Subscriptions_Admin' );
		} else {
			// frontend methods and hooks
			$this->frontend = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-frontend.php', 'WC_Memberships_Integration_Subscriptions_Frontend' );
		}

		// handle AJAX interactions between the two extensions
		$this->ajax = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-ajax.php', 'WC_Memberships_Integration_Subscriptions_Ajax' );

		// extensions lifecycle (activation, deactivation, upgrade, etc.)
		$this->lifecycle = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-lifecycle.php', 'WC_Memberships_Integration_Subscriptions_Lifecycle' );

		// extend WP CLI support
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$this->cli = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-cli.php', 'WC_Memberships_Integration_Subscriptions_CLI' );
		}

		// utilities (batch/background jobs)
		$this->utilities = wc_memberships()->load_class( '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-utilities.php', 'WC_Memberships_Integration_Subscriptions_Utilities' );
	}


	/**
	 * Returns the Subscriptions-tied Membership Plans handler instance.
	 *
	 * @since 1.8.0
	 *
	 * @return WC_Memberships_Integration_Subscriptions_Membership_Plans
	 */
	public function get_plans_instance() {
		return $this->membership_plans;
	}


	/**
	 * Returns the Subscriptions-tied User Memberships handler instance.
	 *
	 * @since 1.8.0
	 *
	 * @return WC_Memberships_Integration_Subscriptions_User_Memberships
	 */
	public function get_user_memberships_instance() {
		return $this->user_memberships;
	}


	/**
	 * Returns the Subscriptions Admin integration instance.
	 *
	 * @since 1.6.0
	 *
	 * @return null|\WC_Memberships_Integration_Subscriptions_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Returns Subscriptions Frontend integration instance.
	 *
	 * @since 1.6.0
	 *
	 * @return null|\WC_Memberships_Integration_Subscriptions_Frontend
	 */
	public function get_frontend_instance() {
		return $this->frontend;
	}


	/**
	 * Returns the Subscriptions Ajax integration instance.
	 *
	 * @since 1.6.0
	 *
	 * @return null|\WC_Memberships_Integration_Subscriptions_Ajax
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Returns the Subscriptions Lifecycle integration instance.
	 *
	 * @since 1.6.0
	 *
	 * @return null|\WC_Memberships_Integration_Subscriptions_Lifecycle
	 */
	public function get_lifecycle_instance() {
		return $this->lifecycle;
	}


	/**
	 * Returns the Subscriptions Free Trial integration instance.
	 *
	 * @since 1.6.0
	 *
	 * @return null|\WC_Memberships_Integration_Subscriptions_Free_Trial
	 */
	public function get_free_trial_instance() {
		return $this->free_trial;
	}


	/**
	 * Returns the Subscriptions Discounts integration instance.
	 *
	 * @since 1.6.0
	 *
	 * @return null|\WC_Memberships_Integration_Subscriptions_Discounts
	 */
	public function get_discounts_instance() {
		return $this->discounts;
	}


	/**
	 * Returns the Subscriptions Utilities instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Memberships_Integration_Subscriptions_Utilities
	 */
	public function get_utilities_instance() {
		return $this->utilities;
	}


	/**
	 * Returns the Subscriptions WP CLI integration instance.
	 *
	 * @since 1.7.0
	 *
	 * @return null|\WC_Memberships_Integration_Subscriptions_CLI
	 */
	public function get_cli_instance() {
		return $this->cli;
	}


	/**
	 * Handles Subscriptions status changes.
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Subscription $subscription Subscription being changed
	 * @param string $new_subscription_status Subscription status changing to
	 * @param string $old_subscription_status Subscription status changing from
	 */
	public function handle_subscription_status_change( \WC_Subscription $subscription, $new_subscription_status, $old_subscription_status ) {

		// get Memberships tied to the Subscription
		$user_memberships = $this->get_memberships_from_subscription( $subscription->get_id() );

		// bail out if no memberships found
		if ( ! $user_memberships ) {
			return;
		}

		// update status of found memberships
		foreach ( $user_memberships as $user_membership ) {
			$this->update_related_membership_status( $subscription, $user_membership, $new_subscription_status );
		}
	}


	/**
	 * Updates the related membership upon subscription date change.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Subscription $subscription
	 * @param string $date_type
	 * @param string $datetime
	 */
	public function update_related_membership_dates( \WC_Subscription $subscription, $date_type, $datetime ) {

		if ( 'end' === $date_type && ( $user_memberships = $this->get_memberships_from_subscription( $subscription->get_id() ) ) ) {

			foreach ( $user_memberships as $user_membership ) {

				$subscription_plan_id = $user_membership->get_plan_id();

				if ( $subscription_plan_id && $this->membership_plans->grant_access_while_subscription_active( $subscription_plan_id ) ) {

					$subscription_plan  = new \WC_Memberships_Integration_Subscriptions_Membership_Plan( $subscription_plan_id );

					if ( $subscription_plan->is_access_length_type( 'subscription' ) ) {
						// Membership length matches subscription length.
						$end_date = ! empty( $datetime ) ? $datetime : '';
					} else {
						// Membership length is decoupled from subscription length.
						$end_date = $subscription_plan->get_expiration_date( current_time( 'timestamp', true ), array( 'product_id' => $user_membership->get_product_id() ) );
					}

					$user_membership->set_end_date( $end_date );
				}
			}
		}
	}


	/**
	 * Cancels a User Membership when the connected Subscription is deleted.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param int $post_id ID of the Subscription post being deleted
	 */
	public function cancel_related_membership( $post_id ) {

		// bail out if the post being deleted is not a subscription
		if ( 'shop_subscription' !== get_post_type( $post_id ) ) {
			return;
		}

		$user_memberships = $this->get_memberships_from_subscription( $post_id );

		if ( ! $user_memberships ) {
			return;
		}

		// get pertaining note
		switch ( current_filter() ) {
			case 'trashed_post':
				$note = __( 'Membership cancelled because subscription was trashed.', 'woocommerce-memberships' );
			break;
			case 'delete_post':
				$note = __( 'Membership cancelled because subscription was deleted.', 'woocommerce-memberships' );
			break;
			default:
				$note = null;
			break;
		}

		// cancel Memberships and add a note
		foreach ( $user_memberships as $user_membership ) {

			$user_membership->cancel_membership( $note );

			// since the subscription is trashed or deleted, reset the subscription link cached value
			unset( $this->has_user_membership_subscription[ (int) $user_membership->get_id() ] );
		}
	}


	/**
	 * Updates related membership status based on the subscription status.
	 *
	 * @since 1.6.0
	 *
	 * @param array|\WC_Subscription $subscription
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership
	 * @param string $new_subscription_status Subscription status changing to
	 * @param string|void $note optional Membership note, if empty will be automatically set by status type
	 */
	public function update_related_membership_status( $subscription, $user_membership, $new_subscription_status, $note = '' ) {

		$plan_id = $user_membership->get_plan_id();

		if ( ! $plan_id || ! $this->membership_plans->grant_access_while_subscription_active( $plan_id ) ) {
			return;
		}

		switch ( $new_subscription_status ) {

			case 'active':

				$trial_end = $this->get_subscription_event_time( $subscription, 'trial_end' );

				if ( $trial_end && $trial_end > current_time( 'timestamp', true ) ) {

					if ( ! $note ) {
						$note = __( 'Membership free trial activated because subscription was re-activated.', 'woocommerce-memberships' );
					}

					$user_membership->update_status( 'free_trial', $note );

					// also update the free trial end date, which now might account for a paused interval
					$user_membership->set_free_trial_end_date( $trial_end );

				} else {

					if ( ! $note ) {
						$note = __( 'Membership activated because subscription was re-activated.', 'woocommerce-memberships' );
					}

					$user_membership->activate_membership( $note );
				}

			break;

			case 'on-hold':

				if ( ! $note ) {
					$note = __( 'Membership paused because subscription was put on-hold.', 'woocommerce-memberships' );
				}

				$user_membership->pause_membership( $note );

			break;

			case 'expired':

				$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );

				// if subscription is used as an installment plan,
				// when the billing cycle is over, the membership shouldn't expire
				if ( ! $user_membership->has_installment_plan() ) {

					if ( ! $note ) {
						$note = __( 'Membership expired because subscription expired.', 'woocommerce-memberships' );
					}

					$user_membership->update_status( 'expired', $note );

				} else {

					// to avoid accidental reactivations of limited memberships
					// after an installment plan has completed, we need to unlink
					// the subscription from the membership
					$this->unlink_membership( $user_membership->get_id(), $subscription );
				}

			break;

			case 'pending-cancel':

				// sanity check: do not send the membership to pending cancel
				// until a free trial is finally cancelled or period has ended
				if ( ! $user_membership->is_in_free_trial_period() ) {

					if ( ! $note ) {
						$note = __( 'Membership marked as pending cancellation because subscription is pending cancellation.', 'woocommerce-memberships' );
					}

					$user_membership->update_status( 'pending', $note );
				}

			break;

			case 'cancelled':

				/**
				 * Toggles whether cancelling a linked membership from a cancelled subscription.
				 *
				 * @since 1.10.1
				 *
				 * @param bool $cancel_membership whether to cancel the membership when the subscription is cancelled (default true)
				 * @param \WC_Memberships_Integration_Subscriptions_User_Membership $user_membership the subscription-tied membership
				 * @param \WC_Subscription $subscription the related subscription
				 */
				$cancel_membership = (bool) apply_filters( 'wc_memberships_cancel_subscription_linked_membership', true, $user_membership, $subscription );

				if ( $cancel_membership ) {

					if ( ! $note ) {
						$note = __( 'Membership cancelled because subscription was cancelled.', 'woocommerce-memberships' );
					}

					$user_membership->cancel_membership( $note );
				}

				// either way the Membership is unlinked from the Subscription at this point
				$this->unlink_membership( $user_membership->get_id(), $subscription );

			break;

			case 'trash':

				if ( ! $note ) {
					$note = __( 'Membership cancelled because subscription was trashed.', 'woocommerce-memberships' );
				}

				$user_membership->cancel_membership( $note );

				$this->unlink_membership( $user_membership->get_id(), $subscription );

			break;
		}
	}


	/**
	 * Handles user membership status changes with Subscriptions.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Memberships_Integration_Subscriptions_User_Membership|\WC_Memberships_User_Membership $user_membership
	 * @param string $old_status
	 * @param string $new_status
	 */
	public function handle_user_membership_status_change( $user_membership, $old_status, $new_status ) {

		// Save the new membership end date and remove the paused date.
		// This means that if the membership was paused, or, for example, paused and then cancelled, and then re-activated, the time paused will be added to the expiry date, so that the end date is pushed back.
		// Note: this duplicates the behavior in core, when status is changed to 'active'
		if ( 'free_trial' === $new_status && $user_membership->get_paused_date() ) {

			// sanity check, maybe reinitialize this object
			if ( ! $user_membership instanceof \WC_Memberships_Integration_Subscriptions_User_Membership ) {
				$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );
			}

			$user_membership->set_end_date( $user_membership->get_end_date() );
			$user_membership->delete_paused_date();
			$user_membership->delete_paused_intervals();

		// when reactivating a subscription, make sure an end date is cleared if the membership length is set to match the subscription's duration
		} elseif ( 'pending' === $old_status
		           && $this->is_membership_linked_to_subscription( $user_membership )
		           && in_array( $new_status, wc_memberships()->get_user_memberships_instance()->get_active_access_membership_statuses(), true ) ) {

			// sanity check, maybe reinitialize this object
			if ( ! $user_membership instanceof \WC_Memberships_Integration_Subscriptions_User_Membership ) {
				$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );
			}

			if ( $user_membership->get_plan()->is_access_length_type( [ 'unlimited', 'subscription' ] ) ) {
				$subscription     = $user_membership instanceof \WC_Memberships_Integration_Subscriptions_User_Membership ? $user_membership->get_subscription() : null;
				$subscription_end = $subscription ? $subscription->get_date( 'end' ) : null;
				$end_date         = $subscription_end ?: null;
			} else {
				$end_date = $user_membership->get_end_date();
			}

			$user_membership->set_end_date( $end_date );
		}
	}


	/**
	 * Handles subscription upgrades/downgrades (switch).
	 *
	 * Note: this is a callback for a hook which is available since Subscriptions 2.0.6+ only.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Subscription $subscription the subscription object
	 * @param array|\WC_Order_Item_Product $new_order_item the new order item (switching to)
	 * @param array $old_order_item the old order item (switching from)
	 */
	public function handle_subscription_switches( $subscription, $new_order_item, $old_order_item ) {

		$subscription_id  = $subscription ? $subscription->get_id() : null;
		$user_memberships = $subscription_id ? $this->get_memberships_from_subscription( $subscription_id ) : array();

		if ( ! empty( $user_memberships ) ) {

			$is_variation   = null;
			$old_product_id = 0;
			$new_product_id = 0;

			// Grab the variation ID for variable product upgrades, or the product_id for grouped product upgrades.
			// Even for grouped products there might still be a variation ID if products grouped are variable products.
			if ( ! empty( $old_order_item['variation_id'] ) ) {
				$is_variation   = true;
				$old_product_id = (int) $old_order_item['variation_id'];
			} elseif ( ! empty( $old_order_item['product_id'] ) ) {
				$is_variation   = false;
				$old_product_id = (int) $old_order_item['product_id'];
			}

			if ( ! empty( $new_order_item['variation_id'] ) ) {
				$new_product_id = (int) $new_order_item['variation_id'];
			} elseif ( ! empty( $new_order_item['product_id'] ) ) {
				$new_product_id = (int) $new_order_item['product_id'];
			}

			if ( $old_product_id > 0 && $old_product_id !== $new_product_id ) {

				// loop found memberships
				foreach ( $user_memberships as $user_membership ) {

					// handle upgrades/downgrades for variable products -- it is important to grab the variation ID if available
					if ( absint( $old_product_id ) === absint( $user_membership->get_product_id( $is_variation ) ) ) {

						$note = __( 'Membership cancelled because subscription was switched.', 'woocommerce-memberships' );

						$user_membership->cancel_membership( $note );

						// unlink the Membership from the Subscription
						$this->unlink_membership( $user_membership->get_id(), $subscription_id );
					}
				}
			}
		}
	}


	/**
	 * Handles subscription removals (and removal undo).
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array|\WC_Order_Item_Product $removed_item subscription product item removed from a subscription
	 * @param \WC_Subscription $subscription subscription object the item is removed from
	 */
	public function handle_subscription_removal( $removed_item, $subscription ) {

		$current_action     = current_action();
		$subscription_id    = $subscription ? $subscription->get_id() : null;
		$is_variation       = null;
		$removed_product_id = 0;

		if ( ! empty( $removed_item['variation_id'] ) ) {
			$is_variation       = true;
			$removed_product_id = $removed_item['variation_id'];
		} elseif ( ! empty( $removed_item['product_id'] ) ) {
			$is_variation       = false;
			$removed_product_id = $removed_item['product_id'];
		}

		if ( 'wcs_user_removed_item' === $current_action ) {

			$user_memberships = $subscription_id ? $this->get_memberships_from_subscription( $subscription_id ) : array();

			if ( $removed_product_id > 0 && ! empty( $user_memberships ) ) {

				foreach ( $user_memberships as $user_membership ) {

					if ( absint( $removed_product_id ) === absint( $user_membership->get_product_id( $is_variation ) ) ) {

						$note = __( 'Membership cancelled because subscription was removed.', 'woocommerce-memberships' );

						$user_membership->cancel_membership( $note );

						// unlink the Membership from the Subscription
						$this->unlink_membership( $user_membership->get_id(), $subscription_id );
					}
				}
			}

		// however, Subscriptions provides an "undo" mechanism in frontend, and we must account for this scenario
		}  elseif ( 'wcs_user_readded_item' === $current_action ) {

			// because it was removed, the membership is no longer linked to a subscription...
			$user_id            = $subscription->get_user_id();
			$membership_plans   = wc_memberships_get_membership_plans();
			$readded_product_id = $removed_product_id;

			// ...so we need to loop all plans to match the removed (now readded) product ID and look for our untied membership
			if ( $user_id > 0 && ! empty( $membership_plans ) ) {

				foreach ( $membership_plans as $plan ) {

					if ( ! $plan->has_products() || ! $plan->has_product( $readded_product_id ) ) {
						continue;
					}

					$user_membership = wc_memberships_get_user_membership( $user_id, $plan );

					// this must be the membership we just cancelled
					if ( $user_membership && $user_membership->is_cancelled() ) {

						$note = __( 'Membership reactivated because subscription was readded.', 'woocommerce-memberships' );

						$user_membership->activate_membership( $note );

						// re-link the membership to the Subscription
						$subscription_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->get_id() );
						$subscription_membership->set_subscription_id( $subscription_id );
					}
				}
			}
		}
	}


	/** Internal & helper methods ******************************************/


	/**
	 * Returns a Subscription status.
	 *
	 * @since 1.5.4
	 *
	 * @param \WC_Subscription $subscription
	 * @return string
	 */
	public function get_subscription_status( $subscription ) {
		return $subscription instanceof \WC_Subscription ? $subscription->get_status() : '';
	}


	/**
	 * Returns a Subscription from a User Membership.
	 *
	 * @since 1.6.0
	 *
	 * @param int|\WC_Memberships_User_Membership $user_membership Membership object or id
	 * @return null|\WC_Subscription The Subscription object, null if not found
	 */
	public function get_subscription_from_membership( $user_membership ) {

		$subscription_id = $this->get_user_membership_subscription_id( $user_membership );

		return ! $subscription_id ? null : wcs_get_subscription( $subscription_id );
	}


	/**
	 * Returns User Memberships from a Subscription.
	 *
	 * @since 1.6.0
	 *
	 * @param int|\WC_Subscription $subscription Subscription post object or ID
	 * @return \WC_Memberships_Integration_Subscriptions_User_Membership[] array of user membership objects or empty array, if none found
	 */
	public function get_memberships_from_subscription( $subscription ) {

		$user_memberships = array();

		if ( is_numeric( $subscription ) ) {
			$subscription_id = (int) $subscription;
		} elseif ( is_object( $subscription ) ) {
			$subscription_id = (int) $subscription->get_id();
		}

		if ( ! empty( $subscription_id ) ) {

			$user_membership_ids = new \WP_Query( array(
				'post_type'        => 'wc_user_membership',
				'post_status'      => wc_memberships_get_user_membership_statuses( false ),
				'fields'           => 'ids',
				'nopaging'         => true,
				'suppress_filters' => 1,
				'meta_query'       => array(
					array(
						'key'   => '_subscription_id',
						'value' => $subscription_id,
						'type' => 'numeric',
					),
				),
			) );

			if ( ! empty( $user_membership_ids->posts ) ) {

				foreach ( $user_membership_ids->posts as $user_membership_id ) {

					$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership_id );

					if ( $user_membership->has_subscription() ) {

						$user_memberships[] = $user_membership;
					}
				}
			}
		}

		return $user_memberships;
	}


	/**
	 * Returns Subscriptions.
	 *
	 * @see wcs_get_subscriptions() but more broad
	 *
	 * @since 1.7.0
	 *
	 * @param array $args
	 * @return \WC_Subscription[]|int[] an associative array of post ids => subscription objects (or IDS if 'fields' => 'ids' is passed in $args ).
	 */
	public function get_subscriptions( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'posts_per_page' => -1,
			'post_status'    => 'any',
		) );

		$args['post_type'] = 'shop_subscription';

		$results = get_posts( $args );

		if ( $results && ! isset( $args['fields'] ) ) {

			$subscriptions = array();

			foreach ( $results as $subscription_post ) {
				$subscriptions[ $subscription_post->ID ] = new \WC_Subscription( $subscription_post );
			}

			return $subscriptions;
		}

		return $results;
	}


	/**
	 * Returns Subscriptions IDs.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args optional, passed to `get_posts()`
	 * @return int[] an array of ids (by default from all the existing subscriptions)
	 */
	public function get_subscriptions_ids( $args = array() ) {

		$args['fields'] = 'ids';

		return $this->get_subscriptions( $args );
	}


	/**
	 * Returns a subscription ID for a membership.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_membership_id User Membership ID
	 * @return string|false
	 */
	public function get_user_membership_subscription_id( $user_membership_id ) {
		return get_post_meta( $user_membership_id, '_subscription_id', true );
	}


	/**
	 * Returns the the Subscription's ID and the Subscription's holder name.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Subscription $subscription a subscription object
	 * @return string
	 */
	public function get_formatted_subscription_id_holder_name( \WC_Subscription $subscription ) {

		/* translators: Placeholders: %1$s - The Subscription's id, %2$s - The Subscription's holder full name */
		return sprintf( __( 'Subscription #%1$s - %2$s', 'woocommerce-memberships' ), $subscription->get_id(), $subscription->get_formatted_billing_full_name() );
	}


	/**
	 * Returns a Subscription event date or time.
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Subscription $subscription the Subscription to get the event for.
	 * @param string $event the event to retrieve a date/time for.
	 * @param string $format 'timestamp' for timestamp output or 'mysql' for date (default).
	 * @return int|string
	 */
	private function get_subscription_event( $subscription, $event, $format = 'mysql' ) {

		$date = '';

		if ( $subscription instanceof \WC_Subscription ) {

			$date = $subscription->get_date( $event );

			// fall back to previously recorded trial end date if the Subscription has entered pending cancellation
			if ( $subscription && empty( $date ) && 'trial_end' === $event && $subscription->has_status( 'pending-cancel' ) ) {
				$date = $subscription->get_meta( 'trial_end_pre_cancellation' );
			}
		}

		return 'timestamp' === $format && ! empty( $date ) ? strtotime( $date ) : $date;
	}


	/**
	 * Returns the date for a Subscription event.
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Subscription $subscription the Subscription to get the event for
	 * @param string $event type of event to retrieve a date for
	 * @return string date in MySQL format
	 */
	public function get_subscription_event_date( $subscription, $event ) {
		return $this->get_subscription_event( $subscription, $event, 'mysql' );
	}


	/**
	 * Returns the timestamp for a Subscription event.
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Subscription $subscription the Subscription to get the event for
	 * @param string $event type of event to retrieve a timestamp for
	 * @return int timestamp
	 */
	public function get_subscription_event_time( $subscription, $event ) {
		return $this->get_subscription_event( $subscription, $event, 'timestamp' );
	}


	/**
	 * Compares a Subscription status with a Membership status.
	 *
	 * Subscription statuses and Membership statuses do not have the same key names.
	 * This helper method compares statuses and maps them to check if they're the same.
	 *
	 * @since 1.6.0
	 *
	 * @param array|\WC_Subscription $subscription a subscription object or array
	 * @param \WC_Memberships_User_Membership $membership a user membership object
	 * @return bool true if the statuses matches, false if they don't
	 */
	public function has_subscription_same_status( $subscription, $membership ) {

		$membership_status   = $membership->get_status();
		$subscription_status = $this->get_subscription_status( $subscription );

		// sanity check, although this shouldn't happen.
		if ( ! $subscription_status && ! $membership_status ) {

			$has_same_status = true;

		} else {

			// Subscription status name => Membership status name.
			$map = array(
				'active'         => 'active',
				'on-hold'        => 'paused',
				'expired'        => 'expired',
				'pending-cancel' => 'pending',
				'trash'          => 'cancelled',
			);

			$has_same_status = ! array_key_exists( $subscription_status, $map ) ? false : $map[ $subscription_status ] === $membership_status;
		}

		return $has_same_status;
	}


	/**
	 * Checks if a Membership Plan has at least one subscription product that grants access.
	 *
	 * @since 1.6.0
	 *
	 * @param int $plan_id \WC_Memberships_Membership_Plan ID
	 * @return bool
	 */
	public function has_membership_plan_subscription( $plan_id ) {

		if ( ! isset( $this->has_membership_plan_subscription[ $plan_id ] ) ) {

			$this->has_membership_plan_subscription[ $plan_id ] = false;

			$plan = wc_memberships_get_membership_plan( $plan_id );

			if ( $plan ) {

				$product_ids = $plan->get_product_ids();
				$product_ids = ! empty( $product_ids ) ? array_map( 'absint',  $product_ids ) : null;

				if ( ! empty( $product_ids ) ) {

					foreach ( $product_ids as $product_id ) {

						if ( \WC_Subscriptions_Product::is_subscription( $product_id ) ) {

							$this->has_membership_plan_subscription[ $plan_id ] = true;
							break;
						}
					}
				}
			}
		}

		return $this->has_membership_plan_subscription[ $plan_id ];
	}


	/**
	 * Checks whether a User Membership has a Subscription-based installment plan.
	 *
	 * @since 1.9.0
	 *
	 * @param int|\WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership membership object or ID
	 * @return bool
	 */
	public function has_membership_installment_plan( $user_membership ) {

		$user_membership_id      = is_object( $user_membership ) ? $user_membership->get_id() : (int) $user_membership;
		$subscription_membership = is_numeric( $user_membership_id ) ? new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership_id ) : null;

		return $subscription_membership && $subscription_membership->has_installment_plan();
	}


	/**
	 * Checks whether a User Membership is Subscription-based or not.
	 *
	 * Method reimplements the logic from the following child membership methods to avoid memory errors:
	 * @see \WC_Memberships_Integration_Subscriptions_User_Membership::has_subscription()
	 * @see \WC_Memberships_Integration_Subscriptions_User_Membership::get_subscription()
	 * @see \WC_Memberships_Integration_Subscriptions_User_Membership::get_subscription_id()
	 *
	 * @since 1.6.0
	 *
	 * @param int|\WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership a membership object or ID
	 * @return bool
	 */
	public function is_membership_linked_to_subscription( $user_membership ) {

		$is_linked     = false;
		$membership_id = $user_membership instanceof \WC_Memberships_User_Membership ? $user_membership->get_id() : $user_membership;

		if ( is_numeric( $membership_id ) ) {

			if ( ! array_key_exists( (int) $membership_id, $this->has_user_membership_subscription ) ) {

				$subscription_id = get_post_meta( $membership_id, '_subscription_id', true );
				$subscription    = is_numeric( $subscription_id ) ? wcs_get_subscription( $subscription_id ) : null;

				$this->has_user_membership_subscription[ (int) $membership_id ] = $subscription instanceof \WC_Subscription;
			}

			$is_linked = $this->has_user_membership_subscription[ (int) $membership_id ];
		}

		return $is_linked;
	}


	/**
	 * Decouples (unlinks) a User Membership from a Subscription.
	 *
	 * Removes Subscriptions information from a Membership.
	 *
	 * @since 1.6.0
	 * @param int|\WC_Memberships_User_Membership $user_membership the User Membership object or ID
	 * @param int|\WC_Subscription $unlink_subscription the Subscription ID or object to unlink
	 * @return null|bool true on success, false on failure or null if Subscription link not found
	 */
	public function unlink_membership( $user_membership, $unlink_subscription ) {

		$user_membership_id        = $user_membership instanceof \WC_Memberships_User_Membership ? $user_membership->get_id() : (int) $user_membership;
		$subscription_membership   = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership_id );
		$linked_subscription_id    = (int) $subscription_membership->get_subscription_id();
		$unlinking_subscription_id = is_object( $unlink_subscription ) ? (int) $unlink_subscription->get_id() : (int) $unlink_subscription;

		if ( $linked_subscription_id > 0 && $unlinking_subscription_id > 0 && $linked_subscription_id !== $unlinking_subscription_id ) {
			$unlinked = null;
		} else {
			$unlinked = $subscription_membership->delete_subscription_id();
		}

		return $unlinked;
	}


	/**
	 * Removes cached value when a membership is linked or unlinked with a subscription.
	 *
	 * @internal
	 *
	 * @since 1.10.7
	 *
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $subscription_membership membership object
	 */
	public function prune_membership_link_cache( $subscription_membership ) {

		unset( $this->has_user_membership_subscription[ (int) $subscription_membership->get_id() ] );
	}


	/**
	 * Adds subscription ID to personal data exported upon an user's access request.
	 *
	 * @internal
	 *
	 * @since 1.10.3
	 *
	 * @param array $personal_data personal data in array form
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership $user_membership membership being exported
	 * @return array
	 */
	public function export_user_membership_personal_data( array $personal_data, \WC_Memberships_User_Membership $user_membership ) {

		if ( $user_membership instanceof \WC_Memberships_Integration_Subscriptions_User_Membership && ( $subscription_id = $user_membership->get_subscription_id() ) ) {

			$personal_data['subscription'] = array(
				'name'  => __( 'Related Subscription', 'woocommerce-memberships' ),
				'value' => $subscription_id,
			);
		}

		return $personal_data;
	}


}
