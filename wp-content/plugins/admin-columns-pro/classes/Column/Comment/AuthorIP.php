<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class AuthorIP extends AC\Column\Comment\AuthorIP
	implements Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_author_IP' );
	}

	public function filtering() {
		return new Filtering\Model\Comment\AuthorIP( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\IP();
	}

}
