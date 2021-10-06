<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Caption extends AC\Column\Media\Caption
	implements Editing\Editable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PostField( 'post_excerpt' );
	}

	public function editing() {
		return new Editing\Service\Media\Caption();
	}

	public function search() {
		return new Search\Comparison\Post\Excerpt();
	}

}