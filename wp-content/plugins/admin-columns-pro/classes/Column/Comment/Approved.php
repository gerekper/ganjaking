<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Approved extends AC\Column\Comment\Approved
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_approved' );
	}

	public function editing() {
		return new Editing\Service\Comment\Approved();
	}

	public function filtering() {
		return new Filtering\Model\Comment\Approved( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Approved();
	}

}