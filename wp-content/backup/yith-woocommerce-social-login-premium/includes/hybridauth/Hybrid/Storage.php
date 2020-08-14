<?php

/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */
require_once realpath(dirname(__FILE__)) . "/StorageInterface.php";

/**
 * HybridAuth storage manager
 */
class Hybrid_Storage implements Hybrid_Storage_Interface {

	/**
	 *
	 */
	private $is_ywsl_session;

	/**
	 * Constructor
	 */
	function __construct() {
		if ( ! session_id() ) {
			if ( ! session_start() ) {
				throw new Exception( "Hybridauth requires the use of 'session_start()' at the start of your script, which appears to be disabled.", 1 );
			}
		}

		$this->is_ywsl_session = function_exists('ywsl_check_wpengine') && ywsl_check_wpengine() && class_exists('YITH_WC_Social_Login_Session');
		$session_id = $this->is_ywsl_session ? YITH_WC_Social_Login_Session()->get_id() : session_id();

		$this->config("php_session_id", $session_id );
		$this->config("version", Hybrid_Auth::$version);
	}

	/**
	 * Saves a value in the config storage, or returns config if value is null
	 *
	 * @param string $key   Config name
	 * @param string $value Config value
	 * @return array|null
	 */
	public function config($key, $value = null) {
		$key = strtolower($key);

		if( $this->is_ywsl_session ){
			$session_config = YITH_WC_Social_Login_Session()->get("HA::CONFIG");

			if ($value) {
				$session_config[ $key ] = $value;
				YITH_WC_Social_Login_Session()->set("HA::CONFIG", $session_config);
			} elseif (isset($session_config[ $key ])) {
				return $session_config[ $key ];
			}
		}else{
			if ($value) {
				$_SESSION["HA::CONFIG"][$key] = serialize($value);
			} elseif (isset($_SESSION["HA::CONFIG"][$key])) {
				return unserialize($_SESSION["HA::CONFIG"][$key]);
			}
		}

		return null;
	}

	/**
	 * Returns value from session storage
	 *
	 * @param string $key Key
	 * @return string|null
	 */
	public function get($key) {
		$key = strtolower($key);
		if( $this->is_ywsl_session ){
			$session_config = YITH_WC_Social_Login_Session()->get("HA::CONFIG");
			if (isset($session_config[$key])) {
				return $session_config[$key];
			}
		}else{
			if (isset($_SESSION["HA::STORE"], $_SESSION["HA::STORE"][$key])) {
				return unserialize($_SESSION["HA::STORE"][$key]);
			}
		}


		return null;
	}

	/**
	 * Saves a key value pair to the session storage
	 *
	 * @param string $key   Key
	 * @param string $value Value
	 * @return void
	 */
	public function set($key, $value) {
		$key = strtolower($key);
		if( $this->is_ywsl_session ){
			$session_config = YITH_WC_Social_Login_Session()->get("HA::CONFIG");
			$session_config[ $key ] = $value;
			YITH_WC_Social_Login_Session()->set("HA::CONFIG", $session_config);
		}else{
			$_SESSION["HA::STORE"][$key] = serialize($value);
		}

	}

	/**
	 * Clear session storage
	 * @return void
	 */
	function clear() {
		if( $this->is_ywsl_session ){
			YITH_WC_Social_Login_Session()->set("HA::CONFIG", array());
		}else{
			$_SESSION["HA::STORE"] = array();
		}
	}

	/**
	 * Delete a specific key from session storage
	 *
	 * @param string $key Key
	 * @return void
	 */
	function delete( $key ) {
		$key = strtolower( $key );

		if ( $this->is_ywsl_session ) {
			$session_config = YITH_WC_Social_Login_Session()->get( "HA::CONFIG" );
			if ( isset( $session_config[ $key ] ) ) {
				unset( $session_config[ $key ] );
				YITH_WC_Social_Login_Session()->set( "HA::CONFIG", $session_config );
			}
		} else {
			if ( isset( $_SESSION["HA::STORE"], $_SESSION["HA::STORE"][ $key ] ) ) {
				$f = $_SESSION['HA::STORE'];
				unset( $f[ $key ] );
				$_SESSION["HA::STORE"] = $f;
			}
		}
	}

	/**
	 * Delete all keys recursively from session storage
	 *
	 * @param string $key Key
	 * @retun void
	 */
	function deleteMatch( $key ) {
		$key = strtolower( $key );

		if ( $this->is_ywsl_session ) {
			$session_config = YITH_WC_Social_Login_Session()->get( "HA::CONFIG" );
			if ( count( $session_config ) ) {

				foreach ( $session_config as $k => $v ) {
					if ( strstr( $k, $key ) ) {
						unset( $session_config[ $k ] );
					}
				}
				YITH_WC_Social_Login_Session()->set( "HA::CONFIG", $session_config );
			}
		} else {
			if ( isset( $_SESSION["HA::STORE"] ) && count( $_SESSION["HA::STORE"] ) ) {
				$f = $_SESSION['HA::STORE'];
				foreach ( $f as $k => $v ) {
					if ( strstr( $k, $key ) ) {
						unset( $f[ $k ] );
					}
				}
				$_SESSION["HA::STORE"] = $f;
			}
		}
	}

	/**
	 * Returns session storage as a serialized string
	 * @return string|null
	 */
	function getSessionData() {
		if ( $this->is_ywsl_session ) {
			$session_config = YITH_WC_Social_Login_Session()->get( "HA::CONFIG" );
			if ( $session_config ) {
				return $session_config;
			}
		} else {
			if ( isset( $_SESSION["HA::STORE"] ) ) {
				return serialize( $_SESSION["HA::STORE"] );
			}
		}

		return null;
	}

	/**
	 * Restores the session from serialized session data
	 *
	 * @param string $sessiondata Serialized session data
	 * @return void
	 */
	function restoreSessionData($sessiondata = null) {
		if ( $this->is_ywsl_session ) {
			YITH_WC_Social_Login_Session()->set( "HA::CONFIG", $sessiondata );
		}else{
			$_SESSION["HA::STORE"] = unserialize($sessiondata);
		}
	}

}
