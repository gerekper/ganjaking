<?php

namespace ACP\Export\Model\CustomField;

use AC\Column;
use ACP\Export\Service;

class Date implements Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$timestamp = ac_helper()->date->strtotime(
			$this->column->get_raw_value( $id )
		);

		if ( ! $timestamp ) {
			return false;
		}

		// Spreadsheet date format
		return date( 'Y-m-d H:i:s', $timestamp );
	}

}