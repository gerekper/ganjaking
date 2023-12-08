<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Taxonomy;

use WP_Term;

interface LabelFormatter {

	public function format_label( WP_Term $term ): string;

	public function format_label_unique( WP_Term $term ): string;

}