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
 * Free Trial integration class for WooCommerce Subscriptions.
 *
 * @since 1.6.0
 */
class WC_Memberships_Integration_Subscriptions_Free_Trial {


	/**
	 * Enables Free Trial Memberships.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// add a free_trial membership status
		add_filter( 'wc_memberships_user_membership_statuses',                   array( $this, 'add_free_trial_status' ) );
		add_filter( 'wc_memberships_edit_user_membership_screen_status_options', array( $this, 'edit_user_membership_screen_status_options' ), 10, 2 );
		add_filter( 'wc_memberships_bulk_edit_user_memberships_status_options',  array( $this, 'remove_free_trial_from_bulk_edit' ) );

		// handle free trial end date changes
		add_action( 'woocommerce_subscription_date_updated', array( $this, 'handle_free_trial_end_date_update' ), 10, 3 );
		add_action( 'woocommerce_subscription_date_deleted', array( $this, 'handle_free_trial_end_date_update' ), 10, 2 );
	}


	/**
	 * Adds free trial status to membership statuses.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $statuses associative array of statuses and labels
	 * @return array
	 */
	public function add_free_trial_status( $statuses ) {

		$statuses = Framework\SV_WC_Helper::array_insert_after( $statuses, 'wcm-active', array(
			'wcm-free_trial' => array(
				'label'       => _x( 'Free Trial', 'Membership Status', 'woocommerce-memberships' ),
				'label_count' => _n_noop( 'Free Trial <span class="count">(%s)</span>', 'Free Trial <span class="count">(%s)</span>', 'woocommerce-memberships' ),
			)
		) );

		return $statuses;
	}


	/**
	 * Removes free trial status from status options.
	 *
	 * An exception is if the membership actually is on free trial.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $statuses array of status options
	 * @param int $user_membership_id User Membership ID
	 * @return array modified array of status options
	 */
	public function edit_user_membership_screen_status_options( $statuses, $user_membership_id ) {

		$user_membership = wc_memberships_get_user_membership( $user_membership_id );

		if ( $user_membership ) {

			$unset = ! $user_membership->has_status( 'free_trial' );

			if ( $unset && $user_membership instanceof \WC_Memberships_Integration_Subscriptions_User_Membership ) {

				$subscription = $user_membership->get_subscription();
				$unset        = ! $subscription || ! ( $subscription->has_status( 'pending-cancel' ) && $user_membership->is_in_free_trial_period() );
			}

			if ( $unset ) {
				unset( $statuses['wcm-free_trial'] );
			}
		}

		return $statuses;
	}


	/**
	 * Removes free trial from bulk edit status options.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 * @param array $statuses array of statuses
	 * @return array modified array of statuses
	 */
	public function remove_free_trial_from_bulk_edit( $statuses ) {

		unset( $statuses['wcm-free_trial'] );

		return $statuses;
	}


	/**
	 * Synchronizes an updated subscription trial end date to its corresponding linked memberships.
	 *
	 * @internal
	 *
	 * @since 1.10.1
	 *
	 * @param \WC_Subscription $subscription a related subscription object
	 * @param string $date_type the time being updated
	 * @param null|string $date optional date in MySQL format, only passed when date is updated and absent when deleted
	 */
	public function handle_free_trial_end_date_update( $subscription, $date_type, $date = null ) {

		if ( 'trial_end' === $date_type ) {

			$integration = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
			$memberships = $integration ? $integration->get_memberships_from_subscription( $subscription ) : array();

			// if the end date has been deleted, look for its historical record if the subscription is now pending cancellation
			if ( empty( $date ) && 'woocommerce_subscription_date_deleted' === current_action() ) {
				$date = $subscription->has_status( 'pending-cancel' ) ? $subscription->get_meta( 'trial_end_pre_cancellation' ) : $date;
			}

			if ( ! empty( $memberships ) ) {

				foreach ( $memberships as $user_membership ) {

					$status = $user_membership->get_status();

					if ( empty( $date ) && $user_membership->delete_free_trial_end_date() ) {

						if ( 'free_trial' === $status ) {

							if ( $user_membership->is_in_active_period() ) {

								$user_membership->update_status( 'active' );

							} else {

								/* @see \WC_Memberships_User_Membership::is_active() */
								if ( $user_membership->get_start_date( 'timestamp' ) > current_time( 'timestamp', true ) ) {
									$user_membership->update_status( 'delayed' );
								} else {
									$user_membership->expire_membership();
								}
							}
						}

					} elseif ( is_string( $date ) && $user_membership->set_free_trial_end_date( $date ) ) {

						$is_in_free_trial = $user_membership->is_in_free_trial_period();

						if ( $is_in_free_trial && 'free_trial' !== $status && $user_membership->is_active() ) {

							$user_membership->update_status( 'free_trial' );

						} elseif ( ! $is_in_free_trial && 'free_trial' === $status ) {

							if ( $user_membership->is_in_active_period() ) {

								$user_membership->update_status( 'active' );

							} else {

								/* @see \WC_Memberships_User_Membership::is_active() */
								if ( $user_membership->get_start_date( 'timestamp' ) > current_time( 'timestamp', true ) ) {
									$user_membership->update_status( 'delayed' );
								} else {
									$user_membership->expire_membership();
								}
							}
						}
					}
				}
			}
		}
	}


}
