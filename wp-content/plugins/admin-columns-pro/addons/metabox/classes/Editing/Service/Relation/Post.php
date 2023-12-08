<?php

namespace ACA\MetaBox\Editing\Service\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA;
use ACP\Helper\Select\Post\PaginatedFactory;

class Post extends ACA\MetaBox\Editing\Service\Relation {

	public function get_value( $id ) {
		return array_map( 'get_the_title', parent::get_value( $id ) );
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'paged'     => $page,
			's'         => $search,
			'post_type' => $this->relation->get_related_field_settings()['post_type'],
		] );
	}

}