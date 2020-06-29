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

namespace SkyVerge\WooCommerce\Memberships\CLI;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Import user memberships from CSV data.
 *
 * @since 1.13.2
 */
class Import_User_Memberships extends \WC_Memberships_CLI_Command {


	/**
	 * Import items from CSV data.
	 *
	 * @subcommand import
	 *
	 * @since 1.13.2
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function import( $args, $assoc_args ) {

		$data = $this->unflatten_array( $assoc_args );

		try {

			$file = current( $args );

			if ( ! $file || ! is_string( $file ) || '' === trim( $file ) ) {
				throw new \WC_REST_Exception( 'woocommerce_memberships_cli_invalid_user_memberships_import_file', __( 'CSV import file invalid or not found.' ), 404 );
			}

			$importer = wc_memberships()->get_utilities_instance()->get_user_memberships_import_instance();

			// since we can't handle batches from the command line we can only run an import via background processing and loopback is required
			if ( ! $importer->test_connection() ) {
				throw new \WC_REST_Exception( 'woocommerce_memberships_cli_user_memberships_import_error', __( 'Support for loopback connections is required to import memberships via command line.' ), 500 );
			}

			// parse request args and create a job, then dispatch it
			try {

				$job_args = [
					// required argument
					'file'               => $this->emulate_file_upload( $file ),
					// default values (overrideable)
					'timezone'           => wc_timezone_string(),
					'cli'                => true,
					'default_start_date' => date( 'Y-m-d', current_time( 'timestamp' ) ),
					'fields_delimiter'   => 'comma',
				];

				// default values (overrideable)
				$toggles = [
					'create_new_memberships'      => true,
					'merge_existing_memberships'  => false,
					'allow_memberships_transfer'  => false,
					'create_new_users'            => false,
					'notify_new_users'            => false,
				];

				foreach ( $toggles as $toggle => $default_value ) {

					if ( isset( $data[ $toggle ] ) ) {
						$job_args[ $toggle ] = in_array( $data[ $toggle ], [ 'yes', true, 1, '1' ], true );
					} else {
						$job_args[ $toggle ] = $default_value;
					}
				}

				if ( isset( $data['default_start_date'] ) ) {

					$job_args['default_start_date'] = wc_memberships_parse_date( $data['default_start_date'] );

					if ( ! $job_args['default_start_date'] ) {
						throw new \WC_REST_Exception( 'woocommerce_memberships_cli_invalid_user_membership_start_date', __( 'Invalid default start date.' ), 400 );
					}
				}

				if ( isset( $data['timezone'] ) ) {

					$job_args['timezone'] = $data['timezone'];

					if ( ! is_string( $job_args['timezone'] ) ) {
						throw new \WC_REST_Exception( 'woocommerce_memberships_cli_invalid_user_membership_timezone', __( 'Invalid timezone.' ), 400 );
					}
				}

				if ( isset( $data['fields_delimiter'] ) ) {

					$job_args['fields_delimiter'] = $data['fields_delimiter'];

					if ( ! in_array( $job_args['fields_delimiter'], [ 'comma', 'tab' ], true ) ) {
						throw new \WC_REST_Exception( 'woocommerce_memberships_cli_invalid_user_membership_csv_fields_delimiter', __( 'Invalid CSV fields delimiter.' ), 400 );
					}
				}

				$importer->create_job( $job_args );
				$importer->dispatch();

			} catch ( \Exception $e ) {

				throw new \WC_REST_Exception( 'woocommerce_memberships_cli_user_memberships_import_error', $e->getMessage(), 500 );
			}

			\WP_CLI::success( 'Successfully dispatched import user memberships background job.' );

		} catch ( \WC_REST_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Returns an array much like a member of the $_FILES global as if it had been uploaded via an HTTP request.
	 *
	 * This is built to properly indicate file upload error messages, but it doesn't handle file size restrictions, or transfer or write errors.
	 * @link http://php.net/manual/en/reserved.variables.files.php
	 *
	 * @since 1.13.2
	 *
	 * @param string $filename the path to the "uploaded" file
	 * @param string $type the MIME Content-Type of the file (this is assumed to be "text/csv" for now)
	 * @return array associative array with file upload data
	 */
	private function emulate_file_upload( $filename, $type = 'text/csv' ) {

		$create_file_array = static function( $filename, $type, $error = UPLOAD_ERR_OK ) {
			return [
				'name'     => basename( $filename ),
				'type'     => $type,
				'tmp_name' => $filename,
				'error'    => $error,
				'size'     => filesize( $filename ),
			];
		};

		if ( false === file_get_contents( $filename ) ) {
			return $create_file_array( $filename, $type, UPLOAD_ERR_NO_FILE );
		}

		return $create_file_array( $filename, $type );
	}


	/**
	 * Outputs the synopsis data for the command.
	 *
	 * @see \WP_CLI::add_command()
	 * @see \WC_CLI_Runner::register_route_commands()
	 *
	 * @since 1.13.2
	 *
	 * @return array associative array of WP CLI data
	 */
	public static function synopsis() {

		$synopsis = [
			[
				'name'        => 'file',
				'description' => __( 'Path to a local CSV file containing user memberships data.', 'woocommerce-memberships' ),
				'type'        => 'positional',
				'optional'    => false,
				'repeating'   => false,
			],
			[
				'name' 	      => 'fields_delimiter',
				'description' => __( 'CSV fields delimiter.', 'woocommerce-memberships' ),
			],
			[
				'name'        => 'timezone',
				'description' => __( 'The timezone in which all dates from imported CSV data are assumed to be (defaults to the current site timezone).', 'woocommerce-memberships' ),
			],
			[
				'name'        => 'default_start_date',
				'description' => __( "Default start date of imported user memberships when a date is not specified from CSV data (defaults to today's date).", 'woocommerce-memberships' ),
			],
			[
				'name'        => 'create_new_memberships',
				'description' => __( 'Create new memberships from CSV data.', 'woocommerce-memberships' ),
				'default'     => 'yes',
			],
			[
				'name'        => 'merge_existing_memberships',
				'description' => __( 'Merge imported CSV data with existing memberships.', 'woocommerce-memberships' ),
				'default'     => 'no',
			],
			[
				'name'        => 'allow_memberships_transfer',
				'description' => __( 'Allow transferring user memberships between users if a different owner is specified in CSV data.', 'woocommerce-memberships' ),
				'default'     => 'no',
			],
			[
				'name'        => 'create_new_users',
				'description' => __( 'Create a new WordPress user from CSV data when could not be determined among existing ones.' , 'woocommerce-memberships' ),
				'default'     => 'no',
			],
			[
				'name'        => 'notify_new_users',
				'description' => __( 'Send email notifications when a new user is created during an import process.', 'woocommerce-memberships' ),
				'default'     => 'no',
			],
		];

		foreach ( $synopsis as $param => $params ) {

			if ( ! isset( $synopsis[ $param ]['optional'] ) ) {
				$synopsis[ $param ]['optional'] = true;
			}

			if ( ! isset( $synopsis[ $param ]['type'] ) ) {
				$synopsis[ $param ]['type'] = 'assoc';
			}

			if ( ! isset( $synopsis[ $param ]['options'] ) && isset( $synopsis[ $param ]['default'] ) && in_array( $synopsis[ $param ]['default'], [ 'yes', 'no' ], true ) ) {
				$synopsis[ $param ]['options'] = [ 'yes', 'no' ];
			}
		}

		return $synopsis;
	}


}
