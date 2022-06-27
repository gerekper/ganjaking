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
		return new Sorting\Model\Media\MetaDataText( 'album' );
	}

	public function export() {
		return new Export\Model\Value( $this );
	}

	public function editing() {
		return new Editing\Service\Media\MetaData\Audio( 'album' );
	}

	public function search() {
		return new Search\Comparison\Media\MetaData( 'album' );
	}

}