<?php

namespace ACP\Column\User;

use AC;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class LastPost extends AC\Column\User\LastPost
	implements Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\User\MaxPostDate( $this->get_related_post_type(), (array) $this->get_related_post_stati() );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Comparison\User\MaxPostDate( $this->get_related_post_type(), (array) $this->get_related_post_stati() );
	}

}