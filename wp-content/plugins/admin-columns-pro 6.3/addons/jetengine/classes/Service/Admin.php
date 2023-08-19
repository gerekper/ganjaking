<?php

namespace ACA\JetEngine\Service;

use AC;
use ACA\JetEngine\Asset;

final class Admin implements AC\Registerable {

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	private $location;

	/**
	 * @param AC\Asset\Location\Absolute $location
	 */
	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_scripts() {
		$script = new Asset\Script\Admin( 'aca-jetengine-admin', $this->location );
		$script->enqueue();
	}

}