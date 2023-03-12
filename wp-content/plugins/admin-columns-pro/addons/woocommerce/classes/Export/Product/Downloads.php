<?php

namespace ACA\WC\Export\Product;

use ACA\WC\Column;
use ACP;

class Downloads implements ACP\Export\Service {

	private $column;

	public function __construct( Column\Product\Downloads $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$values = [];

		foreach ( $this->column->get_raw_value( $id ) as $download ) {
			$values[] = $download->get_file();
		}

		return implode( $this->column->get_separator(), $values );
	}

}