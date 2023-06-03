<?php
/**
 * Smush core class: Smush class
 *
 * @package Smush\Core\Modules
 */

namespace Smush\Core\Modules;

use Smush\Core\Api\Backoff;
use Smush\Core\Api\Request_Multiple;
use Smush\Core\Core;
use Smush\Core\Helper;
use Smush\Core\Error_Handler;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Smush\Smusher;
use Smush\Core\Smush\Smush_Optimization;
use Smush\Core\Webp\Webp_Converter;
use Smush\Core\Webp\Webp_Optimization;
use WP_Error;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Smush
 */
class Smush extends Abstract_Module {
	const ERROR_SSL_CERT = 'ssl_cert_error';

	/**
	 * Meta key to save smush result to db.
	 *
	 * @var string $smushed_meta_key
	 */
	public static $smushed_meta_key = 'wp-smpro-smush-data';

	/**
	 * Images dimensions array.
	 *
	 * @var array $image_sizes
	 */
	public $image_sizes = array();

	/**
	 * Stores the headers returned by the latest API call.
	 *
	 * @var array $api_headers
	 */
	protected $api_headers = array();

	/**
	 * Prevent third party try to run another smush while it's running.
	 *
	 * @access private
	 *
	 * @var bool
	 */
	private $prevent_infinite_loop;

	/**
	 * @var Request_Multiple
	 */
	private $request_multiple;
	/**
	 * @var Backoff
	 */
	private $backoff;

	/**
	 * WP_Smush constructor.
	 */
	public function init() {
		// Update the Super Smush count, after the Smush'ing.
		//add_action( 'wp_smush_image_optimised', array( $this, 'update_lists' ), '', 2 );

		// Smush image (Auto Smush) when `wp_generate_attachment_metadata` filter is fired.
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'smush_image' ), 15, 2 );

		// Delete backup files.
		//add_action( 'delete_attachment', array( $this, 'delete_images' ), 12 );

		// Handle the Async optimisation.
		add_action( 'wp_async_wp_generate_attachment_metadata', array( $this, 'wp_smush_handle_async' ) );
		add_action( 'wp_async_wp_save_image_editor_file', array( $this, 'wp_smush_handle_editor_async' ), '', 2 );

		// Make sure we treat scaled images as additional size.
		//add_filter( 'wp_smush_add_scaled_images_to_meta', array( $this, 'add_scaled_to_meta' ), 10, 2 );

		// Fix SSL CA certificates issue.
		add_action( 'wp_smush_before_smush_file', array( $this, 'fix_ssl_ca_certificate_error' ) );

		$this->request_multiple = new Request_Multiple();
		$this->backoff = new Backoff();
	}

	/**
	 * Check whether to show warning or not for Pro users, if they don't have a valid install
	 *
	 * @return bool
	 */
	public function show_warning() {
		// If it's a free setup, Go back right away!
		if ( ! WP_Smush::is_pro() ) {
			return false;
		}

		// Return. If we don't have any headers.
		if ( ! isset( $this->api_headers ) ) {
			return false;
		}

		// Show warning, if function says it's premium and api says not premium.
		if ( isset( $this->api_headers['is_premium'] ) && ! (int) $this->api_headers['is_premium'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Add/Remove image id from Super Smushed images count.
	 *
	 * @param int    $id       Image id.
	 * @param string $op_type  Add/remove, whether to add the image id or remove it from the list.
	 * @param string $key      Options key.
	 *
	 * @return bool Whether the Super Smushed option was update or not
	 */
	public function update_super_smush_count( $id, $op_type = 'add', $key = 'wp-smush-super_smushed' ) {
		// Get the existing count.
		$super_smushed = get_option( $key, false );

		// Initialize if it doesn't exists.
		if ( ! $super_smushed || empty( $super_smushed['ids'] ) ) {
			$super_smushed = array(
				'ids' => array(),
			);
		}

		// Insert the id, if not in there already.
		if ( 'add' === $op_type && ! in_array( $id, $super_smushed['ids'] ) ) {
			$super_smushed['ids'][] = $id;
		} elseif ( 'remove' === $op_type && false !== ( $k = array_search( $id, $super_smushed['ids'] ) ) ) {
			// Else remove the id from the list.
			unset( $super_smushed['ids'][ $k ] );

			// Reset all the indexes.
			$super_smushed['ids'] = array_values( $super_smushed['ids'] );
		}

		// Add the timestamp.
		$super_smushed['timestamp'] = time();

		update_option( $key, $super_smushed, false );

		// Update to database.
		return true;
	}

	/**
	 * Checks if the image compression is lossy, stores the image id in options table
	 *
	 * @param int    $id     Image Id.
	 * @param array  $stats  Compression Stats.
	 * @param string $key    Meta Key for storing the Super Smushed ids (Optional for Media Library).
	 *                       Need To be specified for NextGen.
	 *
	 * @return bool
	 */
	public function update_lists( $id, $stats, $key = '' ) {
		// If Stats are empty or the image id is not provided, return.
		if ( empty( $stats ) || empty( $id ) || empty( $stats['stats'] ) ) {
			return false;
		}

		// Update Super Smush count.
		if ( isset( $stats['stats']['lossy'] ) && 1 == $stats['stats']['lossy'] ) {
			if ( empty( $key ) ) {
				update_post_meta( $id, 'wp-smush-lossy', 1 );
			} else {
				$this->update_super_smush_count( $id, 'add', $key );
			}
		}

		// Check and update re-smush list for media gallery.
		if ( ! empty( $this->resmush_ids ) && in_array( $id, $this->resmush_ids ) ) {
			$this->update_resmush_list( $id );
		}
	}

	/**
	 * Remove the given attachment id from resmush list and updates it to db
	 *
	 * @param string $attachment_id  Attachment ID.
	 * @param string $mkey           Option key.
	 */
	public function update_resmush_list( $attachment_id, $mkey = 'wp-smush-resmush-list' ) {
		$resmush_list = get_option( $mkey );

		// If there are any items in the resmush list, Unset the Key.
		if ( ! empty( $resmush_list ) && count( $resmush_list ) > 0 ) {
			$key = array_search( $attachment_id, $resmush_list );
			if ( $resmush_list ) {
				unset( $resmush_list[ $key ] );
			}
			$resmush_list = array_values( $resmush_list );
		}

		// If Resmush List is empty.
		if ( empty( $resmush_list ) || 0 === count( $resmush_list ) ) {
			// Delete resmush list.
			delete_option( $mkey );
		} else {
			update_option( $mkey, $resmush_list, false );
		}
	}

	/**
	 * Remove the Update info.
	 *
	 * @param bool $remove_notice  Remove notice.
	 */
	public function dismiss_update_info( $remove_notice = false ) {
		// From URL arg.
		if ( isset( $_GET['dismiss_smush_update_info'] ) && 1 == $_GET['dismiss_smush_update_info'] ) {
			$remove_notice = true;
		}

		// From Ajax.
		if ( ! empty( $_REQUEST['action'] ) && 'dismiss_update_info' === $_REQUEST['action'] ) {
			$remove_notice = true;
		}

		// Update Db.
		if ( $remove_notice ) {
			update_site_option( 'wp-smush-hide_update_info', 1 );
		}
	}

	/**
	 * Check whether to skip a specific image size or not.
	 *
	 * @param string $size  Registered image size.
	 *
	 * @return bool Skip the image size or not.
	 */
	public function skip_image_size( $size ) {
		// No image size specified, Don't skip.
		if ( empty( $size ) ) {
			return false;
		}

		$image_sizes = $this->settings->get_setting( 'wp-smush-image_sizes' );

		// If image sizes aren't set, don't skip any of the image size.
		if ( false === $image_sizes ) {
			return false;
		}

		// Check if the size is in the smush list.
		return is_array( $image_sizes ) && ! in_array( $size, $image_sizes, true );
	}

	private function validate_file( $file_path ) {
		$errors   = new WP_Error();
		$dir_name = trailingslashit( dirname( $file_path ) );

		// Check if file exists and the directory is writable.
		if ( empty( $file_path ) ) {
			$errors->add( 'empty_path', Error_Handler::get_error_message( 'empty_path' ) );
		} elseif ( ! file_exists( $file_path ) || ! is_file( $file_path ) ) {
			// Check that the file exists.
			/* translators: %s: file path */
			$errors->add( 'file_not_found', sprintf( Error_Handler::get_error_message( 'file_not_found' ), basename( $file_path ) ) );
		} elseif ( ! is_writable( $dir_name ) ) {
			// Check that the file is writable.
			/* translators: %s: directory name */
			$errors->add( 'not_writable', sprintf( Error_Handler::get_error_message( 'not_writable' ), $dir_name ) );
		}

		$file_size = file_exists( $file_path ) ? filesize( $file_path ) : '';

		// Check if premium user.
		$max_size = WP_Smush::is_pro() ? WP_SMUSH_PREMIUM_MAX_BYTES : WP_SMUSH_MAX_BYTES;

		// Check if file exists.
		if ( 0 === (int) $file_size ) {
			$errors->add( 'file_not_found', sprintf( Error_Handler::get_error_message( 'file_not_found' ), basename( $file_path ) ) );
		} elseif ( $file_size > $max_size ) {
			// Check size limit.
			$errors->add( 'size_limit', sprintf( Error_Handler::get_error_message( 'size_limit' ), size_format( $file_size, 1 ) ), array(
				'file_name' => basename( $file_path )
			) );
		}

		return $errors;
	}

	private function smush_parallel( $file_paths, $convert_to_webp = false ) {
		$file_errors = array();
		$retry = array();
		$requests = array();
		foreach ( $file_paths as $file_key => $file_path ) {
			$error = $this->validate_file( $file_path );
			if ( $error->has_errors() ) {
				$file_errors[ $file_key ] = $error;
			} else {
				$requests[ $file_key ] = $this->get_multi_api_request_args( $convert_to_webp, $file_path );
			}
		}

		// Send off the valid paths to the API
		$responses = array();
		$this->request_multiple->do_requests( $requests, array(
			'timeout'         => WP_SMUSH_TIMEOUT,
			'connect_timeout' => 5,
			'user-agent'      => WP_SMUSH_UA,
			'complete'        => function ( $response, $response_key ) use ( &$requests, &$responses, &$retry, $file_paths, $convert_to_webp ) {
				// Free up memory
				$requests[ $response_key ] = null;

				$file_path = $file_paths[ $response_key ];
				if ( $this->should_retry_smush( $response ) ) {
					$retry[ $response_key ] = $file_path;
				} else {
					$responses[ $response_key ] = $this->handle_response(
						$response,
						$file_path,
						$convert_to_webp
					);
				}
			},
		) );

		// Retry failures with exponential backoff
		foreach ( $retry as $retry_key => $retry_file_path ) {
			$responses[ $retry_key ] = $this->do_smushit(
				$retry_file_path,
				$convert_to_webp,
				WP_SMUSH_RETRY_ATTEMPTS
			);
		}

		// Merge the responses
		return array_merge( $responses, $file_errors );
	}

	private function smush_sequential( $file_paths, $convert_to_webp = false ) {
		$responses = array();
		foreach ( $file_paths as $file_size => $file_path ) {
			$responses[ $file_size ] = $this->do_smushit( $file_path, $convert_to_webp, WP_SMUSH_RETRY_ATTEMPTS );
		}

		return $responses;
	}

	/**
	 * @param $convert_to_webp
	 * @param $file_path
	 *
	 * @return array
	 */
	private function get_multi_api_request_args( $convert_to_webp, $file_path ) {
		return array(
			'url'     => $this->get_api_url(),
			'headers' => $this->get_api_request_headers( $convert_to_webp ),
			'data'    => file_get_contents( $file_path ),
			'type'    => 'POST',
		);
	}

	/**
	 * Process an image with Smush.
	 *
	 * @since 3.8.0 Added new param $convert_to_webp.
	 *
	 * @param string $file_path        Absolute path to the image.
	 * @param bool   $convert_to_webp  Convert the image to webp.
	 * @param int    $retries  Number of times to retry the operation
	 *
	 * @return array|bool|WP_Error
	 */
	public function do_smushit( $file_path = '', $convert_to_webp = false, $retries = 0 ) {
		// TODO: (stats refactor) handle properly
		return $this->do_smushit_optimization( $file_path, $convert_to_webp, $retries );

		$errors = $this->validate_file( $file_path );
		if ( count( $errors->get_error_messages() ) ) {
			Helper::logger()->error(
				array(
					sprintf( 'Skipped file [%s] due to error:', Helper::clean_file_path( $file_path ) ),
					$errors->get_error_messages(),
				)
			);
			return $errors;
		}

		// Optimize image, and fetch the response.
		$response = $this->backoff->set_wait( WP_SMUSH_RETRY_WAIT )
		                          ->set_max_attempts( $retries )
		                          ->enable_jitter()
		                          ->set_decider( array( $this, 'should_retry_smush' ) )
		                          ->run( function () use ( $file_path, $convert_to_webp ) {
			                          return $this->_post( $file_path, $convert_to_webp );
		                          } );

		return $this->handle_response( $response, $file_path, $convert_to_webp );
	}

	public function should_retry_smush( $response ) {
		return WP_SMUSH_RETRY_ATTEMPTS > 0 && (
				is_wp_error( $response )
				|| 200 !== wp_remote_retrieve_response_code( $response )
			);
	}

	/**
	 * Takes the raw response from the API and performs all the necessary file operations etc.
	 *
	 * @param $response array|WP_Error
	 * @param $file_path string
	 * @param $convert_to_webp boolean
	 *
	 * @return array|WP_Error
	 */
	private function handle_response( $response, $file_path, $convert_to_webp ) {
		$data = $this->parse_response( $response );

		if ( is_wp_error( $data ) ) {
			if ( $data->get_error_code() === self::ERROR_SSL_CERT ) {
				// Switch to http protocol.
				$this->settings->set_setting( 'wp-smush-use_http', 1 );
			}

			$error_format = $convert_to_webp
				? 'Cannot convert to webp for image [%s].'
				: 'Cannot smush image [%s].';

			Helper::logger()->error(
				array(
					sprintf( $error_format, Helper::clean_file_path( $file_path ) ),
					$data->get_error_messages(),
				)
			);

			return $data;
		}

		$bytes_saved = empty( $data->bytes_saved ) ? 0 : $data->bytes_saved;
		if ( $bytes_saved > 0 ) {
			$this->save_smushed_image_file(
				$file_path,
				$convert_to_webp,
				$data->image
			);
		} else {
			// No savings, just add an entry to the log
			Helper::logger()->notice(
				sprintf(
					'The smushed image is larger than the original image [%s] (bytes saved %d), keep original image.',
					Helper::clean_file_path( $file_path ),
					$bytes_saved
				)
			);
		}

		// No need to pass image data any further
		$data->image = null;
		$data->image_md5 = null;

		// Check for API message and store in db.
		if ( ! empty( $data->api_message ) ) {
			$this->add_api_message( (array) $data->api_message );
		}

		// If is_premium is set in response, send it over to check for member validity.
		if ( ! empty( $data->is_premium ) ) {
			$this->api_headers['is_premium'] = $data->is_premium;
		}

		return array(
			'success' => true,
			'data'    => $data,
		);
	}

	/**
	 * Posts an image to Smush.
	 *
	 * @since 3.8.0 Added new param $convert_to_webp.
	 *
	 * @param string $file_path        Path of file to send to Smush.
	 * @param bool   $convert_to_webp  Convert the image to webp.
	 *
	 * @return bool|array array containing success status, and stats
	 */
	private function _post( $file_path, $convert_to_webp = false ) {
		// Temporary increase the limit.
		wp_raise_memory_limit( 'image' );

		return wp_remote_post(
			$this->get_api_url(),
			$this->get_api_request_args( $file_path, $convert_to_webp )
		);
	}

	/**
	 * @param $response array|WP_Error
	 *
	 * @return object|WP_Error
	 */
	private function parse_response( $response ) {
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();

			if ( strpos( $error, 'SSL CA cert' ) !== false ) {
				return new WP_Error(
					self::ERROR_SSL_CERT,
					$error
				);
			} else if ( strpos( $error, 'timed out' ) !== false ) {
				return new WP_Error(
					'time_out',
					esc_html__( "Skipped due to a timeout error. You can increase the request timeout to make sure Smush has enough time to process larger files. define('WP_SMUSH_TIMEOUT', 150);", 'wp-smushit' )
				);
			} else {
				return new WP_Error(
					'error_posting_to_api',
					/* translators: %s: Error message. */
					sprintf( __( 'Error posting to API: %s', 'wp-smushit' ), $error )
				);
			}
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$error = sprintf(
				/* translators: 1: Error code, 2: Error message */
				__( 'Error posting to API: %1$s %2$s', 'wp-smushit' ),
				wp_remote_retrieve_response_code( $response ),
				wp_remote_retrieve_response_message( $response )
			);

			return new WP_Error( 'non_200_response', $error );
		}

		$json = json_decode( wp_remote_retrieve_body( $response ) );
		if ( empty( $json->success ) ) {
			$error = ! empty( $json->data )
				? $json->data
				: __( "Image couldn't be smushed", 'wp-smushit' );

			return new WP_Error( 'unsuccessful_smush', $error );
		}

		if ( empty( $json->data ) ) {
			return new WP_Error( 'no_data', __( 'Unknown API error', 'wp-smushit' ) );
		}

		$data = $json->data;
		$bytes_saved = empty( $data->bytes_saved ) ? 0 : $data->bytes_saved;
		$image = empty( $data->image ) ? '' : $data->image;

		if (
			$bytes_saved > 0
			&& $data->image_md5 !== md5( $image )
		) {
			$error = __( 'Smush data corrupted, try again.', 'wp-smushit' );

			return new WP_Error( 'data_corrupted', $error );
		}

		if ( $bytes_saved > 0 && ! empty( $image ) ) {
			$data->image = base64_decode( $data->image );
		}

		return $data;
	}

	/**
	 * Replace the old API message with the latest one if it doesn't exists already
	 *
	 * @param array $api_message  API message.
	 */
	private function add_api_message( $api_message = array() ) {
		if ( empty( $api_message ) || ! count( $api_message ) || empty( $api_message['timestamp'] ) || empty( $api_message['message'] ) ) {
			return;
		}
		$o_api_message = get_site_option( 'wp-smush-api_message', array() );
		if ( array_key_exists( $api_message['timestamp'], $o_api_message ) ) {
			return;
		}

		$message                              = array();
		$message[ $api_message['timestamp'] ] = array(
			'message' => sanitize_text_field( $api_message['message'] ),
			'type'    => sanitize_text_field( $api_message['type'] ),
			'status'  => 'show',
		);
		update_site_option( 'wp-smush-api_message', $message );
	}

	/**
	 * Fills $placeholder array with values from $data array
	 *
	 * @param array $placeholders  Placeholders array.
	 * @param array $data          Data to fill with.
	 *
	 * @return array
	 */
	public function array_fill_placeholders( array $placeholders, array $data ) {
		$placeholders['percent']     = $data['compression'];
		$placeholders['bytes']       = $data['bytes_saved'];
		$placeholders['size_before'] = $data['before_size'];
		$placeholders['size_after']  = $data['after_size'];
		$placeholders['time']        = $data['time'];

		return $placeholders;
	}

	/**
	 * Returns signature for single size of the smush api message to be saved to db;
	 *
	 * @return array
	 */
	public function get_size_signature() {
		return array(
			'percent'     => 0,
			'bytes'       => 0,
			'size_before' => 0,
			'size_after'  => 0,
			'time'        => 0,
		);
	}

	/**
	 * Calculate saving percentage from existing and current stats
	 *
	 * @param object|string $stats           Stats object.
	 * @param object|string $existing_stats  Existing stats object.
	 *
	 * @return float
	 */
	public function calculate_percentage( $stats = '', $existing_stats = '' ) {
		if ( empty( $stats ) || empty( $existing_stats ) ) {
			return 0;
		}
		$size_before = ! empty( $stats->size_before ) ? $stats->size_before : $existing_stats->size_before;
		$size_after  = ! empty( $stats->size_after ) ? $stats->size_after : $existing_stats->size_after;
		$savings     = $size_before - $size_after;
		if ( $savings > 0 ) {
			$percentage = ( $savings / $size_before ) * 100;
			return $percentage > 0 ? round( $percentage, 2 ) : $percentage;
		}

		return 0;
	}

	public function parallel_available() {
		if ( ! WP_SMUSH_PARALLEL ) {
			return false;
		}

		return $this->curl_multi_exec_available();
	}

	public function curl_multi_exec_available() {
		if ( ! function_exists( 'curl_multi_exec' ) ) {
			return false;
		}

		$disabled_functions = explode( ',', ini_get( 'disable_functions' ) );
		if ( in_array( 'curl_multi_exec', $disabled_functions ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Optimises the image sizes
	 *
	 * Note: Function name is a bit confusing, it is for optimisation, and calls the resizing function as well
	 *
	 * Read the image paths from an attachment's metadata and process each image
	 * with wp_smushit().
	 *
	 * @param int   $attachment_id  Image ID.
	 * @param array $meta           Image metadata.
	 *
	 * @return WP_Error|array
	 */
	public function resize_from_meta_data( $attachment_id, $meta ) {
		// Check if it's real image, and is supported.
		if ( ! Helper::is_smushable( $attachment_id ) ) {
			return $meta;
		}

		// Maybe add scaled file to the meta sizes.
		$meta = apply_filters( 'wp_smush_add_scaled_images_to_meta', $meta, $attachment_id );

		// Flag to check, if uploaded size image should be smushed or not.
		$smush_uploaded = true === $this->settings->get( 'original' );

		$stats = array(
			'stats' => array_merge(
				$this->get_size_signature(),
				array(
					'api_version' => - 1,
					'lossy'       => - 1,
					'keep_exif'   => false,
				)
			),
			'sizes' => array(),
		);

		// File path and URL for original image.
		$file_path = Helper::get_attached_file( $attachment_id );// S3+.
		$file_paths = array();

		// If images has other registered size, smush them first.
		if ( ! empty( $meta['sizes'] ) && ! has_filter( 'wp_image_editors', 'photon_subsizes_override_image_editors' ) ) {
			$optimized_thumbs = array();
			foreach ( $meta['sizes'] as $size_key => $size_data ) {
				// Check if registered size is supposed to be Smushed or not.
				if ( 'full' !== $size_key ) {
					if ( $this->skip_image_size( $size_key ) || isset( $optimized_thumbs[ $size_data['file'] ] ) ) {
						// If a thumbnail file is optimized we don't need to optimize it again.
						continue;
					}
					/**
					 * Save optimized thumbnail file.
					 * We save all cases included failure case which user can re-check images later.
					 */
					$optimized_thumbs[ $size_data['file'] ] = $size_key;
				}

				// We take the original image. The 'sizes' will all match the same URL and
				// path. So just get the dirname and replace the filename.
				$file_path_size = path_join( dirname( $file_path ), $size_data['file'] );

				$ext = Helper::get_mime_type( $file_path_size );
				if ( $ext && ! in_array( $ext, Core::$mime_types, true ) ) {
					continue;
				}

				/**
				 * Allows to skip an image from optimization.
				 *
				 * @param bool   $compress       Optimize image or not.
				 * @param string $size_key       Size of image being smushed.
				 * @param string $file_path_size Full thumbnail path of current size.
				 * @param int    $attachment_id  Attachment ID.
				 *
				 * @since 3.9.6 Add two parameters for the filter.
				 */
				if ( ! apply_filters( 'wp_smush_media_image', true, $size_key, $file_path_size, $attachment_id ) ) {
					continue;
				}

				/**
				 * Check if the file exists on the server,
				 * if not, might try to download it from the cloud (s3).
				 *
				 * @since 3.9.6
				 */
				if ( ! Helper::exists_or_downloaded( $file_path_size, $attachment_id ) ) {
					continue;
				}

				$file_paths[ $size_key ] = $file_path_size;
			}
		} elseif ( ! has_filter( 'wp_image_editors', 'photon_subsizes_override_image_editors' ) ) {
			$smush_uploaded = true;
		}

		/**
		 * Allows to skip an image from optimization.
		 *
		 * @param bool   $compress  Optimize image or not.
		 * @param string $size_key  Size of image being smushed.
		 */
		$smush_full_image = apply_filters( 'wp_smush_media_image', true, 'full', $file_path, $attachment_id );

		// If original size is supposed to be smushed.
		if ( $smush_uploaded && $smush_full_image ) {
			$file_paths['full'] = $file_path;
		}

		if ( $this->parallel_available() ) {
			$responses = $this->smush_parallel( $file_paths );
		} else {
			$responses = $this->smush_sequential( $file_paths );
		}
		foreach ( $responses as $size_key => $response ) {

			if ( is_wp_error( $response ) ) {
				// Logged the error inside do_smushit.
				return $response;
			}

			// If there are no stats or resulting image is larger than original.
			if ( empty( $response['data'] ) || $response['data']->after_size > $response['data']->before_size ) {
				continue;
			}

			// All clear, store the stat.
			$stats['sizes'][ $size_key ] = (object) $this->array_fill_placeholders( $this->get_size_signature(), (array) $response['data'] );
		}

		// Make sure we have the correct API details.
		if ( isset( $response ) && isset( $response['data'] ) && ( empty( $stats['stats']['api_version'] ) || - 1 === $stats['stats']['api_version'] ) ) {
			$stats['stats']['api_version'] = $response['data']->api_version;
			$stats['stats']['lossy']       = $response['data']->lossy;
			$stats['stats']['keep_exif']   = ! empty( $response['data']->keep_exif ) ? $response['data']->keep_exif : 0;
		}

		// Set smush status for all the images, store it in wp-smpro-smush-data.
		$existing_stats = get_post_meta( $attachment_id, self::$smushed_meta_key, true );

		if ( ! empty( $existing_stats ) ) {
			// Update stats for each size.
			if ( isset( $existing_stats['sizes'] ) && ! empty( $stats['sizes'] ) ) {
				foreach ( $existing_stats['sizes'] as $size_name => $size_stats ) {
					// If stats for a particular size doesn't exists.
					if ( empty( $stats['sizes'][ $size_name ] ) ) {
						$stats['sizes'][ $size_name ] = $existing_stats['sizes'][ $size_name ];
					} else {
						$existing_stats_size = (object) $existing_stats['sizes'][ $size_name ];

						// Store the original image size.
						$stats['sizes'][ $size_name ]->size_before = ( ! empty( $existing_stats_size->size_before ) && $existing_stats_size->size_before > $stats['sizes'][ $size_name ]->size_before ) ? $existing_stats_size->size_before : $stats['sizes'][ $size_name ]->size_before;

						// Update compression percent and bytes saved for each size.
						$stats['sizes'][ $size_name ]->bytes   = $stats['sizes'][ $size_name ]->bytes + $existing_stats_size->bytes;
						$stats['sizes'][ $size_name ]->percent = $this->calculate_percentage( $stats['sizes'][ $size_name ], $existing_stats_size );
					}
				}
			}

			// Keep WebP flag.
			if ( isset( $existing_stats['webp_flag'] ) ) {
				$stats['webp_flag'] = $existing_stats['webp_flag'];
			}
		}

		// Sum Up all the stats.
		$stats = WP_Smush::get_instance()->core()->total_compression( $stats );

		// If there was any compression and there was no error during optimization.
		if ( isset( $stats['stats']['bytes'] ) && $stats['stats']['bytes'] >= 0 ) {
			/**
			 * Runs if the image optimization was successful.
			 *
			 * @param int   $attachment_id  Image ID.
			 * @param array $stats          Smush stats for the image.
			 * @param array $meta           Attachment meta.
			 */
			do_action( 'wp_smush_image_optimised', $attachment_id, $stats, $meta );
$stats['stats']['lossy'] = 1;
		}

		update_post_meta( $attachment_id, self::$smushed_meta_key, $stats );

		return $meta;
	}

	/**
	 * Fix SSL CA Certificate issue.
	 *
	 * @since 3.9.6
	 *
	 * Check for use of http url (Hostgator mostly) - got it from smush_image.
	 */
	public function fix_ssl_ca_certificate_error() {
		// Return if the member defined it.
		if ( defined( 'WP_SMUSH_API_HTTP' ) ) {
			return;
		}
		static $use_http;
		/**
		 * Fix for Hostgator.
		 * Check for use of http url (Hostgator mostly).
		 */
		if ( is_null( $use_http ) ) {
			$use_http = $this->settings->get_setting( 'wp-smush-use_http' );
		}

		if ( $use_http ) {
			define( 'WP_SMUSH_API_HTTP', 'http://smushpro.wpmudev.com/1.0/' );
		}
	}

	/**
	 * Add action when we don't smush an image,
	 * or get any errors while smushing.
	 *
	 * @param  int      $attachment_id Attachment ID.
	 * @param  WP_Error $errors an instance of WP_Error.
	 * @param  mixed    $result The data to return.
	 * @return mixed
	 */
	private function no_smushit( $attachment_id, $errors, $result = null ) {
		do_action( 'wp_smush_no_smushit', $attachment_id, $errors );
		return isset( $result ) ? $result : false;
	}

	/**
	 * Smush image
	 *
	 * We need to detect the response status by using $ref_errors->has_errors().
	 *
	 * @since 3.9.6
	 *
	 * @param int      $attachment_id  Attachment ID.
	 * @param array    $ref_meta Original metadata (passed by reference).
	 * @param WP_Error $ref_errors WP_Error (passed by reference).
	 *
	 * @return mixed Returns response data, TRUE if smushed the file, or FALSE with error(s).
	 */
	public function smushit( $attachment_id, &$ref_meta, &$ref_errors ) {
		// TODO: (stats refactor) handle properly
		$meta = $this->run_optimizer( $attachment_id );
		if ( is_wp_error( $meta ) ) {
			$ref_errors = $meta;

			return false;
		} else {
			$ref_meta = $meta;

			return true;
		}

		/**
		 * Prevent infinite loop when someone calls `wp_generate_attachment_metadata` inside smushit.
		 *
		 * By default, we already avoid it via set in-progress,
		 * but it's better to prevent it from another attachment file from third party and issue from object cache too.
		 */
		if ( $this->prevent_infinite_loop ) {
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Handle errors.
		if ( ! $ref_errors || ! is_wp_error( $ref_errors ) ) {
			$ref_errors = new WP_Error();
		}

		$attachment_id = (int) $attachment_id;
		if ( $attachment_id < 1 ) {
			$ref_errors->add( 'missing_id', Error_Handler::get_error_message( 'missing_id' ), array( 'file_name' => 'undefined' ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		$file_name = sprintf( /* translators: %d - attachment ID */
			esc_html__( 'attachment ID: %d', 'wp-smushit' ),
			$attachment_id
		);

		// Check if the file is ignored or animated.
		$is_ignored = (int) get_post_meta( $attachment_id, 'wp-smush-ignore-bulk', true );
		if ( $is_ignored > 0 ) {
			$type = Core::STATUS_ANIMATED === $is_ignored ? 'animated' : 'ignored';
			$ref_errors->add( $type, Error_Handler::get_error_message( $type ), array( 'file_name' => $file_name ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Return the status if the file is in progress.
		if ( get_transient( 'wp-smush-restore-' . $attachment_id ) || get_transient( 'smush-in-progress-' . $attachment_id ) ) {
			$ref_errors->add( 'in_progress', Error_Handler::get_error_message( 'in_progress' ), array( 'file_name' => $file_name ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Image metadata.
		if ( empty( $ref_meta ) ) {
			// We use unfiltered metadata.
			$ref_meta = wp_get_attachment_metadata( $attachment_id, true );
		}
		/**
		 * This is often not set when images are imported to the database, without properly adding the meta values.
		 * Causes PHP Warning: Illegal string offset 'file' message.
		 */
		if ( ! is_array( $ref_meta ) || ! isset( $ref_meta['file'] ) ) {
			$ref_errors->add( 'no_file_meta', Error_Handler::get_error_message( 'no_file_meta' ), array( 'file_name' => $file_name ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Try to get the file name from path.
		$file_name = explode( '/', $ref_meta['file'] );
		$file_name = is_array( $file_name ) ? array_pop( $file_name ) : $ref_meta['file'];
		$file_name = Helper::get_image_media_link( $attachment_id, $file_name );

		/**
		 * Filter: wp_smush_image
		 *
		 * Whether to smush the given attachment id or not
		 *
		 * @param bool $skip  Bool, whether to Smush image or not.
		 * @param int  $ID    Attachment Id, Attachment id of the image being processed.
		 */
		if ( ! apply_filters( 'wp_smush_image', true, $attachment_id ) ) {
			$ref_errors->add( 'skipped_filter', Error_Handler::get_error_message( 'skipped_filter' ), array( 'file_name' => $file_name ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Get the file path for backup.
		$file_path = Helper::get_attached_file( $attachment_id ); // S3+.

		// If the file doesn't exist, return.
		if ( ! file_exists( $file_path ) ) {
			$ref_errors->add( 'file_not_found', sprintf( Error_Handler::get_error_message( 'file_not_found' ), basename( $file_path ) ), array( 'file_name' => $file_name ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Check if file is animated, return.
		if ( Helper::check_animated_status( $file_path, $attachment_id ) ) {
			$ref_errors->add( 'animated', Error_Handler::get_error_message( 'animated' ), array( 'file_name' => $file_name ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Check file size limit.
		$size_exceeded = Helper::size_limit_exceeded( $attachment_id );
		if ( $size_exceeded ) {
			$error_code = WP_Smush::is_pro() ? 'size_pro_limit' : 'size_limit';
			$ref_errors->add( $error_code, sprintf( Error_Handler::get_error_message( $error_code ), size_format( $size_exceeded ) ), array( 'file_name' => $file_name ) );
			return $this->no_smushit( $attachment_id, $ref_errors );
		}

		// Set prevent_infinite_loop before adding some actions, only return after resetting this value.
		$this->prevent_infinite_loop = true;

		/**
		 * Fires before Smushing a file.
		 *
		 * @param int $attachment_id Attachment ID.
		 * @param array $ref_meta Metadata.
		 * @param WP_Error The WP_Error object (passed by reference).
		 *
		 * @hooked self::fix_ssl_ca_certificate_error()
		 * @hooked Smush\Core\Integrations\S3::activate_smush_mode()
		 */
		do_action_ref_array( 'wp_smush_before_smush_file', array( $attachment_id, $ref_meta, &$ref_errors ) );

		// Only Smush if there is no error from third party.
		$has_error = $ref_errors->has_errors();
		if ( ! $has_error ) {
			// Set a transient to avoid multiple request.
			set_transient( 'smush-in-progress-' . $attachment_id, 1, HOUR_IN_SECONDS );

			// Is doing wp_generate_attachment_metadata.
			$generating_metadata = doing_filter( 'wp_generate_attachment_metadata' );

			// Nothing to smush if that is generating metadata while disabling auto-smush.
			if ( $generating_metadata && ! $this->is_auto_smush_enabled() ) {
				// Remove stats and update cache.
				WP_Smush::get_instance()->core()->remove_stats( $attachment_id );
			} else {
				// We only take backup before smushing image.
				WP_Smush::get_instance()->core()->mod->backup->create_backup( $file_path, $attachment_id );
				// While uploading from Mobile App or other sources, admin_init action may not fire.
				// So we need to manually initialize those.
				if ( $generating_metadata ) {
					WP_Smush::get_instance()->core()->mod->resize->initialize( true );
				}
				// Send image for resizing, if enabled resize first before any other operation.
				$updated_meta = $this->resize_image( $attachment_id, $ref_meta );

				/**
				 * Convert PNGs to JPG, it should be run with resize_image in order to retrieve the transparent status from the cache.
				 *
				 * @see SMUSH-1027
				 */
				$updated_meta = WP_Smush::get_instance()->core()->mod->png2jpg->png_to_jpg( $attachment_id, $updated_meta );

				$ref_meta = ! empty( $updated_meta ) ? $updated_meta : $ref_meta;

				// Convert to webp.
				$webp_files = WP_Smush::get_instance()->core()->mod->webp->convert_to_webp( $attachment_id, $ref_meta );
				// Handle webp errors.
				if ( is_wp_error( $webp_files ) ) {
					$ref_errors = $webp_files;
				}

				// Smush the image.
				$smush = $this->resize_from_meta_data( $attachment_id, $ref_meta );
				// Handle compress errors.
				if ( is_wp_error( $smush ) ) {
					// Handle WP_Error.
					$ref_errors->merge_from( $smush );
				}
			}
		}

		/**
		 * Fires after optimizing a file.
		 *
		 * @param int $attachment_id Attachment ID.
		 * @param array $ref_meta Metadata.
		 * @param WP_Error The WP_Error object (passed by reference).
		 *
		 * @hooked Smush\Core\Integrations\S3::release_smush_mode()
		 * @hooked Smush\Core\Integrations\S3::maybe_remove_sizes_from_s3_upload()
		 */
		do_action_ref_array( 'wp_smush_after_smush_file', array( $attachment_id, $ref_meta, &$ref_errors ) );

		// Maybe update metadata after smushing image.
		$has_error = $has_error || $ref_errors && is_wp_error( $ref_errors ) && $ref_errors->has_errors();
		// Update the metadata if there are no errors or converted PNG2JPG or resized image.
		if ( isset( $generating_metadata ) && ! $generating_metadata && ( ! $has_error || did_action( 'wp_smush_png_jpg_converted' ) || did_action( 'wp_smush_image_resized' ) ) ) {
			Helper::wp_update_attachment_metadata( $attachment_id, $ref_meta );
		}

		// Log all errors.
		if ( $has_error ) {
			Helper::logger()->error( $ref_errors->errors );
		}

		// Delete the transient after attachment meta is updated.
		delete_transient( 'smush-in-progress-' . $attachment_id );

		// Reset prevent infinite loop.
		$this->prevent_infinite_loop = null;

		return $has_error ? $this->no_smushit( $attachment_id, $ref_errors ) : true;
	}

	/**
	 * Read the image paths from an attachment's metadata and process each image with wp_smushit().
	 *
	 * @param array $meta  Attachment metadata.
	 * @param int   $id    Attachment ID.
	 *
	 * @return mixed
	 */
	public function smush_image( $meta, $id ) {
		// We need to check if this call originated from Gutenberg and allow only media.
		if ( Helper::is_non_rest_media() ) {
			// If not - return image metadata.
			return $this->no_smushit( $id, null, $meta );
		}

		$upload_attachment    = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
		$is_upload_attachment = 'upload-attachment' === $upload_attachment || isset( $_POST['post_id'] );

		// Our async task runs when action is upload-attachment and post_id found. So do not run on these conditions.
		if ( $is_upload_attachment && defined( 'WP_SMUSH_ASYNC' ) && WP_SMUSH_ASYNC ) {
			return $meta;
		}

		// Is doing wp_generate_attachment_metadata.
		$generating_metadata = doing_filter( 'wp_generate_attachment_metadata' );
		if ( $generating_metadata && ! $this->is_auto_smush_enabled() ) {
			// TODO: the following commented out line is here for historic reference only in case we need it again but hopefully we won't. Remove it once the version 3.13 has been out for a while. The reason why we are removing it is that it causes the test test__global_stats_updated_on_restore to fail.
			// Remove stats and update cache.
			//WP_Smush::get_instance()->core()->remove_stats( $id );

			return $meta;
		}

		/**
		 * Smush image.
		 *
		 * @since 3.9.6
		 *
		 * @param int      $id  Attachment ID.
		 * @param array    $meta Image metadata (passed by reference).
		 * @param WP_Error $errors WP_Error (passed by reference).
		 */
		$this->smushit( $id, $meta, $errors );

		return $meta;
	}

	/**
	 * Smush single images
	 *
	 * @param int  $attachment_id  Attachment ID.
	 * @param bool $return         Return/echo the stats.
	 *
	 * @return array|string|void
	 */
	public function smush_single( $attachment_id, $return = false ) {
		/**
		 * If the smushing option is already set, return the status.
		 *
		 * @since 3.9.6
		 * If it's not in ajax we are already handled it inside self::smushit().
		 */
		if ( ! $return && $attachment_id > 0 && ( get_transient( 'smush-in-progress-' . $attachment_id ) || get_transient( 'wp-smush-restore-' . $attachment_id ) ) ) {
			// Get the button status.
			$status = WP_Smush::get_instance()->library()->generate_markup( $attachment_id );
			wp_send_json_success( $status );
		}

		// Get the image metadata from $_POST.
		$original_meta = ! empty( $_POST['metadata'] ) ? Helper::format_meta_from_post( $_POST['metadata'] ) : '';

		/**
		 * Smush image.
		 *
		 * @since 3.9.6
		 *
		 * @param int      $attachment_id  Attachment ID.
		 * @param array    $original_meta Image metadata (passed by reference).
		 * @param WP_Error $errors WP_Error (passed by reference).
		 */
		$this->smushit( $attachment_id, $original_meta, $errors );

		// Send JSON response if we are not supposed to return the results.
		if ( $errors && is_wp_error( $errors ) && $errors->has_errors() ) {
			if ( $return ) {
				return array( 'error' => $errors->get_error_message() );
			}

			// Prepare data for ajax.
			$error_code = $errors->get_error_code();
			$error_data = $errors->get_error_data();

			$status = array(
				'error'        => $error_code,
				'error_msg'    => Helper::filter_error( $errors->get_error_message( $error_code ), $attachment_id ),
				'html_stats'   => WP_Smush::get_instance()->library()->generate_markup( $attachment_id ),
				'show_warning' => (int) $this->show_warning(),
			);

			// Add error data (file_name) to status.
			if ( $error_data && is_array( $error_data ) ) {
				$status = array_merge( $error_data, $status );
			}

			// Send data.
			wp_send_json_error( $status );
		}

		$this->update_resmush_list( $attachment_id );
		Core::add_to_smushed_list( $attachment_id );

		// Get the button status later after update resmushed list.
		$status = WP_Smush::get_instance()->library()->generate_markup( $attachment_id );

		if ( $return ) {
			return $status;
		}

		wp_send_json_success( $status );
	}

	/**
	 * If auto smush is set to true or not, default is true
	 *
	 * @return int|bool
	 */
	public function is_auto_smush_enabled() {
		$auto_smush = $this->settings->get( 'auto' );

		// Keep the auto smush on by default.
		if ( ! isset( $auto_smush ) ) {
			$auto_smush = 1;
		}

		return $auto_smush;
	}

	/**
	 * Deletes all the backup files when an attachment is deleted
	 * Update resmush List
	 * Update Super Smush image count
	 *
	 * @param int $image_id  Attachment ID.
	 *
	 * @return bool|void
	 */
	public function delete_images( $image_id ) {
		// Update the savings cache.
		WP_Smush::get_instance()->core()->get_savings( 'resize' );

		// Update the savings cache.
		WP_Smush::get_instance()->core()->get_savings( 'pngjpg' );

		// If no image id provided.
		if ( empty( $image_id ) ) {
			return false;
		}

		// Check and Update resmush list.
		$resmush_list = get_option( 'wp-smush-resmush-list' );
		if ( $resmush_list ) {
			$this->update_resmush_list( $image_id );
		}

		/** Delete Backups  */
		// Check if we have any smush data for image.
		WP_Smush::get_instance()->core()->mod->backup->delete_backup_files( $image_id );

		/**
		 * Delete webp.
		 *
		 * Run WebP::delete_images always even when the module is deactivated.
		 *
		 * @since 3.8.0
		 */
		//WP_Smush::get_instance()->core()->mod->webp->delete_images( $image_id, false );
	}

	/**
	 * Calculate saving percentage for a given size stats
	 *
	 * @param object $stats  Stats object.
	 *
	 * @return float|int
	 */
	private function calculate_percentage_from_stats( $stats ) {
		if ( empty( $stats ) || ! isset( $stats->size_before, $stats->size_after ) ) {
			return 0;
		}

		$savings = $stats->size_before - $stats->size_after;
		if ( $savings > 0 ) {
			$percentage = ( $savings / $stats->size_before ) * 100;
			return $percentage > 0 ? round( $percentage, 2 ) : $percentage;
		}

		return 0;
	}

	/**
	 * Perform the resize operation for the image
	 *
	 * @param int   $attachment_id  Attachment ID.
	 * @param array $meta           Attachment meta.
	 *
	 * @return mixed
	 */
	public function resize_image( $attachment_id, $meta ) {
		if ( empty( $attachment_id ) || empty( $meta ) ) {
			return $meta;
		}

		return WP_Smush::get_instance()->core()->mod->resize->auto_resize( $attachment_id, $meta );
	}

	/**
	 * Send a smush request for the attachment
	 *
	 * @param int $id  Attachment ID.
	 */
	public function wp_smush_handle_async( $id ) {
		// If we don't have image id or auto Smush is disabled, return.
		if ( empty( $id ) || ! $this->is_auto_smush_enabled() ) {
			return;
		}

		// Try to use smushit.
		$this->smush_single( $id, true );
	}

	/**
	 * Send a smush request for the attachment
	 *
	 * @param int   $id         Attachment ID.
	 * @param array $post_data  Post data.
	 */
	public function wp_smush_handle_editor_async( $id, $post_data ) {
		// If we don't have image id, or the smush is already in progress for the image, return.
		if ( empty( $id ) || get_transient( 'smush-in-progress-' . $id ) || get_transient( 'wp-smush-restore-' . $id ) ) {
			return;
		}

		// If auto Smush is disabled.
		if ( ! $this->is_auto_smush_enabled() ) {
			return;
		}

		/**
		 * Filter: wp_smush_image
		 *
		 * Whether to smush the given attachment id or not
		 *
		 * @param bool $skip  Whether to Smush image or not.
		 * @param int  $id    Attachment ID of the image being processed.
		 */
		if ( ! apply_filters( 'wp_smush_image', true, $id ) ) {
			return;
		}

		// If filepath is not set or file doesn't exist.
		if ( ! isset( $post_data['filepath'] ) || ! file_exists( $post_data['filepath'] ) ) {
			return;
		}

		$res = $this->do_smushit( $post_data['filepath'] );

		if ( is_wp_error( $res ) || empty( $res['success'] ) || ! $res['success'] ) {
			// Logged the error inside do_smushit.
			return;
		}

		// Update stats if it's the full size image. Return if it's not the full image size.
		if ( get_attached_file( $post_data['postid'] ) !== $post_data['filepath'] ) {
			return;
		}

		// Get the existing Stats.
		$smush_stats = get_post_meta( $post_data['postid'], self::$smushed_meta_key, true );
		$stats_full  = ! empty( $smush_stats['sizes'] ) && ! empty( $smush_stats['sizes']['full'] ) ? $smush_stats['sizes']['full'] : '';

		if ( empty( $stats_full ) ) {
			return;
		}

		// store the original image size.
		$stats_full->size_before = ( ! empty( $stats_full->size_before ) && $stats_full->size_before > $res['data']->before_size ) ? $stats_full->size_before : $res['data']->before_size;
		$stats_full->size_after  = $res['data']->after_size;

		// Update compression percent and bytes saved for each size.
		$stats_full->bytes = $stats_full->size_before - $stats_full->size_after;

		$stats_full->percent          = $this->calculate_percentage_from_stats( $stats_full );
		$smush_stats['sizes']['full'] = $stats_full;

		// Update stats.
		update_post_meta( $post_data['postid'], self::$smushed_meta_key, $smush_stats );
	}

	/**
	 * Make sure we treat the scaled image as an attachment size, rather than the original uploaded image.
	 *
	 * @since 3.9.1
	 *
	 * @param array $meta           Attachment meta data.
	 * @param int   $attachment_id  Attachment ID.
	 *
	 * @return array
	 */
	public function add_scaled_to_meta( $meta, $attachment_id ) {
		// If the image is not a scaled version - do nothing.
		if ( false === strpos( $meta['file'], '-scaled.' ) || ! isset( $meta['original_image'] ) || isset( $meta['sizes']['wp_scaled'] ) ) {
			return $meta;
		}

		$meta['sizes']['wp_scaled'] = array(
			'file'      => basename( $meta['file'] ),
			'width'     => $meta['width'],
			'height'    => $meta['height'],
			'mime-type' => get_post_mime_type( $attachment_id ),
		);

		return $meta;
	}

	/**
	 * @param $file_path
	 * @param $image
	 *
	 * @return string
	 */
	public function put_webp_image_file( $file_path, $image ) {
		$file_path = WP_Smush::get_instance()->core()->mod->webp->get_webp_file_path( $file_path, true );
		file_put_contents( $file_path, $image );

		return $file_path;
	}

	/**
	 * @param $file_path
	 * @param $image
	 *
	 * @return void
	 */
	public function put_smushed_image_file( $file_path, $image ) {
		$temp_file = $file_path . '.tmp';

		// Add the file as tmp.
		file_put_contents( $temp_file, $image );

		// Replace the file.
		$success = rename( $temp_file, $file_path );

		// If temp file still exists, unlink it.
		if ( file_exists( $temp_file ) ) {
			unlink( $temp_file );
		}

		// If file renaming failed.
		if ( ! $success ) {
			copy( $temp_file, $file_path );
			unlink( $temp_file );
		}
	}

	/**
	 * @param $file_path
	 *
	 * @return int
	 */
	public function get_file_permissions( $file_path ) {
		clearstatcache();
		$perms = fileperms( $file_path ) & 0777;
		// Some servers are having issue with file permission, this should fix it.
		if ( empty( $perms ) ) {
			// Source: WordPress Core.
			$stat = stat( dirname( $file_path ) );
			$perms = $stat['mode'] & 0000666; // Same permissions as parent folder, strip off the executable bits.
		}

		return $perms;
	}

	private function save_smushed_image_file( $file_path, $convert_to_webp, $image ) {
		$pre = apply_filters( 'wp_smush_pre_image_write', false, $file_path, $convert_to_webp, $image );
		if ( $pre !== false ) {
			Helper::logger()->notice( 'Another plugin/theme short circuited the image write operation using the wp_smush_pre_image_write filter.' );

			return;
		}

		// Backup the old permissions
		$permissions = $this->get_file_permissions( $file_path );

		// Save the new file
		if ( $convert_to_webp ) {
			$file_path = $this->put_webp_image_file( $file_path, $image );
		} else {
			$this->put_smushed_image_file( $file_path, $image );
		}

		// Restore the old permissions
		chmod( $file_path, $permissions );
	}

	/**
	 * @param $convert_to_webp
	 *
	 * @return string[]
	 */
	private function get_api_request_headers( $convert_to_webp ) {
		$headers = array(
			'accept'       => 'application/json',   // The API returns JSON.
			'content-type' => 'application/binary', // Set content type to binary.
			'lossy'        => $this->settings->get( 'lossy' ) ? 'true' : 'false',
			'exif'         => $this->settings->get( 'strip_exif' ) ? 'false' : 'true',
		);

		if ( $convert_to_webp ) {
			$headers['webp'] = 'true';
		}

		// Check if premium member, add API key.
		$api_key = Helper::get_wpmudev_apikey();
		if ( ! empty( $api_key ) && WP_Smush::is_pro() ) {
			$headers['apikey'] = $api_key;
		}

		return $headers;
	}

	/**
	 * @return string
	 */
	private function get_api_url() {
		return defined( 'WP_SMUSH_API_HTTP' ) ? WP_SMUSH_API_HTTP : WP_SMUSH_API;
	}

	/**
	 * @param $file_path
	 * @param $convert_to_webp
	 *
	 * @return array
	 */
	private function get_api_request_args( $file_path, $convert_to_webp ) {
		return array(
			'headers'    => $this->get_api_request_headers( $convert_to_webp ),
			'body'       => file_get_contents( $file_path ),
			'timeout'    => WP_SMUSH_TIMEOUT,
			'user-agent' => WP_SMUSH_UA,
		);
	}

	/**
	 * @return Request_Multiple
	 */
	public function get_request_multiple() {
		return $this->request_multiple;
	}

	/**
	 * @param Request_Multiple $request_multiple
	 */
	public function set_request_multiple( $request_multiple ) {
		$this->request_multiple = $request_multiple;
	}

	private function do_smushit_optimization( $file_path, $convert_to_webp, $retries ) {
		$errors = $this->validate_file( $file_path );
		if ( count( $errors->get_error_messages() ) ) {
			return $errors;
		}

		$smusher = $convert_to_webp
			? new Webp_Converter()
			: new Smusher();
		$smusher->set_retry_attempts( $retries );
		$data = $smusher->smush_file( $file_path );
		if ( $data ) {
			return array(
				'success' => true,
				'data'    => $data,
			);
		} else {
			return $smusher->get_errors();
		}
	}

	private function run_optimizer( $attachment_id ) {
		$in_progress_error = new WP_Error( 'in_progress', 'Smush already in progress' );
		if ( $this->prevent_infinite_loop ) {
			return $this->no_smushit( $attachment_id, $in_progress_error, $in_progress_error );
		}
		$this->prevent_infinite_loop = true;

		$media_item = Media_Item_Cache::get_instance()->get( $attachment_id );
		$optimizer  = new Media_Item_Optimizer( $media_item );
		$optimized  = $optimizer->optimize();

		$this->prevent_infinite_loop = false;

		if ( $optimized ) {
			return $media_item->get_wp_metadata();
		} else {
			if ( $media_item->has_errors() ) {
				return $media_item->get_errors();
			}
			return $optimizer->get_errors();
		}
	}
}