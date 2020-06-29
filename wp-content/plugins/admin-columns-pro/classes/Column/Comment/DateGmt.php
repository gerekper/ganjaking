<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class DateGmt extends AC\Column\Comment\DateGmt
	implements Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_date_gmt' );
	}

	public function filtering() {
		return new Filtering\Model\Comment\DateGmt( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Date\Gmt();
	}

}