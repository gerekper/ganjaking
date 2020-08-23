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
 * Abstract class for handling jobs in Memberships.
 *
 * @since 1.10.0
 *
 * @method get_jobs( $args = array() ) \stdClass[]
 * @method process_job( \stdClass $job, $items_par_batch = null ) \stdClass
 */
abstract class WC_Memberships_Job_Handler extends Framework\SV_WP_Background_Job_Handler {


	/**
	 * Background job handler constructor.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$this->prefix = 'wc_memberships';

		parent::__construct();

		// schedule a cron job to remove outdated jobs
		if ( ! wp_next_scheduled( 'wc_memberships_jobs_cleanup' ) ) {
			wp_schedule_event( strtotime( 'tomorrow +15 minutes' ), 'daily', 'wc_memberships_jobs_cleanup' );
		}

		// cron job callback to remove outdated jobs
		add_action( 'wc_memberships_jobs_cleanup', array( $this, 'cleanup_jobs' ) );
	}


	/**
	 * Creates a new job.
	 *
	 * @since 1.10.0
	 *
	 * @param array $attrs associative array of job properties
	 * @return null|\stdClass
	 */
	public function create_job( $attrs ) {

		$attrs = wp_parse_args( $attrs, array(
			'name'       => $this->action,
			'progress'   => 0,
			'percentage' => 0,
		) );

		return parent::create_job( $attrs );
	}


	/**
	 * Returns a job.
	 *
	 * @since 1.10.0
	 *
	 * @param null|string|\stdClass $id identifier
	 * @return null|\stdClass object
	 */
	public function get_job( $id = null ) {

		if ( null === $id || ( is_string( $id ) && '' !== $id ) ) {
			$job = parent::get_job( $id );
		} elseif ( is_object( $id ) && isset( $id->id ) ) {
			$job = parent::get_job( $id->id );
		} else {
			$job = null;
		}

		return $job;
	}


	/**
	 * Returns the number of items to process in a job batch, filtered.
	 *
	 * @since 1.10.0
	 *
	 * @param int $items_per_batch number of items to process in a batch
	 * @param \stdClass $job job being processed
	 * @return int minimum 1
	 */
	protected function get_items_per_batch( $items_per_batch, $job ) {

		/**
		 * Filters the number of items to process in a job batch.
		 *
		 * @since 1.10.0
		 *
		 * @param int $items_per_batch must be at least 1
		 * @param \stdClass $job the current job being processed
		 */
		return max( 1, (int) apply_filters( 'wc_memberships_job_items_per_batch', (int) $items_per_batch, $job ) );
	}


	/**
	 * Returns a percentage value from a job's progress.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job
	 * @return string
	 */
	protected function get_percentage( $job ) {
		return Framework\SV_WC_Helper::number_format( max( 0, (int) $job->progress ) / max( 1, (int) $job->total ) * 100 );
	}


	/**
	 * Returns a job's current results.
	 *
	 * @since 1.10.4
	 *
	 * @param \stdClass $job a job object
	 * @return \stdClass results object
	 */
	protected function get_job_results( $job ) {

		if ( ! isset( $job->results ) ) {
			$job->results = new stdClass();
		}

		return (object) $job->results;
	}


	/**
	 * Returns job results information (stub).
	 *
	 * @since 1.10.4
	 *
	 * @param \stdClass $job the job object to get results for
	 * @return string HTML
	 */
	protected function get_job_results_html( $job ) {
		return '';
	}


	/**
	 * Adjusts a job's results for job completion reporting.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job the ongoing job to adjust results for
	 * @param string $key the result property to adjust
	 * @param mixed|null $value the property value to set (optional)
	 * @return \stdClass updated job
	 */
	protected function update_job_results( $job, $key, $value = null ) {

		$results = $this->get_job_results( $job );

		if ( 'html' === $key ) {

			$results->$key = '';

			if ( null === $value ) {
				$results->$key = $this->get_job_results_html( $job );
			} elseif ( is_string( $value ) ) {
				$results->$key = $value;
			}

		} elseif ( null !== $value ) {

			$results->$key = $value;

		} else {

			if ( ! isset( $results->$key ) ) {
				$results->$key = 0;
			}

			if ( is_numeric( $results->$key ) ) {
				$results->$key++;
			}
		}

		$job->results = $results;

		return $job;
	}


	/**
	 * Removes expired jobs that are over 14 days old.
	 *
	 * Used as cron job action hook callback or some specific expired job callbacks.
	 *
	 * @since 1.10.0
	 *
	 * @param null|\stdClass $job job object (used in some callbacks)
	 */
	public function cleanup_jobs( $job = null ) {

		// get all completed or failed jobs
		$all_jobs = $this->get_jobs();

		if ( ! empty( $all_jobs ) ) {

			// loop over the jobs and find those that should be removed
			foreach ( $all_jobs as $old_job ) {

				if ( is_object( $old_job ) && isset( $old_job->id ) ) {
					$job_id = $old_job->id;
				} elseif ( is_string( $old_job ) || is_numeric( $old_job ) ) {
					$job_id = $old_job;
				} else {
					continue;
				}

				if ( is_object( $old_job ) && 'wc_memberships_jobs_cleanup' === current_action() ) {

					if ( 'completed' === $old_job->status && isset( $old_job->completed_at ) && $this->is_date( $old_job->completed_at ) ) {
						$date = $old_job->completed_at;
					} elseif ( 'failed' === $old_job->status && isset( $old_job->failed_at ) && $this->is_date( $old_job->failed_at ) ) {
						$date = $old_job->failed_at;
					} elseif ( isset( $old_job->updated_at ) && $this->is_date( $old_job->updated_at ) )  {
						$date = $old_job->updated_at;
					} elseif ( isset( $old_job->created_at ) && $this->is_date( $old_job->created_at ) ) {
						$date = $old_job->created_at;
					}

					$date = ! empty( $date ) ? strtotime( $date ) : false;

					// job completed/failed at least 14 days ago, remove it (along with the file: triggers action we hook to)
					if ( $date && $date <= strtotime( '14 days ago' ) ) {

						$this->delete_job( $job_id );
					}

				} elseif ( $job && is_object( $job ) && isset( $job->id ) && $job->id === $job_id ) {

					$this->delete_job( $job_id );
				}
			}
		}
	}


	/**
	 * Removes a file attached to a job object (used in import/export handlers).
	 *
	 * Needs to be public as it's used from child handlers as action hook callback.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $object
	 * @return bool
	 */
	public function delete_attached_file( $object ) {
		return isset( $object->file_path ) && @unlink( $object->file_path );
	}


	/**
	 * Loosely checks if a date is valid.
	 *
	 * Helper method used by import/export job handlers.
	 *
	 * @see \wc_memberships_parse_date()
	 * @since 1.10.0
	 *
	 * @param string|int $date a date as timestamp or string format
	 * @return bool
	 */
	protected function is_date( $date ) {

		$format = is_numeric( $date ) ? 'timestamp' : 'mysql';

		return false !== wc_memberships_parse_date( $date, $format );
	}


	/**
	 * Checks whether a string is a timezone identifier.
	 *
	 * @since 1.10.0
	 *
	 * @param string $timezone
	 * @return bool
	 */
	protected function is_timezone( $timezone ) {
		return is_string( $timezone ) && '' !== $timezone && in_array( $timezone, timezone_identifiers_list(), true );
	}


	/**
	 * Ensures if a date is returned in MySQL format.
	 *
	 * Helper method used by import/export job handlers.
	 *
	 * @see \wc_memberships_adjust_date_by_timezone()
	 *
	 * @since 1.10.0
	 *
	 * @param string|int $date a date as timestamp or string format
	 * @param string $timezone timezone to use to convert the date from, defaults to site timezone
	 * @return string datetime string in UTC
	 */
	protected function parse_date_mysql( $date, $timezone = '' ) {

		// fallback to site timezone
		if ( ! $this->is_timezone( $timezone ) ) {
			$timezone = wc_timezone_string();
		}

		// get the date
		if ( is_numeric( $date ) ) {
			$src_date = date( 'Y-m-d H:i:s', (int) $date );
		} else {
			$src_date = date( 'Y-m-d H:i:s', strtotime( $date ) );
		}

		if ( ! empty( $src_date ) ) {

			// no need to adjust date, it's already in UTC
			if ( 'UTC' === $timezone ) {

				try {

					$datetime = new \DateTime( $src_date, new \DateTimeZone( $timezone ) );
					$utc_date = date( 'Y-m-d H:i:s', $datetime->format( 'U' ) );

				} catch ( \Exception $e ) {

					// in case of DateTime errors, just return the date as is but issue an error
					trigger_error( sprintf( 'Failed to parse date "%1$s": %2$s', $date, $e->getMessage() ), E_USER_WARNING );

					$utc_date = $src_date;
				}

			} else {

				try {

					$from_date = new \DateTime( $src_date, new \DateTimeZone( $timezone ) );
					$to_date   = new \DateTimeZone( 'UTC' );
					$offset    = $to_date->getOffset( $from_date );

					// getTimestamp method not used here for PHP 5.2 compatibility
					$timestamp = (int) $from_date->format( 'U' );

				} catch ( \Exception $e ) {

					// in case of DateTime errors, just return the date as is but issue an error
					trigger_error( sprintf( 'Failed to parse date "%1$s" to get timezone offset: %2$s.', $date, $e->getMessage() ), E_USER_WARNING );

					$timestamp = is_numeric( $date ) ? (int) $date : strtotime( $date );
					$offset    = 0;
				}

				$utc_date = date( 'Y-m-d H:i:s', $timestamp + $offset );
			}
		}

		return ! empty( $utc_date ) ? $utc_date : '';
	}


	/**
	 * Returns the standard format CSV file headers for memberships import/export.
	 *
	 * @since 1.10.0
	 *
	 * @param null|\stdClass $job optional batch job object, used in child classes
	 * @return array associative array
	 */
	protected function get_csv_headers( $job = null ) {

		$headers = array(
			'user_membership_id'    => 'user_membership_id',
			'user_id'               => 'user_id',
			'user_name'             => 'user_name',
			'member_first_name'     => 'member_first_name',
			'member_last_name'      => 'member_last_name',
			'member_email'          => 'member_email',
			'membership_plan_id'    => 'membership_plan_id',
			'membership_plan'       => 'membership_plan',
			'membership_plan_slug'  => 'membership_plan_slug',
			'membership_status'     => 'membership_status',
			'has_access'            => 'has_access',
			'product_id'            => 'product_id',
			'order_id'              => 'order_id',
			'member_since'          => 'member_since',
			'membership_expiration' => 'membership_expiration',
		);

		return $headers;
	}


	/**
	 * Returns the CSV fields delimiter to use in CSV handlers.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass|null|string $item batch job object or delimiter name
	 * @return string defaults to comma
	 */
	protected function get_csv_delimiter( $item = null ) {

		if ( is_string( $item ) ) {
			$delimiter_name = $item;
		} elseif ( is_object( $item ) && isset( $item->fields_delimiter ) && is_string( $item->fields_delimiter ) ) {
			$delimiter_name = $item->fields_delimiter;
		} else {
			$delimiter_name = 'comma';
		}

		switch ( $delimiter_name ) {
			case 'tab':
				$delimiter = "\t";
			break;
			case 'comma' :
			default :
				$delimiter = ',';
			break;
		}

		/**
		 * Filters the CSV delimiter.
		 *
		 * @since 1.13.1
		 *
		 * @param string $delimiter the CSV delimiter as a character
		 * @param null|string|\stdClass $item the original context identifier
		 * @param \WC_Memberships_Job_Handler|\WC_Memberships_CSV_Import_User_Memberships|\WC_Memberships_CSV_Export_User_Memberships $handler the job handler instance
		 */
		return (string) apply_filters( 'wc_memberships_csv_delimiter', $delimiter, $item, $this );
	}


	/**
	 * Returns the CSV enclosure to use for import/export handlers.
	 *
	 * @since 1.10.0
	 *
	 * @param null|\stdClass $job batch job object
	 * @return string defaults to '"'
	 */
	protected function get_csv_enclosure( $job = null ) {
		return '"';
	}


	/**
	 * Returns the timezone to use in import/export dates handling.
	 *
	 * Defaults to site timezone.
	 *
	 * @since 1.10.0
	 *
	 * @param null|\stdClass $job batch job object
	 * @return string timezone
	 */
	protected function get_csv_timezone( $job = null ) {
		return wc_timezone_string();
	}


}
