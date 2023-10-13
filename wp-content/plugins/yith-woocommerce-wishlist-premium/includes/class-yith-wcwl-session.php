<?php
/**
 * Wishlist Session Handler
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Session' ) ) {
	/**
	 * This class implements Session handler for wishlist
	 * Unique session id is assigned to any new customer, and registered in a cookie
	 *
	 * Expiration is set accordingly to plugin options
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Session {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Session
		 * @since 3.0.0
		 */
		protected static $instance;

		/**
		 * Session ID.
		 *
		 * @var int $session_id Session ID.
		 */
		protected $session_id;

		/**
		 * Cookie name used for the session.
		 *
		 * @var string cookie name
		 */
		protected $cookie_name;

		/**
		 * Cookie content.
		 *
		 * @var array cookie content.
		 */
		protected $cookie;

		/**
		 * Stores session expiry.
		 *
		 * @var string session due to expire timestamp
		 */
		protected $session_expiring;

		/**
		 * Stores session expiration.
		 *
		 * @var string session expiration timestamp
		 */
		protected $session_expiration;

		/**
		 * True when the cookie exists.
		 *
		 * @var bool Based on whether a cookie exists.
		 */
		protected $has_cookie = false;

		/**
		 * Construct session class
		 */
		public function __construct() {
			// prefetch session cookie.
			add_action( 'init', array( $this, 'get_session_cookie' ), 5 );

			// add action to finalize session.
			add_action( 'init', array( $this, 'finalize_session' ) );
		}

		/**
		 * Setup cookie and customer ID.
		 *
		 * @since 3.0.0
		 */
		public function init_session_cookie() {
			$cookie = $this->get_session_cookie();

			if ( is_array( $cookie ) && ! empty( $cookie['session_id'] ) && ! empty( $cookie['session_expiration'] ) ) {
				if ( is_user_logged_in() ) {
					// If the user logs in, forget session.
					/**
					 * Once customer logs in, we can permanently register wishlists for his account
					 */
					$this->finalize_session();
				} elseif ( time() > $this->session_expiring ) {
					// Update session if its close to expiring.
					$this->set_session_expiration();
					$this->update_session_timestamp( $this->session_id, $this->session_expiration );
				}
			} else {
				$this->set_session_expiration();
				$this->session_id = $this->session_id ? $this->session_id : $this->generate_session_id();
			}

			if ( ! $this->has_cookie ) {
				$this->set_session_cookie();
			}
		}

		/**
		 * Sets the session cookie on-demand
		 *
		 * @return void
		 */
		public function set_session_cookie() {
			/**
			 * APPLY_FILTERS: yith_wcwl_set_session_cookie
			 *
			 * Filter whether to set the session cookie.
			 *
			 * @param bool $set_session_cookie Whether to set session cookie or not
			 *
			 * @return bool
			 */
			if ( headers_sent() || ! apply_filters( 'yith_wcwl_set_session_cookie', true ) ) {
				return;
			}

			$to_hash = $this->session_id . '|' . $this->session_expiration;
			$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

			$cookie_value = array(
				'session_id'         => $this->session_id,
				'session_expiration' => $this->session_expiration,
				'session_expiring'   => $this->session_expiring,
				'cookie_hash'        => $hash,
			);
			yith_setcookie( $this->get_session_cookie_name(), $cookie_value, $this->session_expiration, $this->use_secure_cookie(), true );

			// cookie has been set.
			$this->cookie     = $cookie_value;
			$this->has_cookie = true;
		}

		/**
		 * Get the session cookie, if set. Otherwise return false.
		 *
		 * Session cookies without a customer ID are invalid.
		 *
		 * @return bool|array
		 */
		public function get_session_cookie() {
			if ( ! empty( $this->cookie ) ) {
				return $this->cookie;
			}

			$cookie_value = yith_getcookie( $this->get_session_cookie_name() ); // @codingStandardsIgnoreLine.

			if ( empty( $cookie_value ) || ! is_array( $cookie_value ) ) {
				return false;
			}

			if ( empty( $cookie_value['session_id'] ) ) {
				return false;
			}

			// Validate hash.
			$to_hash = $cookie_value['session_id'] . '|' . $cookie_value['session_expiration'];
			$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

			if ( empty( $cookie_value['cookie_hash'] ) || ! hash_equals( $hash, $cookie_value['cookie_hash'] ) ) {
				return false;
			}

			$this->cookie     = $cookie_value;
			$this->has_cookie = true;

			$this->session_id         = $cookie_value['session_id'];
			$this->session_expiration = $cookie_value['session_expiration'];
			$this->session_expiring   = $cookie_value['session_expiring'];

			return $cookie_value;
		}

		/**
		 * Returns true if system should use HTTPS only cookies
		 *
		 * @return bool
		 */
		public function use_secure_cookie() {
			/**
			 * APPLY_FILTERS: yith_wcwl_session_use_secure_cookie
			 *
			 * Filter whether to use secure cookies.
			 *
			 * @param bool $use_secure_cookie Wether to use secure cookies or not
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcwl_session_use_secure_cookie', wc_site_is_https() && is_ssl() );
		}

		/**
		 * Returns name for the session cookie
		 *
		 * @return string
		 * @since 3.0.3
		 */
		public function get_session_cookie_name() {
			if ( empty( $this->cookie_name ) ) {
				/**
				 * APPLY_FILTERS: yith_wcwl_session_cookie
				 *
				 * Filter the session cookie name.
				 *
				 * @param string $cookie_name Cookie name
				 *
				 * @return string
				 */
				$this->cookie_name = apply_filters( 'yith_wcwl_session_cookie', 'yith_wcwl_session_' . COOKIEHASH );
			}

			return $this->cookie_name;
		}

		/**
		 * Returns current session expiration; if session doesn't exist, creates it; if user is logged in, return false
		 *
		 * @return string Current customer id
		 */
		public function get_session_expiration() {
			$session_id = $this->get_session_id();

			if ( $session_id ) {
				return $this->session_expiration;
			}

			return false;
		}

		/**
		 * Set session expiration.
		 */
		public function set_session_expiration() {
			$this->session_expiring   = time() + yith_wcwl_get_cookie_expiration() - HOUR_IN_SECONDS;
			$this->session_expiration = time() + yith_wcwl_get_cookie_expiration();
		}

		/**
		 * Return true if the current user has an active session, i.e. a cookie to retrieve values.
		 *
		 * @return bool
		 */
		public function has_session() {
			return $this->has_cookie; // @codingStandardsIgnoreLine.
		}

		/**
		 * Returns current session id; if session doesn't exist, creates it; if user is logged in, return false
		 *
		 * @return string Current customer id
		 */
		public function get_session_id() {
			if ( $this->has_session() ) {
				return $this->session_id;
			} elseif ( ! is_user_logged_in() ) {
				$this->init_session_cookie();

				return $this->session_id;
			}

			return false;
		}

		/**
		 * Returns current session id, if any; false otherwise (won't create a session)
		 *
		 * @return string|bool Current customer id, or false, if none
		 */
		public function maybe_get_session_id() {
			if ( $this->has_session() ) {
				return $this->session_id;
			}

			return false;
		}

		/**
		 * Generate a unique customer ID for guests, or return false if logged in.
		 *
		 * Uses Portable PHP password hashing framework to generate a unique cryptographically strong ID.
		 *
		 * @return string|bool
		 */
		public function generate_session_id() {
			$session_id = '';

			if ( is_user_logged_in() ) {
				return false;
			}

			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$hasher     = new PasswordHash( 8, false );
			$session_id = md5( $hasher->get_random_bytes( 32 ) );

			return $session_id;
		}

		/**
		 * Converts session to stable database items
		 *
		 * @return void
		 */
		public function finalize_session() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$cookie = $this->get_session_cookie();

			if ( ! $cookie ) {
				return;
			}

			if ( empty( $cookie['session_id'] ) ) {
				return;
			}

			$user_id    = get_current_user_id();
			$session_id = $cookie['session_id'];

			try {
				WC_Data_Store::load( 'wishlist' )->assign_to_user( $session_id, $user_id );
			} catch ( Exception $e ) {
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return;
			}

			$this->forget_session();
		}

		/**
		 * Update the session expiry timestamp.
		 *
		 * @param string $session_id Session ID.
		 * @param int    $timestamp Timestamp to expire the cookie.
		 */
		public function update_session_timestamp( $session_id, $timestamp ) {
			try {
				WC_Data_Store::load( 'wishlist' )->update_raw(
					array( 'expiration' => 'FROM_UNIXTIME(%d)' ),
					array( $timestamp ),
					array( 'session_id' => '%s' ),
					array( $session_id )
				);
			} catch ( Exception $e ) {
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return;
			}
		}

		/**
		 * Forget all session data without destroying it.
		 */
		public function forget_session() {
			yith_destroycookie( $this->get_session_cookie_name() );

			$this->session_id = $this->generate_session_id();
			$this->cookie     = null;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Session
		 * @since 3.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Session class
 *
 * @return \YITH_WCWL_Session
 * @since 3.0.0
 */
function YITH_WCWL_Session() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Session::get_instance();
}
