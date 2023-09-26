<?php

namespace ACA\EC\Export\Model\Event;

use AC\Column;
use ACP;

class AllDayEvent implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$value = $this->column->get_raw_value( $id );

		return $value
			? '1'
			: '';
	}

}