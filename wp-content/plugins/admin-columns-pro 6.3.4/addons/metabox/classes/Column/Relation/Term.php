<?php

namespace ACA\MetaBox\Column\Relation;

use AC;
use ACA\MetaBox\Column\Relation;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;

class Term extends Relation {

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\Term( $this ) );
	}

	public function search() {
		return new Search\Comparison\Relation\Term( $this->relation );
	}

	public function editing() {
		return new Editing\Service\Relation\Term( $this->relation );
	}

}