<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class Modified extends Column implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'modified' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		return new ACP\Export\Model\Post\PostField( 'post_modified' );
	}

}