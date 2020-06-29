<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Box Office Logger.
 *
 * Can be used to log data to post meta or log debug data via WC_Logger.
 */
class WC_Box_Office_Logger {

	/**
	 * Instance of WC_Logger.
	 *
	 * @since 1.1.0
	 *
	 * @var WC_Logger
	 */
	private $_logger;

	/**
	 * Whether logging is enabled in setting.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $_enable_logging;

	public function __construct() {
		add_action( 'wc_box_office_log_data', array( $this, 'log_to_post_meta' ), 10, 3 );
		add_action( 'wc_box_office_log_debug', array( $this, 'log_to_wc_logger' ) );
	}

	public function log( $message, $post_id, $data = null ) {
		do_action( 'wc_box_office_log_data', $message, $post_id, $data );
	}

	/**
	 * Log debug message.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message Message to log
	 *
	 * @return void
	 */
	public function log_debug( $message ) {
		do_action( 'wc_box_office_log_debug', $message );
	}

	public function log_to_post_meta( $message, $post_id, $data) {
		if ( $post_id ) {
			$entry = array(
				'timestamp' => time(),
				'message'   => $message,
				'data'      => $data,
			);

			$log = get_post_meta( $post_id, 'wc_box_office_log', true );
			if ( is_array( $log ) )
				$log[] = $entry;
			else
				$log = array( $entry );

			update_post_meta( $post_id, 'wc_box_office_log', $log );
		}
	}

	/**
	 * Log message to a file via WC_Logger.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message Message to log
	 *
	 * @return void
	 */
	public function log_to_wc_logger( $message ) {
		if ( ! $this->_logger ) {
			require_once( WC()->plugin_path() . '/includes/class-wc-logger.php' );

			$this->_logger          = new WC_Logger();
			$this->_enable_logging  = get_option( 'box_office_enable_logging', 'no' );
		}

		if ( 'yes' === $this->_enable_logging && ! empty( $message ) ) {
			$this->_logger->add( 'woocommerce_box_office', $message );
		}
	}
}
