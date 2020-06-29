<?php

namespace ACP\Editing\Model\Taxonomy;

use ACP\Editing\Model;
use WP_Term;

class Menu extends Model\Menu {

	/**
	 * @param int $id
	 *
	 * @return string|false
	 */
	protected function get_title( $id ) {
		$term = get_term_by( 'id', $id, $this->column->get_taxonomy() );

		if ( ! $term instanceof WP_Term ) {
			return false;
		}

		return $term->name;
	}

}