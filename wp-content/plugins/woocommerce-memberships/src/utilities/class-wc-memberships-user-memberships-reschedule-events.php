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
 * Job handler to reschedule user memberships events.
 *
 * @since 1.10.0
 */
class WC_Memberships_User_Memberships_Reschedule_Events extends \WC_Memberships_Job_Handler {


	/**
	 * Sets up the job handler.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$this->action   = 'user_memberships_reschedule_events';
		$this->data_key = 'user_membership_ids';

		parent::__construct();

		add_action( "{$this->identifier}_job_complete", array( $this, 'cleanup_jobs' ), 1 );
	}


	/**
	 * Checks whether there is an ongoing job.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function has_ongoing_job() {

		$job = $this->get_job();

		return $job && isset( $job->status ) && 'processing' !== $job->status;
	}


	/**
	 * Reschedules user memberships in background.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job job object
	 * @param int $items_per_batch items to process per batch
	 * @return false|\stdClass
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function process_job( $job, $items_per_batch = 1 ) {

		$items_per_batch = $this->get_items_per_batch( $items_per_batch, $job );

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

		// we need an array of all user memberships to reschedule events for
		if ( ! isset( $job->{$data_key} ) || ! is_array( $job->{$data_key} ) ) {

			$this->fail_job( $job );

			throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'User memberships to reschedule events for are undefined or invalid.', 'woocommerce-memberships' ) );
		}

		/* @type int[] $user_memberships array of user membership IDs */
		$user_memberships = $job->{$data_key};

		$job->total = count( $user_memberships );

		// skip already processed items
		if ( $job->progress && ! empty( $user_memberships ) ) {
			$user_memberships = array_slice( $user_memberships, $job->progress, null, true );
		}

		// loop over unprocessed items and process them
		if ( ! empty( $user_memberships ) ) {

			$processed_memberships = 0;

			foreach ( $user_memberships as $user_membership_id ) {

				$this->process_item( $user_membership_id );

				$processed_memberships++;

				// job limits reached
				if ( $processed_memberships >= $items_per_batch || $this->time_exceeded() || $this->memory_exceeded() ) {
					break;
				}
			}

			$job->progress  += $processed_memberships;
			$job->percentage = $this->get_percentage( $job );

			// update job progress
			$job = $this->update_job( $job );

		} else {

			// if there are no more memberships to process, then we're done
			$job->progress   = $job->total;
			$job->percentage = $this->get_percentage( $job );
		}

		// complete current job
		if ( $job->progress >= $job->total ) {
			$job = $this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Triggers the rescheduling of a membership's expiration events.
	 *
	 * If the email schedules have changed, the user membership events relative to expiration will be adjusted.
	 *
	 * @see \WC_Memberships_User_Membership::schedule_expiration_events()
	 * @see \WC_Memberships_User_Membership::expire_membership()
	 *
	 * @since 1.10.0
	 *
	 * @param int $user_membership_id the user membership to process
	 * @param null $_ unused
	 * @return int
	 */
	public function process_item( $user_membership_id, $_ = null ) {

		if ( $user_membership = wc_memberships_get_user_membership( (int) $user_membership_id ) ) {

			$end_time = $user_membership->get_end_date( 'timestamp' );

			if ( $user_membership->is_expired() ) {
				$user_membership->schedule_post_expiration_events( $end_time );
			} else {
				$user_membership->schedule_expiration_events( $end_time );
			}

			unset( $user_membership );
		}

		return $user_membership_id;
	}


}
