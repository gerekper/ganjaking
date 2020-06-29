<?php

namespace ACP\Export\Model\CustomField;

use ACP\Export\Model;

class Date extends Model {

	public function get_value( $id ) {
		$timestamp = ac_helper()->date->strtotime( $this->column->get_raw_value( $id ) );

		if ( ! $timestamp ) {
			return false;
		}

		// Spreadsheet date format
		return date( 'Y-m-d H:i:s', $timestamp );
	}

}