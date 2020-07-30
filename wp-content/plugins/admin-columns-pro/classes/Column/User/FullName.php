<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class FullName extends AC\Column\User\FullName
	implements Sorting\Sortable, Export\Exportable, Search\Searchable, Editing\Editable {

	public function sorting() {
		return new Sorting\Model\User\FullName();
	}

	public function editing() {
		return new Editing\Model\User\Fullname( $this );
	}

	public function export() {
		return new Export\Model\User\FullName( $this );
	}

	public function search() {
		return new Search\Comparison\User\Name( [ 'first_name', 'last_name' ] );
	}

}