<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Sticky extends AC\Column\Post\Sticky
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\Sticky();
	}

	public function editing() {
		return new Editing\Service\Post\Sticky();
	}

	public function filtering() {
		return new Filtering\Model\Post\Sticky( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Sticky();
	}

}