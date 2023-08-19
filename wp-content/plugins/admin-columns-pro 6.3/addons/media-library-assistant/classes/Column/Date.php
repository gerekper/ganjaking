<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class Date extends Column implements ACP\Export\Exportable, ACP\Editing\Editable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'date' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function editing() {
		return new ACP\Editing\Service\Media\Date();
	}

	public function export() {
		return new ACP\Export\Model\Post\Date();
	}

}