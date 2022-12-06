<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Search;

class Time extends Field {

	use Editing\DefaultServiceTrait,
		Sorting\DefaultSortingTrait;

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), $this->column->get_meta_type() );
	}

}