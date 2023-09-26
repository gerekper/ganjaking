<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;

class BaseFile extends Column {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'base_file' )
		     ->set_group( ColumnGroup::NAME );
	}

}