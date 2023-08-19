<?php

namespace ACA\Pods\Field;

use AC\Settings;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Filtering;
use ACP\Search;

class Text extends Field {

	use Editing\DefaultServiceTrait,
		Sorting\DefaultSortingTrait;

	public function filtering() {
		return new Filtering\Model\Meta( $this->column );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), $this->column->get_meta_type() );
	}

	public function get_dependent_settings() {
		return [
			new Settings\Column\CharacterLimit( $this->column ),
		];
	}

}