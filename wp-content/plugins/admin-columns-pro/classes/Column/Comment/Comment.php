<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Comment extends AC\Column\Comment\Comment
	implements Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Service\Comment\Content();
	}

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_content' );
	}

	public function search() {
		return new Search\Comparison\Comment\Content();
	}

	public function export() {
		return new Export\Model\Comment\Comment( $this );
	}

}