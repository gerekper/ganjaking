<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Search;
use ACP\Sorting;

class TitleRaw extends AC\Column\Post\TitleRaw
	implements Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'title' );
	}

	public function editing() {
		return new Editing\Service\Basic(
			new View\Text(),
			new Storage\Post\Field( 'post_title' )
		);
	}

	public function search() {
		return new Search\Comparison\Post\Title();
	}

}