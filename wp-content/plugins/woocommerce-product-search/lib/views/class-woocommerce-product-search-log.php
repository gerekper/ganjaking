<?php
/**
 * class-woocommerce-product-search-log.php
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

if ( !defined( 'WPS_LOG_ERROR' ) ) {
	define( 'WPS_LOG_ERROR', WooCommerce_Product_Search_Log::ERROR );
}
if ( !defined( 'WPS_LOG_WARNING' ) ) {
	define( 'WPS_LOG_WARNING', WooCommerce_Product_Search_Log::WARNING );
}
if ( !defined( 'WPS_LOG_INFO' ) ) {
	define( 'WPS_LOG_INFO', WooCommerce_Product_Search_Log::INFO );
}
if ( !defined( 'WPS_LOG_VERBOSE' ) ) {
	define( 'WPS_LOG_VERBOSE', WooCommerce_Product_Search_Log::VERBOSE );
}

if ( !function_exists( 'wps_log' ) ) {
	/**
	 * Log the message with given level.
	 *
	 * @param string $message message to log
	 * @param int|string $level log level
	 *
	 * @see WooCommerce_Product_Search_Log::log()
	 */
	function wps_log( $message, $level = WPS_LOG_INFO ) {
		WooCommerce_Product_Search_Log::log( $message, $level );
	}
}
if ( !function_exists( 'wps_log_verbose' ) ) {
	/**
	 * Log the verbose info.
	 *
	 * @since 5.0.0
	 *
	 * @param string $message message to log
	 * @param boolean $always whether to always log
	 */
	function wps_log_verbose( $message, $always = false ) {
		WooCommerce_Product_Search_Log::log( $message, WooCommerce_Product_Search_Log::VERBOSE, $always );
	}
}
if ( !function_exists( 'wps_log_info' ) ) {
	/**
	 * Log the info.
	 *
	 * @param string $message message to log
	 * @param boolean $always whether to always log
	 */
	function wps_log_info( $message, $always = false ) {
		WooCommerce_Product_Search_Log::log( $message, WooCommerce_Product_Search_Log::INFO, $always );
	}
}
if ( !function_exists( 'wps_log_warning' ) ) {
	/**
	 * Log the warning.
	 *
	 * @param string $message message to log
	 * @param boolean $always whether to always log
	 */
	function wps_log_warning( $message, $always = false ) {
		WooCommerce_Product_Search_Log::log( $message, WooCommerce_Product_Search_Log::WARNING, $always );
	}
}
if ( !function_exists( 'wps_log_error' ) ) {
	/**
	 * Log the error.
	 *
	 * @param string $message message to log
	 */
	function wps_log_error( $message ) {
		WooCommerce_Product_Search_Log::log( $message, WooCommerce_Product_Search_Log::ERROR, true );
	}
}

/**
 * Logger
 */
class WooCommerce_Product_Search_Log {

	const ERROR = 0;
	const WARNING = 1;
	const INFO = 2;
	const VERBOSE = 3;

	/**
	 * Uniquely identifies the current process.
	 *
	 * @var int|string process identifier
	 */
	private static $pid = null;

	/**
	 * Log the message.
	 *
	 * ERROR messages are logged always.
	 * Messages INFO and WARNING are logged when WPS_DEBUG is true or WPS_DEBUG_VERBOSE is true.
	 * DETAIL messages are logged only when WPS_DEBUG_VERBOSE is true.
	 *
	 * @param string $message the log message
	 * @param int|string $level the log level
	 */
	public static function log( $message, $level = self::INFO, $always = false ) {
		$log = $always;
		if ( strlen( $message ) > 0 ) {
			if ( is_string( $level ) ) {
				$level = strtolower( $level );
				switch ( $level ) {
					case 'error':
						$level = self::ERROR;
						break;
					case 'warning':
						$level = self::WARNING;
						break;
					case 'verbose':
						$level = self::VERBOSE;
						break;
					default:
						$level = self::INFO;
				}
			}
			switch( $level ) {
				case self::ERROR:
					$log = true;
					break;
				case self::VERBOSE:
					if ( WPS_DEBUG_VERBOSE ) {
						$log = true;
					}
					break;
				default :
					if ( WPS_DEBUG || WPS_DEBUG_VERBOSE ) {
						$log = true;
					}
					break;
			}
		}
		if ( $log ) {
			if ( self::$pid === null ) {
				if ( function_exists( 'getmypid' ) ) {
					self::$pid = @getmypid();
					if ( self::$pid === false ) {
						self::$pid = null;
					}
				}
				if ( self::$pid === null ) {
					self::$pid = hash( 'crc32', rand( 0, time() ) );
				}
			}
			switch( $level ) {
				case self::ERROR:
					$level_str = 'ERROR';
					break;
				case self::WARNING:
					$level_str = 'WARNING';
					break;
				default :

					$level_str = 'INFO';
			}
			error_log( sprintf(
				'WPS | %s | %s | %s',
				$level_str,
				$message,
				self::$pid !== null ? self::$pid : '.'
			) );
		}
	}
}
