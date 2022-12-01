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
 * Background handler to (re)activate subscription-tied memberships when Subscriptions is activated along Memberships or vice versa.
 *
 * @since 1.10.0
 */
class WC_Memberships_Integration_Subscriptions_Utilities_Activation_Background_Job extends \WC_Memberships_Job_Handler {


	/**
	 * Background job handler constructor.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$this->action   = 'subscription_tied_memberships_activation';
		$this->data_key = 'user_membership_ids';

		parent::__construct();

		add_action( "{$this->identifier}_job_complete", array( $this, 'cleanup_jobs' ), 1 );
		add_action( "{$this->identifier}_job_failed",   array( $this, 'cleanup_jobs' ), 1 );
	}


	/**
	 * Processes a Subscriptions/Memberships activation event in background.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job job object
	 * @param int $items_per_batch items to process per batch
	 * @return false|\stdClass job object or false on error
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function process_job( $job, $items_per_batch = 3 ) {

		if ( ! $this->start_time ) {
			$this->start_time = time();
		}

		// indicate that the job has started processing
		if ( 'processing' !== $job->status ) {

			$job->status                = 'processing';
			$job->started_processing_at = current_time( 'mysql' );

			$job = $this->update_job( $job );
		}

		$data_key = $this->data_key;

		if ( ! isset( $job->{$data_key} ) || ! is_array( $job->{$data_key} ) ) {

			$this->fail_job( $job );

			throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'User Memberships to process for Subscriptions reactivation not set or invalid.', 'woocommerce-memberships' ) );
		}

		/* @type int[] $data array of user membership IDs */
		$data = $job->{$data_key};

		$job->total = count( $data );

		// skip already processed items
		if ( $job->progress && ! empty( $data ) ) {
			$data = array_slice( $data, $job->progress, null, true );
		}

		// loop over unprocessed items and process them
		if ( ! empty( $data ) ) {

			$processed_memberships = 0;

			foreach ( $data as $user_membership_id ) {

				$job->batch_ids[] = $this->process_item( $user_membership_id );

				$processed_memberships++;

				// job limits reached
				if ( $processed_memberships >= $items_per_batch || $this->time_exceeded() || $this->memory_exceeded() ) {
					break;
				}
			}

			$job->progress += $processed_memberships;

			// update job progress
			$job = $this->update_job( $job );

		} else {

			$job->progress = $job->total;
		}

		// complete current job
		if ( $job->progress >= $job->total ) {
			$job = $this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Processes user memberships in background.
	 *
	 * Checks if the membership is tied to a membership and perhaps activates it.
	 * This is usually run upon Subscriptions activation on top of Memberships (or vice versa).
	 * It compares whether a subscription is active, and it is tied to a membership that has been paused.
	 * In this way the memberships and the related subscriptions are synchronized again.
	 *
	 * @since 1.10.0
	 *
	 * @param int $user_membership_id ID of the user membership to process
	 * @param null $_ unused
	 * @return int user membership ID
	 */
	public function process_item( $user_membership_id, $_ = null ) {

		// get the membership object from the membership (post) ID
		$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership_id );

		if ( $integration = wc_memberships()->get_integrations_instance()->get_subscriptions_instance() ) {

			// get the related subscription
			$subscription = $integration->get_subscription_from_membership( $user_membership->get_id() );

			if ( $subscription ) {

				// get the subscription's status, to compare with the membership's
				$subscription_status = $integration->get_subscription_status( $subscription );

				// if statuses do not match, update
				if ( ! $integration->has_subscription_same_status( $subscription, $user_membership ) ) {

					// special handling for paused memberships which might be put on free trial
					if ( 'active' === $subscription_status && $user_membership->has_status( 'paused' ) ) {

						// get trial end timestamp
						$trial_end = $integration->get_subscription_event_time( $subscription, 'trial_end' );

						// if there is no trial end date or the trial end date is past and the Subscription is active, activate the membership...
						if ( ! $trial_end || current_time( 'timestamp', true ) >= $trial_end ) {
							$user_membership->activate_membership( __( 'Membership activated because WooCommerce Subscriptions was activated.', 'woocommerce-memberships' ) );
						// ...otherwise, put the membership on free trial
						} else {
							$user_membership->update_status( 'free_trial', __( 'Membership free trial activated because WooCommerce Subscriptions was activated.', 'woocommerce-memberships' ) );
							$user_membership->set_free_trial_end_date( date( 'Y-m-d H:i:s', $trial_end ) );
						}

					// all other membership statuses: simply update the status
					} else {

						$integration->update_related_membership_status( $subscription, $user_membership, $subscription_status );
					}
				}

				if ( ! $user_membership->has_installment_plan() ) {

					$end_date = $integration->get_subscription_event_date( $subscription, 'end' );

					// end date has changed
					if ( strtotime( $end_date ) !== $user_membership->get_end_date( 'timestamp' ) ) {
						$user_membership->set_end_date( $end_date );
					}
				}
			}
		}

		return $user_membership_id;
	}


}
