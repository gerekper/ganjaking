<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;

class FileUrl extends Column {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'file_url' )
		     ->set_group( ColumnGroup::NAME );
	}

}