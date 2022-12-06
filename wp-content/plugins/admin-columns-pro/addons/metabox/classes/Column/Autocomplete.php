<?php

namespace ACA\MetaBox\Column;

use ACA;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;

class Autocomplete extends Select {

	public function is_multiple() {
		return true;
	}

	public function is_ajax() {
		return filter_var( $this->get_field_setting( 'options' ), FILTER_VALIDATE_URL );
	}

	public function search() {
		return ( new Search\Factory\Autocomplete() )->create( $this );
	}

	public function editing() {
		return ( new Editing\ServiceFactory\Autocomplete() )->create( $this );
	}

}