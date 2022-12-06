<?php

namespace ACA\Pods\Field;

use AC\Settings;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Search;

class Wysiwyg extends Field {

	use Editing\DefaultServiceTrait,
		Sorting\DefaultSortingTrait;

	public function get_value( $id ) {
		return $this->column->get_formatted_value( parent::get_value( $id ) );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->column->get_meta_key(), $this->column->get_meta_type() );
	}

	public function get_dependent_settings() {
		return [
			new Settings\Column\WordLimit( $this->column ),
		];
	}

}