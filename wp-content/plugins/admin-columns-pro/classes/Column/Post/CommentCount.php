<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class CommentCount extends AC\Column\Post\CommentCount
	implements Filtering\Filterable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return ( new Sorting\Model\Post\CommentCountFactory )->create( $this->get_setting( AC\Settings\Column\CommentCount::NAME )->get_value() );
	}

	public function filtering() {
		return new Filtering\Model\Post\CommentCount( $this );
	}

	public function export() {
		return new Export\Model\Post\CommentCount( $this );
	}

	public function search() {
		return new Search\Comparison\Post\CommentCount();
	}

}