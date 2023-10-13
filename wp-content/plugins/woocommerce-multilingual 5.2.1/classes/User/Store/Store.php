<?php

namespace WCML\User\Store;


class Store implements Strategy {

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {

		$key = $this->adjustKey( $key );

		return $this->getStrategy( $key )->get( $key );
	}

	/**
	 * @param string   $key
	 * @param mixed    $value
	 */
	public function set( $key, $value ) {

		$key = $this->adjustKey( $key );

		$this->getStrategy( $key )->set( $key, $value );
	}

	/**
	 * @param string $key
	 *
	 * @return Strategy
	 */
	private function getStrategy( $key ) {
		global $woocommerce;

		/**
		 * This filter hook allows to override the storage strategy.
		 *
		 * @since 4.11.0
		 *
		 * @param string 'wc-session' Storage strategy
		 * @param string $key      The key operating the storage
		 */
		switch ( apply_filters( 'wcml_user_store_strategy', 'wc-session', $key ) ) {
			case 'cookie':
				$store = \WPML\Container\make( Cookie::class );
				break;

			case 'wc-session':
			default:
				$store = isset( $woocommerce->session ) ? new WcSession( $woocommerce->session ) : new Noop();
		}

		return $store;
	}

	/**
	 * @param string $key
	 *
	 * @return string $key
	 */
	private function adjustKey( $key ) {

		$prefix = 'wcml_';

		return strpos( $key, $prefix ) === 0 ? $key : $prefix . $key;
	}

}
