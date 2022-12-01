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

use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Job handler for exporting members to a CSV file.
 *
 * @since 1.6.0
 */
class WC_Memberships_CSV_Export_User_Memberships extends \WC_Memberships_Job_Handler {


	/** @var string exports folder name */
	private $exports_dir;


	/**
	 * Sets up the Export handler.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		$this->action      = 'csv_export_user_memberships';
		$this->data_key    = 'user_membership_ids';
		$this->exports_dir = 'memberships_csv_exports';

		parent::__construct();

		// delete export files on failure or job deletion
		add_action( "{$this->identifier}_job_failed",  array( $this, 'delete_export_file' ) );
		add_action( "{$this->identifier}_job_deleted", array( $this, 'delete_export_file' ) );

		if ( isset( $_GET['download_exported_csv_file'], $_GET['job_id'], $_GET['job_name'] ) ) {
			add_action( 'init', [ $this, 'download_exported_file' ] );
		}
	}


	/**
	 * Get CSV headers for export.
	 *
	 * @since 1.10.0
	 *
	 * @param null|\stdClass $job job object
	 * @return array associative array of headers
	 */
	protected function get_csv_headers( $job = null ) {

		$headers = parent::get_csv_headers( $job );

		if ( $job && ! empty( $job->include_profile_fields ) ) {

			$profile_fields = Profile_Fields::get_profile_field_definitions();

			foreach ( $profile_fields as $profile_field ) {

				$headers[ $profile_field->get_slug() ] = $profile_field->get_slug();

				if ( Profile_Fields::TYPE_FILE === $profile_field->get_type() ) {

					// add a column for the attachment URL
					$headers[ $profile_field->get_slug() . '(url)' ] = $profile_field->get_slug() . '(url)';
				}
			}
		}

		if ( $job && ! empty( $job->include_meta_data ) ) {
			$headers['user_membership_meta'] = 'user_membership_meta';
		}

		/**
		 * Filters the User Memberships CSV export file row headers.
		 *
		 * @since 1.6.0
		 *
		 * @param array $csv_headers associative array
		 * @param \WC_Memberships_CSV_Export_User_Memberships_Background_Job $export_instance instance of the export class
		 * @param null|\stdClass $job optional import or export job
		 */
		return (array) apply_filters( 'wc_memberships_csv_export_user_memberships_headers', $headers, $this, $job );
	}


	/**
	 * Returns the CSV enclosure to use in export files.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job export job
	 * @return string defaults to '"'
	 */
	protected function get_csv_enclosure( $job = null ) {

		/**
		 * Filters the CSV export enclosure.
		 *
		 * @since 1.6.0
		 *
		 * @param string $enclosure default double quote `"`
		 * @param \WC_Memberships_CSV_Export_User_Memberships_Background_Job $export_instance instance of the export class
		 * @param \stdClass $job export job
		 */
		return (string) apply_filters( 'wc_memberships_csv_export_enclosure', parent::get_csv_enclosure(), $this, $job );
	}


	/**
	 * Returns the timezone to use in export dates handling.
	 *
	 * Defaults to site timezone.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job ongoing export job
	 * @return string timezone
	 */
	protected function get_csv_timezone( $job = null ) {

		/**
		 * Filters whether exporting dates in UTC.
		 *
		 * @since 1.6.0
		 *
		 * @param bool $dates_in_utc default false
		 * @param \WC_Memberships_CSV_Export_User_Memberships_Background_Job $export_instance instance of the export class
		 * @param \stdClass $job export job
		 */
		$use_utc = (bool) apply_filters( 'wc_memberships_csv_export_user_memberships_dates_in_utc', false, $this, $job );

		return $use_utc ? 'UTC' : parent::get_csv_timezone( $job );
	}


	/**
	 * Returns the export file name.
	 *
	 * @since 1.10.0
	 *
	 * @param string $file_id unique identifier
	 * @return string
	 */
	private function get_file_name( $file_id ) {

		// file name default: blog_name_user_memberships_{$file_id}_YYYY_MM_DD.csv
		$file_name = str_replace( '-', '_', sanitize_file_name( strtolower( get_bloginfo( 'name' ) . '_user_memberships_' . $file_id . '_' . date_i18n( 'Y_m_d', time() ) .  '.csv' ) ) );

		/**
		 * Filters the User Memberships CSV export file name.
		 *
		 * @since 1.6.0
		 *
		 * @param string $file_name file name
		 * @param \WC_Memberships_CSV_Export_User_Memberships_Background_Job $export_instance instance of the export class
		 */
		$file_name = apply_filters( 'wc_memberships_csv_export_user_memberships_file_name', $file_name, $this );

		return is_string( $file_name ) ? trim( $file_name ) : '';
	}


	/**
	 * Returns the export file path.
	 *
	 * @since 1.10.0
	 *
	 * @param string $file_name
	 * @return string
	 */
	private function get_file_path( $file_name = '' ) {

		$upload_dir   = wp_upload_dir( null, false );
		$exports_path = trailingslashit( $upload_dir['basedir'] ) . $this->exports_dir;

		return "{$exports_path}/{$file_name}";
	}


	/**
	 * Returns the export file URL.
	 *
	 * @since 1.10.0
	 *
	 * @param string $file_name
	 * @return string
	 */
	private function get_file_url( $file_name ) {

		$upload_url  = wp_upload_dir( null, false );
		$exports_url = trailingslashit( $upload_url['baseurl']  ) . $this->exports_dir;

		return "{$exports_url}/{$file_name}";
	}


	/**
	 * Creates a new export job and its corresponding output file.
	 *
	 * @since 1.10.0
	 *
	 * @param array $attrs associative array
	 * @return null|\stdClass job created
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function create_job( $attrs ) {

		// makes the current export job file name unique for the current user
		$file_id   = md5( http_build_query( wp_parse_args( $attrs, array( 'user_id' => get_current_user_id() ) ) ) );
		$file_name = $this->get_file_name( $file_id );
		$file_path = $this->get_file_path( $file_name );
		$file_url  = $this->get_file_url( $file_name );

		// given that it could be filtered, we need to ensure there's a valid file name produced
		if ( '' === $file_name ) {
			throw new Framework\SV_WC_Plugin_Exception( esc_html__( "No valid filename given for export file, can't export memberships.", 'woocommerce-memberships' ) );
		}

		$job = parent::create_job( wp_parse_args( $attrs, [
			'file_name'              => $file_name,
			'file_path'              => $file_path,
			'file_url'               => $file_url,
			'fields_delimiter'       => 'comma',
			'include_profile_fields' => false,
			'include_meta_data'      => false,
			'results'                => (object) [
				'skipped'   => 0,
				'exported'  => 0,
				'processed' => 0,
				'html'      => '',
			],
		] ) );

		if ( $job ) {

			// ensure the directory exists
			if ( ! wp_mkdir_p( $this->get_file_path() ) ) {

				$this->fail_job( $job );

				/* translators: Placeholder: %s - directory path */
				throw new Framework\SV_WC_Plugin_Exception( sprintf( esc_html__( 'Could not create an exports folder in "%s".', 'woocommerce-memberships' ) ) );
			}

			// create a file for writing ('w' is write mode)
			$file_handle = @fopen( $file_path, 'w' );

			if ( false === $file_handle || ! is_writable( $file_path ) ) {

				$this->fail_job( $job );

				/* translators: Placeholders: %s - file name */
				throw new Framework\SV_WC_Plugin_Exception( sprintf( esc_html__( 'Could not open the export file %s for writing.', 'woocommerce-memberships' ), $file_path ) );
			}

			/**
			 * Flags whether to add CSV BOM (Byte Order Mark).
			 *
			 * Enables adding a BOM to the exported CSV.
			 *
			 * @since 1.6.0
			 *
			 * @param bool $enable_bom true to add the BOM, false otherwise (default)
			 * @param \WC_Memberships_CSV_Export_User_Memberships_Background_Job $export_instance an instance of the export class
			 */
			if ( true === (bool) apply_filters( 'wc_memberships_csv_export_enable_bom', false, $this ) ) {
				fwrite( $file_handle, chr(0xEF) . chr(0xBB) . chr(0xBF) );
			}

			$headers = $this->get_csv_headers( $job );

			if ( empty( $headers ) ) {

				fclose( $file_handle );

				$this->fail_job( $job );

				throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'Could not find CSV headers to write in export file.', 'woocommerce-memberships' ) );
			}

			// inserts the CSV headers
			fputcsv( $file_handle, $this->prepare_csv_row_data( $headers, $headers ), $this->get_csv_delimiter( $job ), $this->get_csv_enclosure() );
			fclose( $file_handle );
		}

		return $job;
	}


	/**
	 * Exports user memberships in background batches.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job job object
	 * @param int $items_per_batch items to process per batch
	 * @return false|\stdClass
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function process_job( $job, $items_per_batch = 5 ) {

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

		if ( ! isset( $job->{$data_key} ) || ! is_array( $job->{$data_key} ) ) {
			throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'User memberships to export not set or invalid.', 'woocommerce-memberships' ) );
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

				try {

					$job = $this->process_item( $user_membership_id, $job );

				} catch ( Framework\SV_WC_Plugin_Exception $e ) {

					$this->fail_job( $job );

					throw new Framework\SV_WC_Plugin_Exception( $e->getMessage() );
				}

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

			// if there are no more membership to export, then we're done
			$job->progress   = $job->total;
			$job->percentage = $this->get_percentage( $job );
		}

		// complete current job
		if ( $job->progress >= $job->total ) {

			// ensure all job results entries are set
			$results            = $this->get_job_results( $job );
			$results->exported  = isset( $results->exported ) ? max( 0, $results->exported ) : 0;
			$results->skipped   = isset( $results->skipped )  ? max( 0, $results->skipped )  : 0;
			$results->processed = max( 0, $results->exported + $results->skipped );
			$job->results       = $results;

			$job = $this->update_job_results( $job, 'html' );
			$job = $this->complete_job( $job );

			$download_url = wp_nonce_url( admin_url(), 'download-export' );

			// return the download url for the exported file
			$download_url = add_query_arg( [
				'download_exported_csv_file' => 1,
				'job_name'                   => $job->name,
				'job_id'                     => $job->id,
			], $download_url );

			$job->download_url = $download_url;
		}

		return $job;
	}


	/**
	 * Processes one user membership for export.
	 *
	 * @since 1.10.0
	 *
	 * @param int $user_membership_id the user membership to process
	 * @param \stdClass $job related job the item belongs to
	 * @return \stdClass the job object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function process_item( $user_membership_id, $job ) {

		$success = false;

		/**
		 * Filter run before exporting a User Membership as a CSV row.
		 *
		 * @since 1.6.0
		 *
		 * @param null|\WC_Memberships_User_Membership $user_membership User Membership being exported
		 * @param \WC_Memberships_CSV_Export_User_Memberships_Background_Job $export_instance the instance of the export class
		 * @param \stdClass $job current export job
		 */
		$user_membership = apply_filters( 'wc_memberships_before_csv_export_user_membership', wc_memberships_get_user_membership( $user_membership_id ), $this, $job );

		if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

			$headers = $this->get_csv_headers( $job );
			$columns = ! empty( $headers ) ? array_keys( $headers ) : array();
			$row     = array();

			if ( ! empty( $columns ) ) {

				$membership_plan = $user_membership->get_plan();
				$member_id       = $user_membership->get_user_id();
				$user            = $user_membership->get_user();

				if ( ! empty( $job->include_meta_data ) ) {
					$columns[] = 'user_membership_meta';
				}

				foreach ( $columns as $column_name ) {

					switch ( $column_name ) {

						case 'user_membership_id' :
							$value = $user_membership->get_id();
						break;

						case 'user_id' :
							$value = $member_id;
						break;

						case 'user_name' :
							$value = $user instanceof \WP_User ? $user->user_login : '';
						break;

						case 'member_first_name' :
							$value = $user instanceof \WP_User ? $user->first_name : '';
						break;

						case 'member_last_name' :
							$value = $user instanceof \WP_User ? $user->last_name : '';
						break;

						case 'member_email' :
							$value = $user instanceof \WP_User ? $user->user_email : '';
						break;

						case 'member_role' :
							$role  = $user instanceof \WP_User ? array_shift( $user->roles ) : '';
							$value = is_string( $role ) ? $role : '';
						break;

						case 'membership_plan_id' :
							$value = $membership_plan->get_id();
						break;

						case 'membership_plan' :
							$value = $membership_plan->get_name();
						break;

						case 'membership_plan_slug' :
							$value = $membership_plan->get_slug();
						break;

						case 'membership_status' :
							$value = $user_membership->get_status();
						break;

						case 'has_access' :
							$value = $user_membership->is_active() ? strtolower( __( 'Yes', 'woocommerce-memberships' ) ) : strtolower( __( 'No', 'woocommerce-memberships' ) );
						break;

						case 'product_id' :
							$value = $user_membership->get_product_id();
						break;

						case 'order_id' :
							$value = $user_membership->get_order_id();
						break;

						case 'member_since' :
							$value = 'UTC' === $this->get_csv_timezone( $job ) ? $user_membership->get_start_date() : $user_membership->get_local_start_date();
						break;

						case 'membership_expiration' :
							$value = 'UTC' === $this->get_csv_timezone( $job ) ? $user_membership->get_end_date()   : $user_membership->get_local_end_date();
						break;

						case 'user_membership_meta' :

							$meta  = get_post_meta( $user_membership->get_id() );
							$value = '';

							if ( ! empty( $meta ) && is_array( $meta ) ) {

								// these options are useful for escaping meta data converted to JSON, however they are normally available only from PHP 5.3
								if ( defined( 'JSON_HEX_APOS' ) && defined( 'JSON_HEX_QUOT' ) ) {
									$value = wp_json_encode( $meta, JSON_HEX_APOS | JSON_HEX_QUOT, 1024 );
								} else {
									$value = wp_json_encode( $meta, 0, 1024 );
								}
							}

						break;

						default :

							$value = '';

							// check if the column is a profile field
							if ( Profile_Fields::is_profile_field_slug( $column_name ) ) {

								// check if profile fields should be included and if the field is set for this membership
								if ( $job && ! empty( $job->include_profile_fields ) && ! empty( $profile_field = $user_membership->get_profile_field( $column_name ) ) ) {

									if ( Profile_Fields::TYPE_FILE === $profile_field->get_definition()->get_type() ) {

										// the column should contain the attachment ID
										$value = $profile_field->get_value();
									} else {

										$value = $profile_field->get_formatted_value();
									}

								}

							// check if the column is a URL column for a file profile field
							} elseif ( Profile_Fields::is_profile_field_slug( str_replace( '(url)', '', $column_name ) ) ) {

								// check if profile fields should be included and if the field is set for this membership
								if ( $job && ! empty( $job->include_profile_fields ) && ! empty( $profile_field = $user_membership->get_profile_field( str_replace( '(url)', '', $column_name ) ) ) ) {

									if ( Profile_Fields::TYPE_FILE === $profile_field->get_definition()->get_type() ) {

										// the column should contain the attachment URL
										$value = $profile_field->get_formatted_value();
									}
								}

							} else {

								/**
								 * Filter a User Membership CSV data custom column.
								 *
								 * @since 1.6.0
								 *
								 * @param string $value the value that should be returned for this column, default empty string
								 * @param string $key the matching key of this column
								 * @param \WC_Memberships_User_Membership $user_membership User Membership object
								 * @param \WC_Memberships_CSV_Export_User_Memberships $export_instance an instance of the export class
								 * @param \stdClass $job current export job
								 */
								$value = apply_filters( "wc_memberships_csv_export_user_memberships_{$column_name}_column", $value, $column_name, $user_membership, $this, $job );
							}

						break;
					}

					$row[ $column_name ] = (string) $value;
				}

				/**
				 * Filters a User Membership's CSV row data before exporting to file.
				 *
				 * @since 1.6.0
				 *
				 * @param array $row User Membership data in associative array format for CSV output
				 * @param \WC_Memberships_User_Membership $user_membership User Membership object
				 * @param \WC_Memberships_CSV_Export_User_Memberships $export_instance an instance of the export class
				 * @param \stdClass $job current export job
				 */
				$user_membership_csv_row_data = (array) apply_filters( 'wc_memberships_csv_export_user_memberships_row', $row, $user_membership, $this, $job );

				// write the CSV data to file
				if ( ! empty( $user_membership_csv_row_data ) && ! empty( $job->file_path ) ) {

					// open the file to append data ('a' is for 'append')
					$file_handle = @fopen( $job->file_path, 'a' );

					if ( false === $file_handle || ! is_writable( $job->file_path ) ) {

						$this->fail_job( $job );

						/* translators: Placeholders: %s - file name */
						throw new Framework\SV_WC_Plugin_Exception( sprintf( esc_html__( 'Could not open the export file %s for writing.', 'woocommerce-memberships' ), $job->file_path ) );
					}

					// sanitize and prepare the data for CSV writing
					$row = $this->prepare_csv_row_data( $headers, $user_membership_csv_row_data );

					// write CSV row in file and close
					fputcsv( $file_handle, $row, $this->get_csv_delimiter( $job ), $this->get_csv_enclosure( $job ) );
					fclose( $file_handle );

					$success = true;

					/**
					 * Action run after exporting a User Membership as a CSV row.
					 *
					 * @since 1.6.0
					 *
					 * @param \WC_Memberships_User_Membership $user_membership User Membership being exported
					 * @param \WC_Memberships_CSV_Export_User_Memberships_Background_Job $export_instance the instance of the export class
					 * @param \stdClass $job current export job
					 */
					do_action( 'wc_memberships_after_csv_export_user_membership', $user_membership, $this, $job );
				}
			}
		}

		if ( $success ) {
			$job = $this->update_job_results( $job, 'exported' );
		} else {
			$job = $this->update_job_results( $job, 'skipped' );
		}

		return $job;
	}


	/**
	 * Returns an import job results information.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job a job object
	 * @return string HTML
	 */
	protected function get_job_results_html( $job ) {

		$message   = '';

		if ( 0 === $job->results->processed ) {

			$message .= '<p><span class="dashicons dashicons-no"></span>' . esc_html__( 'There were no User Memberships found for export that matched the chosen export criteria.', 'woocommerce-memberships' ) . '</p>';

		} else {

			/* translators: Placeholder: %s - User Memberships to import found in uploaded file */
			$message .= '<p>' . sprintf( _n( '%s User Membership processed for export.', '%s User Memberships processed for export.', $job->results->processed, 'woocommerce-memberships' ), $job->results->processed ) . '</p>';

			if ( $job->results->processed === $job->results->skipped ) {

				$message .= '<p><span class="dashicons dashicons-no"></span>' . __( 'However, no User Memberships were successfully exported.', 'woocommerce-memberships' ) . '</p>';

			} else {

				if ( $job->results->exported > 0 ) {
					/* translators: Placeholder: %s - skipped User Memberships to import from file */
					$message .= '<p>' . sprintf( _n( '%s User Membership successfully exported.', '%s User Memberships successfully exported.', $job->results->exported, 'woocommerce-memberships' ), $job->results->exported ) . '</p>';
				}

				if ( $job->results->skipped > 0 ) {
					/* translators: Placeholder: %s - skipped User Memberships to import from file */
					$message .= '<p>' . sprintf( _n( '%s User Membership skipped.', '%s User Memberships skipped.', $job->skipped, 'woocommerce-memberships' ), $job->skipped ) . '</p>';
				}
			}
		}

		return $message;
	}


	/**
	 * Prepares and sanitizes array data for CSV insertion.
	 *
	 * @since 1.10.0
	 *
	 * @param array $headers CSV headers
	 * @param array $row row data (or headers themselves, for the first row)
	 * @return array
	 */
	private function prepare_csv_row_data( $headers, $row ) {

		$data = array();

		foreach ( $headers as $header_key ) {

			if ( ! isset( $row[ $header_key ] ) ) {
				$row[ $header_key ] = '';
			}

			$value = '';

			// strict string comparison, as values like '0' are valid
			if ( '' !== $row[ $header_key ]  ) {
				$value = $row[ $header_key ];
			}

			// escape spreadsheet sensitive characters with a single quote
			// to prevent CSV injections, by prepending a single quote `'`
			// see: http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
			$untrusted = Framework\SV_WC_Helper::str_starts_with( $value, '=' ) ||
			             Framework\SV_WC_Helper::str_starts_with( $value, '+' ) ||
			             Framework\SV_WC_Helper::str_starts_with( $value, '-' ) ||
			             Framework\SV_WC_Helper::str_starts_with( $value, '@' );

			if ( $untrusted ) {
				$value = "'" . $value;
			}

			$data[] = $value;
		}

		return $data;
	}


	/**
	 * Returns user membership IDs to export.
	 *
	 * @since 1.10.0
	 *
	 * @param array $export_args export arguments
	 * @return int[] User Memberships IDs or empty array if none found
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function get_user_memberships_ids_for_export( array $export_args ) {

		$query_args = array();

		// specific user membership IDs may be specified in the bulk action
		if ( isset( $export_args['user_membership_ids'] ) ) {

			if ( is_array( $export_args['user_membership_ids'] ) ) {

				$query_args['post__in'] = array_map( 'absint', $export_args['user_membership_ids'] );

			} elseif ( 'undefined' === $export_args['user_membership_ids'] ) {

				throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'No User Memberships selected for export.', 'woocommerce-memberships' ) );
			}
		}

		$start_from_date = ! empty( $export_args['start_date_from'] ) ? $export_args['start_date_from'] : false;
		$start_to_date   = ! empty( $export_args['start_date_to'] )   ? $export_args['start_date_to']   : false;
		$end_from_date   = ! empty( $export_args['end_date_from'] )   ? $export_args['end_date_from']   : false;
		$end_to_date     = ! empty( $export_args['end_date_to'] )     ? $export_args['end_date_to']     : false;

		// perhaps add meta query args for dates if there's at least one date set
		if ( $start_from_date || $start_to_date || $end_from_date || $end_to_date ) {

			$query_args['meta_query'] = array();

			// query for User Memberships created within some date
			if ( $start_from_date || $start_to_date ) {
				$query_args['meta_query'][] = $this->get_date_range_meta_query_args( '_start_date', $start_from_date, $start_to_date );
			}

			// query for User Memberships expiring within some date
			if ( $end_from_date || $end_to_date ) {
				$query_args['meta_query'][] = $this->get_date_range_meta_query_args( '_end_date', $end_from_date, $end_to_date );
			}

			// join date query arguments
			if ( 2 === count( $query_args['meta_query'] ) ) {
				$query_args['meta_query']['relation'] = 'AND';
			}
		}

		// query User Memberships with specific plans only
		if ( ! empty( $export_args['plan_ids'] ) ) {
			$query_args['post_parent__in'] = array_map( 'absint', (array) $export_args['plan_ids'] );
		}

		// query User Memberships that have specific statuses (defaults to 'any' otherwise)
		if ( ! empty( $export_args['plan_statuses'] ) ) {
			$query_args['post_status'] = (array) $export_args['plan_statuses'];
		} else {
			$query_args['post_status'] = 'any';
		}

		/**
		 * Filters CSV Export User Memberships query args.
		 *
		 * @since 1.6.0
		 *
		 * @param array $query_args query parameters intended for `get_posts()`
		 */
		$query_args = (array) apply_filters( 'wc_memberships_csv_export_user_memberships_query_args', $query_args );

		// non filterable args
		$query_args['post_type'] = 'wc_user_membership';
		$query_args['fields']    = 'ids';
		$query_args['nopaging']  = true;

		return get_posts( $query_args );
	}


	/**
	 * Returns date range arguments for a WordPress meta query (helper method).
	 *
	 * Converts user input dates into UTC to compare with DB values.
	 * If at least one of the dates is set but invalid it will return empty array.
	 *
	 * @since 1.6.0
	 *
	 * @param string $meta_key meta key to look for datetime values
	 * @param string|bool $from_date start date in YYYY-MM-DD format or false to ignore range end
	 * @param string|bool $to_date end date in YYYY-MM-DD format or false to ignore range end
	 * @return array associative array
	 */
	protected function get_date_range_meta_query_args( $meta_key, $from_date = false, $to_date = false ) {

		$args = array();

		if ( empty( $meta_key ) || ( ! $from_date && ! $to_date ) ) {
			return $args;
		}

		$errors  = 0;
		$value   = '';
		$compare = '=';

		// set args based on range ends content
		if ( $from_date && ! $to_date ) {

			$errors += (int) ! $this->is_date( $from_date );

			if ( 0 === $errors ) {
				$value   = $this->parse_date_mysql( $this->adjust_query_date( $from_date, 'start' ) );
				$compare = '>=';
			}

		} elseif ( ! $from_date && $to_date ) {

			$errors += (int) ! $this->is_date( $to_date );

			if ( 0 === $errors ) {
				$value   = $this->parse_date_mysql( $this->adjust_query_date( $to_date, 'end' ) );
				$compare = '<=';
			}

		} else {

			$errors += (int) ! $this->is_date( $from_date );
			$errors += (int) ! $this->is_date( $to_date );

			if ( 0 === $errors ) {

				$start_date = $this->parse_date_mysql( $this->adjust_query_date( $from_date, 'start' ) );
				$end_date   = $this->parse_date_mysql( $this->adjust_query_date( $to_date, 'end' ) );
				$value      = array( $start_date, $end_date );
				$compare    = 'BETWEEN';
			}
		}

		if ( 0 === $errors ) {

			$args = array(
				'key'     => $meta_key,
				'type'    => 'DATETIME',
				'value'   => $value,
				'compare' => $compare,
			);
		}

		return $args;
	}


	/**
	 * Bumps a date to the beginning or the end of a day.
	 *
	 * Does so for dates strings with unspecified time (e.g. just YYYY-MM-DD).
	 * Useful in datetime queries, when querying between dates.
	 *
	 * @since 1.6.0
	 *
	 * @param string $date a date in YYYY-MM-DD format without time
	 * @param string $edge beginning of end of the day (start or end)
	 * @return string YYYY-MM-DD HH:MM:SS
	 */
	protected function adjust_query_date( $date, $edge = 'start' ) {

		switch ( $edge ) {
			case 'start' :
				return $date . ' 00:00:00';
			case 'end' :
				return $date . ' 23:59:59';
			default :
				return $date;
		}
	}


	/**
	 * Deletes the exported data (file) generated by the job that was deleted.
	 *
	 * This method also runs automatically as a callback upon job deletion or failure.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job export job
	 * @return bool
	 */
	public function delete_export_file( $job ) {

		return $this->delete_attached_file( $job );
	}


	/**
	 * Downloads an exported file.
	 *
	 * @internal
	 *
	 * @since 1.13.2
	 */
	public function download_exported_file() {

		check_admin_referer( 'download-export' );

		if ( ! current_user_can( 'manage_woocommerce_user_memberships' ) ) {
			wp_die( __( 'You do not have the proper permissions to download this file.', 'woocommerce-memberships' ) );
		}

		$job_name = wc_clean( $_GET['job_name'] );
		$job_id   = wc_clean( $_GET['job_id'] );
		$job      = wc_memberships()->get_utilities_instance()->get_job_object( $job_name, $job_id );

		if ( ! $job ) {

			// die with an error message if the download fails
			wp_die( __( 'Export job not found', 'woocommerce-memberships' ), '', [ 'response' => 404 ] );
		}

		$filename = $job->file_name;

		header( 'Content-type: text/csv' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );

		// stream the file
		$fp = fopen( $job->file_path, 'rb' );

		$fpassthru_disabled = $this->is_fpassthru_disabled();

		// fpassthru might be disabled in some hosts (like Flywheel)
		if ( $fpassthru_disabled || ! @fpassthru( $fp ) ) {

			$contents = @stream_get_contents( $fp );

			echo $contents ?: '';
		}

		exit();
	}


	/**
	 * Checks whether fpassthru has been disabled in PHP.
	 *
	 * Helper method, do not open to public.
	 *
	 * @since 1.15.3
	 *
	 * @return bool
	 */
	private function is_fpassthru_disabled() {

		$disabled = false;

		if ( function_exists( 'ini_get' ) ) {

			$disabled_functions = @ini_get( 'disable_functions' );

			$disabled = is_string( $disabled_functions ) && in_array( 'fpassthru', explode( ',', $disabled_functions ), false );
		}

		return $disabled;
	}


}
