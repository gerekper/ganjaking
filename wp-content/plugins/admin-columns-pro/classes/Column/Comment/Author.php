<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

/**
 * @since 4.0
 */
class Author extends AC\Column\Comment\Author
	implements Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function filtering() {
		return new Filtering\Model\Comment\Author( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Author();
	}

	public function export() {
		return new Export\Model\Comment\Author( $this );
	}

}