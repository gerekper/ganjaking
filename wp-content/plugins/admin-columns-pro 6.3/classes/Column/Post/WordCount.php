<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Sorting;

class WordCount extends AC\Column\Post\WordCount
	implements Sorting\Sortable, ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function sorting() {
		return new Sorting\Model\Post\WordCount();
	}

}