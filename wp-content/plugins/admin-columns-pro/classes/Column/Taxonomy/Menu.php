<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;
use ACP\Sorting;

class Menu extends AC\Column\Menu
	implements Editing\Editable, Export\Exportable, Sorting\Sortable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function get_item_type() {
		return 'taxonomy';
	}

	public function get_object_type() {
		return $this->get_taxonomy();
	}

	public function editing() {
		return new Editing\Service\Menu( new Editing\Storage\Taxonomy\Menu( $this->get_object_type(), $this->get_item_type() ) );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function sorting() {
		return new Sorting\Model\Taxonomy\Menu( $this->get_taxonomy() );
	}

}