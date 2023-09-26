<?php

namespace ACA\MetaBox\Service;

use AC;
use AC\Asset\Location;
use AC\Registerable;
use ACA\MetaBox\Asset;

final class Scripts implements Registerable {

	/**
	 * @var Location\Absolute
	 */
	private $location;

	public function __construct( Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'ac/table_scripts/editing', [ $this, 'table_scripts_editing' ] );
	}

	public function admin_scripts() {
		$script = new Asset\Script\Admin( 'aca-metabox-admin', $this->location );
		$script->enqueue();
	}

	public function table_scripts_editing() {
		$style = new AC\Asset\Style( 'aca-metabox-table', $this->location->with_suffix( 'assets/css/table.css' ) );
		$style->enqueue();
	}

}