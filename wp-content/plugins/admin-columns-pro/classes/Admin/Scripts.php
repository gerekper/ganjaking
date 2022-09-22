<?php

namespace ACP\Admin;

use AC\Asset;
use AC\Asset\Enqueueable;
use AC\Registrable;
use ACP\Access\PermissionsStorage;
use ACP\Asset\Script;
use ACP\Transient\LicenseCheckTransient;

class Scripts implements Registrable {

	/**
	 * @var Asset\Location\Absolute
	 */
	private $location;

	/**
	 * @var PermissionsStorage
	 */
	private $permission_storage;

	/**
	 * @var bool
	 */
	private $network_active;

	public function __construct( Asset\Location\Absolute $location, PermissionsStorage $permission_storage, $network_active ) {
		$this->location = $location;
		$this->permission_storage = $permission_storage;
		$this->network_active = (bool) $network_active;
	}

	public function register() {
		add_action( 'ac/admin_scripts', [ $this, 'register_usage_limiter' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_daily_license_check' ] );
	}

	public function register_usage_limiter() {
		if ( $this->permission_storage->retrieve()->has_usage_permission() ) {
			return;
		}

		$assets = [
			new Asset\Style( 'acp-usage-limiter', $this->location->with_suffix( 'assets/core/css/usage-limiter.css' ) ),
			new Asset\Script( 'acp-usage-limiter', $this->location->with_suffix( 'assets/core/js/usage-limiter.js' ) ),
		];

		array_map( [ $this, 'enqueue' ], $assets );
	}

	public function register_daily_license_check() {
		$transient = new LicenseCheckTransient( $this->network_active );

		if ( $transient->is_expired() ) {
			$script = new Script\LicenseCheck( $this->location->with_suffix( 'assets/core/js/license-check.js' ) );
			$script->enqueue();

			$transient->save( DAY_IN_SECONDS );
		}
	}

	private function enqueue( Enqueueable $assets ) {
		$assets->enqueue();
	}

}