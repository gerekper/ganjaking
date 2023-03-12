<?php

namespace ACA\ACF\Export\Model;

use AC\Column;
use ACA;
use ACP;

class Link implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$link = $this->column->get_raw_value( $id );

		return $link['url'] ?? '';
	}

}