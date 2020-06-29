<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce CSV Import Suite Background Import handler class.
 *
 * Subclasses SV_WP_Background_Job_Handler, tailored for
 * processing files. As such, it's different in some key aspects:
 * - job progress (last processed line number) is stored in a dedicated option
 * - job results are stored in a dedicated option
 *
 * @since 3.0.0
 */
class WC_CSV_Import_Suite_Background_Import extends Framework\SV_WP_Background_Job_Handler {


	/**
	 * Initiate new background import handler
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$this->prefix = 'wc_csv_import_suite';
		$this->action = 'background_import';

		parent::__construct();

		add_action( "{$this->identifier}_job_created",  array( $this, 'init_job' ) );
		add_action( "{$this->identifier}_job_complete", array( $this, 'finish_import' ) );
		add_action( "{$this->identifier}_job_failed",   array( $this, 'finish_import' ) );
		add_action( "{$this->identifier}_job_deleted",  array( $this, 'handle_job_delete' ) );
	}


	/**
	 * Create dedicated progress & results options for each new import job
	 *
	 * Keeping progress and results in separate options can prevent potential
	 * bottlenecks when processing very large CSV files with thousands of lines
	 * of code
	 *
	 * @since 3.0.0
	 * @param object $job
	 */
	public function init_job( $job ) {

		// remove old completed jobs
		$this->fifo();

		// Start after line 1 (since first line is the header)
		update_option( "{$this->identifier}_progress_{$job->id}" , array( 'line' => 1, 'pos' => 0 ) );
		update_option( "{$this->identifier}_results_{$job->id}" , '' );
	}


	/**
	 * Clear old, completed & failed jobs from the database
	 *
	 * Makes sure that only up to 10 completed/failed jobs are kept in the database
	 *
	 * @since 3.0.0
	 * @return $this
	 */
	private function fifo() {
		global $wpdb;

		$key       = $this->identifier . '_job_%';
		$completed = '%"status":"completed"%';
		$failed    = '%"status":"failed"%';

		$jobs = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM {$wpdb->options}
			WHERE option_name LIKE %s
			AND ( option_value LIKE %s OR option_value LIKE %s )
		", $key, $completed, $failed ) );

		$threshold = 10;

		if ( $jobs >= $threshold ) {
			$result = $wpdb->query( $wpdb->prepare( "
				DELETE
				FROM {$wpdb->options}
				WHERE option_name LIKE %s
				AND ( option_value LIKE %s OR option_value LIKE %s )
				ORDER BY option_id ASC
				LIMIT 1
			", $key, $completed, $failed ) );
		}

		return $this;
	}


	/**
	 * Finish off an import job
	 *
	 * In 3.1.0 renamed from `cleanup_job` to `finish_import`
	 *
	 * @since 3.0.0
	 * @param object $job import job
	 */
	public function finish_import( $job ) {

	 	// clean up after a job has been completed
		delete_option( "{$this->identifier}_progress_{$job->id}" );
		delete_option( "{$this->identifier}_results_{$job->id}" );

		// add admin notice
		$this->add_import_finished_notice( $job );

		// always turn logging off after an import completes
		update_option( 'wc_csv_import_suite_debug_mode', 'no' );
	}


	/**
	 * Add import finished notice for a user
	 *
	 * @since 3.1.0
	 * @param object|string $job Import job object or ID
	 */
	private function add_import_finished_notice( $job ) {

		if ( is_string( $job ) ) {
			$job = $this->get_job( $job );
		}

		// don't notify if no import job found
		if ( ! $job ) {
			return;
		}

		$message_id = 'wc_csv_import_suite_finished_' . $job->id;

		if ( $job->created_by && ! wc_csv_import_suite()->get_admin_notice_handler()->is_notice_dismissed( $message_id, $job->created_by ) ) {

			$import_notices = get_user_meta( $job->created_by, '_wc_csv_import_suite_notices', true );

			if ( ! $import_notices ) {
				$import_notices = array();
			}

			$import_notices[] = $job->id;

			update_user_meta( $job->created_by, '_wc_csv_import_suite_notices', $import_notices );
		}
	}


	/**
	 * Get job progress
	 *
	 * @since 3.0.0
	 * @param string $job_id Unique job ID
	 * @return int
	 */
	public function get_job_progress( $job_id ) {
		return get_option( "{$this->identifier}_progress_{$job_id}" );
	}


	/**
	 * Get job results
	 *
	 * @since 3.0.0
	 * @param string $job_id Unique job ID
	 * @return array
	 */
	public function get_job_results( $job_id ) {
		return json_decode( get_option( "{$this->identifier}_results_{$job_id}" ), true );
	}


	/**
	 * Update job progress
	 *
	 * @since 3.0.0
	 * @param string $job_id Unique job ID
	 * @param int $progress Progress
	 */
	protected function update_job_progress( $job_id, $progress ) {
		update_option( "{$this->identifier}_progress_{$job_id}", $progress );
	}


	/**
	 * Update job results
	 *
	 * @since 3.0.0
	 * @param string $job_id Unique job ID
	 * @param array $results Batch results
	 */
	protected function update_job_results( $job_id, $results ) {
		update_option( "{$this->identifier}_results_{$job_id}", json_encode( $results ) );
	}


	/**
	 * Process job
	 *
	 * CSV Imports do not have a list of items to loop over. Instead, we
	 * start reading the file line-by-line until we run out of memory or
	 * exceed the time limit.
	 *
	 * @since 3.0.0
	 * @param object $job
	 * @return void|object $job
	 */
	public function process_job( $job, $items_per_batch = null ) {

		$progress  = $this->get_job_progress( $job->id );
		$line      = $progress['line'] + 1;
		$start_pos = $progress['pos'];
		$results   = (array) $this->get_job_results( $job->id );

		// load the correct importer type
		$importer = wc_csv_import_suite()->get_importers_instance()->get_importer( $job->type );

		// no importer found, not much we can do here, halt further processing
		if ( ! $importer ) {

			$message = sprintf( esc_html__( 'Unknown importer "%s". Cancelling.', 'woocommerce-csv-import-suite' ), $job->type );

			wc_csv_import_suite()->log( $message );

			$this->fail_job( $job, $message );

			return;
		}

		// pass each line to importer until memory or time limit is exceeded
		while ( is_numeric( $start_pos ) && $start_pos <= $job->file_size ) {

			// adjust import options for the current line
			$options = (array) $job->options + array(
				'start_pos'  => $start_pos,
				'start_line' => $line,
				'max_lines'  => 1,
			);

			// import the current line
			$importer->import( $job->file_path, $options );

			// add new results and save
			$results += (array) $importer->get_import_results();
			$this->update_job_results( $job->id, $results );

			// update job progress
			$progress = $importer->get_import_progress();
			$this->update_job_progress( $job->id, $progress );

			// set import options for next round
			$start_pos = $progress['pos']; // if reached EOF, this will be empty/null
			$line      = $progress['line'] + 1;

			// memory or time limit reached
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				break;
			}
		}

		// job complete! :)
		if ( ! is_numeric( $start_pos ) || $start_pos >= $job->file_size ) {
			$job->results = $results; // augment job with results before completing
			$this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Handle import job deletion
	 *
	 * @since 3.1.0
	 * @param stdClass $job
	 */
	public function handle_job_delete( $job ) {

		// delete imported file which is older than 14 days
		@unlink( $job->file_path );

		// delete user notices related to the import
		if ( $job->created_by ) {
			wc_csv_import_suite()->remove_import_finished_notice( $job->id, $job->created_by );
		}
	}


	/**
	 * No-op
	 *
	 * @since 3.0.3
	 */
	protected function process_item( $item, $job ) {
		// void
	}


	/**
	 * Deletes completed/failed imported files which are older than 14 days ago.
	 *
	 * @since 3.4.0
	 */
	public function remove_expired_imports() {

		$args = array(
			'status' => array( 'completed', 'failed' ),
		);

		// get all completed or failed jobs
		$all_jobs = wc_csv_import_suite()->get_background_import_instance()->get_jobs( $args );

		if ( empty( $all_jobs ) ) {
			return;
		}

		// loop over the jobs and find those that should be removed
		foreach ( $all_jobs as $job ) {

			$date = 'completed' === $job->status ? $job->completed_at : $job->failed_at;

			// job completed/failed at least 14 days ago, remove it (along with the file)
			if ( strtotime( $date ) <= strtotime( '14 days ago' ) ) {

				wc_csv_import_suite()->get_background_import_instance()->delete_job( $job );
			}
		}
	}


}
