<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Role extends AC\Column\User\Role
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\User\Roles( $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\Model\User\Role( $this );
	}

	public function filtering() {
		return new Filtering\Model\User\Role( $this );
	}

	public function search() {
		return new Search\Comparison\User\Role( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function export() {
		return new Export\Model\User\Role( $this );
	}

}