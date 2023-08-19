<?php
declare( strict_types=1 );

namespace ACA\MLA\Service;

use AC\Registerable;
use ACA\MLA\Editing\TableRows\MediaLibraryRows;
use ACA\MLA\ListScreen;
use ACP\Editing\Ajax\TableRowsFactory;

class Editing implements Registerable {

	public function register(): void
    {
		TableRowsFactory::register( ListScreen\MediaLibrary::class, MediaLibraryRows::class );
	}

}