<?php

namespace ACP\Column\Media;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Artist extends AC\Column\Media\Artist
	implements Sorting\Sortable, Editing\Editable, Export\Exportable, Search\Searchable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function sorting() {
		return new Sorting\Model\Media\MetaDataText( 'artist' );
	}

	public function editing() {
		return new Editing\Service\Media\MetaData\Audio( 'artist' );
	}

	public function export() {
		return new Export\Model\Value( $this );
	}

	public function search() {
		return new Search\Comparison\Media\MetaData( 'artist' );
	}

}