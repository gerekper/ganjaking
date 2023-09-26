<?php

namespace ACA\MLA\Service;

use AC;
use AC\Registerable;
use ACA\MLA\Asset;

class Admin implements Registerable {

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

	public function admin_scripts(): void {
		$script = new Asset\Script\Admin( 'aca-mla-admin', $this->location );
		$script->enqueue();
	}

}