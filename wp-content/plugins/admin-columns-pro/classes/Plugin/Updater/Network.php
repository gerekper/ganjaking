<?php

namespace ACP\Plugin\Updater;

use AC\Plugin\Updater;
use AC\Plugin\Version;

class Network extends Updater {

	const VERSION_KEY = 'acp_version';

	/**
	 * @var Version
	 */
	private $version;

	public function __construct( Version $version ) {
		$this->version = $version;
	}

	/**
	 * @return bool
	 */
	public function is_new_install() {
		// Current and before version 5 check
		return empty( $this->get_stored_version() ) && empty( get_site_option( 'cpupdate_cac-pro' ) );
	}

	protected function update_stored_version( Version $version = null ) {
		if ( null === $version ) {
			$version = $this->version;
		}

		return update_site_option( self::VERSION_KEY, $version );
	}

	/**
	 * @return string
	 */
	public function get_stored_version() {
		return get_site_option( self::VERSION_KEY );
	}

}