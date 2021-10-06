<?php

namespace ACP\Admin;

use AC\Asset;
use AC\Registrable;

class Scripts implements Registrable {

	/**
	 * @var Asset\Location\Absolute
	 */
	private $location;

	public function __construct( Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_scripts() {
		$style = new Asset\Style( 'acp-admin-setup', $this->location->with_suffix( 'assets/core/css/welcome.css' ) );
		$style->enqueue();
		$script = new Asset\Script( 'acp-admin-setup', $this->location->with_suffix( 'assets/core/js/setup.js' ) );
		$script->enqueue();
	}

}