<?php

namespace ACP\Editing\Model\Taxonomy;

use AC;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper\Select;

class TaxonomyParent extends Model\Taxonomy
	implements PaginatedOptions {

	public function get_edit_value( $id ) {
		$term = $this->get_term( $id );

		if ( ! $term || 0 === $term->parent ) {
			return false;
		}

		$parent = $this->get_term( $term->parent );

		if ( ! $parent ) {
			return false;
		}

		return [
			$parent->term_id => $parent->name,
		];
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$entities = new Select\Entities\Taxonomy( [
			'search'       => $search,
			'page'         => $page,
			'exclude_tree' => $id,
			'taxonomy'     => $this->column->get_taxonomy(),
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);

	}

	public function get_view_settings() {
		return [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'multiple'      => false,
			'clear_button'  => true,
		];
	}

	public function save( $id, $value ) {
		return $this->update_term( $id, [ 'parent' => $value ] );
	}

	private function get_term( $id ) {
		return get_term_by( 'id', $id, $this->column->get_taxonomy() );
	}

}