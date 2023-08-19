<?php

namespace ACA\EC\Service;

use AC;
use AC\Registerable;
use ACA\EC\Asset\Script\Admin;

final class Scripts implements Registerable {

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_scripts() {
		$style = new AC\Asset\Style( 'aca-ec-admin', $this->location->with_suffix( 'assets/css/admin.css' ) );
		$style->enqueue();

		$script = new Admin( 'aca-ec-admin', $this->location );
		$script->enqueue();
	}

}