<?php

namespace ACP\Column\User;

use AC;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class ID extends AC\Column\User\ID
	implements Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'ID' );
	}

	public function search() {
		return new Search\Comparison\User\ID();
	}

}