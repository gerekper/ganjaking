<?php

namespace ACA\GravityForms\Export\Model\Entry;

use ACP\Export;

class Check extends Export\Model {

	public function get_value( $id ) {
		return $this->column->get_value( $id ) ? 'checked' : '';
	}

}