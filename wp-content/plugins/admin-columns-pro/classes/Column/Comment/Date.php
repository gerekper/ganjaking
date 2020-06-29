<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

/**
 * @since 4.0
 */
class Date extends AC\Column\Comment\Date
	implements Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function filtering() {
		return new Filtering\Model\Comment\Date( $this );
	}

	public function export() {
		return new Export\Model\Comment\Date( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Date\Date();
	}

}