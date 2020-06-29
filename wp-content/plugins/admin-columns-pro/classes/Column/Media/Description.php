<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class Description extends AC\Column\Media\Description
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PostField( 'post_content' );
	}

	public function editing() {
		return new Editing\Model\Post\Content( $this );
	}

	public function filtering() {
		return new Filtering\Model\Post\Content( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Content();
	}

}