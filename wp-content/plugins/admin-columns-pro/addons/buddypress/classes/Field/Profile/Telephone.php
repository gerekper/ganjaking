<?php

namespace ACA\BP\Field\Profile;

use ACA\BP\Field\Profile;
use ACA\BP\Filtering;
use ACA\BP\Search;

class Telephone extends Profile {

	public function search() {
		return new Search\Profile\Text( $this->column->get_buddypress_field_id() );
	}

}