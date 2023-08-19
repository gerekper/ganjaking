<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class Description extends Column implements ACP\Export\Exportable, ACP\Editing\Editable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'description' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function editing() {
		return new ACP\Editing\Service\Post\Content();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}