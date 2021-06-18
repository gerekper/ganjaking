<?php

namespace ACP\Transient;

use AC\Expirable;
use AC\Storage;

class LicenseCheckTransient implements Expirable {

	/**
	 * @var Storage\Timestamp
	 */
	protected $timestamp;

	public function __construct() {
		$this->timestamp = new Storage\Timestamp(
			new Storage\Option( 'acp_periodic_license_check' )
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