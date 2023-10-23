<?php
/**
 * WooCommerce logger wrapper.
 *
 * @since 2.0.0
 */

namespace KoiLab\WC_Currency_Converter;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Currency_Converter\Utilities\String_Utils;
use WC_Logger_Interface;

/**
 * Class Logger.
 */
class Logger {

	/**
	 * Default log source.
	 *
	 * @var string
	 */
	private static $default_source = 'wc_currency_converter';

	/**
	 * Logger instance.
	 *
	 * @var WC_Logger_Interface
	 */
	private static $logger;

	/**
	 * Logs a message.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param string       $level   Log level.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	protected static function log( $message, $level, $context = array() ) {
		if ( ! self::$logger ) {
			self::$logger = wc_get_logger();
		}

		if ( ! is_array( $context ) ) {
			$context = array( 'source' => (string) $context );
		}

		if ( empty( $context['source'] ) ) {
			$context['source'] = self::$default_source;
		} else {
			$context['source'] = String_Utils::maybe_prefix( $context['source'], self::$default_source . '_' );
		}

		call_user_func( array( self::$logger, $level ), $message, $context );
	}

	/**
	 * Adds an emergency level message.
	 *
	 * System is unusable.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function emergency( $message, $context = array() ) {
		self::log( $message, 'emergency', $context );
	}

	/**
	 * Adds an alert level message.
	 *
	 * Action must be taken immediately.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function alert( $message, $context = array() ) {
		self::log( $message, 'alert', $context );
	}

	/**
	 * Adds a critical level message.
	 *
	 * Critical conditions.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function critical( $message, $context = array() ) {
		self::log( $message, 'critical', $context );
	}

	/**
	 * Adds an error level message.
	 *
	 * Runtime errors that do not require immediate action but should typically be logged
	 * and monitored.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function error( $message, $context = array() ) {
		self::log( $message, 'error', $context );
	}

	/**
	 * Adds a warning level message.
	 *
	 * Exceptional occurrences that are not errors.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function warning( $message, $context = array() ) {
		self::log( $message, 'warning', $context );
	}

	/**
	 * Adds a notice level message.
	 *
	 * Normal but significant events.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function notice( $message, $context = array() ) {
		self::log( $message, 'notice', $context );
	}

	/**
	 * Adds an info level message.
	 *
	 * Interesting events.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function info( $message, $context = array() ) {
		self::log( $message, 'info', $context );
	}

	/**
	 * Adds a debug level message.
	 *
	 * Detailed debug information.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $message Log message.
	 * @param array|string $context Optional. Additional information for log handlers.
	 */
	public static function debug( $message, $context = array() ) {
		self::log( $message, 'debug', $context );
	}
}

class_alias( Logger::class, 'Themesquad\WC_Currency_Converter\Logger' );
