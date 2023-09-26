<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class Title extends Column implements ACP\Export\Exportable, ACP\Editing\Editable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'post_title' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		return new ACP\Export\Model\Post\Title();
	}

	public function editing() {
		return new ACP\Editing\Service\Media\Title();
	}

}