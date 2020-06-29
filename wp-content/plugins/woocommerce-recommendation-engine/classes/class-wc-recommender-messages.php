<?php

/**
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Multisite to newer
 * versions in the future. If you wish to customize WooCommerce Multisite for your
 * needs please refer to http://docs.woothemes.com/document/woocommerce-multisite/ for more information.
 *
 * @package     WC-Multisite/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013, SkyVerge, Inc. and Lucas Stark
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/**
 * <h2>WordPress Admin Message Handler Class</h2>
 *
 * This class provides a reusable wordpress admin messaging facility for setting
 * and displaying messages and error messages across admin page requets without
 * resorting to passing the messages as query vars.
 *
 * <h3>Usage</h3>
 *
 * To use simple instantiate the class then set one or more messages:
 *
 * <pre>
 * $admin_message_handler = new WP_Admin_Message_Handler();
 * $admin_message_handler->add_message( 'Hello World!' );
 * </pre>
 *
 * Then show the messages wherever you need, either with the built-in method
 * or by writing your own:
 *
 * <pre>
 * $admin_message_handler->show_messages();
 * </pre>
 *
 * @since 1.0
 */

class WC_Recommender_Messages {

	/** transient message prefix */
	const MESSAGE_TRANSIENT_PREFIX = '_wc_recommender_admin_message_';

	/** the message id GET name */
	const MESSAGE_ID_GET_NAME = 'wcre';

	/** @var string unique message identifier */
	private $message_id;

	/** @var array array of messages */
	private $messages = array();

	/** @var array array of error messages */
	private $errors = array();

	/**
	 * Construct and initialize the admin message handler class
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// load any available messages
		$this->load_messages();

		add_filter( 'wp_redirect', array($this, 'redirect'), 1, 2 );
	}

	/**
	 * Persist messages
	 *
	 * @since 1.0
	 * @return boolean true if any messages were set, false otherwise
	 */
	public function set_messages() {

		// any messages to persist?
		if ( $this->message_count() > 0 || $this->error_count() > 0 ) {

			set_transient(
				self::MESSAGE_TRANSIENT_PREFIX . $this->get_message_id(), array('errors' => $this->errors, 'messages' => $this->messages), 60 * 60
			);

			return true;
		}

		return false;
	}

	/**
	 * Loads messages
	 *
	 * @since 1.0
	 */
	public function load_messages() {

		if ( isset( $_GET[self::MESSAGE_ID_GET_NAME] ) ) {

			$memo = get_transient( self::MESSAGE_TRANSIENT_PREFIX . $_GET[self::MESSAGE_ID_GET_NAME] );

			if ( isset( $memo['errors'] ) )
				$this->errors = $memo['errors'];
			if ( isset( $memo['messages'] ) )
				$this->messages = $memo['messages'];

			$this->clear_messages( $_GET[self::MESSAGE_ID_GET_NAME] );
		}
	}

	/**
	 * Clear messages and errors
	 *
	 * @since 1.0
	 * @param string $id the messages identifier
	 */
	public function clear_messages( $id ) {
		delete_transient( self::MESSAGE_TRANSIENT_PREFIX . $id );
	}

	/**
	 * Add an error message.
	 *
	 * @since 1.0
	 * @param string $error error message
	 */
	public function add_error( $error ) {
		if ( is_wp_error( $error ) ) {
			foreach ( $error->get_error_codes() as $code ) {
				$this->errors[] = implode(', ', $error->get_error_messages( $code ));
			}
		} else {
			$this->errors[] = $error;
		}
	}

	/**
	 * Add a message.
	 *
	 * @since 1.0
	 * @param string $message the message to add
	 */
	public function add_message( $message ) {
		$this->messages[] = $message;
	}

	/**
	 * Get error count.
	 *
	 * @since 1.0
	 * @return int error message count
	 */
	public function error_count() {
		return sizeof( $this->errors );
	}

	/**
	 * Get message count.
	 *
	 * @since 1.0
	 * @return int message count
	 */
	public function message_count() {
		return sizeof( $this->messages );
	}

	/**
	 * Get error messages
	 *
	 * @since 1.0
	 * @return array of error message strings
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Get messages
	 *
	 * @since 1.0
	 * @return array of message strings
	 */
	public function get_messages() {
		return $this->messages;
	}

	/**
	 * Render the errors and messages.
	 *
	 * @since 1.0
	 */
	public function show_messages() {
		if ( $this->error_count() > 0 ) {
			echo "<div id=\"notice\" class=\"error\"><ul><li><strong>" . implode( '</strong></li><li><strong>', $this->get_errors() ) . "</strong></li></ul></div>";
		}

		if ( $this->message_count() > 0 ) {
			echo "<div id=\"message\" class=\"updated\"><ul><li><strong>" . implode( '</strong></li><li><strong>', $this->get_messages() ) . "</strong></li></ul></div>";
		}
	}

	/**
	 * Redirection hook which persists messages into session data.
	 *
	 * @since 1.0
	 * @param string $location the URL to redirect to
	 * @param int $status the http status
	 * @return string the URL to redirect to
	 */
	public function redirect( $location, $status ) {

		// add the admin message id param to the
		if ( $this->set_messages() ) {
			$location = add_query_arg( self::MESSAGE_ID_GET_NAME, $this->get_message_id(), $location );
		}

		return $location;
	}

	/**
	 * Generate a unique id to identify the messages
	 *
	 * @since 1.0
	 * @return string unique identifier
	 */
	private function get_message_id() {
		if ( !isset( $this->message_id ) )
			$this->message_id = wp_create_nonce( __FILE__ );

		return $this->message_id;
	}

}
