<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Export;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class Taxonomy extends Column implements ACP\Export\Exportable, ACP\Editing\Editable {

	public function __construct() {
		// type is set runtime
		$this->set_original( true )
		     ->set_group( ColumnGroup::NAME );
	}

	public function editing() {
		return new ACP\Editing\Service\Post\Taxonomy( $this->get_taxonomy(), false );
	}

	public function export() {
		return new Export\Model\Taxonomy( $this->get_taxonomy() );
	}

	/**
	 * @return string
	 */
	public function get_taxonomy() {
		return substr( $this->get_type(), 2 );
	}

}