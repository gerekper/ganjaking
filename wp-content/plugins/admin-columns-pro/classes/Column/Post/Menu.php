<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Sorting;

class Menu extends AC\Column\Post\Menu
	implements Editing\Editable, Export\Exportable, Sorting\Sortable {

	public function editing() {
		return new Editing\Service\Post\Menu( $this->get_object_type(), $this->get_item_type() );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function sorting() {
		return new Sorting\Model\Post\Menu();
	}

}