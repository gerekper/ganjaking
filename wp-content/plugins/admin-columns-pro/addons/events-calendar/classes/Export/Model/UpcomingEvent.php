<?php

namespace ACA\EC\Export\Model;

use AC\Column;
use ACP;

class UpcomingEvent implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$event_id = $this->column->get_raw_value( $id );

		return $event_id
			? get_the_title( (int) $event_id )
			: '';
	}

}