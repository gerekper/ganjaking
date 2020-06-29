<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Excerpt extends AC\Column\Comment\Excerpt
	implements Editing\Editable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_content' );
	}

	public function editing() {
		return new Editing\Model\Comment\Comment( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Content();
	}

}