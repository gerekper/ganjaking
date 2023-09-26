<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Export;
use ACA\MLA\Service\ColumnGroup;
use ACP\Export\Exportable;

class CustomField extends Column implements Exportable {

	/**
	 * Define column properties
	 * set_type( 'c_' . field number ) is done by the calling function.
	 */
	public function __construct() {
		// type is set runtime
		$this->set_original( true )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		return new Export\Model\CustomField( $this->get_name() );
	}

}