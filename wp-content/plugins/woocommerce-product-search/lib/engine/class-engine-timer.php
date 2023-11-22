<?php
/**
 * class-engine-timer.php
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
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Timer for performance stats.
 */
class Engine_Timer {

	/**
	 * Cache group
	 *
	 * @var string
	 */
	const CACHE_GROUP = 'ixwps_engine_timer';

	/**
	 * Product count cache key
	 *
	 * @var string
	 */
	const COUNT_CACHE_KEY = 'count';

	/**
	 * Timer data cache lifetime
	 *
	 * @var int
	 */
	const CACHE_LIFETIME = Cache::HOUR;

	/**
	 * @var string
	 */
	private $name = '';

	/**
	 * @var string
	 */
	private $context = null;

	/**
	 * @var number
	 */
	private $start = 0.0;

	/**
	 * @var number
	 */
	private $stop = 0.0;

	/**
	 * @var boolean
	 */
	private $log = false;

	/**
	 * @var boolean
	 */
	private $verbose = false;

	/**
	 * @var object
	 */
	private $object = null;

	/**
	 * Create a timer instance.
	 *
	 * @param object $object
	 * @param string $context
	 */
	public function __construct( $object = null, $context = null ) {
		if ( $object !== null && is_object( $object ) ) {
			$this->object = $object;
			$name = get_class( $object );
			$p = strrpos( $name , '\\' );
			if ( $p !== false ) {
				$name = substr( $name, $p + 1 );
			}
			$name = str_replace( '_', ' ', $name );
			if ( $name !== null && is_string( $name ) ) {
				$this->name = $name;
			}
			if ( $context !== null && is_string( $context ) ) {
				$context = preg_replace( '/[^a-zA-Z0-9-_]/', '', $context );
				if ( is_string( $context ) ) {
					$context = trim( $context );
					if ( strlen( $context ) > 0 ) {
						$this->context = $context;
					}
				}
			}
		}

		$options = get_option( 'woocommerce-product-search', null );
		$this->log = isset( $options[\WooCommerce_Product_Search::LOG_QUERY_TIMES] ) ? $options[\WooCommerce_Product_Search::LOG_QUERY_TIMES] : \WooCommerce_Product_Search::LOG_QUERY_TIMES_DEFAULT;

		if ( defined( 'WPS_DEBUG_ENGINE_TIMER' ) ) {
			if ( is_scalar( WPS_DEBUG_ENGINE_TIMER ) ) {
				$this->log = boolval( WPS_DEBUG_ENGINE_TIMER );
			}
		}

		if ( !$this->log ) {
			if ( defined( 'WPS_DEBUG_VERBOSE' ) && WPS_DEBUG_VERBOSE ) {
				$this->log = true;
			}
		}

		if ( defined( 'WPS_DEBUG_ENGINE_TIMER_VERBOSE' ) ) {
			if ( is_scalar( WPS_DEBUG_ENGINE_TIMER_VERBOSE ) ) {
				$this->verbose = boolval( WPS_DEBUG_ENGINE_TIMER_VERBOSE );
			}
		}
	}

	/**
	 * Enable or disable logging.
	 *
	 * @param boolean $log enable
	 *
	 * @return boolean enabled
	 */
	public function set_log( $log ) {
		if ( is_bool( $log ) ) {
			$this->log = boolval( $log );
		}
		return $this->log;
	}

	/**
	 * Whether logging is enabled.
	 *
	 * @return boolean enabled
	 */
	public function get_log() {
		return $this->log;
	}

	/**
	 * Start timer.
	 */
	public function start() {
		$this->start = function_exists( 'microtime' ) ? microtime( true ) : time();
	}

	/**
	 * Stop timer.
	 */
	public function stop() {
		$this->stop = function_exists( 'microtime' ) ? microtime( true ) : time();
	}

	/**
	 * Provide timing.
	 *
	 * @return number
	 */
	public function get_timing() {
		return $this->stop - $this->start;
	}

	/**
	 * Make a timing stats log entry.
	 *
	 * @param int|string $level
	 * @param string $extra
	 */
	public function log( $level = null, $extra = null ) {
		if ( $this->log ) {
			if ( $level === null ) {
				$level = \WooCommerce_Product_Search_Log::INFO;
			}

			if ( $this->verbose ) {
				if ( is_string( $level ) ) {
					$level = strtolower( $level );
				}
				switch ( $level ) {
					case 'verbose':
					case \WooCommerce_Product_Search_Log::VERBOSE:
						$level = \WooCommerce_Product_Search_Log::INFO;
						break;
				}
			}
			$s = $this->get_timing();
			if ( $s >= 0 ) {

				$cache = Cache::get_instance();
				$total = $cache->get( self::COUNT_CACHE_KEY, self::CACHE_GROUP );
				if ( $total === null ) {

					global $wpdb;
					$total = intval( $wpdb->get_var(
						"SELECT count(*) FROM $wpdb->posts WHERE post_type IN ( 'product', 'product_variation' ) AND post_status IN ( 'publish', 'pending', 'draft' )"
					) );
					$cache->set( self::COUNT_CACHE_KEY, $total, self::CACHE_GROUP, self::CACHE_LIFETIME );
				}

				$current_url = ( is_ssl() ? 'https://' : 'http://' ) . ( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '' ) . ( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
				if ( defined( 'WP_CLI' ) && WP_CLI ) {
					$current_url = 'wp-cli';
				}
				$is_cache_hit = false;
				if ( $this->object !== null && method_exists( $this->object, 'is_cache_hit') ) {
					$is_cache_hit = $this->object->is_cache_hit();
				}
				$is_cache_write = false;
				if ( $this->object !== null && method_exists( $this->object, 'is_cache_write') ) {
					$is_cache_write = $this->object->is_cache_write();
				}
				$count = 0;
				if ( $this->object !== null && method_exists( $this->object, 'get_count') ) {
					$count = $this->object->get_count();
				}

				$cs = '-';
				if ( $this->object !== null && method_exists( $this->object, 'get_parameters' ) ) {
					$cs_p = json_encode( $this->object->get_parameters() );
					if ( is_string( $cs_p ) ) {
						if ( strlen( $cs_p ) > 0 ) {
							$cs = hash( 'crc32', $cs_p );
						}
					}
				}
				$name = $this->name;
				if ( $this->context !== null ) {
					$name .= ' [' . $this->context . ']';
				}
				$message = sprintf(
					'%1$s : %2$fs [N=%3$d K=%4$d R%5$s W%6$s %7$s %8$s]',
					$name,
					$s,
					$total,
					$count,
					$is_cache_hit ? '+' : '-',
					$is_cache_write ? '+' : '-',
					$cs,
					$current_url
				);
				if ( is_string( $extra ) ) {
					$extra = sanitize_text_field( $extra );
					$extra = trim( $extra );
					if ( strlen( $extra ) > 0 ) {
						$message .= sprintf( ' {%s}', $extra );
					}
				}
				wps_log( $message, $level );
			}
		}
	}
}
