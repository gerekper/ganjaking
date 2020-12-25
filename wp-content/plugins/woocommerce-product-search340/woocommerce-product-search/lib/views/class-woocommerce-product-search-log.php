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

if ( !defined( 'WPS_LOG_INFO' ) ) {
	define( 'WPS_LOG_INFO', WooCommerce_Product_Search_Log::INFO );
}
if ( !defined( 'WPS_LOG_WARNING' ) ) {
	define( 'WPS_LOG_WARNING', WooCommerce_Product_Search_Log::WARNING );
}
if ( !defined( 'WPS_LOG_ERROR' ) ) {
	define( 'WPS_LOG_ERROR', WooCommerce_Product_Search_Log::ERROR );
}

if ( !function_exists( 'wps_log' ) ) {
	/**
	 * Log the message with given level.
	 *
	 * @param string $message message to log
	 * @param int $level log level
	 *
	 * @see WooCommerce_Product_Search_Log::log()
	 */
	function wps_log( $message, $level = WPS_LOG_INFO ) {
		WooCommerce_Product_Search_Log::log( $message, $level );
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

	const INFO = 0;
	const WARNING = 1;
	const ERROR = 2;

	/**
	 * Log the message. INFO and WARNING are logged only when WPS_DEBUG is true.
	 * ERROR is logged always.
	 *
	 * @param string $message the log message
	 * @param int $level the log level
	 */
	public static function log( $message, $level = self::INFO, $always = false ) {
		$log = $always;
		if ( strlen( $message ) > 0 ) {
			switch( $level ) {
				case self::ERROR :
					$log = true;
					break;
				default :
					if ( WPS_DEBUG ) {
						$log = true;
					}
					break;
			}
		}
		if ( $log ) {
			$pid = @getmypid();
			if ( $pid === false ) {
				$pid = 'ERROR';
			}
			switch( $level ) {
				case self::ERROR :
					$level_str = 'ERROR';
					break;
				case self::WARNING :
					$level_str = 'WARNING';
					break;
				default :
					$level_str = 'INFO';
			}
			
			error_log( sprintf(
				'WPS | %s | %s | %s',
				$level_str,
				$message,
				$pid
			) );
		}
	}
}
