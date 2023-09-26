<?php
/**
 * WC_CSP_Check_Result class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores error/notice messages returned by restrictions.
 *
 * @class    WC_CSP_Check_Result
 * @version  1.11.0
 *
 */
class WC_CSP_Check_Result {

	/** @var array Array of stored messages. */
	public $messages;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->messages = array();
	}

	/**
	 * Add a new message.
	 *
	 * @param   string  $message_code
	 * @param   string  $message_text
	 * @param   string  $message_type
	 * @param   array   $debug_info (Optional)
	 * @return  boolean
	 */
	public function add( $message_code, $message_text, $message_type = 'error', $debug_info = array() ) {

		if ( $message_code && $message_text ) {

			$message           = array();
			$message[ 'code' ] = $message_code;
			$message[ 'text' ] = $message_text;
			$message[ 'type' ] = $message_type;

			if ( WC_CSP_Debugger::is_running() ) {
				$message[ 'debug_info' ] = $debug_info;
			}

			// Prevent duplicate messages.
			foreach ( $this->messages as $check_message ) {
				if ( $check_message === $message ) {
					return false;
				}
			}

			$this->messages[] = $message;

			return true;
		}

		return false;
	}

	/**
	 * True if messages exist.
	 *
	 * @return boolean
	 */
	public function has_messages() {

		if ( ! empty( $this->messages ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get stored messages.
	 *
	 * @param  string $code
	 * @param  string $type
	 * @return array
	 */
	public function get_messages( $code = '', $type = '' ) {

		$messages = array();

		if ( ! empty( $this->messages ) ) {

			foreach ( $this->messages as $message ) {

				if ( $code && $type && $message[ 'code' ] === $code && $message[ 'type' ] === $type ) {
					$messages[] = $message;
				} elseif ( $code && $message[ 'code' ] === $code ) {
					$messages[] = $message;
				} elseif ( $type && $message[ 'type' ] === $type ) {
					$messages[] = $message;
				} elseif ( ! $code && ! $type ) {
					$messages[] = $message;
				}
			}
		}

		return $messages;
	}
}
