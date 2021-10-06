<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Album extends AC\Column\Media\Album
	implements Sorting\Sortable, Editing\Editable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Media\MetaDataText( $this->get_sub_key() );
	}

	public function export() {
		return new Export\Model\Value( $this );
	}

	public function editing() {
		return new Editing\Service\Media\MetaData( new Editing\View\Text(), $this->get_sub_key() );
	}

	public function search() {
		return new Search\Comparison\Media\MetaData( $this->get_sub_key() );
	}

}