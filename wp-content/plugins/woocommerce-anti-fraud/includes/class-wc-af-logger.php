<?php



/**

 * Required functions.
 */

if ( ! function_exists( 'woothemes_queue_update' ) ) {

	require_once( 'woo-includes/woo-functions.php' );

}



class Af_Logger {



	private static function pretty_print( $log ) {

		if ( is_object( $log ) || is_array( $log ) ) {

			return print_r( $log, true );

		}

		return $log;

	}



	public static function trace( $log ) {

			error_log( self::pretty_print( $log ) );

	}



	public static function debug( $log ) {

		if ( 'yes' == get_option( 'wc_af_enable_debug_logging' ) ) {

			error_log( self::pretty_print( $log ) );

		}

	}



	public static function error( $log ) {

			error_log( self::pretty_print( $log ) );

	}

}



