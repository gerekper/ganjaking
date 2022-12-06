<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Filtering;
use ACP\Search;

class Color extends Field {

	use Editing\DefaultServiceTrait,
		Sorting\DefaultSortingTrait;

	public function get_value( $id ) {
		return ac_helper()->string->get_color_block( $this->get_raw_value( $id ) );
	}

	public function filtering() {
		return new Filtering\Model\Meta( $this->column );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), $this->column->get_meta_type() );
	}

}