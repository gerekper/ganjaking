<?php

namespace ACA\Types\Field\Repeatable;

use ACA\Types\Export;
use ACA\Types\Field;

class Skype extends Field\Skype {

	public function get_value( $id ) {
		$values = [];

		foreach ( (array) $this->get_raw_value( $id ) as $string ) {
			$values[] = $this->format( $string );
		}

		return ac_helper()->html->small_block( $values );
	}

	public function export() {
		return new Export\Field\Skype( $this->column );
	}

}