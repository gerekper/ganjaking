<?php

namespace ACP\Column\User;

use AC;
use ACP\Search;
use ACP\Sorting;
use ACP\ConditionalFormat;

/**
 * @since 4.0
 */
class ID extends AC\Column\User\ID
	implements Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function sorting() {
		return new Sorting\Model\OrderBy( 'ID' );
	}

	public function search() {
		return new Search\Comparison\User\ID();
	}

}