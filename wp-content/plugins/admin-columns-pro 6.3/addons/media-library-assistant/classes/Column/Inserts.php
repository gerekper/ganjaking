<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Export\Model\Inserted;
use ACA\MLA\Service\ColumnGroup;
use ACP;
use MLACore;

class Inserts extends Column implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'inserted' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		if ( ! MLACore::$process_inserted_in ) {
			return false;
		}

		return new Inserted( );
	}

}