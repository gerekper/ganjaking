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
class AuthorEmail extends AC\Column\Comment\AuthorEmail
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_author_email' );
	}

	public function editing() {
		return new Editing\Service\Basic( new Editing\View\Email(), new Editing\Storage\Post\Field( 'comment_author_email' ) );
	}

	public function filtering() {
		return new Filtering\Model\Comment\AuthorEmail( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Email();
	}

}