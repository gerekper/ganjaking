<?php

namespace ACA\GravityForms\Column\Entry;

use ACA\GravityForms\Column;

class MultipleChoices extends Column\Entry\Choices {

	public function get_value( $id ) {
		$value = explode( ', ', $this->get_entry_value( $id ) );

		return $this->get_formatted_value( $value );
	}

}