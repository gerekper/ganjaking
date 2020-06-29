<?php
/**
 * Main class
 *
 * @author YITH
 * @package YITH WooCommerce Social Login
 * @version 1.3.0
 */

if ( ! defined( 'YITH_YWSL_INIT' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WC_Social_Login_Session' ) ){
	/**
	 * YITH WooCommerce Social Login main class
	 *
	 * @since 1.3.0
	 */
	class YITH_WC_Social_Login_Session {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Social_Login_Session
		 * @since 1.3.0
		 */
		protected static $instance;

		/**
		 * Holds session data
		 *
		 * @var array
		 * @access private
		 * @since 1.3.0
		 */
		private $session;

		/**
		 * Whether to use PHP $_SESSION or WP_Session
		 *
		 * @var bool
		 * @access private
		 * @since 1.3.0
		 */
		private $use_php_sessions = false;

		/**
		 * Session index prefix
		 *
		 * @var string
		 * @access private
		 * @since 1.3.0
		 */
		private $prefix = '';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Social_Login_Session
		 * @since 1.3.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.3.0
		 */
		public function __construct() {
			$this->use_php_sessions = $this->use_php_sessions();

			if (  ! $this->use_php_sessions ) {
				if ( is_multisite() ) {
					$this->prefix = '_' . get_current_blog_id();
				}
				// Use PHP SESSION (must be enabled via the EDD_USE_PHP_SESSIONS constant)
				add_action( 'init', array( $this, 'maybe_start_session' ), - 2 );
			} else {
				if( ! $this->should_start_session() ) {
					return;
				}
				// Use WP_Session (default)
				if ( ! defined( 'WP_SESSION_COOKIE' ) ) {
					define( 'WP_SESSION_COOKIE', 'ywsl_wp_session' );
				}

				if ( ! class_exists( 'Recursive_ArrayAccess' ) ) {
					require_once YITH_YWSL_INC . 'wp_session/class-recursive-arrayaccess.php';
				}


				if ( ! class_exists( 'WP_Session' ) ) {
					require_once YITH_YWSL_INC . 'wp_session/class-wp-session.php';
					require_once YITH_YWSL_INC . 'wp_session/wp-session.php';
				}

				add_filter( 'wp_session_expiration_variant', array( $this, 'set_expiration_variant_time' ), 99999 );
				add_filter( 'wp_session_expiration', array( $this, 'set_expiration_time' ), 99999 );
			}

			if ( empty( $this->session ) && ! $this->use_php_sessions ) {
				add_action( 'plugins_loaded', array( $this, 'init' ), 12 );
			} else {
				add_action( 'init', array( $this, 'init' ), -1 );
			}
		}

		/**
		 * Setup the WP_Session instance
		 *
		 * @access public
		 * @since 1.3.0
		 * @return void
		 */
		public function init() {
			if( $this->use_php_sessions ) {
				$this->session = isset( $_SESSION['ywsl' . $this->prefix ] ) && is_array( $_SESSION['ywsl' . $this->prefix ] ) ? $_SESSION['ywsl' . $this->prefix ] : array();
			} else {
				$this->session = WP_Session::get_instance();
			}
			$use_cookie = $this->use_login_cookie();
			$login       = $this->get( 'ywsl_login' );
			if ( $use_cookie ) {
				if( ! empty( $login )  ) {
					$this->set_login_cookie();
				} else {
					$this->set_login_cookie( false );
				}
			}
			return $this->session;
		}


		/**
		 * Set a cookie to identify whether the cart is empty or not
		 *
		 * This is for hosts and caching plugins to identify if caching should be disabled
		 *
		 * @since 1.3.0
		 * @param bool $set Whether to set or destroy
		 * @return void
		 */
		public function set_login_cookie( $set = true ) {
			if( ! headers_sent() ) {
				if( $set ) {
					@setcookie( 'ywsl_login_process', '1', time() + 30 * 60, COOKIEPATH, COOKIE_DOMAIN, false );
				} else {
					if ( isset($_COOKIE['ywsl_login_process']) ) {
						@setcookie( 'ywsl_login_process', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
					}
				}
			}
		}

		/**
		 * Force the cookie expiration variant time to 23 hours
		 *
		 * @since 1.3.0
		 * @param int $exp Default expiration (1 hour)
		 * @return int
		 */
		public function set_expiration_variant_time( $exp ) {
			return ( 30 * 60 * 23 );
		}

		/**
		 * Force the cookie expiration time to 24 hours
		 *
		 * @since 1.3.0
		 * @param int $exp Default expiration (1 hour)
		 * @return int Cookie expiration time
		 */
		public function set_expiration_time( $exp ) {
			return ( 30 * 60 * 24 );
		}


		/**
		 * Set a session variable
		 *
		 * @since 1.3.0
		 *
		 * @param string $key Session key
		 * @param int|string|array $value Session variable
		 * @return mixed Session variable
		 */
		public function set( $key, $value ) {
			$key = sanitize_key( $key );
			if ( is_array( $value ) ) {
				$this->session[ $key ] = wp_json_encode( $value );
			} else {
				$this->session[ $key ] = esc_attr( $value );
			}
			if( $this->use_php_sessions ) {
				$_SESSION['ywsl' . $this->prefix ] = $this->session;
			}
			return $this->session[ $key ];
		}

		/**
		 * Retrieve a session variable
		 *
		 * @access public
		 * @since 1.3.0
		 * @param string $key Session key
		 * @return mixed Session variable
		 */
		public function get( $key ) {
			$key    = sanitize_key( $key );
			$return = false;
			if ( isset( $this->session[ $key ] ) && ! empty( $this->session[ $key ] ) ) {
				preg_match( '/[oO]\s*:\s*\d+\s*:\s*"\s*(?!(?i)(stdClass))/', $this->session[ $key ], $matches );
				if ( ! empty( $matches ) ) {
					$this->set( $key, null );
					return false;
				}

				if ( is_numeric( $this->session[ $key ] ) ) {
					$return = $this->session[ $key ];
				} else {
					$maybe_json = json_decode( $this->session[ $key ] );
					// Since json_last_error is PHP 5.3+, we have to rely on a `null` value for failing to parse JSON.
					if ( is_null( $maybe_json ) ) {
						$is_serialized = is_serialized( $this->session[ $key ] );
						if ( $is_serialized ) {
							$value = @unserialize( $this->session[ $key ] );
							$this->set( $key, (array) $value );
							$return = $value;
						} else {
							$return = $this->session[ $key ];
						}
					} else {
						$return = json_decode( $this->session[ $key ], true );
					}
				}
			}
			return $return;
		}

		/**
		 * Determines if a user has set the YWSL_USE_LOGIN_COOKIE
		 *
		 * @since  1.3.0
		 * @return bool If the store should use the edd_items_in_cart cookie to help avoid caching
		 */
		public function use_login_cookie() {
			$ret = true;
			if ( defined( 'YWSL_USE_LOGIN_COOKIE' ) && ! YWSL_USE_LOGIN_COOKIE ) {
				$ret = false;
			}
			return (bool) apply_filters( 'ywsl_use_login_cookie', $ret );
		}

		/**
		 * Starts a new session if one hasn't started yet.
		 *
		 * Checks to see if the server supports PHP sessions
		 * or if the YWSL_USE_PHP_SESSIONS constant is defined
		 *
		 * @access public
		 * @since 1.3.0
		 * @author Emanuela Castorina
		 * @return boolean $return True if we are using PHP sessions, false otherwise
		 */
		public function use_php_sessions() {
			$return = false;


			// If the database variable is already set, no need to run autodetection
			$use_php_sessions = (bool) get_option( 'ywsl_use_php_sessions' );

			if ( ! $use_php_sessions ) {
				// Attempt to detect if the server supports PHP sessions
				if( function_exists( 'session_start' ) ) {
					$this->set( 'ywsl_use_php_sessions', 1 );
					if( $this->get( 'ywsl_use_php_sessions' ) ) {
						$return = true;
						// Set the database option
						update_option( 'ywsl_use_php_sessions', true );
					}
				}
			} else {
				$return = $use_php_sessions;
			}
			// Enable or disable PHP Sessions based on the EDD_USE_PHP_SESSIONS constant
			if ( defined( 'YWSL_USE_PHP_SESSIONS' ) ) {
				$return = YWSL_USE_PHP_SESSIONS;
			}

			return (bool) apply_filters( 'ywsl_use_php_sessions', $return );
		}

		/**
		 * Starts a new session if one hasn't started yet.
		 */
		public function maybe_start_session() {
			if( !$this->should_start_session() ) {
				return;
			}
			if( ! session_id() && ! headers_sent() ) {
				session_start();
			}
		}

		/**
		 * Determines if we should start sessions
		 *
		 * @since  1.3.0
		 * @return bool
		 */
		public function should_start_session() {
			$start_session = true;
			if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
				$blacklist = $this->get_blacklist();
				$uri       = ltrim( $_SERVER['REQUEST_URI'], '/' );
				$uri       = untrailingslashit( $uri );
				if ( in_array( $uri, $blacklist ) ) {
					$start_session = false;
				}
				if ( false !== strpos( $uri, 'feed=' ) ) {
					$start_session = false;
				}
			}

			return apply_filters( 'ywsl_start_session', $start_session );
		}

		/**
		 * Retrieve the URI blacklist
		 *
		 * These are the URIs where we never start sessions
		 *
		 * @since  1.3.0
		 * @return array
		 */
		public function get_blacklist() {
			$blacklist = apply_filters( 'ywsl_session_start_uri_blacklist', array(
				'feed',
				'feed/rss',
				'feed/rss2',
				'feed/rdf',
				'feed/atom',
				'comments/feed'
			) );
			// Look to see if WordPress is in a sub folder or this is a network site that uses sub folders
			$folder = str_replace( network_home_url(), '', get_site_url() );
			if( ! empty( $folder ) ) {
				foreach( $blacklist as $path ) {
					$blacklist[] = $folder . '/' . $path;
				}
			}
			return $blacklist;
		}

		/**
		 * Retrieve session ID
		 *
		 * @since 1.3.0
		 * @return string Session ID
		 */
		public function get_id() {
			if( is_object( $this->session ) ){
				return $this->session->session_id;
			}

			return false;
		}

	}

	/**
	 * Unique access to instance of YITH_WC_Social_Login_Session class
	 *
	 * @return \YITH_WC_Social_Login_Session
	 */
	function YITH_WC_Social_Login_Session() {
		return YITH_WC_Social_Login_Session::get_instance();
	}

}

