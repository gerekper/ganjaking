<?php

namespace ACA\MetaBox\Editing\Service\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;

class Term extends ACA\MetaBox\Editing\Service\Relation {

	public function get_value( $id ) {
		$results = [];

		foreach ( parent::get_value( $id ) as $term_id ) {
			$results[ $term_id ] = ac_helper()->taxonomy->get_term_display_name( get_term( $term_id ) );
		}

		return $results;
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->relation->get_related_field_settings()['taxonomy'],
		] );
	}

}