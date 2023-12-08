<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Taxonomy;

use WP_Term;

interface GroupFormatter {

	public function format( WP_Term $term ): string;

}