<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class Author extends Column implements ACP\Editing\Editable, ACP\Export\Exportable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'author' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function editing() {
		return new ACP\Editing\Service\Post\Author();
	}

	public function export() {
		return new ACP\Export\Model\Post\Author();
	}

}