<?php
/**
 * API request class: Request
 *
 * Handles all the internal stuff to form and process a proper API request.
 *
 * @since 3.0
 * @package Smush\Core\Api
 */

namespace Smush\Core\Api;

use Exception;
use WP_Error;
use Smush\Core\Helper;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Request.
 */
class Request {

	/**
	 * API service.
	 *
	 * @since 3.0
	 *
	 * @var null|API
	 */
	private $service = null;

	/**
	 * Request max timeout.
	 *
	 * @since 3.0
	 *
	 * @var int
	 */
	private $timeout = 15;

	/**
	 * Header arguments.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	private $headers = array();

	/**
	 * POST arguments.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	private $post_args = array();

	/**
	 * GET arguments.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	private $get_args = array();

	/**
	 * Request constructor.
	 *
	 * @since 3.0
	 *
	 * @param Abstract_API $service  API service.
	 *
	 * @throws Exception  Init exception.
	 */
	public function __construct( $service ) {
		if ( ! $service instanceof Abstract_API ) {
			throw new Exception( __( 'Invalid API service.', 'wp-smushit' ), 404 );
		}

		$this->service = $service;
	}

	/**
	 * Get the current site URL.
	 *
	 * The network_site_url() of the WP installation. (Or network_home_url if not passing an API key).
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_this_site() {
		if ( defined( 'WP_SMUSH_API_DOMAIN' ) && WP_SMUSH_API_DOMAIN ) {
			return WP_SMUSH_API_DOMAIN;
		}

		return network_site_url();
	}

	/**
	 * Set request timeout.
	 *
	 * @since 3.0
	 *
	 * @param int $timeout  Request timeout (seconds).
	 */
	public function set_timeout( $timeout ) {
		$this->timeout = $timeout;
	}

	/**
	 * Add a new request argument for POST requests.
	 *
	 * @since 3.0
	 *
	 * @param string $name   Argument name.
	 * @param string $value  Argument value.
	 */
	public function add_post_argument( $name, $value ) {
		$this->post_args[ $name ] = $value;
	}

	/**
	 * Add a new request argument for GET requests.
	 *
	 * @since 3.0
	 *
	 * @param string $name   Argument name.
	 * @param string $value  Argument value.
	 */
	public function add_get_argument( $name, $value ) {
		$this->get_args[ $name ] = $value;
	}

	/**
	 * Add a new request argument for GET requests.
	 *
	 * @since 3.0
	 *
	 * @param string $name   Argument name.
	 * @param string $value  Argument value.
	 */
	public function add_header_argument( $name, $value ) {
		$this->headers[ $name ] = $value;
	}

	/**
	 * Make a POST API call.
	 *
	 * @since 3.0
	 *
	 * @param string $path    Endpoint route.
	 * @param array  $data    Data array.
	 *
	 * @return mixed|WP_Error
	 */
	public function post( $path, $data = array() ) {
		try {
			$result = $this->request( $path, $data );
			return $result;
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Make a GET API call.
	 *
	 * @since 3.0
	 *
	 * @param string $path    Endpoint route.
	 * @param array  $data    Data array.
	 *
	 * @return mixed|WP_Error
	 */
	public function get( $path, $data = array() ) {
		try {
			$result = $this->request( $path, $data, 'get' );
			return $result;
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Make a HEAD API call.
	 *
	 * @since 3.0
	 *
	 * @param string $path  Endpoint route.
	 * @param array  $data  Data array.
	 *
	 * @return mixed|WP_Error
	 */
	public function head( $path, $data = array() ) {
		try {
			$result = $this->request( $path, $data, 'head' );
			return $result;
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Make a PATCH API call.
	 *
	 * @since 3.0
	 *
	 * @param string $path  Endpoint route.
	 * @param array  $data  Data array.
	 *
	 * @return mixed|WP_Error
	 */
	public function patch( $path, $data = array() ) {
		try {
			$result = $this->request( $path, $data, 'patch' );
			return $result;
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Make a DELETE API call.
	 *
	 * @since 3.0
	 *
	 * @param string $path  Endpoint route.
	 * @param array  $data  Data array.
	 *
	 * @return mixed|WP_Error
	 */
	public function delete( $path, $data = array() ) {
		try {
			$result = $this->request( $path, $data, 'delete' );
			return $result;
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Get API endpoint URL for request.
	 *
	 * @since 3.0
	 *
	 * @param string $path  Endpoint path.
	 *
	 * @return string
	 */
	private function get_api_url( $path = '' ) {
		$base = defined( 'WPMUDEV_CUSTOM_API_SERVER' ) && WPMUDEV_CUSTOM_API_SERVER
			? WPMUDEV_CUSTOM_API_SERVER
			: 'https://wpmudev.com/';

		$url = "$base/api/{$this->service->name}/{$this->service->version}/";
		$url = trailingslashit( $url . $path );

		return $url;
	}

	/**
	 * Add authorization header.
	 *
	 * @since 3.0
	 */
	private function sign_request() {
		if ( ! empty( $this->service->api_key ) ) {
			$this->add_header_argument( 'Authorization', 'Basic ' . $this->service->api_key );
		}
	}

	/**
	 * Make an API request.
	 *
	 * @since 3.0
	 *
	 * @param string $path    API endpoint route.
	 * @param array  $data    Data array.
	 * @param string $method  API method.
	 *
	 * @return array|WP_Error
	 */
	private function request( $path, $data = array(), $method = 'post' ) {
		$url = $this->get_api_url( $path );

		$this->sign_request();

		$url = add_query_arg( $this->get_args, $url );
		if ( 'post' !== $method && 'patch' !== $method && 'delete' !== $method ) {
			$url = add_query_arg( $data, $url );
		}

		$args = array(
			'user-agent' => WP_SMUSH_UA,
			'headers'    => $this->headers,
			'sslverify'  => false,
			'method'     => strtoupper( $method ),
			'timeout'    => $this->timeout,
		);

		if ( ! $args['timeout'] || 2 === $args['timeout'] ) {
			$args['blocking'] = false;
		}

		switch ( strtolower( $method ) ) {
			case 'patch':
			case 'delete':
			case 'post':
				if ( is_array( $data ) ) {
					$args['body'] = array_merge( $data, $this->post_args );
				} else {
					$args['body'] = $data;
				}

				$response = wp_remote_post( $url, $args );
				break;
			case 'head':
				$response = wp_remote_head( $url, $args );
				break;
			case 'get':
				$response = wp_remote_get( $url, $args );
				break;
			default:
				$response = wp_remote_request( $url, $args );
				break;
		}

		// Log error.
		if ( is_wp_error( $response ) ) {
			Helper::logger()->api()->error( sprintf( 'Error [%s->%s]: %s', $method, $path, $response->get_error_message() ) );
		}

		return $response;
	}


}