<?php

namespace ACP\Transient;

use AC\Expirable;
use AC\Storage;

class UpdateCheckTransientHourly implements Expirable {

	/**
	 * @var Storage\Timestamp
	 */
	protected $timestamp;

	public function __construct() {
		$this->timestamp = new Storage\Timestamp(
			new Storage\Option( 'acp_periodic_update_plugins_check_hourly' )
		);
	}

	/**
	 * @param int|null $value
	 *
	 * @return bool
	 */
	public function is_expired( int $value = null ): bool
    {
		return $this->timestamp->is_expired( $value );
	}

	public function delete() {
		$this->timestamp->delete();
	}

	/**
	 * @return bool
	 */
	public function save() {
		return $this->timestamp->save( time() + HOUR_IN_SECONDS );
	}

}