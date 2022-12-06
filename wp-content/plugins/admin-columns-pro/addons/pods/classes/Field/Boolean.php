<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Filtering;
use ACA\Pods\Sorting;
use ACP\Search;

class Boolean extends Field {

	use Sorting\DefaultSortingTrait,
		Editing\DefaultServiceTrait;

	public function get_value( $id ) {
		return ac_helper()->icon->yes_or_no( '1' === $this->get_raw_value( $id ) );
	}

	public function filtering() {
		return new Filtering\TrueFalse( $this->column );
	}

	public function search() {
		return new Search\Comparison\Meta\Checkmark( $this->column->get_meta_key(), $this->column->get_meta_type() );
	}

}