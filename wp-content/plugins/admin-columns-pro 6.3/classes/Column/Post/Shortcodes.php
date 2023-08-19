<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Sorting;

class Shortcodes extends AC\Column\Post\Shortcodes
	implements Sorting\Sortable, Export\Exportable {

	public function sorting() {
		return new Sorting\Model\Post\Shortcodes();
	}

	public function export() {
		return new Export\Model\Post\Shortcodes( $this );
	}

}