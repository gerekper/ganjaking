<?php

namespace ACA\GravityForms\Export\Model\Entry;

use ACP\Export;

class ItemList extends Export\Model {

	public function get_value( $id ) {
		$items = unserialize( (string) $this->column->get_raw_value( $id ), [ 'allowed_classes' => false ] );

		return implode( ', ', $items );
	}

}