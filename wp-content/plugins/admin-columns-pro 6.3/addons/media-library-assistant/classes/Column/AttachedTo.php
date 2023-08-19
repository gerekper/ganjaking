<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Export\Model;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class AttachedTo extends Column implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'attached_to' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		return new Model\AttachedTo();
	}

}