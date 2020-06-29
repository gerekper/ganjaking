<?php

namespace ACP;

use ACP\Type\License\Key;

class LicenseKeyRepository {

	const OPTION_KEY = 'acp_subscription_key';

	/** @var bool */
	private $network_active;

	public function __construct( $network_active = false ) {
		$this->network_active = (bool) $network_active;
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
		return $this->network_active
			? get_site_option( self::OPTION_KEY )
			: get_option( self::OPTION_KEY );
	}

	public function save( Key $license_key ) {
		$this->network_active
			? update_site_option( self::OPTION_KEY, $license_key->get_value() )
			: update_option( self::OPTION_KEY, $license_key->get_value(), false );
	}

	public function delete() {
		$this->network_active
			? delete_site_option( self::OPTION_KEY )
			: delete_option( self::OPTION_KEY );
	}

}