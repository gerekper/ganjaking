<?php

namespace ACA\YoastSeo\Service;

use AC\Asset\Location\Absolute;
use AC\Registerable;
use ACA\YoastSeo\Asset;

final class Admin implements Registerable {

	/**
	 * @var Absolute
	 */
	private $location;

	public function __construct( Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_scripts() {
		$script = new Asset\Script\Admin( 'aca-yoast-admin', $this->location );
		$script->enqueue();
	}

}