<?php

namespace WCML\User\Store;


class Cookie implements Strategy {

	/**
	 * @var \WPML_Cookie
	 */
	public $cookieHandler;


	/**
	 * Cookie constructor.
	 *
	 * @param \WPML_Cookie $cookieHandler
	 */
	public function __construct( \WPML_Cookie $cookieHandler ) {
		$this->cookieHandler = $cookieHandler;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {

		return $this->cookieHandler->get_cookie( $key );
	}

	/**
	 * @param string   $key
	 * @param mixed    $value
	 */
	public function set( $key, $value ) {

		if ( ! $this->cookieHandler->headers_sent() ) {

			/**
			 * This filter hook allows to override the expiration cookie time.
			 *
			 * @since 4.11.0
			 *
			 * @param int    $expiration Expiration cookie time.
			 * @param string $key        The key operating the storage.
			 */
			$expiration = time() + (int) apply_filters( 'wcml_cookie_expiration', 48 * HOUR_IN_SECONDS, $key );

			$this->cookieHandler->set_cookie( $key, $value, $expiration, COOKIEPATH, COOKIE_DOMAIN );
		}
	}
}
