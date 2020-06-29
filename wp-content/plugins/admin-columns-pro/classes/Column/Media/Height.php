<?php

namespace ACP\Column\Media;

use AC;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Height extends AC\Column\Media\Height
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Media\Height();
	}

}