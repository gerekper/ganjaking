<?php

namespace ACP\Column\Media;

use AC;
use ACP\Export;
use ACP\Sorting;

class Dimensions extends AC\Column\Media\Dimensions
	implements Sorting\Sortable, Export\Exportable {

	public function sorting() {
		return new Sorting\Model\Media\Dimensions();
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}