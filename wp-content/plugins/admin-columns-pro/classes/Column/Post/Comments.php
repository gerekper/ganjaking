<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Search;

class Comments extends AC\Column\Post\Comments
	implements Export\Exportable, Search\Searchable {

	public function export() {
		return new Export\Model\Post\Comments( $this );
	}

	public function search() {
		return new Search\Comparison\Post\CommentCount();
	}

}