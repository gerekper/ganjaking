<?php

namespace ACP\Column\Post;

use AC;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class ID extends AC\Column\Post\ID
	implements Sorting\Sortable, Filtering\Filterable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'ID' );
	}

	public function filtering() {
		return new Filtering\Model\Post\ID( $this );
	}

	public function search() {
		return new Search\Comparison\Post\ID();
	}

}