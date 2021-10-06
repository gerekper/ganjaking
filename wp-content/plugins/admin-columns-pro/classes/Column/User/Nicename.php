<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Nicename extends AC\Column\User\Nicename
	implements Editing\Editable, Export\Exportable, Search\Searchable, Sorting\Sortable {

	public function editing() {
		return new Editing\Service\User\Nicename( $this->get_label() );
	}

	public function export() {
		return new Export\Model\User\Nicename( $this );
	}

	public function search() {
		return new Search\Comparison\User\Nicename();
	}

	public function sorting() {
		return new Sorting\Model\User\UserField( 'user_nicename' );
	}

}