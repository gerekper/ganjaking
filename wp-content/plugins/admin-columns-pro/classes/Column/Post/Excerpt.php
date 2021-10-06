<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class Excerpt extends AC\Column\Post\Excerpt
	implements Sorting\Sortable, Editing\Editable, Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PostExcerpt();
	}

	public function filtering() {
		return new Filtering\Model\Post\Excerpt( $this );
	}

	public function editing() {
		return new Editing\Service\Post\Excerpt();
	}

	public function export() {
		return new Export\Model\StrippedRawValue( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Excerpt();
	}

}