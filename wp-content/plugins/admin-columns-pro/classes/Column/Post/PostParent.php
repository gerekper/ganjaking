<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class PostParent extends AC\Column\Post\PostParent
	implements Sorting\Sortable, Editing\Editable, Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PostParent();
	}

	public function editing() {
		return new Editing\Model\Post\PostParent( $this );
	}

	public function filtering() {
		return new Filtering\Model\Post\PostParent( $this );
	}

	public function export() {
		return new Export\Model\PostTitleFromPostId( $this );
	}

	public function search() {
		return new Search\Comparison\Post\PostParent( $this->get_post_type() );
	}

}