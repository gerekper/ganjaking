<?php

namespace ACP\Column\Media;

use AC;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Sorting;

class AvailableSizes extends AC\Column\Media\AvailableSizes
	implements Sorting\Sortable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function sorting() {
		return new Sorting\Model\Media\AvailableSizes();
	}

	public function export() {
		return new Export\Model\Value( $this );
	}

}