<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Settings;
use ACP\Sorting;

/**
 * @since 4.0
 */
class WordCount extends AC\Column\Comment\WordCount
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Comment\FieldFormat( 'comment_content', new Sorting\FormatValue\WordCount() );
	}

}