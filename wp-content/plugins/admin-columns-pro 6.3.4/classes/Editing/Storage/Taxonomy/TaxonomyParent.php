<?php

namespace ACP\Editing\Storage\Taxonomy;

class TaxonomyParent extends Field {

	public function __construct( $taxonomy ) {
		parent::__construct( $taxonomy, 'parent' );
	}

	public function get( int $id ) {
		$parent_id = parent::get( $id );

		if ( ! $parent_id ) {
			return false;
		}

		$parent = get_term_by( 'id', $parent_id, $this->taxonomy );

		if ( ! $parent ) {
			return false;
		}

		return [
			$parent->term_id => $parent->name,
		];
	}

}