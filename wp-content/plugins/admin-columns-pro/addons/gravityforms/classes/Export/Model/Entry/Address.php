<?php

namespace ACA\GravityForms\Export\Model\Entry;

use AC\Column;
use ACP\Export;

class Address implements Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		return strip_tags( str_replace( '<br />', '; ', $this->column->get_value( $id ) ) );
	}

}