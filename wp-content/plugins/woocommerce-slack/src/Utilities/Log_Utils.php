<?php
/**
 * Log utilities.
 *
 * @since 1.3.0
 */

namespace Themesquad\WC_Slack\Utilities;

/**
 * Class Log_utils.
 */
class Log_Utils {

	/**
	 * The logger instance.
	 *
	 * @var \WC_Logger_Interface $log
	 */
	protected static $logger;

	/**
	 * Is the debug mode enabled?
	 *
	 * @var bool $debug_mode
	 */
	protected static $debug_mode;

	/**
	 * Logs a message.
	 *
	 * @since 1.3.0
	 *
	 * @param string $message The message to log.
	 * @param string $level   The level.
	 * @param string $handle  Optional. The log handler. Default: wcslack.
	 */
	public static function log( $message, $level = \WC_Log_Levels::NOTICE, $handle = 'wcslack' ) {
		if ( ! self::$logger ) {
			self::$logger = wc_get_logger();
		}

		if ( method_exists( self::$logger, $level ) ) {
			call_user_func( array( self::$logger, $level ), $message, array( 'source' => $handle ) );
		} else {
			self::$logger->add( $handle, $message );
		}
	}

	/**
	 * Logs a message only in debug mode.
	 *
	 * @since 1.3.0
	 *
	 * @param string $message The message to log.
	 * @param string $level   The level.
	 * @param string $handle  Optional. The log handler. Default: wcslack.
	 */
	public static function debug( $message, $level = \WC_Log_Levels::NOTICE, $handle = 'wcslack' ) {
		if ( self::is_debug_mode() ) {
			self::log( $message, $level, $handle );
		}
	}

	/**
	 * Gets if the plugin is in debug mode or not.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	protected static function is_debug_mode() {
		if ( ! isset( self::$debug_mode ) ) {
			$settings         = get_option( 'woocommerce_wcslack_settings', array() );
			self::$debug_mode = isset( $settings['debug'] ) && wc_string_to_bool( $settings['debug'] );
		}

		return self::$debug_mode;
	}
}
