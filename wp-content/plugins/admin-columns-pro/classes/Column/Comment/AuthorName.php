<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class AuthorName extends AC\Column\Comment\AuthorName
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_author' );
	}

	public function editing() {
		return new Editing\Service\Basic( new Editing\View\Text(), new Editing\Storage\Comment\Field( 'comment_author' ) );
	}

	public function filtering() {
		return new Filtering\Model\Comment\AuthorName( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Author();
	}

}