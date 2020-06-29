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
class AuthorUrl extends AC\Column\Comment\AuthorUrl
	implements Editing\Editable, Sorting\Sortable, Filtering\Filterable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_author_url' );
	}

	public function editing() {
		return new Editing\Model\Comment\AuthorURL( $this );
	}

	public function filtering() {
		return new Filtering\Model\Comment\AuthorUrl( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Url();
	}

}