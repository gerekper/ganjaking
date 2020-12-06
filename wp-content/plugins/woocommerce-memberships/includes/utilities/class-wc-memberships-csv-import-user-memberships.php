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

use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Exceptions\Invalid_Field;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Job handler for importing members from a CSV file.
 *
 * @since 1.6.0
 */
class WC_Memberships_CSV_Import_User_Memberships extends \WC_Memberships_Job_Handler {


	/** @var string imports folder name */
	private $imports_dir;


	/**
	 * Sets up the Import handler.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		$this->action      = 'csv_import_user_memberships';
		$this->data_key    = 'file_path';
		$this->imports_dir = 'memberships_csv_imports';

		parent::__construct();

		// delete import file upon completion
		add_action( "{$this->identifier}_job_complete", array( $this, 'delete_import_file' ) );
		add_action( "{$this->identifier}_job_failed",   array( $this, 'delete_import_file' ) );
		add_action( "{$this->identifier}_job_deleted",  array( $this, 'delete_import_file' ) );
	}


	/**
	 * Handles unusual CSV file formats and file contents encoding.
	 *
	 * @since 1.10.0
	 *
	 * @param string $contents
	 */
	private function handle_file_encoding( $contents = '' ) {

		// this helps with files from some spreadsheet/csv editors,
		// such as Excel on Mac computers which seem to handle line breaks differently
		@ini_set( 'auto_detect_line_endings', true );

		// handle character encoding
		if ( '' !== $contents && ( $enc = mb_detect_encoding( $contents, 'UTF-8, ISO-8859-1', true ) ) ) {
			setlocale( LC_ALL, 'en_US.' . $enc );
		}
	}


	/**
	 * Get an error message for file upload failure
	 *
	 * @see http://php.net/manual/en/features.file-upload.errors.php
	 *
	 * @since 1.10.0
	 *
	 * @param int|null $error_code a PHP error code
	 * @return string
	 */
	private function get_file_upload_error_message( $error_code = null ) {

		switch ( $error_code ) {
			case 1 :
			case 2 :
				return __( 'The file uploaded exceeds the maximum file size allowed.', 'woocommerce-memberships' );
			case 3 :
				return __( 'The file was only partially uploaded. Please try again.', 'woocommerce-memberships' );
			case 4 :
				return __( 'No file was uploaded.', 'woocommerce-memberships' );
			case 6 :
				return __( 'Missing a temporary folder to store the file. Please contact your host.', 'woocommerce-memberships' );
			case 7 :
				return __( 'Failed to write file to disk. Perhaps a permissions error, please contact your host.', 'woocommerce-memberships' );
			case 8 :
				return __( 'A PHP Extension stopped the file upload. Please contact your host.', 'woocommerce-memberships' );
			default :
				return __( 'File upload error.', 'woocommerce-memberships' );
		}
	}


	/**
	 * Returns the CSV enclosure to use when reading import files.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job import job
	 * @return string defaults to '"'
	 */
	protected function get_csv_enclosure( $job = null ) {

		/**
		 * Filters the CSV import enclosure.
		 *
		 * @since 1.6.0
		 *
		 * @param string $enclosure default double quote `"`
		 * @param \WC_Memberships_CSV_Import_User_Memberships_Background_Job $import_instance instance of the import class
		 * @param \stdClass $job import job
		 */
		return (string) apply_filters( 'wc_memberships_csv_import_enclosure', parent::get_csv_enclosure(), $this, $job );
	}


	/**
	 * Returns the timezone to use in import dates handling.
	 *
	 * Defaults to site timezone.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job current job
	 * @return string timezone
	 */
	protected function get_csv_timezone( $job = null ) {

		if ( $job && isset( $job->timezone ) && is_string( $job->timezone ) ) {
			$timezone = 'UTC' === $job->timezone || $this->is_timezone( $job->timezone ) ? $job->timezone : parent::get_csv_timezone( $job );
		} else {
			$timezone = parent::get_csv_timezone( $job );
		}

		/**
		 * Filters the import timezone.
		 *
		 * @since 1.6.0
		 *
		 * @param string $timezone a valid timezone
		 * @param \WC_Memberships_CSV_Import_User_Memberships_Background_Job $import_instance instance of the export class
		 * @param \stdClass $job import job
		 */
		return (string) apply_filters( 'wc_memberships_csv_import_timezone', $timezone, $this, $job );
	}


	/**
	 * Creates a new import job.
	 *
	 * @since 1.10.0
	 *
	 * @param array $attrs import job properties
	 * @return null|\stdClass import job object or null on failure
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function create_job( $attrs ) {

		$attrs = wp_parse_args( $attrs, array(
			'create_new_memberships'     => true,
			'merge_existing_memberships' => true,
			'allow_memberships_transfer' => false,
			'create_new_users'           => false,
			'notify_new_users'           => false,
			'fields_delimiter'           => 'comma',
			'timezone'                   => wc_timezone_string(),
			'cli'                        => false,
			'total'                      => 0,
			'results'                    => (object) array(
				'memberships_created' => 0,
				'memberships_merged'  => 0,
				'users_created'       => 0,
				'rows_skipped'        => 0,
				'profile_fields'      => [],
				'html'                => '',
			),
		) );

		// optional default start date
		if ( empty( $attrs['default_start_date'] ) || ! $this->is_date( $attrs['default_start_date'] ) ) {
			$attrs['default_start_date'] = date( 'Y-m-d', current_time( 'timestamp' ) );
		}

		// no file provided
		if ( empty( $attrs['file'] ) || ! is_array( $attrs['file'] ) ) {
			throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'You need to provide a valid CSV file to import memberships from.', 'woocommerce-memberships' ) );
		}

		// file upload has an error
		if ( isset( $attrs['file']['error'] ) && is_numeric( $attrs['file']['error'] ) && $attrs['file']['error'] > 0 ) {
			throw new Framework\SV_WC_Plugin_Exception( $this->get_file_upload_error_message( $attrs['file']['error'] ) );
		}

		// file is too big
		if ( isset( $attrs['file']['size'] ) && is_numeric( $attrs['file']['size'] ) > wc_let_to_num( ini_get( 'post_max_size' ) ) ) {
			throw new Framework\SV_WC_Plugin_Exception( $this->get_file_upload_error_message( 1 ) );
		} else {
			$attrs['file_size'] = (int) $attrs['file']['size'];
		}

		$file_name = isset( $attrs['file']['name'] ) && is_string( $attrs['file']['name'] ) ? trim( $attrs['file']['name'] ) : '';

		if ( '' === $file_name ) {
			// this shouldn't happen, yet issue an error in case
			throw new Framework\SV_WC_Plugin_Exception( $this->get_file_upload_error_message() );
		} else {
			// in the remote eventuality two users are uploading the same file, make each file unique
			$attrs['file_name'] = sanitize_file_name( uniqid( '', false ) . '_' . $file_name );
		}

		// prepare imports directory to move the file so we can process it later
		$upload_dir    = wp_upload_dir( null, false );
		$imports_dir   = trailingslashit( $upload_dir['basedir'] ) . $this->imports_dir;
		$imported_path = trailingslashit( $imports_dir ) . $attrs['file_name'];
		$htaccess_path = trailingslashit( $imports_dir ) . '.htaccess';

		/* translators: Placeholders: %1$s - file name, %2$s - file path */
		$write_error_message = sprintf( esc_html__( 'Failed to move the file "%1$s" to "%2$s" for processing: is the directory writable?', 'woocommerce-memberships' ), $file_name, $imports_dir );

		// cannot create the imports directory
		if ( ! wp_mkdir_p( $imports_dir ) ) {
			throw new Framework\SV_WC_Plugin_Exception( $write_error_message );
		}

		// protect import files in case of failed/frozen job
		if ( ! file_exists( $htaccess_path ) && $file_handle = @fopen( $htaccess_path, 'w' ) ) {

			fwrite( $file_handle, 'deny from all' );
			fclose( $file_handle );
		}

		// when importing memberships from the CLI, we use the file path provided directly from the CLI user instead of creating a temporary file from a form upload
		if ( defined( 'WP_CLI' ) && WP_CLI && ! empty( $attrs['file']['tmp_name'] ) ) {

			$file_name     = $attrs['file']['name'];
			$imported_path = untrailingslashit( str_replace( $file_name, '', $attrs['file']['tmp_name'] ) );

			$attrs['cli']       = true;
			$attrs['file_name'] = $file_name;
			$attrs['file_path'] = $imported_path . '/' . $file_name;

		// when importing from a form upload, create a temporary file in the designated WordPress import path
		} else {

			// cannot move file to imports directory
			if ( ! @move_uploaded_file( $attrs['file']['tmp_name'], $imported_path ) ) {
				throw new Framework\SV_WC_Plugin_Exception( $write_error_message );
			} else {
				$attrs['file_path'] = $imported_path;
			}
		}

		$this->handle_file_encoding( $file_name );

		$file_handle = @fopen( $attrs['file_path'], 'r' );

		// this shouldn't happen by this point, yet throw an error in case
		if ( false === $file_handle || ( ! ( defined( 'WP_CLI' ) && WP_CLI ) && ! is_writable( $attrs['file_path'] ) ) ) {
			throw new Framework\SV_WC_Plugin_Exception( $write_error_message );
		}

		$rows = -1;

		while( ! feof( $file_handle ) ){
			fgets( $file_handle );
			$rows++;
		}

		fclose( $file_handle );

		if ( $rows < 1 ) {
			throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'The uploaded file seems not to contain user memberships data.', 'woocommerce-memberships' ) );
		}

		$attrs['total']    = $rows;
		$attrs['position'] = 0;

		wc_memberships()->log( 'Started new user memberships CSV import job...' );
		wc_memberships()->log( print_r( $attrs, true ) );

		return parent::create_job( $attrs );
	}


	/**
	 * Imports user memberships in background batches.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job import job
	 * @param int $items_per_batch items to process per batch
	 * @return \stdClass job object
	 * @throws Framework\SV_WC_Plugin_Exception upon error
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

		/* translators: Placeholder: %s - file path (if available) */
		$read_error_message = sprintf( esc_html__( 'Uploaded file %s not found or not readable.', 'woocommerce-memberships' ), ! empty( $job->file_path ) && is_string( $job->file_path ) ? '"' . $job->file_path . '"' : '' );

		// source file does not exist or it is not readable
		if ( empty( $job->file_name ) || ! is_string( $job->file_name ) || empty( $job->file_path ) || ! is_string( $job->file_path ) || ! is_readable( $job->file_path ) ) {

			$this->fail_job( $job );

			/* translators: Placeholder: %s - file path */
			throw new Framework\SV_WC_Plugin_Exception( $read_error_message );
		}

		$this->handle_file_encoding( $job->file_name );

		$file_handle = @fopen( $job->file_path, 'r' );

		// cannot open file read error
		if ( false === $file_handle ) {

			$this->fail_job( $job );

			throw new Framework\SV_WC_Plugin_Exception( $read_error_message );
		}

		$processed_lines  = 0;
		$file_position    = max( 0, (int) $job->position );
		$current_position = $file_position;
		$file_size        = max( 0, (int) $job->file_size );
		$headers          = fgetcsv( $file_handle, 0, $this->get_csv_delimiter( $job ), $this->get_csv_delimiter( 'import' ) );
		$headers          = is_array( $headers ) ? array_map( 'trim', $headers ) : $headers;
		$headers_columns  = $headers ? count( $headers ) : 0;

		// cannot parse headers
		if ( 0 === $headers_columns ) {

			fclose( $file_handle );

			$this->fail_job( $job );

			throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'Could not find valid CSV headers in uploaded file to import from.', 'woocommerce-memberships' ) );
		}

		// moves the internal pointer of the file to the start position for the current batch
		if ( 0 !== $file_position ) {
			fseek( $file_handle, $file_position );
		}

		if ( ! isset( $job->items_per_batch ) ) {
			$job->items_per_batch = $this->get_items_per_batch( $items_per_batch, $job );
		}

		$delimiter = $this->get_csv_delimiter( $job );
		$enclosure = $this->get_csv_enclosure( $job );

		// continue processing as long as we find more rows
		while ( $row = fgetcsv( $file_handle, 0, $delimiter, $enclosure ) ) {

			$row_columns = count( $row );

			// Combines headers to rows, ensuring the number of columns matches or array_combine would error.
			// This tries to rectify malformed CSV data where a row may could have an extra column than the headers (or less), rather than error out.
			if ( 0 === $row_columns ) {
				// no data, don't bother, but still process this for end of job stats
				$row = array();
			} elseif ( $row_columns === $headers_columns ) {
				// row is ok: just combine as it is
				$row = array_combine( $headers, $row );
			} elseif ( $row_columns > $headers_columns ) {
				// row has more columns: trim the array
				$row = array_combine( $headers, array_slice( $row, 0, $headers_columns ) );
			} else {
				// row has less columns: fill with blanks
				$row = array_combine( $headers, array_merge( $row, array_fill( 0, $headers_columns - $row_columns, '' ) ) );
			}

			// import data and update job results
			$job = $this->process_item( $row, $job );

			// get the new position in file after reading a new row
			$current_position = ftell( $file_handle );

			if ( is_numeric( $current_position ) ) {
				$file_position = (int) $current_position;
			} else {
				// if an error occurred, skip to the end of the file
				$file_position = (int) $file_size;
			}

			$processed_lines++;

			// end of file
			if ( ! is_numeric( $current_position ) || $file_position >= $file_size ) {
				break;
			}

			// batch limits reached
			if ( isset( $job->items_per_batch ) && $processed_lines >= $job->items_per_batch ) {
				break;
			}

			// system limits reached
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {

				if ( ! isset( $job->items_per_batch ) ) {
					$job->items_per_batch = $this->get_items_per_batch( max( 1, min( $items_per_batch, $processed_lines - 1 ) ), $job );
				}

				break;
			}
		}

		fclose( $file_handle );

		$file_handle = $row = $headers = null;

		unset( $file_handle, $row, $headers );

		// update file references for the next batch
		$job->position    = $file_position;
		$job->progress   += $processed_lines;
		$job->percentage  = $this->get_percentage( $job );

		$this->update_job( $job );

		// import has completed
		if ( $job->progress >= $job->total || $job->position >= $job->file_size || ! is_numeric( $current_position ) ) {

			$job->position   = $job->file_size;
			$job->progress   = $job->total;
			$job->percentage = $this->get_percentage( $job );

			$job = $this->update_job_results( $job, 'html' );
			$job = $this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Imports a user membership from a row of import data.
	 *
	 * When creating new memberships, the only required field is either `membership_plan_id` or `membership_plan_slug`.
	 * This is in order to determine a Membership Plan to assign to a User Membership.
	 * If the id is unspecified or not found among the plans available, it will try to look for one using the plan's post slug.
	 *
	 * A `user_membership_id` field is required only if we want to update an existing User Membership.
	 *
	 * A `user_id` needs to exist if we are not allowing to create new users.
	 * If updating an existing User Membership, the `user_id` has to match the user connected to that membership.
	 * If `user_id` is not specified, there is an option to attempt retrieving a WP user from `user_name` (WP login name) or `member_email` email address fields.
	 * When creating new users, an email must be specified or the row will be skipped.
	 * The `user_name` is used to create a login name, if conflicts with an existing one, the import script will use the first piece of the email address, perhaps with a random numerical suffix.
	 *
	 * @since 1.10.0
	 *
	 * @param array $row user membership array data
	 * @param \stdClass $job the current job object
	 * @return \stdClass the job object
	 */
	public function process_item( $row, $job ) {

		// try to get a plan from id or slug
		$membership_plan_id   = isset( $row['membership_plan_id'] )   && is_numeric( trim( $row['membership_plan_id'] ) ) ? (int) trim( $row['membership_plan_id'] ) : null;
		$membership_plan_slug = isset( $row['membership_plan_slug'] ) && is_string( $row['membership_plan_slug'] )        ? trim( $row['membership_plan_slug'] )     : null;
		$membership_plan      = null;

		if ( is_int( $membership_plan_id ) && $membership_plan_id > 0 ) {
			$membership_plan = wc_memberships_get_membership_plan( $membership_plan_id );
		}

		if ( ! $membership_plan && ! empty( $membership_plan_slug ) ) {
			$membership_plan = wc_memberships_get_membership_plan( $membership_plan_slug );
		}

		// try to get an existing user membership from an id
		$existing_user_membership = $this->get_user_membership_from_row( $row, $membership_plan );

		if ( ! $membership_plan && ! $existing_user_membership ) {
			// bail out if we can't process a plan or a user membership to begin with
			$skip_row = true;
		} elseif ( ! $existing_user_membership && false === $job->create_new_memberships ) {
			// bail if no User Membership is found and we do not create new memberships
			$skip_row = true;
		} elseif ( $existing_user_membership && false === $job->merge_existing_memberships ) {
			// bail if there is already a User Membership and we are not supposed to merge
			$skip_row = true;
		} else {
			$skip_row = false;
		}

		if ( ! $skip_row ) {

			$import_data = array();

			// prepare variables
			$import_data['membership_plan_id']    = $membership_plan_id;
			$import_data['membership_plan_slug']  = $membership_plan_slug;
			$import_data['membership_plan_name']  = ! empty( $row['membership_plan'] )   ? $row['membership_plan']   : null;
			$import_data['membership_plan']       = $membership_plan;
			$import_data['user_membership']       = $existing_user_membership;
			$import_data['user_membership_id']    = $existing_user_membership instanceof \WC_Memberships_User_Membership ? $existing_user_membership->get_id() : 0;
			$import_data['user_id']               = ! empty( $row['user_id'] )           ? $row['user_id']           : null;
			$import_data['user_name']             = ! empty( $row['user_name'] )         ? $row['user_name']         : null;
			$import_data['product_id']            = ! empty( $row['product_id'] )        ? $row['product_id']        : null;
			$import_data['order_id']              = ! empty( $row['order_id'] )          ? $row['order_id']          : null;
			$import_data['member_email']          = ! empty( $row['member_email'] )      ? $row['member_email']      : null;
			$import_data['member_first_name']     = ! empty( $row['member_first_name'] ) ? $row['member_first_name'] : null;
			$import_data['member_last_name']      = ! empty( $row['member_last_name'] )  ? $row['member_last_name']  : null;
			$import_data['membership_status']     = ! empty( $row['membership_status'] ) ? $row['membership_status'] : null;
			$import_data['member_since']          = ! empty( $row['member_since'] )      ? $row['member_since']      : null;

			// we don't check for empty here, because an empty string membership expiration means the membership is unlimited
			if ( isset( $row['membership_expiration'] ) && ( is_string( $row['membership_expiration'] ) || is_numeric( $row['membership_expiration'] ) ) ) {
				$import_data['membership_expiration'] = $row['membership_expiration'];
			// however, if the value is not set at all, or its type is invalid, this will be skipped afterwards
			} else {
				$import_data['membership_expiration'] = null;
			}

			// add the profile fields' data
			$import_data = array_merge( $this->get_profile_fields_import_data( $row ), $import_data );

			/**
			 * Filter CSV User Membership import data before processing an import.
			 *
			 * @since 1.6.0
			 *
			 * @param array $import_data the imported data as associative array
			 * @param string $action either 'create' or 'merge' (update) a User Membership
			 * @param array $columns CSV columns raw data
			 * @param array $row CSV row raw data
			 * @param \stdClass $job import job
			 */
			$import_data = (array) apply_filters( 'wc_memberships_csv_import_user_memberships_data', $import_data, true === $job->create_new_memberships ? 'create' : 'merge', array_combine( array_keys( $row ), array_keys( $row ) ), $row, $job );

			// create or update a User Membership and bump counters
			if ( ! $existing_user_membership && true === $job->create_new_memberships ) {
				$job = $this->import_user_membership( 'create', $import_data, $job );
			} elseif ( $existing_user_membership && true === $job->merge_existing_memberships ) {
				$job = $this->import_user_membership( 'merge', $import_data, $job );
			}

		} else {

			$job = $this->update_job_results( $job, 'rows_skipped' );
		}

		$membership_plan = $existing_user_membership = $import_data = null;

		unset( $membership_plan, $existing_user_membership, $import_data );

		return $job;
	}


	/**
	 * Gets a user membership from row data.
	 *
	 * @since 1.12.3
	 *
	 * @param array $row_data CSV row data
	 * @param \WC_Memberships_Membership_Plan $membership_plan the plan
	 * @return null|\WC_Memberships_User_Membership|\WC_Memberships_User_Membership[]
	 */
	private function get_user_membership_from_row( $row_data, $membership_plan ) {

		$user_membership = null;

		// try by getting the user membership ID directly
		if ( isset( $row_data['user_membership_id'] ) && '' !== trim( $row_data['user_membership_id'] ) ) {

			$user_membership = wc_memberships_get_user_membership( is_numeric( $row_data['user_membership_id'] ) ? (int) $row_data['user_membership_id'] : 0 );

		// for anything else we need a plan
		} elseif ( $membership_plan instanceof \WC_Memberships_Membership_Plan ) {

			$user = null;

			// try by user login
			if ( isset( $row_data['user_id'] ) && is_numeric( $row_data['user_id'] ) ) {
				$user = get_user_by( 'id', (int) $row_data['user_id'] );
			}
			if ( ! $user && isset( $row_data['user_name'] ) && '' !== trim( $row_data['user_name'] ) ) {
				$user = get_user_by( 'login', trim( $row_data['user_name'] ) );
			}
			// try by user email
			if ( ! $user && isset( $row_data['member_email'] ) && is_email( trim( $row_data['member_email'] ) ) ) {
				$user = get_user_by( 'email', trim( $row_data['member_email'] ) );
			}

			$user_membership = $user ? wc_memberships_get_user_membership( $user->ID, $membership_plan ) : null;
		}

		return $user_membership;
	}


	/**
	 * Gets the member profile fields' values from row data.
	 *
	 * @since 1.19.0
	 *
	 * @param array $row_data CSV row data
	 * @return array
	 */
	private function get_profile_fields_import_data( $row_data ) {

		$profile_fields_import_data = $row_data;

		foreach ( $row_data as $key => $value ) {

			if ( ! Profile_Fields::is_profile_field_slug( $key ) ) {
				unset( $profile_fields_import_data[ $key ] );
			}
		}

		return $profile_fields_import_data;
	}


	/**
	 * Creates or updates a User Membership according to import data.
	 *
	 * @see \WC_Memberships_CSV_Import_User_Memberships::import_user_memberships()
	 *
	 * @since 1.10.0
	 *
	 * @param string $action either 'create' or 'merge' (for updating/merging)
	 * @param array $import_data User Membership import data
	 * @param \stdClass $job job object being processed
	 * @return \stdClass job object
	 */
	private function import_user_membership( $action, $import_data, $job ) {

		$user_membership = null;

		if ( in_array( $action, [ 'create', 'merge' ], false ) ) {

			// make sure an user id exists
			$user    = $this->import_user_id( $action, $import_data, $job );
			$user_id = current( $user );

			if ( $user_id > 0 ) {

				$user_handling = key( $user );

				if ( 'created' === $user_handling ) {
					$job = $this->update_job_results( $job, 'users_created' );
				}

				// update the import data with the retrieved id
				$import_data['user_id'] = $user_id;

				if ( 'merge' === $action && isset( $import_data['user_membership'] ) && $import_data['user_membership'] instanceof \WC_Memberships_User_Membership ) {

					// update an existing User Membership
					$user_membership = $this->update_user_membership( $user_id, $import_data, $job );

				} elseif ( 'create' === $action && isset( $import_data['membership_plan'] ) && $import_data['membership_plan'] instanceof \WC_Memberships_Membership_Plan ) {

					// sanity check: bail out if user is already member
					if ( ! wc_memberships_is_user_member( $user_id, $import_data['membership_plan'], false ) ) {

						// create the User Membership
						try {

							$user_membership = wc_memberships_create_user_membership( [
								'user_membership_id' => 0,
								'plan_id'            => $import_data['membership_plan']->get_id(),
								'user_id'            => $user_id,
								'product_id'         => ! empty( $import_data['product_id'] ) ? (int) $import_data['product_id'] : 0,
								'order_id'           => ! empty( $import_data['order_id'] )   ? (int) $import_data['order_id']   : 0,
							], 'create' );

						} catch ( Framework\SV_WC_Plugin_Exception $e ) {

							$user_membership = null;
						}
					}
				}

				/* translators: Placeholder: %s - User display name */
				$import_note = sprintf( __( "Membership created from %s's import.", 'woocommerce-memberships' ), wp_get_current_user()->display_name );

				if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

					if ( 'create' === $action ) {

						// leave a note on the membership to help tracking the import operations
						$user_membership->add_note( $import_note );

						$job = $this->update_job_results( $job, 'memberships_created' );

					} elseif ( 'merge' === $action ) {

						$job = $this->update_job_results( $job, 'memberships_merged' );
					}

					// update meta upon create or update action
					$user_membership = $this->update_user_membership_meta( $user_membership, $action, $import_data, $job );

					// update member profile fields upon create or update action
					$user_membership = $this->update_member_profile_fields( $user_membership, $import_data, $job );

					/**
					 * Fires upon creating or updating a User Membership from import data.
					 *
					 * @since 1.6.0
					 *
					 * @param \WC_Memberships_User_Membership $user_membership User Membership object
					 * @param string $action either 'create' or 'merge' (update) a User Membership
					 * @param array $data import data
					 * @param \stdClass $job import job
					 */
					do_action( 'wc_memberships_csv_import_user_membership', $user_membership, $action, $import_data, $job );
				}

				// special handling of free memberships that should be created contextually to a new user generation from import options
				if ( 'created' === $user_handling ) {

					$free_plans = wc_memberships()->get_plans_instance()->get_free_membership_plans();

					if ( $user_membership instanceof \WC_Memberships_User_Membership ) {
						// remove from index the membership that was already handled by the main import
						unset( $free_plans[ $user_membership->get_id() ] );
					}

					if ( ! empty( $free_plans ) ) {

						foreach ( $free_plans as $plan ) {

							// this would check internally if the user is already member or not
							if ( $free_membership = wc_memberships()->get_plans_instance()->grant_access_to_free_membership( $user_id, false, $plan ) ) {

								// leave a note on the membership to help tracking the import operations
								$free_membership->add_note( $import_note );

								$job = $this->update_job_results( $job, 'memberships_created' );

								/* this filter is documented in class-wc-memberships-csv-import-user-membership.php */
								do_action( 'wc_memberships_csv_import_user_membership', $free_membership, $action, $import_data, $job );
							}
						}
					}
				}
			}
		}

		if ( null === $user_membership ) {
			$job = $this->update_job_results( $job, 'rows_skipped' );
		}

		$user_membership = $free_membership = $free_memberships = $free_plans = $user = null;

		unset( $user_membership, $free_membership, $free_memberships, $free_plans, $user );

		return $job;
	}


	/**
	 * Updates a User Membership.
	 *
	 * @since 1.10.0
	 *
	 * @param int $user_id User ID to update Membership for
	 * @param array $data User Membership data to update
	 * @param \stdClass $job job object being processed
	 * @return false|\WC_Memberships_User_Membership
	 */
	private function update_user_membership( $user_id, $data, $job ) {

		$user_membership    = $data['user_membership'];
		$membership_plan    = isset( $data['membership_plan'] ) && $data['membership_plan'] instanceof \WC_Memberships_Membership_Plan ? $data['membership_plan'] : null;
		$transfer_ownership = false;
		$previous_owner     = $user_membership->get_user_id();
		$update_args        = array();

		// check for users conflict
		if ( (int) $user_id !== $previous_owner ) {

			if ( true === $job->allow_memberships_transfer ) {
				$transfer_ownership = true;
			} else {
				return false;
			}
		}

		// check for plans conflict
		if ( null !== $membership_plan && (int) $user_membership->get_plan_id() !== (int) $membership_plan->get_id() ) {

			// bail out if the user is already a non-expired member of the plan we're transferring to
			if ( wc_memberships_is_user_active_member( $user_id, $membership_plan->get_id() ) || wc_memberships_is_user_delayed_member( $user_id, $membership_plan->get_id() ) ) {
				return false;
			}

			$update_args = array_merge( $update_args, array(
				'ID'          => $user_membership->get_id(),
				'post_parent' => $membership_plan->get_id(),
				'post_type'   => 'wc_user_membership',
			) );
		}

		// maybe update the post object first
		if ( ! empty( $update_args ) ) {

			$update = wp_update_post( $update_args, true );

			// ...so we can bail out in case of errors
			if ( 0 === $update || is_wp_error( $update ) ) {
				return false;
			}

			$user_membership = new \WC_Memberships_User_Membership( $update );
		}

		// maybe transfer this membership
		if ( true === $transfer_ownership ) {
			$user_membership->transfer_ownership( $user_id );
		}

		$membership_plan = null;

		unset( $membership_plan );

		return $user_membership;
	}


	/**
	 * Updates the member profile fields.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @param array $data import data
	 * @param \stdClass $job job object being processed
	 * @return \WC_Memberships_User_Membership
	 */
	private function update_member_profile_fields( \WC_Memberships_User_Membership $user_membership, $data, $job ) {

		foreach ( $data as $key => $value ) {

			if ( Profile_Fields::is_profile_field_slug( $key ) ) {

				try {

					$user_membership->set_profile_field( $key, $value );

				} catch ( \Exception $exception ) {

					$code = $exception->getCode();

					$job->results->profile_fields[ $code ] = ( isset( $job->results->profile_fields[ $code ] ) ? $job->results->profile_fields[ $code ] + 1 : 1 );
				}
			}
		}

		$this->update_job( $job );

		return $user_membership;
	}


	/**
	 * Updates a User Membership meta data.
	 *
	 * @since 1.10.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @param string $action either 'create' or 'merge' a User Membership
	 * @param array $data import data
	 * @param \stdClass $job job object being processed
	 * @return \WC_Memberships_User_Membership
	 */
	private function update_user_membership_meta( \WC_Memberships_User_Membership $user_membership, $action, $data, $job ) {

		$timezone = $this->get_csv_timezone( $job );

		// maybe update the product that grants access
		if ( ! empty( $data['product_id'] )  && ( 'create' === $action || $job->merge_existing_memberships || $user_membership->get_product_id() <= 0 ) ) {
			$user_membership->set_product_id( trim( $data['product_id'] ) );
		}

		// maybe update the order that granted access
		if ( ! empty( $data['order_id'] ) && ( 'create' === $action || $job->merge_existing_memberships || $user_membership->get_order_id() <= 0 ) ) {
			$user_membership->set_order_id( trim( $data['order_id'] ) );
		}

		// maybe update start date
		if ( ! empty( $data['member_since'] ) ) {

			if ( ( 'create' === $action || $job->merge_existing_memberships ) && $this->is_date( $data['member_since'] ) ) {
				$user_membership->set_start_date( $this->parse_date_mysql( trim( $data['member_since'] ), $timezone ) );
			}

		} elseif ( 'create' === $action && $this->is_date( $job->default_start_date ) ) {

			$user_membership->set_start_date( $this->parse_date_mysql( $job->default_start_date, $timezone ) );
		}

		// maybe update status
		if ( ( 'create' === $action || $job->merge_existing_memberships ) && wc_memberships()->get_user_memberships_instance()->is_user_membership_status( trim( $data['membership_status'] ) ) ) {

			$user_membership->update_status( trim( $data['membership_status'] ) );

		} elseif ( 'create' === $action ) {

			/**
			 * Filters the default User Membership status to be applied during an import, when not specified.
			 *
			 * @since 1.6.0
			 *
			 * @param string $default_status default 'active'
			 * @param \WC_Memberships_User_Membership $user_membership the current User Membership object
			 * @param array $data import data for the current User Membership
			 */
			$default_membership_status = apply_filters( 'wc_memberships_csv_import_default_user_membership_status', 'active', $user_membership, $data );

			if ( 'active' !== $default_membership_status && wc_memberships()->get_user_memberships_instance()->is_user_membership_status( $default_membership_status ) ) {
				$user_membership->update_status( $default_membership_status );
			}
		}

		// maybe update end date (this could affect status)
		if ( 'create' === $action || $job->merge_existing_memberships ) {

			// parse the date type
			if ( is_numeric( $data['membership_expiration'] ) ) {
				$expiration_date = (int) $data['membership_expiration'];
			} elseif ( is_string( $data['membership_expiration'] ) ) {
				$expiration_date = trim( $data['membership_expiration'] );
			} else {
				$expiration_date = $data['membership_expiration']; // likely null (not set)
			}

			// membership has an expiration date
			if ( $this->is_date( $expiration_date ) ) {
				$user_membership->set_end_date( $this->parse_date_mysql( $expiration_date, $timezone ) );
			// membership is unlimited
			} elseif ( '' === $expiration_date || 'unlimited' === $expiration_date ) {
				$user_membership->set_end_date( '' );
			// forces to reschedule expiration events for sanity
			} else {
				$user_membership->set_end_date( $user_membership->get_end_date() );
			}

			// get the (maybe) new end date
			$expiry_date = $user_membership->get_end_date( 'timestamp' );

			// if expiry date is in the past (with 1 minute buffer), set the membership as expired
			if ( is_numeric( $expiry_date ) && $expiry_date - 60 <= current_time( 'timestamp', true ) && ! $user_membership->is_expired() && ! $user_membership->is_cancelled() ) {
				$user_membership->expire_membership();
			// sanity check for memberships created with a start date in the future
			} elseif ( ! $user_membership->has_status( 'delayed' )  && $user_membership->get_start_date( 'timestamp' ) > current_time( 'timestamp', true ) ) {
				$user_membership->update_status( 'delayed' );
			}
		}

		return $user_membership;
	}


	/**
	 * Obtains a user ID from an existing user or a newly created one.
	 *
	 * @since 1.10.0
	 *
	 * @param string $action either 'merge' or 'create
	 * @param array $import_data import data
	 * @param \stdClass $job current job in progress
	 * @return array an associative array reflecting the user handling and the user ID (if 0, the import is unsuccessful)
	 */
	private function import_user_id( $action, $import_data, $job )  {

		// try to get a user from user data, by id or other fields
		$user     = $this->get_user( $import_data );
		$user_id  = $user instanceof \WP_User ? $user->ID : 0;
		$handling = 'updated';

		// if can't determine a valid user, try to create one
		if ( 0 === $user_id && $job->create_new_users && ( 'create' === $action || ( $job->allow_memberships_transfer && isset( $import_data['member_email'] ) ) ) ) {

			$user     = $this->create_user( $import_data, $job );
			$user_id  = $user ? $user->ID : $user_id;
			$handling = 'created';
		}

		unset( $user );

		return [ $handling => $user_id ];
	}


	/**
	 * Returns a user from import data.
	 *
	 * @since 1.10.0
	 *
	 * @param $user_data array imported user information
	 * @return false|\WP_User
	 */
	private function get_user( $user_data ) {

		$user = false;

		if ( isset( $user_data['user_id'] ) && is_numeric( $user_data['user_id'] ) ) {
			$user = get_user_by( 'id', (int) $user_data['user_id'] );
		}

		// look for a user using alternative fields other than id
		if ( ! $user ) {

			// try first to get user by login name
			if ( ! empty( $user_data['user_name'] ) ) {
				$user = get_user_by( 'login', $user_data['user_name'] );
			}

			// if it fails, try to get user by email
			if ( ! $user && isset( $user_data['member_email'] ) && is_email( $user_data['member_email'] ) ) {
				$user = get_user_by( 'email', $user_data['member_email'] );
			}
		}

		return $user;
	}


	/**
	 * Creates a user from import data.
	 *
	 * An email is required, then attempts to create a login name from the 'user_name' field.
	 * If not found, tries to make one from the 'member_email' field using the string piece before "@".
	 * However, if a user already exists with this name, it appends to this piece a random string as suffix.
	 *
	 * @since 1.10.0
	 *
	 * @param array $import_data arguments to create a user, must contain at least a 'member_email' key
	 * @param \stdClass $job job object
	 * @return false|\WP_User
	 */
	private function create_user( $import_data, $job ) {

		// we need at least a valid email
		if ( empty( $import_data['member_email'] ) || ! is_email( $import_data['member_email'] ) )  {
			return false;
		}

		$email    = $import_data['member_email'];
		$username = null;

		if ( ! empty( $import_data['user_name'] ) && ! get_user_by( 'login', $import_data['user_name'] ) ) {

			$username = $import_data['user_name'];
		}

		if ( ! $username ) {

			$email_name = explode( '@', $email );

			if ( ! get_user_by( 'login', $email_name[0] ) ) {
				$username = $email_name[0];
			} else {
				$username = uniqid( $email_name[0], false );
			}
		}

		$user_data = [
			'user_login' => wp_slash( $username ),
			'user_email' => wp_slash( $email ),
			'first_name' => ! empty( $import_data['member_first_name'] ) ? $import_data['member_first_name'] : '',
			'last_name'  => ! empty( $import_data['member_last_name'] )  ? $import_data['member_last_name']  : '',
			'role'       => 'customer',
		];

		/**
		 * Filters how to handle imported user password generation.
		 *
		 * If true, WooCommerce will generate the password and display the password in the welcome email.
		 * If false, WordPress will generate the password quietly and WooCommerce won't display it in the welcome email.
		 *
		 * @since 1.19.2
		 *
		 * @param bool $notify_new_users_password whether the password will be displayed in WooCommerce emails
		 * @param array $import_data the user import data
		 * @param \stdClass $job member import job
		 */
		if ( ! empty( $job->notify_new_users ) && (bool) apply_filters( 'wc_memberships_csv_import_woocommerce_generate_password', 'yes' === get_option( 'woocommerce_registration_generate_password' ), $import_data, $job ) ) {
			$user_data['user_pass'] = ''; /** handled in {@see wc_create_new_customer()} */
		} else {
			$user_data['user_pass'] = wp_generate_password();
		}

		// we need to unhook our automatic handling to avoid race conditions or duplicated free memberships creation
		remove_action( 'user_register', [ wc_memberships()->get_plans_instance(), 'grant_access_to_free_membership' ], 10 );

		// do not send default Wordpress emails on manual user creation in the current thread
		add_filter( 'send_password_change_email', '__return_false' );
		add_filter( 'send_email_change_email',    '__return_false' );

		// optionally notify a newly created user by sending a new account WooCommerce New Account email notification
		if ( ! empty( $job->notify_new_users ) ) {
			$user_id = wc_create_new_customer( $user_data['user_email'], $user_data['user_login'], $user_data['user_pass'], $user_data );
		} else {
			$user_id = wp_insert_user( $user_data );
		}

		/* this hook is documented in class-wc-memberships-membership-plans.php */
		add_action( 'user_register', [ wc_memberships()->get_plans_instance(), 'grant_access_to_free_membership' ], 10, 2 );

		return is_wp_error( $user_id ) ? false : get_user_by( 'id', $user_id );
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

		$results             = (object) $job->results;
		$memberships_created = max( 0, (int) $results->memberships_created );
		$memberships_merged  = max( 0, (int) $results->memberships_merged );
		$users_created       = max( 0, (int) $results->users_created );
		$processed_rows      = max( 0, $memberships_created + $memberships_merged );
		$skipped_rows        = max( 0, (int) $results->rows_skipped );
		$total_rows          = max( 0, (int) $job->total );
		$message             = '';

		if ( 0 === $total_rows ) {

			$message .= '<p><span class="dashicons dashicons-no"></span>' .  __( 'Could not find User Memberships to import from uploaded file.', 'woocommerce-memberships' ) . '</p>';

		} else {

			/* translators: Placeholder: %s - User Memberships to import found in uploaded file */
			$message .= '<p>' . sprintf( _n( '%s record found in file.', '%s records found in file.', $job->total, 'woocommerce-memberships' ), $job->total ) . '</p>';

			if ( $processed_rows > 0 ) {

				/* translators: Placeholder: %s - User Memberships processed during import from file */
				$message .= ' ' . sprintf( _n( '%s row processed for import.', '%s rows processed for import.', $processed_rows, 'woocommerce-memberships' ), $processed_rows );

				$message .= '<ul>';

				if ( $memberships_created > 0 ) {
					/* translators: Placeholder: %s - User Memberships created in import */
					$message .= '<li>' . sprintf( _n( '%s new User Membership created.', '%s new User Memberships created.', $memberships_created, 'woocommerce-memberships' ), $memberships_created ) . '</li>';
				}

				if ( $memberships_merged > 0 ) {
					/* translators: Placeholder: %s - User Memberships updated during import */
					$message .= '<li>' . sprintf( _n( '%s existing User Membership updated.', '%s existing User Memberships updated.', $memberships_merged, 'woocommerce-memberships' ), $memberships_merged ) . '</li>';
				}

				if ( $users_created > 0 ) {
					/* translators: Placeholder: %s - users created during import */
					$message .= '<li>' . sprintf( _n( '%s new user was created during import.', '%s new users were created during import.', $users_created, 'woocommerce-memberships' ), $users_created ) . '</li>';
				}

				if ( $skipped_rows > 0 ) {
					/* translators: Placeholder: %s - skipped User Memberships to import from file */
					$message .= '<li>' . sprintf( _n( '%s row skipped.', '%s rows skipped.', $skipped_rows, 'woocommerce-memberships' ), $skipped_rows ) . '</li>';
				}

				foreach ( $results->profile_fields as $error_code => $error_count ) {

					if ( $error_count > 0 ) {

						switch ( $error_code ) {

							case Invalid_Field::ERROR_REQUIRED_VALUE:
								$message .= '<li>' . __( 'Some required profile fields had empty values and were not imported.', 'woocommerce-memberships' ) . '</li>';
							break;

							case Invalid_Field::ERROR_INVALID_PLAN:
								$message .= '<li>' . __( 'Some profile fields could not be populated for users based on their assigned membership plans.', 'woocommerce-memberships' ) . '</li>';
							break;

							case Invalid_Field::ERROR_INVALID_VALUE:
								$message .= '<li>' . __( 'Some profile fields had invalid values and were not imported.', 'woocommerce-memberships' ) . '</li>';
							break;
						}
					}
				}

				$message .= '</ul>';

			} else {

				$message .= '<p><span class="dashicons dashicons-no"></span>' . esc_html__( 'However, no User Memberships were created or updated with the given options.', 'woocommerce-memberships' ) . '</p>';
			}
		}

		return $message;
	}


	/**
	 * Deletes the imported data (file) generated by the job that was deleted.
	 *
	 * This method also runs automatically as a callback upon job deletion or failure.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job import job
	 * @return bool
	 */
	public function delete_import_file( $job ) {

		if ( is_object( $job ) ) {
			$job_data = (array) $job;
		} else {
			$job_data = [];
		}

		// do not actually delete the original file if importing via WP CLI, only log message
		if ( isset( $job_data['cli'] ) && true === $job_data['cli'] ) {
			$success = true;
		} else {
			$success = $this->delete_attached_file( $job );
		}

		switch ( current_action() ) {
			case "{$this->identifier}_job_failed" :
				$log_message = 'User memberships CSV import job failed.';
			break;
			case "{$this->identifier}_job_deleted" :
				$log_message = 'User memberships CSV import job deleted.';
			break;
			case "{$this->identifier}_job_complete" :
			default:
				$log_message = 'User memberships CSV import job completed.';
			break;
		}

		wc_memberships()->log( $log_message );
		wc_memberships()->log( print_r( $job_data, true ) );

		return $success;
	}


}
