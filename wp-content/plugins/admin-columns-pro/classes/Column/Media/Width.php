<?php

namespace ACP\Column\Media;

use AC;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Width extends AC\Column\Media\Width
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Media\Width();
	}

}