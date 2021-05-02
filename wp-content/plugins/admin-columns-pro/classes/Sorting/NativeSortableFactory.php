<?php

namespace ACP\Sorting;

use AC;
use AC\ColumnRepository;
use ACP\Sorting\NativeSortable\NativeSortableRepository;
use ACP\Sorting\NativeSortable\Storage;

class NativeSortableFactory {

	public function create( AC\ListScreen $list_screen ) {
		return new NativeSortableRepository(
			new ColumnRepository( $list_screen ),
			new Storage( $list_screen->get_storage_key() )
		);
	}

}