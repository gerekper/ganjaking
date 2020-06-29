<?php

namespace ACP\Filtering;

class Cache {

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @param string $key
	 */
	public function __construct( $key ) {
		$this->set_key( $key );
	}

	/**
	 * Set Cache id. Max length for site_transient name is 40 characters,
	 *
	 * @param string $key
	 * @source https://core.trac.wordpress.org/ticket/15058
	 */
	private function set_key( $key ) {
		$this->key = md5( $key );
	}

	/**
	 * Put some data into the cache
	 *
	 * @param mixed    $data
	 * @param null|int $seconds
	 */
	public function put( $data, $seconds = null ) {
		update_site_option( 'ac_cache_data_' . $this->key, $data );

		$seconds = $this->get_seconds( $seconds );

		if ( $seconds ) {
			update_site_option( 'ac_cache_expires_' . $this->key, time() + absint( $seconds ) );
		}
	}

	/**
	 * @return string|false
	 */
	public function get() {
		return get_site_option( 'ac_cache_data_' . $this->key );
	}

	/**
	 * @return bool
	 */
	public function is_expired() {
		$expired = get_site_option( 'ac_cache_expires_' . $this->key );

		return ! $expired || time() > $expired;
	}

	/**
	 * @param int $seconds Expiration in seconds
	 *
	 * @return null|int
	 */
	protected function get_seconds( $seconds ) {
		if ( ! preg_match( '/^[1-9][0-9]*$/', $seconds ) ) {
			return null;
		}

		return $seconds;
	}

	public function delete() {
		delete_site_option( 'ac_cache_data_' . $this->key );
		delete_site_option( 'ac_cache_expires_' . $this->key );
	}

}