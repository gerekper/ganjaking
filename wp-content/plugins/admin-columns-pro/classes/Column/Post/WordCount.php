<?php

namespace ACP\Column\Post;

use AC;
use ACP\Sorting;

class WordCount extends AC\Column\Post\WordCount
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Post\WordCount();
	}

}