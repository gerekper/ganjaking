<?php

namespace ACA\MetaBox\Editing\ServiceFactory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing\StorageFactory;
use ACP\Editing\Service\Basic;
use ACP\Editing\View;

final class File {

	public function create( Column $column ) {
		return $column->is_clonable()
			? false
			: new Basic(
				( new View\Media() )->set_clear_button( true )->set_multiple( $column->is_multiple() ),
				( new StorageFactory() )->create( $column )
			);
	}

}