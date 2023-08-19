<?php
declare( strict_types=1 );

namespace ACP\Export\Asset;

use AC;
use AC\Asset\Location;
use ACP\Export\ListScreen;
use LogicException;

class TableScriptFactory {

	private $location;

	public function __construct( Location $location ) {
		$this->location = $location;
	}

	public function create( AC\ListScreen $list_screen, bool $show_button ): Script\Table {
		if ( ! $list_screen instanceof ListScreen ) {
			throw new LogicException( 'Invalid list screen.' );
		}

		return new Script\Table(
			'acp-export-listscreen',
			$this->location->with_suffix( 'assets/export/js/listscreen.js' ),
			$list_screen->export(),
			( new ExportVarFactory( $list_screen ) )->create(),
			$show_button
		);
	}

}