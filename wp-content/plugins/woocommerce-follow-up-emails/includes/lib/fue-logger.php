<?php

/**
 * Class FUE_Logger
 */
class FUE_Logger {
	const LOG_NONE  = 0;
	const LOG_ERROR = 1;
	const LOG_INFO  = 2;
	const LOG_DEBUG = 4;
	const LOG_ALL   = 8;

	public $log_level;
	public $log_file;

	public function __construct( $log_level, $log_file ) {
		$this->log_level = $log_level;
		$this->log_file = $log_file;
	}

	public function info( $message ) {
		if ( $this->log_level >= self::LOG_INFO ) {
			$this->write_log( 'info', $message );
		}
	}
	public function debug( $message ) {
		if ( $this->log_level >= self::LOG_DEBUG ) {
			$this->write_log( 'debug', $message );
		}
	}
	public function error( $message ) {
		if ( $this->log_level >= self::LOG_ERROR ) {
			$this->write_log( 'error', $message );
		}
	}

	private function write_log( $severity, $message ) {
		if ( is_writable( $this->log_file ) ) {
			$date       = current_time( 'mysql' );
			$message    = sprintf( "[%s][%s] - %s\n", $date, strtoupper( $severity ), $message );

			$fp = @fopen( $this->log_file, 'a+' );
			fputs( $fp, $message );
			fclose( $fp );
		} else {
			trigger_error( sprintf( 'FUE_Logger: The logfile %s is not writable.', $this->log_file ), E_USER_NOTICE );
		}
	}

}