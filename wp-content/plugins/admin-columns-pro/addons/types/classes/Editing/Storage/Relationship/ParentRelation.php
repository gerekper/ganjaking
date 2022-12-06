<?php

namespace ACA\Types\Editing\Storage\Relationship;

use ACA\Types;

class ParentRelation extends Types\Editing\Storage\Relationship {

	protected function connect_post( $source_id, $connect_id ) {
		toolset_connect_posts( $this->relationship, $source_id, $connect_id );
	}

	protected function disconnect_post( $source_id, $connect_id ) {
		toolset_disconnect_posts( $this->relationship, $source_id, $connect_id );
	}
}