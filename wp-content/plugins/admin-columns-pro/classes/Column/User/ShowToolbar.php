<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class ShowToolbar extends AC\Column\User\ShowToolbar
	implements Filtering\Filterable, Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\User\Meta( 'show_admin_bar_front' );
	}

	public function editing() {
		return new Editing\Service\User\ShowToolbar();
	}

	public function filtering() {
		return new Filtering\Model\User\ShowToolbar( $this );
	}

	public function search() {
		return new Search\Comparison\User\TrueFalse( 'show_admin_bar_front' );
	}

}