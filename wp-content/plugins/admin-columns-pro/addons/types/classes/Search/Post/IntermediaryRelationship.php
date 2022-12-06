<?php

namespace ACA\Types\Search\Post;

use ACP\Search\Operators;

class IntermediaryRelationship extends Relationship {

	public function __construct( $relationship, $related_post_type, $role ) {
		parent::__construct( $relationship, $related_post_type, $role, 'intermediary' );
	}

	protected function get_default_operators() {
		return new Operators( [
			Operators::EQ,
		] );
	}

}