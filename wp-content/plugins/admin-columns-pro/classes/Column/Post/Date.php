<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

/**
 * @since 4.0
 */
class Date extends AC\Column\Post\Date
	implements Filtering\Filterable, Editing\Editable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Model\Post\Date( $this );
	}

	public function filtering() {
		return new Filtering\Model\Post\Date( $this );
	}

	public function export() {
		return new Export\Model\Post\Date( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Date\PostDate();
	}

}