<?php

namespace ACA\ACF\Editing\Storage\Read;

use AC;
use ACA\ACF\Editing\Storage\ReadStorage;

class Column implements ReadStorage {

	/**
	 * @var AC\Column
	 */
	private $column;

	public function __construct( AC\Column $column ) {
		$this->column = $column;
	}

	public function get( int $id ) {
		$value = $this->column->get_raw_value( $id );

		return $value ?? false;
	}

}