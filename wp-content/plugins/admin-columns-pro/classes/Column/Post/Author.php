<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Author extends AC\Column\Post\Author
	implements Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'author' );
	}

	public function editing() {
		return new Editing\Model\Post\Author( $this );
	}

	public function export() {
		return new Export\Model\Post\Author( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Author( $this->get_post_type() );
	}

}