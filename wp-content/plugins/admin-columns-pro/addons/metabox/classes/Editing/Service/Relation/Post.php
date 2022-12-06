<?php

namespace ACA\MetaBox\Editing\Service\Relation;

use ACA;
use ACP;

class Post extends ACA\MetaBox\Editing\Service\Relation {

	public function get_value( $id ) {
		return array_map( 'get_the_title', parent::get_value( $id ) );
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		$args = [
			'post_type' => $this->relation->get_related_field_settings()['post_type']
		];

		return new ACP\Helper\Select\Paginated\Posts( $s, $paged, $args );
	}

}