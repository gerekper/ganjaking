<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Logger {

	/**
	 * @var WC_XR_Settings
	 */
	private $settings;

	/**
	 * WC_XR_Logger constructor.
	 *
	 * @param WC_XR_Settings $settings
	 */
	public function __construct( WC_XR_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Check if logging is enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {

		// Check if debug is on
		if ( 'on' === $this->settings->get_option( 'debug' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Write the message to log
	 *
	 * @param String $message
	 */
	public function write( $message ) {

		// Check if enabled
		if ( $this->is_enabled() ) {

			// Logger object
			$wc_logger = wc_get_logger();

			// Add to logger
			$wc_logger->add( 'xero', $message );
		}

	}

}