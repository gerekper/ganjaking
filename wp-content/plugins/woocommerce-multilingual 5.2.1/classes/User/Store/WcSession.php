<?php

namespace WCML\User\Store;


class WcSession implements Strategy {

	/**
	 * @var \WC_Session
	 */
	private $session;

	/**
	 * WcSession constructor.
	 *
	 * @param \WC_Session $session
	 */
	public function __construct( \WC_Session $session ) {

		$this->session = $session;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {

		return $this->session->get( $key );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value ) {

		$this->session->set( $key, $value );
	}
}
