<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Taxonomy\GroupFormatter;

use ACP\Helper\Select\Taxonomy\GroupFormatter;
use WP_Term;

class Taxonomy implements GroupFormatter {

	public function format( WP_Term $term ): string {
		return ac_helper()->taxonomy->get_taxonomy_label( $term->taxonomy );
	}

}