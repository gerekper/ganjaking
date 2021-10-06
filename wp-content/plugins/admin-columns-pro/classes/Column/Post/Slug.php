<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Slug extends AC\Column\Post\Slug
	implements Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PostField( 'post_name' );
	}

	public function editing() {
		return new Editing\Service\Post\Slug();
	}

	public function search() {
		return new Search\Comparison\Post\PostName();
	}

}