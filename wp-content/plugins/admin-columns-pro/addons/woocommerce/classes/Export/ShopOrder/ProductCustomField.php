<?php

namespace ACA\WC\Export\ShopOrder;

use AC\Collection;
use AC\Column;
use ACP;

class ProductCustomField implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$collection = $this->column->get_raw_value( $id );

		if ( ! $collection instanceof Collection ) {
			return '';
		}

		return $collection->implode( ', ' );
	}

}