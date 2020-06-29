<?php

namespace ACP\Plugin\NetworkUpdate;

use AC\Plugin;
use AC\Plugin\Updater\Site;
use ACP\Entity\License;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Plugin\Update;

/**
 * Migrates license settings for the multisite network
 */
class V5000 extends Update\V5000 {

	public function apply_update() {
		parent::apply_update();

		// Run the update script for the first (main) site in order to migrate Layout settings for Sites and Network. We need to perform all necessary updates
		$stored_version = AC()->get_stored_version();
		$updater = new Site( AC() );
		$updater
			->add_update( new Plugin\Update\V3005( $stored_version ) )
			->add_update( new Plugin\Update\V3007( $stored_version ) )
			->add_update( new Plugin\Update\V3201( $stored_version ) )
			->add_update( new Plugin\Update\V4000( $stored_version ) )
			->parse_updates();
	}

	// For network

	protected function save_license( License $license ) {
		( new LicenseRepository( true ) )->save( $license );
		( new LicenseKeyRepository( true ) )->save( $license->get_key() );
	}

	protected function get_license_option( $option = '' ) {
		return get_site_option( self::LICENSE_OPTION_KEY . $option );
	}

}