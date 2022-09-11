<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * HybridAuth Session Storage class
 *
 * This class provides a custom session storage for HybridAuth using WC sessions.
 * Unfortunately, there's no other way to provide a custom storage engine but to
 * declare the Hybrid_Storage class before loading HybridAuth.
 *
 * @since 2.0.0
 */
class Hybrid_Storage {


	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		// start a session if needed
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		if ( ! $this->get_hybridauth_session() ) {

			$this->set_hybridauth_session( array(
				'store'  => array(),
				'config' => array(),
			) );
		}

		$this->config( 'version', Hybrid_Auth::$version );
	}


	/**
	 * Saves a value in the config storage, or returns config if value is null
	 *
	 * @since 2.0.0
	 * @param string $key Config name
	 * @param string $value Optional. Config value
	 * @return array|null
	 */
	public function config( $key, $value = null ) {

		$session = $this->get_hybridauth_session();
		$key     = strtolower( $key );

		if ( $value ) {

			$session['config'][ $key ] = serialize( $value );

			$this->set_hybridauth_session( $session );

		} elseif ( isset( $session['config'][ $key ] ) ) {

			return unserialize( $session['config'][ $key ] );

		}

		return null;
	}


	/**
	 * Returns value from session storage
	 *
	 * @since 2.0.0
	 * @param string $key Key
	 * @return mixed|null
	 */
	public function get( $key ) {

		$session = $this->get_hybridauth_session();
		$key     = strtolower( $key );

		if ( isset( $session['store'][ $key ] ) ) {
			return unserialize( $session['store'][ $key ] );
		}

		return null;
	}


	/**
	 * Saves a key-value pair to the session storage
	 *
	 * @since 2.0.0
	 * @param string $key Key
	 * @param string $value Value
	 */
	public function set( $key, $value ) {

		$session                  = $this->get_hybridauth_session();
		$key                      = strtolower( $key );
		$session['store'][ $key ] = serialize( $value );

		$this->set_hybridauth_session( $session );
	}


	/**
	 * Clear session storage
	 *
	 * @since 2.0.0
	 */
	function clear() {

		$session          = $this->get_hybridauth_session();
		$session['store'] = array();

		$this->set_hybridauth_session( $session );
	}


	/**
	 * Delete a specific key from session storage
	 *
	 * @since 2.0.0
	 * @param string $key Key
	 */
	function delete( $key ) {

		$session = $this->get_hybridauth_session();
		$key     = strtolower( $key );

		if ( isset( $session['store'][ $key ] ) ) {

			unset( $session['store'][ $key ] );

			$this->set_hybridauth_session( $session );
		}
	}


	/**
	 * Delete all keys recursively from session storage
	 *
	 * @since 2.0.0
	 * @param string $key Key
	 */
	function deleteMatch( $key ) {

		$session = $this->get_hybridauth_session();
		$key     = strtolower( $key );

		if ( $session['store'] && count( $session['store'] ) ) {

			foreach ( $session['store'] as $k => $v ) {

				if ( strstr( $k, $key ) ) {
					unset( $session['store'][ $k ] );
				}
			}

			$this->set_hybridauth_session( $session );
		}
	}


	/**
	 * Returns session storage as a serialized string
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	function getSessionData() {

		$session = $this->get_hybridauth_session();

		return isset( $session['store'] ) ? serialize( $session['store'] ) : null;
	}


	/**
	 * Restores the session from serialized session data
	 *
	 * @since 2.0.0
	 * @param string $sessiondata Serialized session data
	 */
	function restoreSessionData( $sessiondata = null ) {

		$session          = $this->get_hybridauth_session();
		$session['store'] = unserialize( $sessiondata );

		$this->set_hybridauth_session( $session );
	}


	/**
	 * Get HybridAuth session data from WC session
	 *
	 * @since 2.0.0
	 * @return array
	 */
	private function get_hybridauth_session() {

		return WC()->session->get( 'hybridauth' );
	}


	/**
	 * Get HybridAuth session data from WC session
	 *
	 * @since 2.0.0
	 * @param array $data Session data
	 */
	private function set_hybridauth_session( $data ) {

		WC()->session->set( 'hybridauth', $data );
	}


}
