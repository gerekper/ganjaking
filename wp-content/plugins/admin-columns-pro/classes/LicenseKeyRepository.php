<?php

namespace ACP;

use AC\Storage\KeyValuePair;
use AC\Storage\OptionFactory;
use ACP\Type\License\Key;

class LicenseKeyRepository {

	const OPTION_KEY = 'acp_subscription_key';

	/**
	 * @var KeyValuePair
	 */
	private $storage;

	public function __construct( $network_active = false ) {
		$this->storage = ( new OptionFactory() )->create( self::OPTION_KEY, (bool) $network_active );
	}

	public function find() {
		$key = defined( 'ACP_LICENCE' ) && ACP_LICENCE
			? ACP_LICENCE
			: $this->get();

		if ( ! Key::is_valid( $key ) ) {
			return null;
		}

		return new Key( $key );
	}

	private function get() {
		return $this->storage->get();
	}

	public function save( Key $license_key ) {
		return $this->storage->save( $license_key->get_value() );
	}

	public function delete() {
		return $this->storage->delete();
	}

}