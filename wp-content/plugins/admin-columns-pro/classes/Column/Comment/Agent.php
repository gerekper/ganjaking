<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class Agent extends AC\Column\Comment\Agent
	implements Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_agent' );
	}

	public function filtering() {
		return new Filtering\Model\Comment\Agent( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Agent();
	}

}