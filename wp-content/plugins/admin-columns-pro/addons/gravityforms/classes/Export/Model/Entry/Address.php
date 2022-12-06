<?php

namespace ACA\GravityForms\Export\Model\Entry;

use ACP\Export;

class Address extends Export\Model {

	public function get_value( $id ) {
		return strip_tags( str_replace( '<br />', '; ', $this->column->get_value( $id ) ) );
	}

}