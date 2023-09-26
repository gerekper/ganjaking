<?php

namespace ACA\GravityForms\Export\Model\Entry;

use AC\Column;
use ACP\Export;

class ItemList implements Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$items = unserialize( (string) $this->column->get_raw_value( $id ), [ 'allowed_classes' => false ] );

		return implode( ', ', $items );
	}

}