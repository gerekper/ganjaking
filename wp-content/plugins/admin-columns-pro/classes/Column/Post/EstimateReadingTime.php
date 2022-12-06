<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Sorting;

class EstimateReadingTime extends AC\Column\Post\EstimatedReadingTime
	implements Sorting\Sortable, Export\Exportable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function sorting() {
		return new Sorting\Model\Post\EstimateReadingTime();
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}