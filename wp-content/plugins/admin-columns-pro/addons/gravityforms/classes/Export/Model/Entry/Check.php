<?php

namespace ACA\GravityForms\Export\Model\Entry;

use AC\Column;
use ACP\Export;

class Check implements Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		return $this->column->get_value( $id )
			? 'checked'
			: '';
	}

}