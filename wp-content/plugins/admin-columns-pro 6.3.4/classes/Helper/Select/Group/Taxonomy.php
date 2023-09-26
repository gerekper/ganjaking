<?php

namespace ACP\Helper\Select\Group;

use AC;

class Taxonomy extends AC\Helper\Select\Group {

	/**
	 * @param /WP_Term $term
	 * @param AC\Helper\Select\Option $option
	 *
	 * @return string
	 */
	public function get_label( $term, AC\Helper\Select\Option $option ) {
		$taxonomy = get_taxonomy( $term->taxonomy );

		return $taxonomy->label;
	}

}