<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Export;
use ACP\Search;

/**
 * @since 4.1
 */
class Post extends AC\Column\Comment\Post
	implements Export\Exportable, Search\Searchable {

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Post();
	}

}