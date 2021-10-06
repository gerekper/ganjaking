<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

class Date extends AC\Column\Media\Date
	implements Filtering\Filterable, Editing\Editable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Service\Media\Date();
	}

	public function filtering() {
		return new Filtering\Model\Post\Date( $this );
	}

	public function export() {
		return new Export\Model\Post\Date( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Date\PostDate();
	}

}