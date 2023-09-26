<?php

namespace ACA\Pods\Service;

use AC\Asset\Location\Absolute;
use AC\Registerable;
use ACA\Pods\Asset\Script\Admin;

final class Scripts implements Registerable {

	/**
	 * @var Absolute
	 */
	private $location;

	/**
	 * @param Absolute $location
	 */
	public function __construct( Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_scripts() {
		$script = new Admin( 'aca-pods-admin', $this->location );
		$script->enqueue();
	}

}