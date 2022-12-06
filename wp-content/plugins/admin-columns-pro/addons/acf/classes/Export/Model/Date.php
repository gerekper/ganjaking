<?php

namespace ACA\ACF\Export\Model;

use ACA;
use ACP;
use DateTime;

class Date extends ACP\Export\Model {

	public function get_value( $id ) {
		$value = $this->column->get_raw_value( $id );

		if ( ! $value ) {
			return '';
		}

		$date = DateTime::createFromFormat( 'Ymd', $value );

		return $date ? $date->format( 'Y-m-d' ) : '';
	}

}