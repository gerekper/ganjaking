<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class Name extends Column implements ACP\Export\Exportable, ACP\Editing\Editable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'post_name' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		return new ACP\Export\Model\Post\PostField( 'post_name' );
	}

	public function editing() {
		return new ACP\Editing\Service\Post\Slug();
	}

}