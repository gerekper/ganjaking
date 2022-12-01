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
 * Helper object to get subscription-specific properties of a user membership.
 *
 * @since 1.7.0
 *
 * @method \WC_Memberships_Membership_Plan|\WC_Memberships_Integration_Subscriptions_Membership_Plan get_plan()
 */
class WC_Memberships_Integration_Subscriptions_User_Membership extends \WC_Memberships_User_Membership {


	/** @var null|int the associated subscription ID */
	protected $subscription_id;

	/** @var string|int the subscription meta key name */
	protected $subscription_id_meta = '_subscription_id';

	/** @var string Installment plan meta key */
	protected $has_installment_plan_meta = '_has_installment_plan';

	/** @var string Trial end meta for free trial memberships */
	protected $free_trial_end_date_meta = '_free_trial_end_date';


	/**
	 * Subscription-tied User Membership constructor.
	 *
	 * @since 1.7.0
	 *
	 * @param int|\WP_Post $user_membership ID or post object
	 */
	public function __construct( $user_membership ) {

		parent::__construct( $user_membership );

		$this->set_meta_keys();

		$this->type = 'subscription';
	}


	/**
	 * Returns meta keys used to store user membership meta data.
	 *
	 * @since 1.11.0
	 *
	 * @return string[]
	 */
	public function get_meta_keys() {

		// add subscriptions-specific meta data keys to the default set
		return array_merge( parent::get_meta_keys(), array(
			'_subscription_id',
			'_has_installment_plan',
			'_free_trial_end_date',
		) );
	}


	/**
	 * Checks whether the subscription follows an installment plan option.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function has_installment_plan() {

		$has_installment_plan = get_post_meta( $this->id, $this->has_installment_plan_meta, true );

		if ( ! is_numeric( $has_installment_plan ) && $this->has_subscription() ) {

			// maybe set this membership to have an installment plan, if no previous record was found
			$has_installment_plan = $this->maybe_set_installment_plan();
		}

		return (bool) $has_installment_plan;
	}


	/**
	 * Flags the subscription tied membership to have an installment plan.
	 *
	 * Note: once set, normally this flag should persist even after a subscription link has been removed.
	 * @see \WC_Memberships_Integration_Subscriptions_User_Membership::remove_installment_plan() for forcefully removing this flag.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function maybe_set_installment_plan() {

		$plan = $this->get_plan();

		// sanity check
		if ( ! $plan ) {
			return false;
		}

		$plan = new \WC_Memberships_Integration_Subscriptions_Membership_Plan( $plan->post );

		// If this plan has a subscription and the subscription is in installment mode,
		// save this condition in a post meta which will persist also after unlinking.
		if ( $this->has_subscription() && $plan->has_installment_plan() ) {

			return (bool) update_post_meta( $this->id, $this->has_installment_plan_meta, $this->get_subscription_id() );
		}

		return false;
	}


	/**
	 * Removes the flag that signals that the membership uses an installment plan via subscription.
	 *
	 * Note: it is intended for the post meta holding this status flag to exist after a subscription link has been removed.
	 * Removing this flag should be intentional, possibly when the plan has been reassigned or its details changed.
	 *
	 * @see \WC_Memberships_Integration_Subscriptions_User_Membership::maybe_set_installment_plan() counterpart
	 *
	 * @since 1.12.3
	 *
	 * @return bool success
	 */
	public function remove_installment_plan() {

		return delete_post_meta( $this->id, $this->has_installment_plan_meta );
	}


	/**
	 * Checks whether the membership is tied to a subscription.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function has_subscription() {

		return null !== $this->get_subscription();
	}


	/**
	 * Sets the linked Subscription ID.
	 *
	 * @since 1.7.0
	 *
	 * @param string|int $subscription_id the subscription ID
	 * @return bool success
	 */
	public function set_subscription_id( $subscription_id ) {

		$set_subscription_id = false;

		if ( $subscription = wcs_get_subscription( $subscription_id ) ) {

			$old_subscription_id = $this->get_subscription_id();
			$new_subscription_id = (int) $subscription->get_id();
			$set_subscription_id = (bool) update_post_meta( $this->id, $this->subscription_id_meta, $new_subscription_id );

			if ( $set_subscription_id ) {

				$this->subscription_id = $new_subscription_id;

				$this->maybe_set_installment_plan();

				/**
				 * Fires when a Membership is tied to a new Subscription.
				 *
				 * @since 1.8.0
				 *
				 * @param \WC_Memberships_Integration_Subscriptions_User_Membership $user_membership the User Membership linked to a Subscription
				 * @param int $new_subscription_id the ID of the new Subscription the membership is now linked to
				 * @param null|int $old_subscription_id the ID of the Subscription the membership may have been linked to previously
				 */
				do_action( 'wc_memberships_user_membership_linked_to_subscription', $this, $new_subscription_id, $old_subscription_id );
			}
		}

		return $set_subscription_id;
	}


	/**
	 * Returns the linked Subscription ID.
	 *
	 * @since 1.7.0
	 *
	 * @return int|null Subscription ID or null if not linked/found
	 */
	public function get_subscription_id() {

		if ( null === $this->subscription_id ) {

			$subscription_id = get_post_meta( $this->id, $this->subscription_id_meta, true );

			$this->subscription_id = is_numeric( $subscription_id ) ? (int) $subscription_id : null;
		}

		return $this->subscription_id;
	}


	/**
	 * Returns the linked Subscription object.
	 *
	 * @since 1.7.0
	 *
	 * @return null|\WC_Subscription
	 */
	public function get_subscription() {

		$subscription_id = $this->get_subscription_id();
		$subscription    = ! empty( $subscription_id ) ? wcs_get_subscription( $subscription_id ) : null;

		return $subscription instanceof \WC_Subscription ? $subscription : null;
	}


	/**
	 * Removes the Subscription link.
	 *
	 * @since 1.7.0
	 *
	 * @return bool success
	 */
	public function delete_subscription_id() {

		$subscription_id = $this->get_subscription_id();
		$unlinked        = delete_post_meta( $this->id, $this->subscription_id_meta );

		if ( $unlinked ) {

			$this->subscription_id = null;

			/**
			 * Fires when a Membership is unlinked from a Subscription.
			 *
			 * @since 1.8.0
			 *
			 * @param \WC_Memberships_Integration_Subscriptions_User_Membership $user_membership the User Membership linked to a Subscription
			 * @param int $subscription_id the Subscription ID detached from the User Membership
			 */
			do_action( 'wc_memberships_user_membership_unlinked_from_subscription', $this, $subscription_id );
		}

		return $unlinked;
	}


	/**
	 * Returns the renew membership URL for frontend use.
	 *
	 * This may correspond to a subscription early renewal URl for subscription-linked memberships.
	 *
	 * @since 1.14.0
	 *
	 * @return string URL
	 */
	public function get_renew_membership_url() {

		// WooCommerce Subscriptions 2.4.4 is the minimum version that we could use as in earlier versions a nonce would make it difficult to use early renewal URLs in email contexts
		if (    function_exists( 'wcs_get_early_renewal_url' )
		     && $this->can_be_renewed_early()
		     && version_compare( \WC_Subscriptions::$version, '2.4.4', '>=' ) ) {

			$url = wcs_get_early_renewal_url( $this->get_subscription() );

		} else {

			$url = parent::get_renew_membership_url();
		}

		return $url;
	}


	/**
	 * Checks whether the user membership can be renewed early by the user.
	 *
	 * @since 1.14.0
	 *
	 * @return bool
	 */
	public function can_be_renewed_early() {

		// note: this is intentional, do not call child method in this class to avoid infinite recursion loops
		$early_renewal = parent::can_be_renewed();

		if ( $subscription = $this->get_subscription() ) {

			$early_renewal     = false;
			$expiry_date       = $this->get_subscription_end_date();
			$next_bill_on_date = $this->get_next_bill_on_date();

			if ( ( $expiry_date || $next_bill_on_date ) && function_exists( 'wcs_can_user_renew_early' ) && class_exists( 'WCS_Early_Renewal_Manager' ) ) {

				$early_renewal = \WCS_Early_Renewal_Manager::is_early_renewal_enabled() && wcs_can_user_renew_early( $this->get_subscription(), $this->get_user_id() );

				if ( $early_renewal ) {

					// memberships on installment plans can be renewed only if not on fixed dates in the past
					if ( $this->has_installment_plan() && $this->get_plan() && $this->plan->is_access_length_type( 'fixed' ) ) {

						$fixed_end_date = $this->plan->get_access_end_date( 'timestamp' );
						$early_renewal  = ! empty( $fixed_end_date ) ? $fixed_end_date < current_time( 'timestamp', true ) : $early_renewal;

					// for all other memberships, we want to make sure that only subscriptions with a set end date are offered to be renewed early from a memberships perspective
					} elseif ( ! $expiry_date ) {

						$early_renewal  = false;
					}
				}
			}
		}

		return $early_renewal;
	}


	/**
	 * Checks whether the user membership can be renewed by the user.
	 *
	 * Subscription-tied memberships can be renewed if the subscription has expired or early renewal is allowed.
	 * Note: does not check whether the user has the actual capability to renew.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function can_be_renewed() {

		$can_be_renewed = parent::can_be_renewed();
		$subscription   = $this->get_subscription();

		// make sure that besides the subscription the membership has an order linked
		if ( $subscription instanceof \WC_Subscription && $this->order_contains_subscription() ) {

			// check if the subscription has a valid status to be resubscribed or early renewal is allowed
			$can_be_renewed_early = $can_be_renewed = $this->can_be_renewed_early();

			if ( ! $can_be_renewed_early ) {

				$can_be_renewed = $subscription->has_status( [ 'expired', 'cancelled', 'pending-cancel', 'on-hold' ] );

				// memberships on installment plans can be renewed only if not on fixed dates in the past
				if ( $can_be_renewed && $this->has_installment_plan() && $this->get_plan() && $this->plan->is_access_length_type( 'fixed' ) ) {

					$fixed_end_date = $this->plan->get_access_end_date( 'timestamp' );
					$can_be_renewed = ! empty( $fixed_end_date ) ? $fixed_end_date < current_time( 'timestamp', true ) : $can_be_renewed;
				}
			}

			// ultimately, check if the member can repurchase the subscription
			if ( $can_be_renewed && ! $can_be_renewed_early && function_exists( 'wcs_can_user_resubscribe_to' ) ) {

				$can_be_renewed = wcs_can_user_resubscribe_to( $subscription, $this->get_user_id() );
			}
		}

		return $can_be_renewed;
	}


	/**
	 * Checks whether the order that granted access contains a subscription.
	 *
	 * Helper method, do not open to public.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	private function order_contains_subscription() {

		if ( ! $this->get_order() ) {
			$contains_subscription = false;
		} else {
			$contains_subscription = wcs_order_contains_subscription( $this->get_order_id() );
		}

		return $contains_subscription;
	}


	/**
	 * Gets the linked subscription's optionally set end date in UTC.
	 *
	 * Helper method, do not open to public.
	 *
	 * @since 1.13.2
	 *
	 * @param string $format a valid PHP format, or 'timestamp', or 'mysql'
	 * @return int|string|null
	 */
	private function get_subscription_end_date( $format = 'mysql' ) {

		$subscription = $this->get_subscription();
		$expiry_date  = ! $subscription ?: $subscription->get_time( 'end', 'gmt' );

		if ( ! empty( $expiry_date ) ) {
			$expiry_date = wc_memberships_format_date( $expiry_date, $format );
		}

		return ! empty( $expiry_date ) ? $expiry_date : null;
	}


	/**
	 * Gets the linked subscription's next payment date.
	 *
	 * Helper method, do not open to public.
	 *
	 * @since 1.10.4
	 *
	 * @param string $format a valid PHP format, or 'timestamp', or 'mysql'
	 * @param string $timezone a valid timezone string (use 'gmt' for UTC, or 'site' in place of wc_timezone_string())
	 * @return int|null|string
	 */
	private function get_subscription_next_payment_date( $format, $timezone ) {

		$next_payment_date = null;
		$subscription      = $this->get_subscription();

		if ( $subscription && $this->has_status( [ 'active', 'delayed', 'free_trial', 'paused' ] ) ) {
			$next_payment_date = $subscription->get_time( 'next_payment', $timezone );
		}

		if ( ! empty( $next_payment_date ) ) {
			$next_payment_date = wc_memberships_format_date( $next_payment_date, $format );
		}

		return ! empty( $next_payment_date ) ? $next_payment_date : null;
	}


	/**
	 * Returns the next bill on datetime in UTC.
	 *
	 * @since 1.10.4
	 *
	 * @param string $format either 'mysql', 'timestamp' or other valid PHP date format
	 * @return int|null|string
	 */
	public function get_next_bill_on_date( $format = 'mysql' ) {

		// note: 'gmt' rather than 'UTC' as the timezone is intended here as this is passed to Subscriptions
		 return $this->get_subscription_next_payment_date( $format, 'gmt' );
	}


	/**
	 * Returns the next bill on localized datetime.
	 *
	 * @since 1.10.4
	 *
	 * @param string $format either 'mysql', 'timestamp' or other valid PHP date format
	 * @return int|null|string
	 */
	public function get_next_bill_on_local_date( $format = 'mysql' ) {

		// note: 'site' as the timezone is intended here as this is passed to Subscriptions
		return $this->get_subscription_next_payment_date( $format, 'site' );
	}


	/**
	 * Checks if the membership is in the free trial period.
	 *
	 * Note: this does not check the free trial User Membership status itself.
	 * @see \WC_Memberships_User_Membership::has_status()
	 *
	 * @since 1.7.1
	 *
	 * @return bool
	 */
	public function is_in_free_trial_period() {

		$is_free_trial       = false;
		$free_trial_end_date = $this->get_free_trial_end_date( 'timestamp' );

		if ( is_numeric( $free_trial_end_date ) && $free_trial_end_date !== 0 ) {
			$is_free_trial = current_time( 'timestamp', true ) < $free_trial_end_date;
		}

		return $is_free_trial;
	}


	/**
	 * Sets the membership free trial end datetime.
	 *
	 * @since 1.7.1
	 *
	 * @param string $date date in MySQL format
	 * @return bool whether date was set successfully
	 */
	public function set_free_trial_end_date( $date ) {

		$success = false;

		if ( $free_trial_end_date = wc_memberships_parse_date( $date, 'mysql' ) ) {

			$success = update_post_meta( $this->id, $this->free_trial_end_date_meta, $free_trial_end_date );
		}

		return (bool) $success;
	}


	/**
	 * Returns the membership free trial end date.
	 *
	 * @since 1.7.1
	 *
	 * @param string $format either 'mysql' (default) or 'timestamp'
	 * @return int|null|string
	 */
	public function get_free_trial_end_date( $format = 'mysql' ) {

		$date = get_post_meta( $this->id, $this->free_trial_end_date_meta, true );

		if ( empty( $date ) ) {

			$subscription = $this->get_subscription();

			if ( $subscription && $subscription->has_status( 'pending-cancel' ) ) {
				$date = $subscription->get_meta( 'trial_end_pre_cancellation' );
			}
		}

		return ! empty( $date ) ? wc_memberships_format_date( $date, $format ) : null;
	}


	/**
	 * Returns the membership free trial end date localized datetime.
	 *
	 * @since 1.7.1
	 *
	 * @param string $format optional, defaults to 'mysql'
	 * @return null|int|string the localized free trial end date in the chosen format
	 */
	public function get_local_trial_end_date( $format = 'mysql' ) {

		// get the date timestamp
		$date = $this->get_free_trial_end_date( $format );

		// adjust the date to the site's local timezone
		return ! empty( $date ) ? wc_memberships_adjust_date_by_timezone( $date, $format ) : null;
	}


	/**
	 * Deletes the free trial end date.
	 *
	 * @since 1.7.1
	 *
	 * @return bool success
	 */
	public function delete_free_trial_end_date() {

		return delete_post_meta( $this->id, $this->free_trial_end_date_meta );
	}


	/**
	 * Returns the subscription-linked membership end datetime.
	 *
	 * @since 1.10.4
	 *
	 * @param string $format optional, defaults to 'mysql'
	 * @param bool $include_paused optional: whether to include the time this membership has been paused (defaults to true)
	 * @return null|int|string the end date in the chosen format
	 */
	public function get_end_date( $format = 'mysql', $include_paused = true ) {

		$date        = parent::get_end_date( $format, $include_paused );
		$integration = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();

		// if there is a date and no installment plan, it may indicate that the subscription has a set end date
		if ( null !== $date && $integration && ! $this->has_installment_plan() ) {

			$expiry_date = $this->get_subscription_end_date();

			// if there is no subscription end date, then it could mean a date has been forced on the membership instead, so ignore this
			if ( null !== $expiry_date ) {
				$date = wc_memberships_format_date( $date, $format );
			}
		}

		return ! empty( $date ) ? $date : null;
	}


	/**
	 * Sets expiration events for the subscription-linked membership.
	 *
	 * If the subscription has a fixed end date, allow scheduling some expiration events, so we can trigger ending soon emails, for instance.
	 *
	 * @see \WC_Memberships_User_Memberships::trigger_expiration_events()
	 * @see \WC_Memberships_Integration_Subscriptions::update_related_membership_status()
	 *
	 * @since 1.14.0
	 *
	 * @param int|null $end_timestamp membership end date timestamp: when empty (unlimited membership), it will just clear any existing scheduled event
	 */
	public function schedule_expiration_events( $end_timestamp = null ) {

		if ( $subscription = $this->get_subscription() ) {

			// is there a subscription expiry date set?
			$subscription_end_timestamp = $this->get_subscription_end_date( 'timestamp' );

			if ( is_int( $subscription_end_timestamp ) ) {

				// schedule ending soon emails using the parent logic
				parent::schedule_expiration_events( $subscription_end_timestamp );

				// unschedule expiration emails anyway: we only want to send ending soon emails, as the subscription will trigger expiration of the linked membership then
				as_unschedule_action( 'wc_memberships_user_membership_expiry', [ 'user_membership_id' => $this->get_id() ], 'woocommerce-memberships' );
				return;
			}
		}

		// proceed as usual
		parent::schedule_expiration_events( $end_timestamp );
	}


}
