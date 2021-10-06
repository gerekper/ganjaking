<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class Formats extends AC\Column\Post\Formats
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\Taxonomy( $this->get_taxonomy(), false );
	}

	public function editing() {
		return new Editing\Service\Post\Formats();
	}

	public function filtering() {
		return new Filtering\Model\Post\Formats( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Formats( $this->get_taxonomy() );
	}

}