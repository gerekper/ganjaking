<?php

namespace ACP\Column\Post;

use AC;
use ACP\Sorting;

class Depth extends AC\Column\Post\Depth
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Post\Depth();
	}

}