<?php
/**
 * MailChimp for WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * MailChimp Sync Background processing handler.
 *
 * @since 1.0.0
 */
class Background_Sync extends Framework\SV_WP_Background_Job_Handler {


	/**
	 * Background_Sync constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->prefix   = 'wc_memberships_mailchimp_sync';
		$this->action   = 'background_sync';
		$this->data_key = 'user_ids';

		parent::__construct();
	}


	/**
	 * Processes a sync job.
	 *
	 * This method is overridden because we need to do special handling for
	 * MailChimp batch processing. Rather than make any API call for each user,
	 * we use their APIs Batch feature to update a set of users, 100 at a time
	 * by default.
	 *
	 * Each time the set limit is reached, we make an API call and update the
	 * job progress with the number of users that were successfully batched. If
	 * the API call fails, we consider those users unprocessed and try again.
	 *
	 * The JS & AJAX implementation takes care of checking the status of the
	 * batches and updating the admin accordingly.
	 *
	 * @since 1.0.0
	 *
	 * @param \stdClass $job job object
	 * @param null $_ unused
	 * @return \stdClass background job
	 * @throws \Exception
	 */
	public function process_job( $job, $_ = null ) {

		if ( ! $this->start_time ) {
			$this->start_time = time();
		}

		// Indicate that the job has started processing
		if ( 'processing' !== $job->status ) {

			$job->status                = 'processing';
			$job->started_processing_at = current_time( 'mysql' );

			$job = $this->update_job( $job );
		}

		$data_key = $this->data_key;

		if ( ! isset( $job->{$data_key} ) ) {
			throw new \SV_WC_Plugin_Exception( sprintf( __( 'Job data key "%s" not set', 'woocommerce-plugin-framework' ), $data_key ) );
		}

		if ( ! is_array( $job->{$data_key} ) ) {
			throw new \SV_WC_Plugin_Exception( sprintf( __( 'Job data key "%s" is not an array', 'woocommerce-plugin-framework' ), $data_key ) );
		}

		$data = $job->{$data_key};

		$job->total = count( $data );

		// progress indicates how many items have been processed,
		// it does NOT indicate the processed item key in any way
		if ( ! isset( $job->progress ) ) {
			$job->progress = 0;
		}

		// skip already processed items
		if ( $job->progress && ! empty( $data ) ) {
			$data = array_slice( $data, $job->progress, null, true );
		}

		// loop over unprocessed items and process them
		if ( ! empty( $data ) ) {

			$batch_users = array();

			/**
			 * Filters the number of users to prepare before pinging the MailChimp API with a batch.
			 *
			 * @since 1.0.0
			 *
			 * @param int $batch_size batch size
			 */
			$users_per_batch = (int) apply_filters( 'wc_memberships_mailchimp_job_users_per_batch', 100 );

			foreach ( $data as $item ) {

				// get the user object and add it to the batch
				if ( $user = $this->process_item( $item, $job ) ) {

					$batch_users[] = $user;

					// job limits reached
					if ( count( $batch_users ) >= $users_per_batch || $this->time_exceeded() || $this->memory_exceeded() ) {
						break;
					}
				}
			}

			if ( ! empty( $batch_users ) ) {

				$batch_data = wc_memberships_mailchimp()->get_api_instance()->sync_list_members( MailChimp_Lists::get_list(), $batch_users );

				if ( $batch_data ) {
					$job->batch_ids[] = $batch_data->id;
				}

				$job->progress += count( $batch_users );
			}

			// update job progress
			$job = $this->update_job( $job );
		}

		// complete current job
		if ( $job->progress >= count( $job->{$data_key} ) ) {
			$job = $this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Process an item from job data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $item Job data item to iterate over
	 * @param \stdClass $job Job instance
	 * @return \WP_User|bool
	 */
	protected function process_item( $item, $job ) {

		return get_userdata( $item );
	}


}
