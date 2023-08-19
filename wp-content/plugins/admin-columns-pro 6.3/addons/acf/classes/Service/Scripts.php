<?php

namespace ACA\ACF\Service;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Asset\Style;
use AC\Registerable;
use ACA\ACF\Asset\Script\Admin;

class Scripts implements Registerable {

	/**
	 * @var Absolute
	 */
	private $location;

	public function __construct( Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/table_scripts/editing', [ $this, 'table_scripts_editing' ] );
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function table_scripts_editing() {
		$script = new Script( 'aca-acf-table', $this->location->with_suffix( 'assets/js/table.js' ), [ 'jquery' ] );
		$script->enqueue();

		$style = new Style( 'aca-acf-table', $this->location->with_suffix( 'assets/css/table.css' ) );
		$style->enqueue();
	}

	public function admin_scripts() {
		$style = new Style( 'aca-acf-admin', $this->location->with_suffix( 'assets/css/admin.css' ) );
		$style->enqueue();

		$script = new Admin( 'aca-acf-admin', $this->location );
		$script->enqueue();
	}

}