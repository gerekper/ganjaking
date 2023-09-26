<?php

namespace ACA\ACF\Column\FieldGroup;

use AC;
use ACA\ACF\Search;
use ACP\Search\Searchable;

class Location extends AC\Column implements Searchable {

	public function __construct() {
		$this
			->set_type( 'acf-location' )
			->set_original( true );
	}

	public function search() {
		return new Search\Comparison\FieldGroup\Location();
	}

}