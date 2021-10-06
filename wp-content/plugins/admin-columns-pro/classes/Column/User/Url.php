<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class Url extends AC\Column\User\Url
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\User\UserField( 'user_url' );
	}

	public function editing() {
		return new Editing\Service\User\Url( $this->get_label() );
	}

	public function filtering() {
		return new Filtering\Model\User\Url( $this );
	}

	public function search() {
		return new Search\Comparison\User\Url();
	}

}