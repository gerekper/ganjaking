<?php

namespace ACA\ACF\Export\Model;

use AC\Column;
use ACA;
use ACP;
use DateTime;

class Date implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$value = $this->column->get_raw_value( $id );

		if ( ! $value ) {
			return '';
		}

		$date = DateTime::createFromFormat( 'Ymd', $value );

		return $date
			? $date->format( 'Y-m-d' )
			: '';
	}

}