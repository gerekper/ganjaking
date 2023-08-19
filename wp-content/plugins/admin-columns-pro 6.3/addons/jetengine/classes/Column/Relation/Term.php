<?php

namespace ACA\JetEngine\Column\Relation;

use ACA\JetEngine\Column\Relation;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Search;

class Term extends Relation {

	protected function register_settings() {
		$this->add_setting( new \AC\Settings\Column\Term( $this ) );
	}

	public function search() {
		return new Search\Comparison\Relation\Term( $this->relation, $this->is_relation_parent(), $this->get_related_object() );
	}

	public function editing() {
		$storage = $this->is_relation_parent()
			? new Editing\Storage\RelationshipChildren( $this->relation )
			: new Editing\Storage\RelationshipParents( $this->relation );

		return new Editing\Service\Relation\Term( $storage, $this->has_many(), $this->get_related_object() );
	}

}