<?php

namespace ACA\GravityForms\Column\Entry;

use ACA\GravityForms\Column;
use GFAPI;

class Product extends Column\Entry {

	public function get_value( $id ) {
		$entry = GFAPI::get_entry( $id );
		$title_key = $this->get_field_id() . '.1';
		$price_key = $this->get_field_id() . '.2';
		$quantity_key = $this->get_field_id() . '.3';

		$value = '';
		if ( isset( $entry[ $quantity_key ] ) && $entry[ $quantity_key ] ) {
			$value .= $entry[ $quantity_key ] . 'x ';
		}

		$value .= $entry[ $title_key ];
		if ( $entry[ $price_key ] ) {
			$value .= sprintf( '<br />(%s)', $entry[ $price_key ] );
		}

		return $value;
	}

}