<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting\DefaultSortingTrait;
use ACP\Search;

class Code extends Field {

	use Editing\DefaultServiceTrait,
		DefaultSortingTrait;

	public function get_value( $id ) {
		return ac_helper()->html->codearea( parent::get_value( $id ) );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), $this->column->get_meta_type() );
	}

}