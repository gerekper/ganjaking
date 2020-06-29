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

if ( ! class_exists( 'WP_Importer' ) ) return;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce CSV Import Suite base Importer class
 * for managing the import process of a CSV file.
 *
 * All concrete importers must subclass this.
 *
 * @since 3.0.0
 */
class WC_CSV_Import_Suite_Importer extends \WP_Importer {


	/** @var string importer title */
	protected $title;

	/** @var string CSV delimiter */
	protected $delimiter = ',';

	/** @var string file being imported */
	protected $file;

	/** @var array import results */
	protected $results = array();

	/** @var bool has this importer been dispatched? */
	protected $has_dispatched = false;

	/** @var array valid delimiters */
	private $valid_delimiters;

	/** @var int current CSV line number **/
	protected $line_num;

	/** @var array Import progress **/
	protected $import_progress = array();

	/** @var array Import results **/
	protected $import_results = array();

	/** @var array Taxonomy terms created during import */
	private $inserted_terms = array();

	/** @var array Translatable strings for the UI, similar to post type labels **/
	protected $i18n = array();


	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->i18n = array(
			'count'          => esc_html__( '%s items' ),
			'count_inserted' => esc_html__( '%s items inserted' ),
			'count_merged'   => esc_html__( '%s items merged' ),
			'count_skipped'  => esc_html__( '%s items skipped' ),
			'count_failed'   => esc_html__( '%s items failed' ),
		);
	}


	/**
	 * Manages the separate stages of the CSV import process
	 *
	 * This method may be called either before any output is sent to the buffer
	 * or when some output has already been sent. The first case should only
	 * be used for handling POST requests in the CSV import process, and visitor
	 * should be redirected to a idempotent page after processing the request.
	 *
	 * The importer class uses the 'redirect after post' pattern - and all
	 * subclasses must also follow the same pattern.
	 * This is to make sure reloading a screen during import does not alter the
	 * import progress in any way.
	 *
	 * 1. Display introductory text and source select
	 * 2. Handle the physical upload/sideload of the source
	 * 3. Detect delimiter, display preview and import options
	 * 4. Display column mapper
	 * 5. Kick-off parsing & importing from the input source
	 *
	 * @since 3.0.0
	 */
	public function dispatch() {

		// prevent dispatching more than once
		if ( $this->has_dispatched ) {
			return;
		}

		$this->has_dispatched = true;

		$step   = isset( $_GET['step'] )       ? (int) $_GET['step'] : 0;
		$type   = isset( $_GET['import'] )     ? sanitize_key( $_GET['import'] ) : null;
		$source = isset( $_GET['source'] )     ? sanitize_key( $_GET['source'] ) : null;
		$action = isset( $_REQUEST['action'] ) ? trim( $_REQUEST['action'] ) : null;
		$file   = isset( $_REQUEST['file'] )   ? trim( $_REQUEST['file'] ) : null;
		$job_id = isset( $_REQUEST['job_id'] ) ? trim( $_REQUEST['job_id'] ) : null;

		if ( ! $action ) {
			$this->header();
		}

		// non-idempotent steps - action/POST request handlers
		if ( $action ) {

			switch ( $action ) {

				// handle source upload/sideload
				case 'upload':

					check_admin_referer( 'import-upload' );
					$file_path = $this->handle_upload( $type, $source );

					if ( $file_path ) {

						$redirect_to = admin_url( 'admin.php?import=' . $type . '&step=2&file=' . urlencode( $file_path ) );

						wp_safe_redirect( $redirect_to );
						exit;
					}
				break;

				// kick-off parsing & import
				case 'kickoff':

					check_admin_referer( 'import-woocommerce' );

					if ( $file ) {

						$bytes = filesize( $file );

						/**
						 * Filter CSV import options
						 *
						 * @since 3.0.0
						 * @param array $options
						 * @param string $file Path to CSV file
						 * @param string $type Import type
						 */
						$options = apply_filters( 'wc_csv_import_suite_import_options', (array) $_REQUEST['options'], $file, $type );

						// Setting default options will ensure that these options are set
						// even if they're unchecked
						$default_options = array(
							'merge'               => false,
							'dry_run'             => false,
							'insert_non_matching' => false,
							'debug_mode'          => false,
						);

						$options = wp_parse_args( $options, $default_options );

						// add logging if it's been enabled for this import
						if ( $options['debug_mode'] ) {
							update_option( 'wc_csv_import_suite_debug_mode', 'yes' );
						}

						$job_attrs = array(
							'type'      => $type,
							'file_path' => $file,
							'file_size' => $bytes,
							'options'   => $options,
						);

						$this->start_background_import( $job_attrs );

					}
				break;

				case 'run_live':

					check_admin_referer( 'import-woocommerce' );

					if ( $job_id ) {

						$results = get_option( 'wc_csv_import_suite_background_import_job_' . $job_id );

						if ( $results ) {
							$job = json_decode( $results, true );

							if ( 'completed' == $job['status'] ) {

								$options            = $job['options'];
								$options['dry_run'] = false;

								$job_attrs = $job;
								$job_attrs['options'] = $options;
							}

							$this->start_background_import( $job_attrs );
						}

					}

				break;

			}
		}

		// idempotent steps
		else {

			switch ( $step ) {

				// 0. greeting and import source options form
				case 0:
					// render job import progress
					if ( $job_id ) {
						$this->render_import_progress( $_GET['job_id'] );
					}

					else {
						$this->render_import_source_options();
					}
				break;

				// 1. display file upload / url / copy-paste input form
				case 1:
					$this->render_source_input_form( $type, $source );
				break;

				// 2. detect delimiter and render additional options & preview
				case 2:

					if ( $file ) {

						// sanity check - does the file exist?
						$this->ensure_file_is_readable( $file );

						$sample       = \WC_CSV_Import_Suite_Parser::get_sample( $file );
						$delimiter    = $this->guess_delimiter( $sample );
						list( $data, $headers ) = \WC_CSV_Import_Suite_Parser::parse_sample_data( $file, $delimiter );

						$data = array( 1 => $headers ) + $data;

						$this->render_import_options( $data, $delimiter );
					}

				break;

				// 3. display column mapper
				case 3:

					if ( $file ) {

						// sanity check - does the file exist?
						$this->ensure_file_is_readable( $file );

						$options = (array) $_REQUEST['options'];

						list( $data, $raw_headers ) = \WC_CSV_Import_Suite_Parser::parse_sample_data( $file, $options['delimiter'], 3 );

						$this->render_column_mapper( $data, $options, $raw_headers );
					}

				break;
			}
		}

		if ( ! $action ) {
			$this->footer();
		}
	}


	/**
	 * Kick off background import for the provided importer
	 *
	 * Will redirect the browser to the import progress screen
	 *
	 * @since 3.0.0
	 * @param array $data Job attrs for WC_CSV_Import_Suite_Background_Import
	 */
	private function start_background_import( $attrs ) {

		// sanity check - does the file exist? the file may be removed between
		// dry & live run
		$this->ensure_file_is_readable( $attrs['file_path'] );

		$background_jobs = wc_csv_import_suite()->get_background_import_instance();

		$job = $background_jobs->create_job( $attrs );
		$background_jobs->dispatch();

		$redirect_to = admin_url( 'admin.php?import=' . $attrs['type'] . '&job_id=' . urlencode( $job->id ) );

		wp_safe_redirect( $redirect_to );
		exit;
	}


	/**
	 * Display import page title
	 *
	 * @since 3.0.0
	 */
	protected function header() {
		echo '<div class="wrap"><div class="icon32" id="icon-woocommerce-importer"><br></div>';
		echo '<h2>' . $this->get_title() . '</h2>';

		wc_csv_import_suite()->get_message_handler()->load_messages();
		wc_csv_import_suite()->get_message_handler()->show_messages();
	}


	/**
	 * Close div.wrap
	 *
	 * @since 3.0.0
	 */
	protected function footer() {
		echo '<script type="text/javascript">jQuery( ".importer_loader, .progress" ).hide();</script>';
		echo '</div>';
	}


	/**
	 * Render introductory text and source select form
	 *
	 * @since 3.0.0
	 */
	protected function render_import_source_options() {

		$upload_dir = wp_upload_dir();

		/**
		 * Filter available import source options
		 *
		 * @since 3.0.0
		 * @param array $options Array of source options
		 */
		$source_options = apply_filters( 'wc_csv_import_suite_source_options', array(

			array(
				'value'       => 'upload',
				'title'       => __( 'CSV or tab-delimited text file', 'woocommerce-csv-import-suite' ),
				'description' => __( 'Upload & import data from .csv or .txt files', 'woocommerce-csv-import-suite' ),
				'default'     => true,
			),

			array(
				'value'       => 'url',
				'title'       => __( 'URL or file path', 'woocommerce-csv-import-suite' ),
				'description' => __( 'Import data from an URL or path to a file on the server.', 'woocommerce-csv-import-suite' ),
			),

			array(
				'value'       => 'copypaste',
				'title'       => __( 'Copy/paste from file', 'woocommerce-csv-import-suite' ),
				'description' => __( 'Copy & paste data from .xls, .xlsx or .csv files.', 'woocommerce-csv-import-suite' ),
			),

		) );

		include( 'admin/views/html-import-source-options.php' );
	}


	/**
	 * Render source input form
	 *
	 * @since 3.0.0
	 * @param string $source
	 */
	protected function render_source_input_form( $type, $source = 'upload' ) {

		// give this instance a descriptive name to be used in the view template
		$csv_importer = $this;

		include( 'admin/views/html-import-source-form.php' );
	}


	/**
	 * Render import source input fields
	 *
	 * Render form fields for a specific import source type. For example,
	 * this function will render the upload form controls for `upload`
	 * source type.
	 *
	 * Custom source types are supported via the action hook.
	 *
	 * @since 3.0.0
	 * @param string $source
	 */
	protected function render_source_input_fields( $source = 'upload' ) {

		// give this instance a descriptive name to be used in the view templates
		$csv_importer = $this;

		switch ( $source ) {

			case 'upload':

				$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
				$size       = size_format( $bytes );
				$upload_dir = wp_upload_dir();

				include( 'admin/views/html-import-source-fields-upload.php' );
			break;

			case 'url':
				include( 'admin/views/html-import-source-fields-url.php' );
			break;

			case 'copypaste':
				include( 'admin/views/html-import-source-fields-copypaste.php' );
			break;

		}

		/**
		 * Fires when rendering input fields for an import source type
		 *
		 * Allows 3rd parties to handle custom import sources
		 *
		 * @since 3.0.0
		 * @param string $source
		 */
		do_action( 'wc_csv_import_suite_import_source_input_fields', $source );

	}


	/**
	 * Render import options & preview from CSV input
	 *
	 * @since 3.0.0
	 * @param string $data
	 * @param string $delimiter
	 * @return string HTML import options & preview
	 */
	protected function render_import_options( $data, $delimiter ) {

		$rows = \WC_CSV_Import_Suite_Parser::generate_html_rows( $data );

		// give this instance a descriptive name to be used in the view template
		$csv_importer = $this;

		include( 'admin/views/html-import-options.php' );

		wc_enqueue_js( 'wc_csv_import_suite.is_import_options_screen = true;' );
	}


	/**
	 * Render advanced import options
	 *
	 * @since 3.0.0
	 */
	protected function render_advanced_import_options() {
		// no-op, implement in subclass as needed
	}


	/**
	 * Render column mapper for CSV import
	 *
	 * @since 3.0.0
	 * @param array $input
	 * @param array $options
	 * @param array $raw_headers
	 * @return string HTML for column mapper
	 */
	protected function render_column_mapper( $data, $options, $raw_headers ) {

		$headers     = array_keys( $data[2] ); // data always starts from 2nd line
		$columns     = array();
		$sample_size = count( $data );

		foreach ( $headers as $heading ) {

			$importer = sanitize_key( $_GET['import'] );

			// determine default mapping for heading
			$mapping = \WC_CSV_Import_Suite_Parser::normalize_heading( $heading );

			if ( Framework\SV_WC_Helper::str_starts_with( $heading, 'meta:' ) ) {
				$mapping = 'import_as_meta';
			}

			if ( Framework\SV_WC_Helper::str_starts_with( $heading, 'tax:' ) ) {
				$mapping = 'import_as_taxonomy';
			}

			/**
			 * Filter default CSV column <-> field mapping
			 *
			 * @since 3.0.0
			 * @param string $map_to Field to map the column to. Defaults to column name
			 * @param string $column Column name from CSV file
			 */
			$default_mapping = apply_filters( "wc_csv_import_suite_{$importer}_column_default_mapping", $mapping, $heading );

			$columns[ $heading ] = array(
				'default_mapping' => $default_mapping,
				'sample_values'   => array(),
			);

			foreach ( $data as $row ) {
				$columns[ $heading ]['sample_values'][] = isset( $row[ $heading ] ) ? $row[ $heading ] : '';
			}
		}

		/**
		 * Filter column mapping options
		 *
		 * @since 3.0.0
		 * @param array $mapping_options Associative array of column mapping options
		 * @param string $importer Importer type
		 * @param array $headers Normalized headers
		 * @param array $raw_headers Raw headers from CSV file
		 * @param array $columns Associative array as 'column' => 'default mapping'
		 */
		$mapping_options = apply_filters( 'wc_csv_import_suite_column_mapping_options', $this->get_column_mapping_options(), $importer, $headers, $raw_headers, $columns );
		$mapping_options['import_as_meta']     = __( 'Custom Field with column name', 'woocommerce-csv-import-suite' );
		$mapping_options['import_as_taxonomy'] = __( 'Taxonomy with column name', 'woocommerce-csv-import-suite' );

		// give this instance a descriptive name to be used in the view template
		$csv_importer = $this;

		include( 'admin/views/html-import-column-mapper.php' );
	}


	/**
	 * Generate mapping options HTML
	 *
	 * @since 3.0.0
	 * @param array $options
	 * @param string $field
	 * @return string HTML
	 */
	public function generate_mapping_options_html( $options, $field ) {

		$output = '';

		foreach ( $options as $key => $value ) {

			if ( is_array( $value ) ) {

				$output .= '<optgroup label="' . esc_attr( $key ) .'">';

				foreach ( $value as $_key => $_value ) {
					$output .= $this->generate_select_option_html( $_key, $_value, $field );
				}

				$output .= '</optgroup>';

			} else {
				$output .= $this->generate_select_option_html( $key, $value, $field );
			}
		}

		return $output;
	}


	/**
	 * Generate HTML for a single option
	 *
	 * @since 3.0.0
	 * @param mixed $value
	 * @param mixed $label
	 * @param mixed $selected
	 * @return string
	 */
	private function generate_select_option_html( $value, $label, $selected ) {

		if ( is_int( $value ) && is_string( $label ) ) {
			$value = $label;
		}

		return '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $selected, false ) . ' >' . esc_html( $label ) . '</option>';
	}


	/**
	 * Render import progress / results
	 *
	 * @since 3.0.0
	 * @param string $job_id
	 */
	protected function render_import_progress( $job_id ) {

		// get job data
		$background_jobs = wc_csv_import_suite()->get_background_import_instance();
		$job             = $background_jobs->get_job( $job_id );

		if ( empty( $job ) ) {
			echo '<p>' . sprintf( esc_html__( 'Could not find job "%s". It may have been completed a while ago, deleted or never existed.', 'woocommerce-csv-import-suite' ), esc_html( $job_id ) ) . '</p>';
			return;
		}

		$filename               = basename( $job->file_path );
		$is_complete            = 'completed' === $job->status;
		$progress               = $background_jobs->get_job_progress( $job->id );
		$percentage             = ! $is_complete && $progress['pos'] ? round( $progress['pos'] / $job->file_size * 100 ) : 0;
		$options                = (array) $job->options;
		$results                = $is_complete ? $job->results : $background_jobs->get_job_results( $job->id );
		$some_skipped_or_failed = false;

		$counts = array(
			'inserted' => 0,
			'merged'   => 0,
			'skipped'  => 0,
			'failed'   => 0,
		);

		// count results
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {

				$counts[ $result['status'] ]++;

				// check if any lines were skipped or failed
				if ( ! $some_skipped_or_failed && in_array( $result['status'], array( 'skipped', 'failed' ), true ) ) {
					$some_skipped_or_failed = true;
				}
			}
		}

		// prepare chart legends
		$legends = array(

			'inserted' => array(
				'title' => sprintf( $this->i18n['count_inserted'], '<strong><span class="amount">' . $counts['inserted'] . '</span></strong> ' ),
				'label' => esc_html__( 'Inserted', 'woocommerce-csv-import-suite' ),
				'color' => '#5cc488',
				'highlight_series' => 0,
			),

			'merged' => array(
				'title' => sprintf( $this->i18n['count_merged'], '<strong><span class="amount">' . $counts['merged'] . '</span></strong> ' ),
				'label' => esc_html__( 'Merged', 'woocommerce-csv-import-suite' ),
				'color' => '#3498db',
				'highlight_series' => 1,
			),

			'skipped' => array(
				'title' => sprintf( $this->i18n['count_skipped'], '<strong><span class="amount">' . $counts['skipped'] . '</span></strong> ' ),
				'label' => esc_html__( 'Skipped', 'woocommerce-csv-import-suite' ),
				'color' => '#f1c40f',
				'highlight_series' => 2,
			),

			'failed' => array(
				'title' => sprintf( $this->i18n['count_failed'], '<strong><span class="amount">' . $counts['failed'] . '</span></strong> ' ),
				'label' => esc_html__( 'Failed', 'woocommerce-csv-import-suite' ),
				'color' => '#e74c3c',
				'highlight_series' => 3,
			),
		);

		// give this instance a descriptive name to be used in the view template
		$csv_importer = $this;

		include( 'admin/views/html-import-progress.php' );

		$run_live_url = wp_nonce_url( admin_url( 'admin.php?import=' . esc_attr( $_GET['import'] ) . '&job_id=' . esc_attr( $job_id ) . '&action=run_live' ), 'import-woocommerce' );

		wp_register_script( 'wc-reports', WC()->plugin_url() . '/assets/js/admin/reports.min.js', array( 'jquery', 'jquery-ui-datepicker' ), WC_VERSION );

		wp_enqueue_script( 'wc-reports' );
		wp_enqueue_script( 'flot' );
		wp_enqueue_script( 'flot-pie' );

		wc_enqueue_js( "
			wc_csv_import_suite.is_import_progress_screen = true;
			wc_csv_import_suite.chart_legends = " . json_encode( $legends ) .  ";
			wc_csv_import_suite.status_counts = " . json_encode( $counts ) .  ";
			wc_csv_import_suite.i18n.chart_tooltip = '" . $this->i18n['count'] . "';
			wc_csv_import_suite.draw_results_chart();
		" );

		if ( ! $is_complete ) {

			/* translators: Placeholders: %1$s, %3$s - opening <a> tag, %2$s, %4$s - closing </a> tag */
			$dry_run_complete = sprintf( esc_html__( 'Performed a dry run with the selected file. No database records were inserted or updated. %1$sRun a live import now%2$s or %3$sChange import settings%4$s.', 'woocommerce-csv-import-suite' ), '<a href="' . wp_nonce_url( admin_url( 'admin.php?import=' . esc_attr( $_GET['import'] ) . '&job_id=' . esc_attr( $job->id ) . '&action=run_live' ), 'import-woocommerce' ) . '">', '</a>', '<a href="' . admin_url( 'admin.php?import=' . esc_attr( $_GET['import'] ) . '&step=2&file=' . urlencode( $job->file_path ) ) . '">', '</a>' );

			wc_enqueue_js( "
				wc_csv_import_suite.i18n.dry_run_complete = '" . $dry_run_complete . "';
				wc_csv_import_suite.file_size = " . (int) $job->file_size . ";
				wc_csv_import_suite.progress = " . (int) $progress['pos'] . ";
				wc_csv_import_suite.processed_items = " . ( is_array( $results ) || is_object( $results ) ? count( $results ) : 0 ) . ";
				wc_csv_import_suite.results = " . json_encode( $results ) . ";
				wc_csv_import_suite.dry_run = " . ( ( (bool) $job->options['dry_run'] ) ? 'true' : 'false' ) . ";
				wc_csv_import_suite.display_import_progress( '" . $job_id . "' );
			" );
		}
	}


	/**
	 * Handles the CSV source upload/sideload
	 *
	 * @since 3.0.0
	 * @param string $type
	 * @param string $source
	 * @return string File path in local filesystem or false on failure
	 */
	protected function handle_upload( $type, $source = 'upload' ) {

		$file_path = false;

		switch ( $source ) {

			// handle uploaded files
			case 'upload':

				// add filter upload_dir to change default upload directory to store uploaded csv files
				add_filter( 'upload_dir', array( $this, 'change_upload_dir' ) );

				// add filter to randomize the imported file's name with a time-stamp
				add_filter( 'wp_handle_upload_prefilter', array( $this, 'randomize_imported_file_name' ) );

				$results = wp_import_handle_upload();

				// remove filter wp_handle_upload_prefilter
				remove_filter( 'wp_handle_upload_prefilter', array( $this, 'randomize_imported_file_name' ) );

				// remove filter upload_dir
				remove_filter( 'upload_dir', array( $this, 'change_upload_dir' ) );

				if ( isset( $results['error'] ) ) {
					$this->handle_upload_error( $results['error'] );
					return false;
				}

				$file_path = $results['file'];

			break;

			// handle URL or path input
			case 'url':

				if ( empty( $_POST['url'] ) ) {
					$error = __( 'Please provide a file path or URL', 'woocommerce-csv-import-suite' );
					$this->handle_upload_error( $error );
					return false;
				}

				// if this is an URL, try to sideload the file
				if ( filter_var( $_POST['url'], FILTER_VALIDATE_URL ) ) {

					require_once( ABSPATH . 'wp-admin/includes/file.php' );

					// download the URL to a temp file
					$temp_file = download_url( $_POST['url'], 5 );

					if ( is_wp_error( $temp_file ) ) {
						$this->handle_upload_error( $temp_file );
						return false;
					}

					// array based on $_FILE as seen in PHP file uploads
					$input = array(
						'name'     => basename( $_POST['url'] ),
						'type'     => 'image/png',
						'tmp_name' => $temp_file,
						'error'    => 0,
						'size'     => filesize( $temp_file ),
					);

					// move the temporary file into the uploads directory
					$results = wp_handle_sideload( $input, array( 'test_form' => false ) );

					if ( ! empty( $results['error'] ) ) {
						$this->handle_upload_error( $results['error'] );
						return false;
					}

					$file_path = $results['file'];
				}

				// perhaps it's a path to file?
				else {

					if ( ! is_readable( $_POST['url'] ) ) {
						$error = sprintf( __( 'Could not find the file %s', 'woocommerce-csv-import-suite' ), esc_html( $_POST['url'] ) );
						$this->handle_upload_error( $error );
						return false;
					}

					$file_path = esc_attr( $_POST['url'] );
				}

			break;

			// handle copy-pasted data
			case 'copypaste':

				$data = stripslashes( $_POST['copypaste'] );

				if ( empty( $data ) ) {
					$error = __( 'Please enter some data to import', 'woocommerce-csv-import-suite' );
					$this->handle_upload_error( $error );
					return false;
				}

				$results = wp_upload_bits( $type . '-' . date( 'Ymd-His' ) . '.csv', null, $data );

				if ( ! empty( $results['error'] ) ) {
					$this->handle_upload_error( $results['error'] );
					return false;
				}

				$file_path = $results['file'];

			break;

		}

		return $file_path;
	}


	/**
	 * Ensure that the provided file path is readable
	 *
	 * If file not readbale, will redirect user back to the previous screen
	 * with an appropriate error message.
	 *
	 * @since 3.1.0
	 * @param string $file_path
	 */
	private function ensure_file_is_readable( $file_path ) {

		if ( ! is_readable( $file_path ) ) {

			/* translators: Placeholders: %s - file path */
			$this->handle_upload_error( sprintf( __( 'Cannot open file %s for importing. The file may not exist or is not readable by WordPress.', 'woocommerce-csv-import-suite' ), $file_path ) );
		}
	}


	/**
	 * Handle source upload error
	 *
	 * @since 3.0.0
	 * @param string|WP_Error $error Error message
	 */
	protected function handle_upload_error( $error ) {

		$message = is_wp_error( $error ) ? $error->get_error_message() : $error;
		$message = sprintf( esc_html__( 'Sorry, there has been an error: %s', 'woocommerce-csv-import-suite' ), $message );

		wc_csv_import_suite()->get_message_handler()->add_error( $message );

		wp_redirect( wp_get_referer() );
		exit;
	}


	/**
	 * Import a CSV file, or a part of it
	 *
	 * @since 3.0.0
	 * @param string $file Path to file
	 * @param array $options General & import type specific options
	 */
	 public function import( $file, $options = array() ) {

		$this->file    = $file;

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		// read raw data from CSV file
		list( $parsed_data, $raw_headers, $position, $last_line_num ) = \WC_CSV_Import_Suite_Parser::parse( $file, $options );

		$this->import_progress = array(
			'line' => $last_line_num,
			'pos'  => $position
		);

		$this->import_lines( $parsed_data, $last_line_num, $options, $raw_headers );

		// done importing, cleanup
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		do_action( 'import_end' );
	}


	/**
	 * Import each line one-by-one from CSV
	 *
	 * @since 3.0.0
	 * @param array $parsed_data Parsed data from CSV
	 * @param int $last_line_num Last parsed line number in parsed data
	 * @param array $options Import/parsing options
	 * @param bool $raw_headers Raw headers from CSV file
	 */
	protected function import_lines( $parsed_data, $last_line_num, $options, $raw_headers ) {

		$is_multiline_format = $this->is_multiline_format( $raw_headers );

		// loop over extracted lines and import them one by one
		for ( $line_num = $options['start_line']; $line_num <= $last_line_num; ) {

			wc_csv_import_suite()->log( '---' );

			// the line num key may not be set in cases where a single item spans
			// across multiple lines and the total number of lines in CSV is more than
			// total number of parsed items. if this ever happens, stop importing.
			if ( ! isset( $parsed_data[ $line_num ] ) ) {
				break;
			}

			$item          = $parsed_data[ $line_num ];
			$parsed_item   = null;
			$related_items = null;

			// set internal current line number counter
			$this->line_num = $line_num;

			// the item might span across multiple lines, try to look up all the
			// lines related to this item
			if ( $is_multiline_format && $item_identifier = $this->get_item_identifier( $item ) ) {

				$first_line_num = $line_num; // store first line number for this item
				$related_items  = $this->find_related_items( $item_identifier, $parsed_data, $line_num, $options );

				// looks like some lines belong together to form a single item. Let's
				// parse them all one by one
				if ( ! empty( $related_items ) ) {

					$related_items = array( $first_line_num => $item ) + $related_items;
					$parsed_item   = $this->parse_multiline_item( $item_identifier, $related_items, $options, $raw_headers );
				}

			}

			// single item per line - this one's easy!
			if ( empty( $related_items ) && ! $parsed_item ) {
				try {
					$parsed_item = $this->parse_item( $item, $options, $raw_headers );
				} catch ( WC_CSV_Import_Suite_Import_Exception $e ) {
					$this->add_import_result( 'skipped', $e->getMessage() );
				}
			}

			if ( $parsed_item ) {
				$this->process_item( $parsed_item, $options, $raw_headers );
			}

			// increment line index manually
			$line_num++;

			unset( $item, $parsed_item );
		}
	}


	/**
	 * Find related items (lines) in a multi-line format CSV file
	 *
	 * @since 3.0.0
	 * @param mixed $item_identifier Identifier used to match related lines
	 * @param array $parsed_data Parsed data from CSV
	 * @param int $line_num Current line number counter. Passed by reference
	 * @param array $options Import options
	 * @return array Array of related items/lines
	 */
	protected function find_related_items( $item_identifier, $parsed_data, &$line_num, $options ) {

		$related_items = array();

		do {

			$next_item      = $next_parsed_item = null;
			$item_continued = $results          = false;

			// the next line has already been read from the CSV file
			if ( isset( $parsed_data[ $line_num + 1 ] ) ) {
				$next_item = $parsed_data[ $line_num + 1 ];
			}

			// read the next line from CSV file
			else {

				$_options = $options;
				$_options['start_pos']  = $this->import_progress['pos']; // we continue from last pointer position
				$_options['start_line'] = $this->import_progress['line'] + 1; // but we need to increment the line number ourselves
				$_options['max_lines']  = 1;

				// read raw data from CSV file
				$results   = \WC_CSV_Import_Suite_Parser::parse( $this->file, $_options );
				$next_item = ! empty( $results[0] ) ? $results[0][ $line_num + 1 ] : null;
			}

			// an item (line) was successfully found
			if ( ! empty( $next_item ) ) {

				// check if the next line is related to the last (current) line
				$item_continued = $item_identifier == $this->get_item_identifier( $next_item );

				// if the next item identifier matches current, we know those lines
				// belong together to form a single item
				if ( $item_continued ) {

					// increment import progress
					// NB! Intentional overwrite of $line_num
					$line_num++;

					$related_items[ $line_num ] = $next_item;

					$this->import_progress = array(
						'line' => $line_num,
						'pos'  => $results[2], // last file position pointer
					);
				}
			}

		} while ( $next_item && $item_continued );

		return $related_items;
	}


	/**
	 * Parse related items in a multi-line CSV format
	 *
	 * @since 3.0.0
	 * @param mixed $item_identifier Common identifier for the related lines
	 * @param array $related_items Items that are related and make up 1 single item
	 * @param array $options Import options
	 * @param array $raw_headers Raw headers from CSV
	 * @throws WC_CSV_Import_Suite_Import_Exception
	 * @return string[] parsed data
	 */
	protected function parse_multiline_item( $item_identifier, $related_items, $options, $raw_headers ) {

		$parsed_items       = array();
		$skipped_items      = array();
		$related            = implode( ', ', array_keys( $related_items ) );

		wc_csv_import_suite()->log( sprintf( __( '> Preparing multi-line item %s (rows %s)', 'woocommerce-csv-import-suite' ), $item_identifier, $related ) );

		foreach ( $related_items as $line_num => $item ) {

			// set the internal line number counter - this will ensure that any
			// validation errors are reported with correct line numbers
			$this->line_num = $line_num;

			$_parsed_item = null;

			try {
				$_parsed_item = $this->parse_item( $item, $options, $raw_headers );

			} catch ( WC_CSV_Import_Suite_Import_Exception $e ) {

				$this->add_import_result( 'skipped', $e->getMessage() );
				$skipped_items[] = $line_num;
			}

			if ( $_parsed_item ) {
				$parsed_items[ $line_num ] = $_parsed_item;
			}
		}

		// one ore more lines were skipped. we want to skip importing all related
		// lines to avoid data corruption.
		if ( ! empty( $skipped_items ) ) {

			$delta   = array_diff( array_keys( $related_items ), $skipped_items );
			$skipped = implode( ', ', $delta );

			wc_csv_import_suite()->log( sprintf( __( '> Skipped importing rows %s due to issues with related rows.', 'woocommerce-csv-import-suite' ), $skipped ) );

			return null;
		}

		// no errors occured in parsing stage, let's merge the parsed items
		// into a single item
		else {
			return $this->merge_parsed_items( $parsed_items );
		}
	}


	/**
	 * Checks whether the CSV uses a multi-line format
	 *
	 * Checks whether data for a single item spans across multiple physical lines
	 * in the CSV file.
	 *
	 * Implement at subclass level.
	 *
	 * @since 3.0.0
	 * @param array $raw_headers Raw CSV headers
	 * @return bool
	 */
	protected function is_multiline_format( $raw_headers ) {
		return false;
	}


	/**
	 * Get identifier for a single item
	 *
	 * Utility method to get a unique identifier for a single item in a CSV file.
	 * Useful for detecting physical lines in a CSV file to form a single item.
	 *
	 * @since 3.0.0
	 * @param array $data Item data, either raw data from CSV parser, mapped to
	 *                    columns, or parsed item data
	 * @return int|string|null
	 */
	public function get_item_identifier( $data ) {
		return null;
	}


	/**
	 * Merge data from multiple parsed lines into one item
	 *
	 * Must be implemented at subclass level. By default wilkl return the first
	 * item.
	 *
	 * @since 3.0.0
	 * @param array $items Array of parsed items
	 * @return array
	 */
	protected function merge_parsed_items( $items ) {
		return array_shift( $items );
	}


	/**
	 * Parse an item into something usable
	 *
	 * Override this method on subclass level
	 *
	 * @since 3.0.0
	 * @param array $item Raw item data from CSV
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 * @return mixed|bool Parsed item or false on failure
	 */
	protected function parse_item( $item, $options = array(), $raw_headers = array() ) {
		return $item;
	}


	/**
	 * Process an item
	 *
	 * This usually means inserting or updating something in the database.
	 * Override this method on subclass level.
	 *
	 * @since 3.0.0
	 * @param mixed $item Parsed item ready for processing
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 * @return mixed
	 */
	protected function process_item( $item, $options = array(), $raw_headers = array() ) {
		// no-op
	}


	/**
	 * Parses taxonomy & terms from a key and its values.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key
	 * @param string $value
	 * @return array|null Array with parsed taxonomy name and it's terms, or null on failure
	 */
	public function parse_taxonomy_terms( $key, $value ) {

		// get taxonomy
		$taxonomy = trim( str_replace( 'tax:', '', $key ) );

		// exists?
		if ( ! taxonomy_exists( $taxonomy ) ) {
			wc_csv_import_suite()->log( sprintf( __('> > Skipping taxonomy "%s" - it does not exist.', 'woocommerce-csv-import-suite'), $taxonomy ) );
			return null;
		}

		// get terms - ID => parent
		$terms     = array();
		$raw_terms = explode( '|', $value );
		$raw_terms = array_map( 'trim', $raw_terms );

		// handle term hierarchy (>)
		foreach ( $raw_terms as $raw_term ) {

			if ( Framework\SV_WC_Helper::str_exists( $raw_term, '>' ) ) {

				$raw_term = explode( '>', $raw_term );
				$raw_term = array_map( 'trim', $raw_term );
				$raw_term = array_map( 'esc_html', $raw_term );
				$raw_term = array_filter( $raw_term );

				$parent = 0;
				$loop   = 0;

				foreach ( $raw_term as $term ) {

					$loop ++;
					$term_id = '';

					if ( isset( $this->inserted_terms[ $taxonomy ][ $parent ][ $term ] ) ) {

						$term_id = $this->inserted_terms[ $taxonomy ][ $parent ][ $term ];

					} elseif ( $term ) {

						// check term existence
						$term_may_exist = term_exists( $term, $taxonomy, absint( $parent ) );

						if ( is_array( $term_may_exist ) ) {

							$possible_term = get_term( $term_may_exist['term_id'], $taxonomy );

							if ( $possible_term->parent == $parent ) {
								$term_id = $term_may_exist['term_id'];
							}
						}

						if ( ! $term_id ) {

							// create appropriate slug
							$slug = array();

							for ( $i = 0; $i < $loop; $i ++ ) {
								$slug[] = $raw_term[ $i ];
							}

							$slug = sanitize_title( implode( '-', $slug ) );
							$t    = wp_insert_term( $term, $taxonomy, array( 'parent' => $parent, 'slug' => $slug ) );

							if ( ! is_wp_error( $t ) ) {
								$term_id = $t['term_id'];
							} else {
								wc_csv_import_suite()->log( sprintf( __( '> > (' . $this->get_line_num() . ') Failed to import term %s, parent %s - %s', 'woocommerce-csv-import-suite' ), sanitize_text_field( $term ), sanitize_text_field( $parent ), sanitize_text_field( $taxonomy ) ) );
								break;
							}
						}

						$this->inserted_terms[ $taxonomy ][ $parent ][ $term ] = $term_id;
					}

					if ( ! $term_id ) {
						break;
					}

					// sdd to terms, ready to set if this is the final term
					if ( count( $raw_term ) === $loop ) {
						$terms[] = $term_id;
					}

					$parent = $term_id;
				}

			} else {

				$term_id  = '';
				$raw_term = esc_html( $raw_term );

				if ( isset( $this->inserted_terms[ $taxonomy ][0][ $raw_term ] ) ) {

					$term_id = $this->inserted_terms[ $taxonomy ][0][ $raw_term ];

				} elseif ( $raw_term ) {

					// Check term existance
					$term_exists = term_exists( $raw_term, $taxonomy, 0 );
					$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : 0;

					if ( ! $term_id ) {
						$t = wp_insert_term( trim( $raw_term ), $taxonomy, array( 'parent' => 0 ) );

						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
						} else {
							wc_csv_import_suite()->log( sprintf( __( '> > Failed to import term %s %s', 'woocommerce-csv-import-suite' ), esc_html( $raw_term ), esc_html( $taxonomy ) ) );
							break;
						}
					}

					$this->inserted_terms[ $taxonomy ][0][ $raw_term ] = $term_id;
				}

				// store terms for later insertion
				if ( $term_id ) {
					$terms[] = $term_id;
				}
			}
		}

		return ! empty( $terms ) ? array( $taxonomy, $terms ) : null;
	}


	/**
	 * Process terms
	 *
	 * @since 3.0.0
	 * @param int $post_id
	 * @param array $terms_to_process
	 */
	protected function process_terms( $post_id, $terms_to_process ) {

		if ( empty( $terms_to_process ) || ! is_array( $terms_to_process ) ) {
			return;
		}

		// add categories, tags and other terms
		$terms_to_set = array();

		foreach ( $terms_to_process as $term_group ) {

			$taxonomy = $term_group['taxonomy'];
			$terms    = $term_group['terms'];

			if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			if ( ! is_array( $terms ) ) {
				$terms = array( $terms );
			}

			$terms_to_set[ $taxonomy ] = array();

			foreach ( $terms as $term_id ) {
				if ( $term_id ) {
					$terms_to_set[ $taxonomy ][] = (int) $term_id;
				}
			}
		}

		foreach ( $terms_to_set as $tax => $ids ) {
			wp_set_post_terms( $post_id, $ids, $tax, false );
		}
	}


	/**
	 * Log a row's import status
	 *
	 * @since 3.0.0
	 * @param int $line_num Line number from CSV file
	 * @param string $status Status
	 * @param string $message Optional
	 * @param bool $log Optional. Whether to log the result or not. Defaults to true
	 */
	public function add_import_result( $status, $message = '', $log = true ) {

		$this->import_results[ $this->get_line_num() ] = array(
			'status'  => $status,
			'message' => $message,
		);

		if ( $log ) {

			$labels = array(
				'inserted' => esc_html__( 'Inserted', 'woocommerce-csv-import-suite' ),
				'merged'   => esc_html__( 'Merged', 'woocommerce-csv-import-suite' ),
				'skipped'  => esc_html__( 'Skipped', 'woocommerce-csv-import-suite' ),
				'failed'   => esc_html__( 'Failed', 'woocommerce-csv-import-suite' ),
			);

			$status_label = isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
			$log_message  = sprintf( "> > %s. %s", $status_label, $message );

			wc_csv_import_suite()->log( $log_message );
		}
	}


	/**
	 * Get current line number
	 *
	 * @since 3.3.0
	 * @return int
	 */
	public function get_line_num() {
		return $this->line_num;
	}


	/**
	 * Get import results
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_import_results() {
		return $this->import_results;
	}


	/**
	 * Get import progress
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_import_progress() {
		return $this->import_progress;
	}


	/**
	 * Guess CSV delimiter used in input
	 *
	 * @since 3.0.0
	 * @return string
	 */
	protected function guess_delimiter( $input ) {

		$lines   = explode( '\n', $input );

		$line_count       = count( $lines );
		$best_delta       = null;
		$best_delimiter   = ','; // always fall back to comma
		$prev_field_count = null;

		foreach ( array_keys( $this->get_valid_delimiters() ) as $delimiter ) {

			$delta             = $avg_field_count = 0;
			$prev_field_count  = null;
			$total_field_count = 0;

			// try to parse the lines with the current delimiter
			foreach ( $lines as $line_num => $line ) {

				$data = $this->str_getcsv( $line, $delimiter );

				if ( empty( $data ) ) {
					continue;
				}

				$field_count        = count( $data );
				$total_field_count += $field_count;

				if ( null === $prev_field_count ) {
					$prev_field_count = $field_count;
				}

				else if ( $field_count > 1 ) {
					$delta += abs( $field_count - $prev_field_count );
					$prev_field_count = $field_count;
				}

			}

			$avg_field_count = $total_field_count / $line_count;

			if ( null === $best_delta || ( $delta < $best_delta && $avg_field_count >= 2 ) ) {

				$best_delta     = $delta;
				$best_delimiter = $delimiter;
			}

		}

		return $best_delimiter;
	}


	/**
	 * Parse CSV data from a string
	 *
	 * Added to provide compatibility with PHP versions < 5.3
	 *
	 * @since 3.1.0
	 * @param string $input
	 * @param string $delimiter
	 * @param return array
	 */
	private function str_getcsv( $input, $delimiter ) {

		if ( function_exists( 'str_getcsv' ) ) {

			return str_getcsv( $input, $delimiter );

		} else {

			$handle = fopen( 'php://temp', 'r+' );

			fwrite( $handle, $input );
			rewind( $handle );

			$data = fgetcsv( $handle, $delimiter );

			fclose( $handle );

			return $data;
		}
	}


	/**
	 * Count the number of lines in a TXT/CSV file
	 *
	 * @since 3.0.0
	 * @param string $file Path to file
	 * @return int|bool Number of lines in file, false on failure
	 */
	protected function count_lines_in_file( $file ) {

		$count = -1;

		// first, try *nix commands. This will only work if host has
		// enabled `exec()`, `wc` command is available and the file
		// uses LF or CRLF line endings. It will fail (report 0 lines)
		// on CR line endings.
		if ( function_exists( 'exec' ) ) {

			exec( 'wc -l < ' . escapeshellarg( $file ), $result, $exit );

			// no exit code means the command executed successfully
			if ( ! $exit && isset( $result[0] ) ) {
				$count = (int) $result[0];
			}
		}

		// if the previous method failed, use PHP
		if ( $count < 1 ) {

			$count = -1; // PHP line counts are off by 1

			@ini_set( 'auto_detect_line_endings', true );

			$handle = fopen( $file, "r" );

			while( ! feof( $handle ) ) {
			  $line = fgets( $handle );
			  $count++;
			}

			fclose( $handle );
		}

		return $count > 0 ? $count : false;
	}


	/**
	 * Get a list of possible valid delimiters
	 *
	 * @since 3.0.0
	 * @return array List of valid delimiters
	 */
	public function get_valid_delimiters() {

		if ( ! isset( $this->valid_delimiters ) ) {

			/**
			 * Filter the list of available valid delimiters
			 *
			 * @since 3.0.0
			 * @param array $delimiters
			 */
			$this->valid_delimiters = apply_filters( 'wc_csv_import_suite_delimiter_choices', array(
				","  => __( 'Comma', 'woocommerce-csv-import-suite' ),
				";"  => __( 'Semicolon', 'woocommerce-csv-import-suite' ),
				"\t" => __( 'Tab', 'woocommerce-csv-import-suite' ), // double quotes are significant
			) );
		}

		return $this->valid_delimiters;
	}


	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 *
	 * @see \WP_Importer::bump_request_timeout()
	 * @since 3.0.0
	 * @param int $val timeout value
	 * @return int 60 seconds
	 */
	public function bump_request_timeout( $val ) {
		return MINUTE_IN_SECONDS;
	}


	/**
	 * Get the title for the importer
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}


	/**
	 * Change default upload directory to csv_imports to store uploaded csv files.
	 *
	 * @since 3.4.0
	 * @param array $dirs array of upload directory data with keys of 'path', 'url', 'subdir, 'basedir', and 'error'.
	 * @return array
	 */
	public function change_upload_dir( $dirs ) {

		$subdir = '/csv_imports';

		$dirs['subdir'] = $subdir;
		$dirs['path']   = $dirs['basedir'] . $subdir;
		$dirs['url']    = $dirs['baseurl'] . $subdir;

		return $dirs;
	}


	/**
	 * Randomize the imported file's name with a time-stamp.
	 *
	 * @since 3.4.0
	 * @param array $file an array of data for a single file
	 * @return array
	 */
	function randomize_imported_file_name( $file ) {

		$file['name'] = uniqid( null, true ) . '-' . $file['name'];

		return $file;
	}


}
