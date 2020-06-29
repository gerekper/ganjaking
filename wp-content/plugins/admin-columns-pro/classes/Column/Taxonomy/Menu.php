<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Sorting;

class Menu extends AC\Column\Menu
	implements Editing\Editable, Export\Exportable, Sorting\Sortable {

	public function get_item_type() {
		return 'taxonomy';
	}

	public function get_object_type() {
		return $this->get_taxonomy();
	}

	public function editing() {
		return new Editing\Model\Taxonomy\Menu( $this );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function sorting() {
		return new Sorting\Model\Taxonomy\Menu( $this->get_taxonomy() );
	}

}