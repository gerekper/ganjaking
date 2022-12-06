<?php

namespace ACA\MetaBox\Column\Relation;

use ACA\MetaBox\Column\Relation;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACP;

class User extends Relation {

	public function register_settings() {
		$this->add_setting( new ACP\Settings\Column\User( $this ) );
	}

	public function search() {
		return new Search\Comparison\Relation\User( $this->relation );
	}

	public function editing() {
		return new Editing\Service\Relation\User( $this->relation );
	}

}