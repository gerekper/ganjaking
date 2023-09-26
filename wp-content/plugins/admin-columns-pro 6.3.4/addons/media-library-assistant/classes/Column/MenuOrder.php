<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP\Editing;
use ACP\Export;

class MenuOrder extends Column implements Export\Exportable, Editing\Editable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'menu_order' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		return new Export\Model\Post\PostField( 'menu_order' );
	}

	public function editing() {
		return new Editing\Service\Post\Order();
	}

}