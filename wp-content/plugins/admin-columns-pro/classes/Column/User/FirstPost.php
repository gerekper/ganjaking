<?php

namespace ACP\Column\User;

use AC;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class FirstPost extends AC\Column\User\FirstPost
	implements Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\User\MaxPostDate( $this->get_related_post_type(), (array) $this->get_related_post_stati(), true );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Comparison\User\MaxPostDate( $this->get_related_post_type(), (array) $this->get_related_post_stati(), true );
	}

}