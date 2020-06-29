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
 * WooCommerce CSV Import Suite AJAX handler class.
 *
 * @since 3.0.0
 */
class WC_CSV_Import_Suite_Ajax {


	/**
	 * Initialize the AJAX class
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wc_csv_import_suite_get_preview',  array( $this, 'get_preview' ) );
		add_action( 'wp_ajax_wc_csv_import_suite_get_progress', array( $this, 'get_progress' ) );

		// handle dismissed admin notices
		add_action( 'wc_csv_import_suite_dismiss_notice', array( $this, 'handle_dismiss_notice' ), 10, 2 );
	}

	/**
	 * Get preview for sample CSV data
	 *
	 * @since 3.0.0
	 */
	public function get_preview() {

		check_ajax_referer( 'get-csv-preview', 'security' );

		$delimiter = isset( $_GET['delimiter'] ) ? $_GET['delimiter'] : ',';
		$file      = isset( $_GET['file'] )      ? $_GET['file']     : '';

		if ( ! $file ) {
			wp_die();
		}

		list( $data, $headers ) = \WC_CSV_Import_Suite_Parser::parse_sample_data( $file, $delimiter );

		$data = array( 1 => $headers ) + $data;

		echo \WC_CSV_Import_Suite_Parser::generate_html_rows( $data );

		wp_die();
	}


	/**
	 * Get import progress
	 *
	 * @since 3.0.0
	 */
	public function get_progress() {

		check_ajax_referer( 'get-import-progress', 'security' );

		$job_id       = isset( $_GET['job_id'] )       ? $_GET['job_id']       : '';
		$results_from = isset( $_GET['results_from'] ) ? $_GET['results_from'] : null;

		if ( ! $job_id ) {

			wp_send_json_error( array(
				'message' => esc_html__( 'Missing import job ID.', 'woocommerce-customer-order-csv-export' ),
			) );

		}

		$results  = array();
		$progress = get_option( 'wc_csv_import_suite_background_import_progress_' . $job_id );

		// if the progress option is not found, it most probably means that the
		// job has completed processing
		if ( false === $progress ) {

			$complete = get_option( 'wc_csv_import_suite_background_import_job_' . $job_id );

			if ( $complete ) {
				$job = json_decode( $complete, true );

				if ( 'completed' == $job['status'] ) {
					$progress = $job['file_size'];
					$results  = $job['results'];
				}
			}
		} else {
			$results  = get_option( 'wc_csv_import_suite_background_import_results_' . $job_id );
			$results  = json_decode( $results, true );
			$progress = $progress['pos'];
		}

		// optionally return only a subset of results
		if ( $results_from && is_array( $results ) && ! empty( $results ) ) {

			$lines = array_keys( $results );

			// only return a subset if the requested subset starting line is greater
			// than the lowest line in the results
			if ( $results_from >= min( $lines ) ) {

				$index   = array_search( $results_from, $lines );
				$results = $index ? array_slice( $results, $index, null, true ) : null;
			}
		}

		wp_send_json( array(
			'progress' => $progress,
			'results'  => $results,
		) );
	}


	/**
	 * Handle dismissing admin notices
	 *
	 * Removes any import finished notices from db
	 *
	 * @since 3.1.0
	 * @param string $message_id
	 * @param int $user_id
	 */
	public function handle_dismiss_notice( $message_id, $user_id ) {

		if ( Framework\SV_WC_Helper::str_starts_with( $message_id, 'wc_csv_import_suite_finished_' ) ) {

			$parts  = explode( '_', $message_id );
			$job_id = array_pop( $parts );

			wc_csv_import_suite()->remove_import_finished_notice( $job_id, $user_id );

		}
	}


}
