<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YITH_YWRAQ_Session class.
 *
 * Implements the WC_Session abstract class
 * Partly based on WC_Session_Handler by Woothemes.
 *
 * @class   YITH_YWRAQ_Session
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
class YITH_YWRAQ_Session extends WC_Session {

	/**
	 * Cookie name
	 *
	 * @var $_cookie
	 */
	private $_cookie;

	/**
	 * Session due to expire timestamp
	 *
	 * @var $_session_expiring
	 */
	private $_session_expiring;

	/**
	 * Session expiration timestamp
	 *
	 * @var $_session_expiration
	 */
	private $_session_expiration;

	/**
	 * Bool based on whether a cookie exists
	 *
	 * @var $_has_cookie
	 */
	private $_has_cookie = false;

	/**
	 * Constructor for the session class.
	 */
	public function __construct() {
		$this->_cookie = ywraq_get_cookie_name() . '_' . COOKIEHASH;

		$cookie = $this->get_session_cookie();
		if ( $cookie ) {
			$this->_customer_id        = $cookie[0];
			$this->_session_expiration = $cookie[1];
			$this->_session_expiring   = $cookie[2];
			$this->_has_cookie         = true;

			// Update session if its close to expiring.
			if ( time() > $this->_session_expiring ) {

				$this->set_session_expiration();

				$session_expiry_option = '_' . ywraq_get_cookie_name() . '_expires_' . $this->_customer_id;
				// Check if option exists first to avoid auloading cleaned up sessions.
				if ( false === get_option( $session_expiry_option ) ) {
					add_option( $session_expiry_option, $this->_session_expiration, '', 'no' );
				} else {
					update_option( $session_expiry_option, $this->_session_expiration );
				}
			}
		} else {
			$this->set_session_expiration();
			$this->_customer_id = $this->generate_customer_id();
		}

		$this->_data = $this->get_session_data();

		// Actions.
		add_action( 'woocommerce_cleanup_sessions', array( $this, 'cleanup_sessions' ), 10 );
		add_action( 'shutdown', array( $this, 'save_data' ), 20 );
		add_action( 'clear_auth_cookie', array( $this, 'destroy_session' ) );
		if ( ! is_user_logged_in() ) {
			add_action( 'woocommerce_thankyou', array( $this, 'destroy_session' ) );
		}
	}

	/**
	 * Sets the session cookie on-demand (usually after adding an item to the request a quote list).
	 *
	 * Warning: Cookies will only be set if this is called before the headers are sent.
	 *
	 * @param $set
	 */
	public function set_customer_session_cookie( $set ) {
		if ( $set ) {
			// Set/renew our cookie.

			$to_hash           = $this->_customer_id . $this->_session_expiration;
			$cookie_hash       = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
			$cookie_value      = $this->_customer_id . '||' . $this->_session_expiration . '||' . $this->_session_expiring . '||' . $cookie_hash;
			$this->_has_cookie = true;

			// Set the cookie.
			wc_setcookie( $this->_cookie, $cookie_value, $this->_session_expiration, apply_filters( 'yith_ywraq_session_use_secure_cookie', false ) );
		}
	}

	/**
	 * Return true if the current user has an active session, i.e. a cookie to retrieve values
	 *
	 * @return boolean
	 */
	public function has_session() {
		return isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie || is_user_logged_in();
	}

	/**
	 * Set_session_expiration function.
	 */
	public function set_session_expiration() {
		$this->_session_expiring   = time() + intval( apply_filters( 'yith_ywraq_session_expiring', 60 * 60 * 47 ) ); // 47 Hours
		$this->_session_expiration = time() + intval( apply_filters( 'yith_ywraq_session_expiration', 60 * 60 * 48 ) ); // 48 Hours
	}

	/**
	 * Generate a unique customer ID for guests, or return user ID if logged in.
	 *
	 * Uses Portable PHP password hashing framework to generate a unique cryptographically strong ID.
	 *
	 * @return int|string
	 */
	public function generate_customer_id() {
		if ( is_user_logged_in() ) {
			return get_current_user_id();
		} else {
			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$hasher = new PasswordHash( 8, false );

			return md5( $hasher->get_random_bytes( 32 ) );
		}
	}

	/**
	 * Get Session Coockie
	 *
	 * @return bool|array
	 */
	public function get_session_cookie() {
		if ( empty( $_COOKIE[ $this->_cookie ] ) ) {
			return false;
		}


		list( $customer_id, $session_expiration, $session_expiring, $cookie_hash ) = explode( '||', $_COOKIE[ $this->_cookie ] );

		// Validate hash.
		$to_hash = $customer_id . $session_expiration;
		$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

		if ( $hash !== $cookie_hash ) {
			return false;
		}


		return array( $customer_id, $session_expiration, $session_expiring, $cookie_hash );
	}

	/**
	 * Get Session Data
	 *
	 * @return array
	 */
	public function get_session_data() {
		$cookie_name = ywraq_get_cookie_name();

		return $this->has_session() ? (array) get_option( '_' . $cookie_name . '_' . $this->_customer_id, array() ) : array();
	}

	/**
	 * Save Data
	 */
	public function save_data() {
		// Dirty if something changed - prevents saving nothing new.
		if ( $this->_dirty && $this->has_session() ) {
			$cookie_name           = ywraq_get_cookie_name();
			$session_option        = '_' . $cookie_name . '_' . $this->_customer_id;
			$session_expiry_option = '_' . $cookie_name . '_expires_' . $this->_customer_id;
			if ( false === get_option( $session_option ) ) {
				add_option( $session_option, $this->_data, '', 'no' );
				add_option( $session_expiry_option, $this->_session_expiration, '', 'no' );
			} else {
				update_option( $session_option, $this->_data );
			}
		}
	}

	/**
	 * Destroy all session data
	 */
	public function destroy_session() {
		// Clear cookie.
		wc_setcookie( $this->_cookie, '', time() - YEAR_IN_SECONDS, apply_filters( 'yith_ywraq_session_use_secure_cookie', false ) );

		$cookie_name = ywraq_get_cookie_name();
		// Delete session.
		$session_option        = '_' . $cookie_name . '_' . $this->_customer_id;
		$session_expiry_option = '_' . $cookie_name . '_expires_' . $this->_customer_id;

		delete_option( $session_option );
		delete_option( $session_expiry_option );

		// Clear data.
		$this->_data        = array();
		$this->_dirty       = false;
		$this->_customer_id = $this->generate_customer_id();
	}

	/**
	 * Cleanup Sessions
	 */
	public function cleanup_sessions() {
		global $wpdb;

		if ( ! defined( 'WP_SETUP_CONFIG' ) && ! defined( 'WP_INSTALLING' ) ) {
			$now                = time();
			$expired_sessions   = array();
			$wc_session_expires = $wpdb->get_col( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '\_yith_ywraq\_session\_expires\_%' AND option_value < '$now'" );

			foreach ( $wc_session_expires as $option_name ) {
				$session_id         = substr( $option_name, 20 );
				$expired_sessions[] = $option_name;  // Expires key.
				$expired_sessions[] = "_yith_ywraq_session_$session_id"; // Session key.
			}

			if ( ! empty( $expired_sessions ) ) {
				$expired_sessions_chunked = array_chunk( $expired_sessions, 100 );
				foreach ( $expired_sessions_chunked as $chunk ) {
					if ( wp_using_ext_object_cache() ) {
						// delete from object cache first, to avoid cached but deleted options.
						foreach ( $chunk as $option ) {
							wp_cache_delete( $option, 'options' );
						}
					}

					// delete from options table.
					$option_names = implode( "','", $chunk );
					$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name IN ('$option_names')" );
				}
			}
		}
	}
}
