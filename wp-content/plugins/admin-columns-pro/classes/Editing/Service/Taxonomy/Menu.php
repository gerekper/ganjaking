<?php

namespace ACP\Editing\Service\Taxonomy;

use ACP\Editing\Service;
use WP_Term;

class Menu extends Service\Menu {

	/**
	 * @param int $id
	 *
	 * @return string|false
	 */
	protected function get_title( $id ) {
		$term = get_term_by( 'id', $id, $this->object_type );

		if ( ! $term instanceof WP_Term ) {
			return false;
		}

		return $term->name;
	}

}