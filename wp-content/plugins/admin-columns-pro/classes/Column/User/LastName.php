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
class LastName extends AC\Column\User\LastName
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\User\Meta( $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\Model\Meta( $this );
	}

	public function filtering() {
		return new Filtering\Model\Meta( $this );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), AC\MetaType::USER );
	}

}