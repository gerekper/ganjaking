<?php

namespace ACP\Column\Media;

use AC;
use ACP\ConditionalFormat;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Width extends AC\Column\Media\Width
	implements Sorting\Sortable, ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function sorting() {
		return new Sorting\Model\Media\Width();
	}

}