<?php

namespace ACP\Export;

use AC;
use AC\Asset\Location;
use AC\Registrable;
use ACP\Export\Asset\Script;

class TableScreen implements Registrable {

	/**
	 * @var Location
	 */
	protected $location;

	public function __construct( Location $location ) {
		$this->location = $location;
	}

	public function register() {
		add_action( 'ac/table/list_screen', [ $this, 'load_list_screen' ] );
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
	}

	/**
	 * Load a list screen and potentially attach the proper exporting information to it
	 *
	 * @param AC\ListScreen $list_screen List screen for current table screen
	 *
	 * @since 1.0
	 */
	public function load_list_screen( AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$list_screen->export()->attach();
		}
	}

	public function scripts() {
		$style = new AC\Asset\Style( 'acp-export-listscreen', $this->location->with_suffix( 'assets/export/css/listscreen.css' ) );
		$style->enqueue();

		$script = new Script\Table( 'acp-export-listscreen', $this->location->with_suffix( 'assets/export/js/listscreen.js' ) );
		$script->enqueue();
	}

}