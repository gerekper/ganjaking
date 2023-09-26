<?php

namespace ACA\JetEngine\Column\Relation;

use AC\Settings;
use ACA\JetEngine\Column\Relation;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Search;

class User extends Relation {

	protected function register_settings() {
		$this->add_setting( new Settings\Column\User( $this ) );
		$this->add_setting( new Settings\Column\UserLink( $this ) );
	}

	public function search() {
		return new Search\Comparison\Relation\User( $this->relation, $this->is_relation_parent() );
	}

	public function editing() {
		$storage = $this->is_relation_parent()
			? new Editing\Storage\RelationshipChildren( $this->relation )
			: new Editing\Storage\RelationshipParents( $this->relation );

		return new Editing\Service\Relation\User( $storage, $this->has_many() );
	}

}