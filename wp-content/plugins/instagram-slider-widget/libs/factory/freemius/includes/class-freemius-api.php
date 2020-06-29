<?php

namespace WBCR\Factory_Freemius_111;

use Freemius_Api_WordPress;
use Freemius_Exception;
use Wbcr_Factory423_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FS_Api
 *
 * Wraps Freemius API SDK to handle:
 * 1. Clock sync.
 * 2. Fallback to HTTP when HTTPS fails.
 * 3. Adds caching layer to GET requests.
 * 4. Adds consistency for failed requests by using last cached version.
 *
 * @author Webcraftic <wordpress.webraftic@gmail.com>, Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 2018, Webcraftic Ltd
 *
 * @package core
 * @since 1.0.0
 */
final class Api {
	
	/**
	 * @var Freemius_Api_WordPress
	 */
	private $api;
	
	/**
	 * @var Wbcr_Factory423_Plugin
	 */
	private $plugin;
	
	/**
	 * @var Api[]
	 */
	private static $instances = array();
	
	/**
	 * @var int Clock diff in seconds between current server to API server.
	 */
	private static $clock_diff;
	
	/**
	 * @param Wbcr_Factory423_Plugin $slug
	 * @param string $scope 'app', 'developer', 'user' or 'install'.
	 * @param number $id Element's id.
	 * @param string $public_key Public key.
	 * @param bool|string $secret_key Element's secret key.
	 * @param bool $is_sandbox
	 */
	private function __construct( Wbcr_Factory423_Plugin $plugin, $scope, $id, $public_key, $secret_key, $is_sandbox ) {
		if ( ! class_exists( 'Freemius_Api_WordPress' ) ) {
			require_once WP_FS__DIR_SDK . '/FreemiusWordPress.php';
		}
		
		$this->api = new Freemius_Api_WordPress( $scope, $id, $public_key, $secret_key, $is_sandbox );
		
		$this->plugin = $plugin;
		
		self::$clock_diff = $plugin->getPopulateOption( 'freemius_api_clock_diff', 0 );
		Freemius_Api_WordPress::SetClockDiff( self::$clock_diff );
		
		if ( $plugin->getPopulateOption( 'api_force_http', false ) ) {
			Freemius_Api_WordPress::SetHttp();
		}
	}
	
	/**
	 * @param Wbcr_Factory423_Plugin $plugin
	 * @param string $scope 'app', 'developer', 'user' or 'install'.
	 * @param number $id Element's id.
	 * @param string $public_key Public key.
	 * @param bool $is_sandbox
	 * @param bool|string $secret_key Element's secret key.
	 *
	 * @return Api
	 */
	public static function instance( Wbcr_Factory423_Plugin $plugin, $scope, $id, $public_key, $is_sandbox, $secret_key = false ) {
		$identifier = md5( $plugin->getPluginName() . $scope . $id . $public_key . ( is_string( $secret_key ) ? $secret_key : '' ) . json_encode( $is_sandbox ) );
		
		if ( ! isset( self::$instances[ $identifier ] ) ) {
			self::$instances[ $identifier ] = new self( $plugin, $scope, $id, $public_key, $secret_key, $is_sandbox );
		}
		
		return self::$instances[ $identifier ];
	}
	
	/**
	 * Check if valid ping request result.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.1
	 *
	 * @param mixed $pong
	 *
	 * @return bool
	 */
	public function is_valid_ping( $pong ) {
		return Freemius_Api_WordPress::Test( $pong );
	}
	
	/**
	 * Override API call to wrap it in servers' clock sync method.
	 *
	 * @param string $path
	 * @param string $method
	 * @param array $params
	 *
	 * @return array|mixed|string
	 * @throws Freemius_Exception
	 */
	public function call( $path, $method = 'GET', $params = array() ) {
		return $this->_call( $path, $method, $params );
	}
	
	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public function get_url( $path = '' ) {
		return Freemius_Api_WordPress::GetUrl( $path, $this->api->IsSandbox() );
	}
	
	/**
	 * Get API request URL signed via query string.
	 *
	 * @param string $path
	 *
	 * @return string
	 * @throws Freemius_Exception
	 */
	public function get_signed_url( $path ) {
		return $this->api->GetSignedUrl( $path );
	}
	
	#----------------------------------------------------------------------------------
	#region Error Handling
	#----------------------------------------------------------------------------------
	
	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.2.1.5
	 *
	 * @param mixed $result
	 *
	 * @return bool Is API result contains an error.
	 */
	public static function is_api_error( $result ) {
		return ( is_object( $result ) && isset( $result->error ) ) || is_string( $result );
	}
	
	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  2.0.0
	 *
	 * @param mixed $result
	 *
	 * @return bool Is API result contains an error.
	 */
	public static function is_api_error_object( $result ) {
		return ( is_object( $result ) && isset( $result->error ) && isset( $result->error->message ) );
	}
	
	/**
	 * Checks if given API result is a non-empty and not an error object.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.2.1.5
	 *
	 * @param mixed $result
	 * @param string|null $required_property Optional property we want to verify that is set.
	 *
	 * @return bool
	 */
	public static function is_api_result_object( $result, $required_property = null ) {
		return ( is_object( $result ) && ! isset( $result->error ) && ( empty( $required_property ) || isset( $result->{$required_property} ) ) );
	}
	
	/**
	 * Checks if given API result is a non-empty entity object with non-empty ID.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.2.1.5
	 *
	 * @param mixed $result
	 *
	 * @return bool
	 */
	/*static function is_api_result_entity( $result ) {
		return self::is_api_result_object( $result, 'id' ) && FS_Entity::is_valid_id( $result->id );
	}*/
	
	/**
	 * Get API result error code. If failed to get code, returns an empty string.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  2.0.0
	 *
	 * @param mixed $result
	 *
	 * @return string
	 */
	public static function get_error_code( $result ) {
		if ( is_object( $result ) && isset( $result->error ) && is_object( $result->error ) && ! empty( $result->error->code ) ) {
			return $result->error->code;
		}
		
		return '';
	}
	#endregion
	
	/**
	 * Find clock diff between server and API server, and store the diff locally.
	 *
	 * @param bool|int $diff
	 *
	 * @return bool|int False if clock diff didn't change, otherwise returns the clock diff in seconds.
	 */
	private function sync_clock_diff( $diff = false ) {
		
		// Sync clock and store.
		$new_clock_diff = ( false === $diff ) ? Freemius_Api_WordPress::FindClockDiff() : $diff;
		
		if ( $new_clock_diff === self::$clock_diff ) {
			return false;
		}
		
		self::$clock_diff = $new_clock_diff;
		
		// Update API clock's diff.
		Freemius_Api_WordPress::SetClockDiff( self::$clock_diff );
		
		// Store new clock diff in storage.
		$this->plugin->updatePopulateOption( 'freemius_api_clock_diff', self::$clock_diff );
		
		return $new_clock_diff;
	}
	
	/**
	 * Override API call to enable retry with servers' clock auto sync method.
	 *
	 * @param string $path
	 * @param string $method
	 * @param array $params
	 * @param bool $retry Is in retry or first call attempt.
	 *
	 * @return array|mixed|string
	 */
	private function _call( $path, $method = 'GET', $params = array(), $retry = false ) {
		
		$result = $this->api->Api( $path, $method, $params );
		
		if ( null !== $result && isset( $result->error ) && isset( $result->error->code ) && 'request_expired' === $result->error->code ) {
			if ( ! $retry ) {
				$diff = isset( $result->error->timestamp ) ? ( time() - strtotime( $result->error->timestamp ) ) : false;
				
				// Try to sync clock diff.
				if ( false !== $this->sync_clock_diff( $diff ) ) {
					// Retry call with new synced clock.
					return $this->_call( $path, $method, $params, true );
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Test API connectivity.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.9 If fails, try to fallback to HTTP.
	 * @since  1.1.6 Added a 5-min caching mechanism, to prevent from overloading the server if the API if
	 *         temporary down.
	 *
	 * @return bool True if successful connectivity to the API.
	 */
	/*public static function test( $plugin ) {
		self::init( $plugin );
		
		$cache_key = 'ping_test';
		
		$test = self::$_cache->get_valid( $cache_key, null );
		
		if ( is_null( $test ) ) {
			$test = Freemius_Api_WordPress::Test();
			
			if ( false === $test && Freemius_Api_WordPress::IsHttps() ) {
				// Fallback to HTTP, since HTTPS fails.
				Freemius_Api_WordPress::SetHttp();
				
				self::$_options->set_option( 'api_force_http', true, true );
				
				$test = Freemius_Api_WordPress::Test();
				
				if ( false === $test ) {
					/**
					 * API connectivity test fail also in HTTP request, therefore,
					 * fallback to HTTPS to keep connection secure.
					 *
					 * @since 1.1.6
					 */
	/*	self::$_options->set_option( 'api_force_http', false, true );
	}
}

self::$_cache->set( $cache_key, $test, WP_FS__TIME_5_MIN_IN_SEC );
}

return $test;
}*/
	
	/**
	 * Check if API is temporary down.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.6
	 *
	 * @return bool
	 */
	/*public static function is_temporary_down() {
		self::init();
		
		$test = self::$_cache->get_valid( 'ping_test', null );
		
		return ( false === $test );
	}*/
	
	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.6
	 *
	 * @return object
	 */
	/*private function get_temporary_unavailable_error() {
		return (object) array(
			'error' => (object) array(
				'type'    => 'TemporaryUnavailable',
				'message' => 'API is temporary unavailable, please retry in ' . ( self::$_cache->get_record_expiration( 'ping_test' ) - WP_FS__SCRIPT_START_TIME ) . ' sec.',
				'code'    => 'temporary_unavailable',
				'http'    => 503
			)
		);
	}*/
	
	/**
	 * Ping API for connectivity test, and return result object.
	 *
	 * @author   Vova Feldman (@svovaf)
	 * @since    1.0.9
	 *
	 * @param null|string $unique_anonymous_id
	 * @param array $params
	 *
	 * @return object
	 */
	/*public function ping( $unique_anonymous_id = null, $params = array() ) {
		if ( self::is_temporary_down() ) {
			return $this->get_temporary_unavailable_error();
		}
		
		$pong = is_null( $unique_anonymous_id ) ? Freemius_Api_WordPress::Ping() : $this->_call( 'ping.json?' . http_build_query( array_merge( array( 'uid' => $unique_anonymous_id ), $params ) ) );
		
		if ( $this->is_valid_ping( $pong ) ) {
			return $pong;
		}
		
		if ( self::should_try_with_http( $pong ) ) {
			// Fallback to HTTP, since HTTPS fails.
			Freemius_Api_WordPress::SetHttp();
			
			$this->plugin->updatePopulateOption( 'api_force_http', true );
			
			$pong = is_null( $unique_anonymous_id ) ? Freemius_Api_WordPress::Ping() : $this->_call( 'ping.json?' . http_build_query( array_merge( array( 'uid' => $unique_anonymous_id ), $params ) ) );
			
			if ( ! $this->is_valid_ping( $pong ) ) {
				$this->plugin->updatePopulateOption( 'api_force_http', false );
			}
		}
		
		return $pong;
	}*/
	
	/**
	 * Check if based on the API result we should try
	 * to re-run the same request with HTTP instead of HTTPS.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.6
	 *
	 * @param $result
	 *
	 * @return bool
	 */
	/*private static function should_try_with_http( $result ) {
		if ( ! Freemius_Api_WordPress::IsHttps() ) {
			return false;
		}
		
		return ( ! is_object( $result ) || ! isset( $result->error ) || ! isset( $result->error->code ) || ! in_array( $result->error->code, array(
				'curl_missing',
				'cloudflare_ddos_protection',
				'maintenance_mode',
				'squid_cache_block',
				'too_many_requests',
			) ) );
	}*/
}