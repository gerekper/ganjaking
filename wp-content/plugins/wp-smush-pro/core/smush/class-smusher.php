<?php

namespace Smush\Core\Smush;

use Smush\Core\Api\Backoff;
use Smush\Core\Api\Request_Multiple;
use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Settings;
use Smush\Core\Upload_Dir;
use WP_Error;
use WP_Smush;

/**
 * Takes raw image file paths and processes them through the Smush API.
 */
class Smusher {
	const ERROR_SSL_CERT = 'ssl_cert_error';
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var Request_Multiple
	 */
	private $request_multiple;
	/**
	 * @var Backoff
	 */
	private $backoff;
	/**
	 * @var \WDEV_Logger|null
	 */
	private $logger;
	/**
	 * @var int
	 */
	private $retry_attempts;
	/**
	 * @var int
	 */
	private $retry_wait;
	/**
	 * @var int
	 */
	private $timeout;
	/**
	 * @var string
	 */
	private $user_agent;
	/**
	 * @var int
	 */
	private $connect_timeout;
	/**
	 * @var boolean
	 */
	private $smush_parallel;
	/**
	 * @var WP_Error
	 */
	private $errors;
	/**
	 * @var File_System
	 */
	private $fs;
	/**
	 * @var Upload_Dir
	 */
	private $upload_dir;

	public function __construct() {
		$this->retry_attempts  = WP_SMUSH_RETRY_ATTEMPTS;
		$this->retry_wait      = WP_SMUSH_RETRY_WAIT;
		$this->user_agent      = WP_SMUSH_UA;
		$this->smush_parallel  = WP_SMUSH_PARALLEL;
		$this->timeout         = WP_SMUSH_TIMEOUT;
		$this->connect_timeout = 5;

		$this->settings         = Settings::get_instance();
		$this->logger           = Helper::logger();
		$this->request_multiple = new Request_Multiple();
		$this->backoff          = new Backoff();
		$this->errors           = new WP_Error();
		$this->fs               = new File_System();
		$this->upload_dir       = new Upload_Dir();
	}

	/**
	 * @param $file_paths string[]
	 *
	 * @return boolean[]|object[]
	 */
	public function smush( $file_paths ) {
		$this->set_errors( new WP_Error() );

		if ( $this->parallel_available() ) {
			return $this->smush_parallel( $file_paths );
		} else {
			return $this->smush_sequential( $file_paths );
		}
	}

	/**
	 * @param $file_paths string[]
	 *
	 * @return boolean[]|object[]
	 */
	private function smush_parallel( $file_paths ) {
		$retry    = array();
		$requests = array();
		foreach ( $file_paths as $size_key => $size_file_path ) {
			$requests[ $size_key ] = $this->get_parallel_request_args( $size_file_path );
		}

		// Send off the valid paths to the API
		$responses = array();
		$this->request_multiple->do_requests( $requests, array(
			'timeout'         => $this->timeout,
			'connect_timeout' => $this->connect_timeout,
			'user-agent'      => $this->user_agent,
			'complete'        => function ( $response, $response_size_key ) use ( &$requests, &$responses, &$retry, $file_paths ) {
				// Free up memory
				$requests[ $response_size_key ] = null;
				$size_file_path                 = $file_paths[ $response_size_key ];

				if ( $this->should_retry_smush( $response ) ) {
					$retry[ $response_size_key ] = $size_file_path;
				} else {
					$responses[ $response_size_key ] = $this->handle_response( $response, $response_size_key, $size_file_path );
				}
			},
		) );

		// Retry failures with exponential backoff
		foreach ( $retry as $retry_size_key => $retry_size_file ) {
			$responses[ $retry_size_key ] = $this->smush_file( $retry_size_file, $retry_size_key );
		}

		return $responses;
	}

	/**
	 * @param $file_paths string[]
	 *
	 * @return boolean[]|object[]
	 */
	private function smush_sequential( $file_paths ) {
		$responses = array();
		foreach ( $file_paths as $size_key => $size_file_path ) {
			$responses[ $size_key ] = $this->smush_file( $size_file_path, $size_key );
		}

		return $responses;
	}

	/**
	 * @param $file_path string
	 * @param $size_key string
	 *
	 * @return bool|object
	 */
	public function smush_file( $file_path, $size_key = '' ) {
		$response = $this->backoff->set_wait( $this->retry_wait )
		                          ->set_max_attempts( $this->retry_attempts )
		                          ->enable_jitter()
		                          ->set_decider( array( $this, 'should_retry_smush' ) )
		                          ->run( function () use ( $file_path ) {
			                          return $this->make_post_request( $file_path );
		                          } );

		return $this->handle_response( $response, $size_key, $file_path );
	}

	private function make_post_request( $file_path ) {
		// Temporary increase the limit.
		wp_raise_memory_limit( 'image' );

		return wp_remote_post(
			$this->get_api_url(),
			$this->get_api_request_args( $file_path )
		);
	}

	private function get_api_request_args( $file_path ) {
		return array(
			'headers'    => $this->get_api_request_headers( $file_path ),
			'body'       => $this->fs->file_get_contents( $file_path ),
			'timeout'    => $this->timeout,
			'user-agent' => $this->user_agent,
		);
	}

	/**
	 * @param $response
	 * @param $size_key string
	 * @param $file_path string
	 *
	 * @return bool|object
	 */
	private function handle_response( $response, $size_key, $file_path ) {
		$data = $this->parse_response( $response, $size_key, $file_path );

		if ( ! $data ) {
			if ( $this->has_error( self::ERROR_SSL_CERT ) ) {
				// Switch to http protocol.
				$this->settings->set_setting( 'wp-smush-use_http', 1 );
			}

			return false;
		}

		if ( $data->bytes_saved > 0 ) {
			$optimized_image_saved = $this->save_smushed_image_file( $file_path, $data->image );
			if ( ! $optimized_image_saved ) {
				$this->add_error(
					$size_key,
					'image_not_saved',
					/* translators: %s: File path. */
					sprintf( __( 'Smush was successful but we were unable to save the file due to a file system error: [%s].', 'wp-smushit' ), $this->upload_dir->get_human_readable_path( $file_path ) )
				);

				return false;
			}
		}

		// No need to pass image data any further
		$data->image     = null;
		$data->image_md5 = null;

		// Check for API message and store in db.
		if ( ! empty( $data->api_message ) ) {
			$this->add_api_message( (array) $data->api_message );
		}

		return $data;
	}

	protected function save_smushed_image_file( $file_path, $image ) {
		$pre = apply_filters( 'wp_smush_pre_image_write', false, $file_path, $image );
		if ( $pre !== false ) {
			$this->logger->notice( 'Another plugin/theme short circuited the image write operation using the wp_smush_pre_image_write filter.' );

			// Assume that the plugin/theme responsible took care of it
			return true;
		}

		// Backup the old permissions
		$permissions = $this->get_file_permissions( $file_path );

		// Save the new file
		$success = $this->put_smushed_image_file( $file_path, $image );

		// Restore the old permissions
		// TODO: this is the only chmod but restoring in the comment suggests that we changed the permissions before, what are we doing?
		chmod( $file_path, $permissions );

		return $success;
	}

	private function put_smushed_image_file( $file_path, $image ) {
		$temp_file = $file_path . '.tmp';

		$success = $this->put_image_using_temp_file( $file_path, $image, $temp_file );

		// Clean up
		if ( $this->fs->file_exists( $temp_file ) ) {
			$this->fs->unlink( $temp_file );
		}

		return $success;
	}

	private function put_image_using_temp_file( $file_path, $image, $temp_file ) {
		$file_written = file_put_contents( $temp_file, $image );
		if ( ! $file_written ) {
			return false;
		}

		$renamed = rename( $temp_file, $file_path );
		if ( $renamed ) {
			return true;
		}

		$copied = $this->fs->copy( $temp_file, $file_path );
		if ( $copied ) {
			return true;
		}

		return false;
	}

	private function get_file_permissions( $file_path ) {
		clearstatcache();
		$perms = fileperms( $file_path ) & 0777;
		// Some servers are having issue with file permission, this should fix it.
		if ( empty( $perms ) ) {
			// Source: WordPress Core.
			$stat  = stat( dirname( $file_path ) );
			$perms = $stat['mode'] & 0000666; // Same permissions as parent folder, strip off the executable bits.
		}

		return $perms;
	}

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
	 * @param $response
	 * @param $size_key string
	 * @param $file_path string
	 *
	 * @return object|false
	 */
	private function parse_response( $response, $size_key, $file_path ) {
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();

			if ( strpos( $error, 'SSL CA cert' ) !== false ) {
				$this->add_error( $size_key, self::ERROR_SSL_CERT, $error );

				return false;
			} else if ( strpos( $error, 'timed out' ) !== false ) {
				$this->add_error(
					$size_key,
					'time_out',
					esc_html__( "Skipped due to a timeout error. You can increase the request timeout to make sure Smush has enough time to process larger files. define('WP_SMUSH_TIMEOUT', 150);", 'wp-smushit' )
				);

				return false;
			} else {
				$this->add_error(
					$size_key,
					'error_posting_to_api',
					/* translators: %s: Error message. */
					sprintf( __( 'Error posting to API: %s', 'wp-smushit' ), $error )
				);

				return false;
			}
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$error = sprintf(
			/* translators: 1: Error code, 2: Error message. */
				__( 'Error posting to API: %1$s %2$s', 'wp-smushit' ),
				wp_remote_retrieve_response_code( $response ),
				wp_remote_retrieve_response_message( $response )
			);

			$this->add_error( $size_key, 'non_200_response', $error );

			return false;
		}

		$json = json_decode( wp_remote_retrieve_body( $response ) );
		if ( empty( $json->success ) ) {
			$error = ! empty( $json->data )
				? $json->data
				: __( "Image couldn't be smushed", 'wp-smushit' );

			$this->add_error( $size_key, 'unsuccessful_smush', $error );

			return false;
		}

		if (
			empty( $json->data )
			|| empty( $json->data->before_size )
			|| empty( $json->data->after_size )
		) {
			$this->add_error( $size_key, 'no_data', __( 'Unknown API error', 'wp-smushit' ) );

			return false;
		}

		$data                   = $json->data;
		$data->bytes_saved      = isset( $data->bytes_saved ) ? (int) $data->bytes_saved : 0;
		$optimized_image_larger = $data->after_size > $data->before_size;
		if ( $optimized_image_larger ) {
			$this->add_error(
				$size_key,
				'optimized_image_larger',
				/* translators: 1: File path, 2: Savings bytes. */
				sprintf( 'The smushed image is larger than the original image [%s] (bytes saved %d), keep original image.', $this->upload_dir->get_human_readable_path( $file_path ), $data->bytes_saved )
			);

			return false;
		}

		$image = empty( $data->image ) ? '' : $data->image;
		if ( $data->bytes_saved > 0 ) {
			// Because of the API response structure, the following should only be done when there are some bytes_saved.

			if ( $data->image_md5 !== md5( $image ) ) {
				$error = __( 'Smush data corrupted, try again.', 'wp-smushit' );
				$this->add_error( $size_key, 'data_corrupted', $error );

				return false;
			}

			if ( ! empty( $image ) ) {
				$data->image = base64_decode( $data->image );
			}
		}

		return $data;
	}

	public function should_retry_smush( $response ) {
		return $this->retry_attempts > 0 && (
				is_wp_error( $response )
				|| 200 !== wp_remote_retrieve_response_code( $response )
			);
	}

	private function get_parallel_request_args( $file_path ) {
		return array(
			'url'     => $this->get_api_url(),
			'headers' => $this->get_api_request_headers( $file_path ),
			'data'    => $this->fs->file_get_contents( $file_path ),
			'type'    => 'POST',
		);
	}

	/**
	 * @return string
	 */
	private function get_api_url() {
		return defined( 'WP_SMUSH_API_HTTP' ) ? WP_SMUSH_API_HTTP : WP_SMUSH_API;
	}

	/**
	 * @return string[]
	 */
	protected function get_api_request_headers( $file_path ) {
		$headers = array(
			'accept'       => 'application/json',   // The API returns JSON.
			'content-type' => 'application/binary', // Set content type to binary.
			'exif'         => $this->settings->get( 'strip_exif' ) ? 'false' : 'true',
		);

		$headers['lossy'] = $this->settings->get_lossy_level_setting();

		// Check if premium member, add API key.
		$api_key = Helper::get_wpmudev_apikey();
		if ( ! empty( $api_key ) && WP_Smush::is_pro() ) {
			$headers['apikey'] = $api_key;

			$is_large_file = $this->is_large_file( $file_path );
			if ( $is_large_file ) {
				$headers['islarge'] = 1;
			}
		}

		return $headers;
	}

	private function is_large_file( $file_path ) {
		$file_size = file_exists( $file_path ) ? filesize( $file_path ) : 0;
		$cut_off   = $this->settings->get_large_file_cutoff();

		return $file_size > $cut_off;
	}

	/**
	 * @return bool
	 */
	public function parallel_available() {
		if ( ! $this->smush_parallel ) {
			return false;
		}

		return $this->curl_multi_exec_available();
	}

	/**
	 * @return bool
	 */
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
	 * @param int $retry_attempts
	 *
	 * @return Smusher
	 */
	public function set_retry_attempts( $retry_attempts ) {
		$this->retry_attempts = $retry_attempts;

		return $this;
	}

	/**
	 * @param int $timeout
	 */
	public function set_timeout( $timeout ) {
		$this->timeout = $timeout;
	}

	/**
	 * @param bool $smush_parallel
	 *
	 * @return Smusher
	 */
	public function set_smush_parallel( $smush_parallel ) {
		$this->smush_parallel = $smush_parallel;

		return $this;
	}

	/**
	 * @param Request_Multiple $request_multiple
	 *
	 * @return Smusher
	 */
	public function set_request_multiple( $request_multiple ) {
		$this->request_multiple = $request_multiple;

		return $this;
	}

	public function get_errors() {
		return $this->errors;
	}

	/**
	 * @param $errors WP_Error
	 *
	 * @return void
	 */
	private function set_errors( $errors ) {
		$this->errors = $errors;
	}

	/**
	 * @param $size_key string
	 * @param $code string
	 * @param $message string
	 *
	 * @return void
	 */
	private function add_error( $size_key, $code, $message ) {
		// Log the error
		$this->logger->error( "[$size_key] $message" );
		// Add the error
		$this->errors->add( $code, "[$size_key] $message" );
	}

	/**
	 * @param $code string
	 *
	 * @return bool
	 */
	private function has_error( $code ) {
		return ! empty( $this->errors->get_error_message( $code ) );
	}
}