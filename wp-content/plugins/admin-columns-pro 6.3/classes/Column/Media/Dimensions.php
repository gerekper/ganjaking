<?php

namespace ACP\Column\Media;

use AC;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Sorting;

class Dimensions extends AC\Column\Media\Dimensions
	implements Sorting\Sortable, Export\Exportable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function sorting() {
		return new Sorting\Model\Media\Dimensions();
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}