<?php

namespace ACP\Column\Media;

use AC;
use ACP\Export;
use ACP\Sorting;

/**
 * @since 4.0
 */
class FileSize extends AC\Column\Media\FileSize
	implements Sorting\Sortable, Export\Exportable {

	public function sorting() {
		return new Sorting\Model\Media\FileSize();
	}

	public function export() {
		return new Export\Model\Value( $this );
	}

}