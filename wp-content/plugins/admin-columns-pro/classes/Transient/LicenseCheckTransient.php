<?php

namespace ACP\Transient;

use AC\Expirable;
use AC\Storage;

class LicenseCheckTransient implements Expirable {

	const CACHE_KEY = 'acp_periodic_license_check';

	/**
	 * @var Storage\Timestamp
	 */
	protected $timestamp;

	public function __construct( $network_only = false ) {
		$this->timestamp = new Storage\Timestamp(
			( new Storage\OptionFactory() )->create( self::CACHE_KEY, $network_only )
		);
	}

	/**
	 * @param int|null $time
	 *
	 * @return bool
	 */
	public function is_expired( $time = null ) {
		return $this->timestamp->is_expired( $time );
	}

	public function delete() {
		$this->timestamp->delete();
	}

	/**
	 * @param int $expiration Time until expiration in seconds.
	 *
	 * @return bool
	 */
	public function save( $expiration ) {
		// Always store timestamp before option data.
		return $this->timestamp->save( time() + (int) $expiration );
	}

}