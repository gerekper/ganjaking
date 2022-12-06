<?php

namespace ACA\MetaBox\Editing\Service\Relation;

use ACA;
use ACP;

class Term extends ACA\MetaBox\Editing\Service\Relation {

	public function get_value( $id ) {
		$results = [];

		foreach( parent::get_value( $id ) as $term_id ){
			$results[ $term_id ] = ac_helper()->taxonomy->get_term_display_name( get_term( $term_id ) );
		}

		return $results;
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		return new ACP\Helper\Select\Paginated\Terms( $s, $paged, [ $this->relation->get_related_field_settings()['taxonomy'] ] );
	}

}