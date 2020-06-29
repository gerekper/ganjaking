<?php
/**
 * class-woocommerce-product-search-worker.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Index worker.
 */
class WooCommerce_Product_Search_Worker {

	const INDEX_WORKER_STATUS          = 'index-worker-status';
	const INDEX_WORKER_STATUS_DEFAULT  = false;
	const START_DELTA                  = 1;
	const WORK_CYCLE                   = 'work-cycle';
	const WORK_CYCLE_DEFAULT           = 60;
	const IDLE_CYCLE                   = 'idle-cycle';
	const IDLE_CYCLE_DEFAULT           = 300;
	const LIMIT_PER_WORK_CYCLE         = 'limit-per-work-cycle';
	const LIMIT_PER_WORK_CYCLE_DEFAULT = 40;
	const TEST_CRON_REQUEST_TIMEOUT    = 5; 
	const TEST_CRON_MIN_FAIL_STATUS    = 300; 

	/**
	 * Initialize
	 */
	public static function init() {


		add_action( 'woocommerce_product_search_work', array( __CLASS__, 'work' ), 10, 0 );
		self::schedule();
		add_action( 'woocommerce_product_search_deactivate', array( __CLASS__, 'deactivate' ) );
	}

	/**
	 * Worker start
	 */
	public static function start() {


		wps_log_info( 'Worker is starting.' );
		$options = get_option( 'woocommerce-product-search', array() );
		$options[self::INDEX_WORKER_STATUS] = true;
		update_option( 'woocommerce-product-search', $options );
		self::schedule( self::START_DELTA );
	}

	/**
	 * Worker stop
	 */
	public static function stop() {


		wps_log_info( 'Worker is stopping.' );
		$options = get_option( 'woocommerce-product-search', array() );
		$options[self::INDEX_WORKER_STATUS] = false;
		update_option( 'woocommerce-product-search', $options );
		self::wp_unschedule_hook( 'woocommerce_product_search_work' );
	}

	/**
	 * Worker schedule
	 */
	private static function schedule( $delta = null ) {


		$options = get_option( 'woocommerce-product-search', array() );
		$status = isset( $options[self::INDEX_WORKER_STATUS] ) ? $options[self::INDEX_WORKER_STATUS] : self::INDEX_WORKER_STATUS_DEFAULT;

		if ( $status ) {

			if ( $delta !== null && $delta >= 0 ) {
				self::wp_unschedule_hook( 'woocommerce_product_search_work' );
			}
			if ( !self::get_next_scheduled() ) {
				$indexer = new WooCommerce_Product_Search_Indexer();
				$processable = $indexer->get_processable_count();
				$total       = $indexer->get_total_count();
				if ( $processable > 0 ) {
					if ( $delta !== null ) {
						$delta = intval( $delta );
						if ( $delta <= 0 ) {
							$delta = null;
						}
					}
					if ( $delta === null ) {
						$next = time() + self::get_work_cycle();
					} else {
						$next = time() + $delta;
					}
				} else {
					$next = time() + self::get_idle_cycle();
				}
				$scheduled = wp_schedule_single_event( $next, 'woocommerce_product_search_work' );
				wps_log_info( sprintf(
					'Worker @ %s; next scheduled @ %s',
					date( 'Y-m-d H:i:s', time() ),
					date( 'Y-m-d H:i:s', $next ) )
				);
				if ( $scheduled === false ) {
					wps_log_error( 'Worker could not schedule next work cycle.' );
				}
			}
		}
	}

	/**
	 * Worker deactivate
	 */
	public static function deactivate() {


		self::wp_unschedule_hook( 'woocommerce_product_search_work' );
	}

	/**
	 * Worker work
	 */
	public static function work() {


		$indexer = new WooCommerce_Product_Search_Indexer();
		$c = $indexer->get_processable_count();
		if ( $c > 0 ) {
			$indexer->work();
		}
		$indexer->gc();
		unset( $indexer );
	}

	/**
	 * Worker status
	 *
	 * @return boolean status
	 */
	public static function get_status() {


		$options = get_option( 'woocommerce-product-search', array() );
		return isset( $options[self::INDEX_WORKER_STATUS] ) ? $options[self::INDEX_WORKER_STATUS] : self::INDEX_WORKER_STATUS_DEFAULT;
	}

	/**
	 * Worker next scheduled
	 *
	 * @return int|false
	 */
	public static function get_next_scheduled() {


		return wp_next_scheduled( 'woocommerce_product_search_work' );
	}

	/**
	 * Worker work cycle
	 *
	 * @return int
	 */
	public static function get_work_cycle() {


		$options = get_option( 'woocommerce-product-search', array() );
		$max_execution_time = self::get_work_cycle_default();
		return intval( isset( $options[self::WORK_CYCLE] ) ? $options[self::WORK_CYCLE] : $max_execution_time );
	}

	/**
	 * Default work cycle
	 *
	 * @return int
	 */
	public static function get_work_cycle_default() {

		$max_execution_time = intval( ini_get( 'max_execution_time' ) );
		if ( $max_execution_time <= 0 ) {
			$max_execution_time = self::WORK_CYCLE_DEFAULT;
		}
		if ( $max_execution_time >= self::IDLE_CYCLE_DEFAULT ) {
			$max_execution_time = self::WORK_CYCLE_DEFAULT;
		}

		$max_input_time = ini_get( 'max_input_time' ); 
		if ( $max_input_time !== false ) { 
			$max_input_time = intval( $max_input_time );
			switch ( $max_input_time ) {
				case -1 : 
					break;
				case 0 : 
					break;
				default :

					$max_execution_time = min( $max_execution_time, $max_input_time );
			}
		}
		return $max_execution_time;
	}

	/**
	 * Worker idle cycle
	 *
	 * @return int
	 */
	public static function get_idle_cycle() {


		$options = get_option( 'woocommerce-product-search', array() );
		return intval( isset( $options[self::IDLE_CYCLE] ) ? $options[self::IDLE_CYCLE] : self::IDLE_CYCLE_DEFAULT );
	}

	/**
	 * Worker limit
	 *
	 * @return int limit per cycle
	 */
	public static function get_limit_per_work_cycle() {


		$options = get_option( 'woocommerce-product-search', array() );
		return intval( isset( $options[self::LIMIT_PER_WORK_CYCLE] ) ? $options[self::LIMIT_PER_WORK_CYCLE] : self::LIMIT_PER_WORK_CYCLE_DEFAULT );
	}

	/**
	 * Unschedules all events attached to the hook.
	 *
	 * This function is available as of WP 4.9.0 - added here for backwards compatibility.
	 *
	 * @param string $hook Action hook, the execution of which will be unscheduled.
	 */
	public static function wp_unschedule_hook( $hook ) {
		if ( function_exists( 'wp_unschedule_hook' ) ) {
			wp_unschedule_hook( 'woocommerce_product_search_work' );
		} else {
			$crons = _get_cron_array();
			foreach( $crons as $timestamp => $args ) {
				unset( $crons[ $timestamp ][ $hook ] );
				if ( empty( $crons[ $timestamp ] ) ) {
					unset( $crons[ $timestamp ] );
				}
			}
			_set_cron_array( $crons );
		}
	}

	/**
	 * Test cron - returns WP_Error if cron test fails, null if it's ok.
	 *
	 * @return null or WP_Error
	 */
	public static function cron_test() {
		$error = null;
		$now = function_exists( 'microtime' ) ? microtime( true ) : time();
		$doing_wp_cron = sprintf( '%.22F', $now );
		$cron_request = apply_filters(
			'cron_request',
			array(
				'url' => add_query_arg( 'doing_wp_cron', $doing_wp_cron, site_url( 'wp-cron.php' ) ),
				'key' => $doing_wp_cron,
				'args' => array(
					'timeout' => self::TEST_CRON_REQUEST_TIMEOUT,
					'blocking' => true,
					'sslverify' => apply_filters( 'https_local_ssl_verify', false )
				)
			),
			$doing_wp_cron
		);
		$response = wp_remote_post( $cron_request['url'], $cron_request['args'] );
		if ( !is_wp_error( $response ) ) {
			$code = wp_remote_retrieve_response_code( $response );
			if ( empty( $code ) || intval( $code ) >= self::TEST_CRON_MIN_FAIL_STATUS ) {
				$error = new WP_Error(
					'wps_cron_test_failed',
					sprintf(
						__( 'HTTP Response Status: %d %s', 'woocommerce-product-search' ),
						esc_html( $code ),
						esc_html( self::get_reason_phrase( $code ) )
					)
				);
			}
		}
		return $error;
	}

	/**
	 * Returns the reason-phrase for the HTTP status code.
	 *
	 * For unknown codes 'Unknown' is returned (smart right?)
	 *
	 * @param int $code
	 *
	 * @return string
	 */
	public static function get_reason_phrase( $code ) {
		$text = 'Unknown';
		$code = '' . intval( $code );
		$descriptions = array(
			'100' => 'Continue',
			'101' => 'Switching Protocols',
			'102' => 'Processing',
			'103' => 'Early Hints',
			'200' => 'OK',
			'201' => 'Created',
			'202' => 'Accepted',
			'203' => 'Non-Authoritative Information',
			'204' => 'No Content',
			'205' => 'Reset Content',
			'206' => 'Partial Content',
			'207' => 'Multi-Status',
			'208' => 'Already Reported',
			'226' => 'IM Used',
			'300' => 'Multiple Choices',
			'301' => 'Moved Permanently',
			'302' => 'Found',
			'303' => 'See Other',
			'304' => 'Not Modified',
			'305' => 'Use Proxy',
			'306' => 'Switch Proxy',
			'307' => 'Temporary Redirect',
			'308' => 'Permanent Redirect',
			'400' => 'Bad Request',
			'401' => 'Unauthorized',
			'402' => 'Payment Required',
			'403' => 'Forbidden',
			'404' => 'Not Found',
			'405' => 'Method Not Allowed',
			'406' => 'Not Acceptable',
			'407' => 'Proxy Authentication Required',
			'408' => 'Request Timeout',
			'409' => 'Conflict',
			'410' => 'Gone',
			'411' => 'Length Required',
			'412' => 'Precondition Failed',
			'413' => 'Payload Too Large',
			'414' => 'URI Too Long',
			'415' => 'Unsupported Media Type',
			'416' => 'Range Not Satisfiable',
			'417' => 'Expectation Failed',
			'418' => 'I\'m a Teapot',
			'421' => 'Misdirected Request',
			'422' => 'Unprocessable Entity',
			'423' => 'Locked',
			'424' => 'Failed Dependency',
			'426' => 'Upgrade Required',
			'428' => 'Precondition Required',
			'429' => 'Too Many Requests',
			'431' => 'Request Header Fields Too Large',
			'451' => 'Unavailable For Legal Reasons',
			'500' => 'Internal Server Error',
			'501' => 'Not Implemented',
			'502' => 'Bad Gateway',
			'503' => 'Service Unavailable',
			'504' => 'Gateway Timeout',
			'505' => 'HTTP Version Not Supported',
			'506' => 'Variant Also Negotiates',
			'507' => 'Insufficient Storage',
			'508' => 'Loop Detected',
			'510' => 'Not Extended',
			'511' => 'Network Authentication Required'
		);
		if ( key_exists( $code, $descriptions ) ) {
			$text = $descriptions[$code];
		}
		return $text;
	}
}
WooCommerce_Product_Search_Worker::init();
