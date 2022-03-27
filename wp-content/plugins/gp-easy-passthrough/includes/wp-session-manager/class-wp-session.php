<?php

namespace GP_Easy_Passthrough;

use Recursive_ArrayAccess;

/**
 * WordPress session managment.
 *
 * Standardizes WordPress session data using database-backed options for storage.
 * for storing user session information.
 *
 * @package    WordPress
 * @subpackage Session
 * @since      3.7.0
 */

/**
 * WordPress Session class for managing user session data.
 *
 * @package WordPress
 * @since   3.7.0
 */
final class WP_Session extends Recursive_ArrayAccess {
	/**
	 * ID of the current session.
	 *
	 * @var string
	 */
	public $session_id;

	/**
	 * Unix timestamp when session expires.
	 *
	 * @var int
	 */
	protected $expires;

	/**
	 * Unix timestamp indicating when the expiration time needs to be reset.
	 *
	 * @var int
	 */
	protected $exp_variant;

	/**
	 * Singleton instance.
	 *
	 * @var bool|WP_Session
	 */
	private static $instance = false;

	/**
	 * Retrieve the current session instance.
	 *
	 * @param bool $session_id Session ID from which to populate data.
	 *
	 * @return bool|WP_Session
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Default constructor.
	 * Will rebuild the session collection from the given session ID if it exists. Otherwise, will
	 * create a new session with that ID.
	 *
	 * @param $session_id
	 *
	 * @uses apply_filters Calls `wp_session_expiration` to determine how long until sessions expire.
	 */
	protected function __construct() {
		/**
		 * This filter disables GPEP session manager which makes GPEP usable
		 * only with <a href="https://gravitywiz.com/documentation/gravity-forms-easy-passthrough/#easy-passthrough-token">tokens</a>.
		 *
		 * @param boolean $disable_session_manager  Disables all GPEP session cookies
		 * @since 1.9.3
		 */
		if ( apply_filters( 'gpep_disable_session_manager', false ) ) {
			return;
		}
		if ( isset( $_COOKIE[ GPEP_SESSION_COOKIE ] ) ) {
			$cookie        = stripslashes( $_COOKIE[ GPEP_SESSION_COOKIE ] );
			$cookie_crumbs = explode( '||', $cookie );

			$this->session_id  = preg_replace( '/[^A-Za-z0-9_]/', '', $cookie_crumbs[0] );
			$this->expires     = absint( $cookie_crumbs[1] );
			$this->exp_variant = absint( $cookie_crumbs[2] );

			// Update the session expiration if we're past the variant time
			if ( time() > $this->exp_variant ) {
				$this->set_expiration();
				delete_option( "_wp_session_expires_{$this->session_id}" );
				add_option( "_wp_session_expires_{$this->session_id}", $this->expires, '', 'no' );
			}
		} else {
			$this->session_id = WP_Session_Utils::generate_id();
			$this->set_expiration();
		}

		$this->read_data();

		add_action( 'wp_footer', array( $this, 'set_cookie' ) );
		add_action( 'gform_preview_footer', array( $this, 'set_cookie' ) );

	}

	/**
	 * Set both the expiration time and the expiration variant.
	 *
	 * If the current time is below the variant, we don't update the session's expiration time. If it's
	 * greater than the variant, then we update the expiration time in the database.  This prevents
	 * writing to the database on every page load for active sessions and only updates the expiration
	 * time if we're nearing when the session actually expires.
	 *
	 * By default, the expiration time is set to 30 minutes.
	 * By default, the expiration variant is set to 24 minutes.
	 *
	 * As a result, the session expiration time - at a maximum - will only be written to the database once
	 * every 24 minutes.  After 30 minutes, the session will have been expired. No cookie will be sent by
	 * the browser, and the old session will be queued for deletion by the garbage collector.
	 *
	 * @uses apply_filters Calls `wp_session_expiration_variant` to get the max update window for session data.
	 * @uses apply_filters Calls `wp_session_expiration` to get the standard expiration time for sessions.
	 */
	protected function set_expiration() {

		/**
		 * Modify the cookie expiration variant time.
		 *
		 * @since 1.0.12
		 *
		 * @param int $exp_variant Default cookie expiration variant time.
		 */
		$this->exp_variant = time() + (int) apply_filters( 'gpep_expiration_variant', 24 * MINUTE_IN_SECONDS );

		/**
		 * Modify the cookie expiration time.
		 *
		 * @since 1.0.12
		 *
		 * @param int $expires Default cookie expiration time.
		 */
		$this->expires = time() + (int) apply_filters( 'gpep_expiration', 30 * MINUTE_IN_SECONDS );

	}

	/**
	 * Set the session cookie
	 * @uses apply_filters Calls `wp_session_cookie_secure` to set the $secure parameter of setcookie()
	 */
	public function set_cookie() {

		// Get secure state.
		$secure = apply_filters( 'wp_session_cookie_secure', false );
		$secure = $secure ? 'true' : 'false';

		// Prepare cookie value.
		$value = $this->session_id . '||' . $this->expires . '||' . $this->exp_variant;

		/**
		 * Modify the path used for the cookie.
		 *
		 * @since 1.0.9
		 *
		 * @param string $path Default cookie path set by WordPress.
		 */
		$path = apply_filters( 'gpep_cookie_path', COOKIEPATH );

		// Prepare expire date.
		$expires = (int) apply_filters( 'wp_session_expiration', 30 * 60 ) * 1000;
		$expires = 'new Date( new Date().getTime() + ' . $expires . ' )';

		// Prepare cookie attributes.
		$atts  = '{';
		$atts .= '"expires": ' . $expires . ',';
		$atts .= '"path": "' . $path . '",';
		$atts .= '"domain": "' . COOKIE_DOMAIN . '",';
		$atts .= '"SameSite": "Lax",';
		$atts .= '"secure": ' . $secure;
		$atts .= '}';

		// Prepare script output.
		$script  = '<script type="text/javascript">' . PHP_EOL;
		$script .= 'jQuery( function() {' . PHP_EOL;
		$script .= 'if ( window.Cookies ) {' . PHP_EOL;
		$script .= 'Cookies.set( "' . GPEP_SESSION_COOKIE . '", "' . $value . '", ' . $atts . ' );' . PHP_EOL;
		$script .= '}' . PHP_EOL;
		$script .= '} );' . PHP_EOL;
		$script .= '</script>' . PHP_EOL;

		echo $script;

	}

	/**
	 * Read data from a transient for the current session.
	 *
	 * Automatically resets the expiration time for the session transient to some time in the future.
	 *
	 * @return array
	 */
	protected function read_data() {
		$this->container = get_option( "_wp_session_{$this->session_id}", array() );

		return $this->container;
	}

	/**
	 * Write the data from the current session to the data storage system.
	 */
	public function write_data() {

		// No need to store an empty array in the DB
		if ( $this->container === array() ) {
			return;
		}

		$option_key = "_wp_session_{$this->session_id}";

		if ( false === get_option( $option_key ) ) {
			add_option( "_wp_session_{$this->session_id}", $this->container, '', 'no' );
			add_option( "_wp_session_expires_{$this->session_id}", $this->expires, '', 'no' );
		} else {
			delete_option( "_wp_session_{$this->session_id}" );
			add_option( "_wp_session_{$this->session_id}", $this->container, '', 'no' );
		}
	}

	/**
	 * Output the current container contents as a JSON-encoded string.
	 *
	 * @return string
	 */
	public function json_out() {
		return json_encode( $this->container );
	}

	/**
	 * Decodes a JSON string and, if the object is an array, overwrites the session container with its contents.
	 *
	 * @param string $data
	 *
	 * @return bool
	 */
	public function json_in( $data ) {
		$array = json_decode( $data, true );

		if ( is_array( $array ) ) {
			$this->container = $array;

			return true;
		}

		return false;
	}

	/**
	 * Regenerate the current session's ID.
	 *
	 * @param bool $delete_old Flag whether or not to delete the old session data from the server.
	 */
	public function regenerate_id( $delete_old = false ) {
		if ( $delete_old ) {
			delete_option( "_wp_session_{$this->session_id}" );
		}

		$this->session_id = WP_Session_Utils::generate_id();

		$this->set_cookie();
	}

	/**
	 * Check if a session has been initialized.
	 *
	 * @return bool
	 */
	public function session_started() {
		return ! ! self::$instance;
	}

	/**
	 * Return the read-only cache expiration value.
	 *
	 * @return int
	 */
	public function cache_expiration() {
		return $this->expires;
	}

	/**
	 * Flushes all session variables.
	 */
	public function reset() {
		$this->container = array();
	}
}
