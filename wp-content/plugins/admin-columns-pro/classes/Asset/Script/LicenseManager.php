<?php

namespace ACP\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;

class LicenseManager extends Script {

	public function __construct( $handle, Location $location ) {
		parent::__construct( $handle, $location->with_suffix( 'assets/core/js/license-manager.js' ) );
	}

	public function register() {
		parent::register();

		wp_localize_script( $this->handle, 'ACP_LICENSE_I18N', [
			'license_removal' => __( 'Are you sure you want deactivate Admin Columns Pro?', 'codepress-admin-columns' ),
			'license_removal_explanation' => __( 'You need to fill in your license key again if you want to reactivate.', 'codepress-admin-columns' )
		] );
	}

}