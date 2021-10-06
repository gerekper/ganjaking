<?php

namespace ACP\ApplyFilter\CustomField;

use AC;

class StoredDateFormat implements AC\ApplyFilter {

	/**
	 * @var AC\Column
	 */
	private $column;

	public function __construct( AC\Column\CustomField $column ) {
		$this->column = $column;
	}

	public function apply_filters( $date_format ) {
		return apply_filters( 'acp/custom_field/stored_date_format', $date_format, $this->column );
	}

}