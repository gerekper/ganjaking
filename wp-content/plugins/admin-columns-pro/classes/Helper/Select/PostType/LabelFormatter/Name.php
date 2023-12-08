<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\PostType\LabelFormatter;

use ACP\Helper\Select\PostType\LabelFormatter;
use WP_Post_Type;

class Name implements LabelFormatter {

	public function format_label( WP_Post_Type $post_type ): string {
        return sprintf( '%s (%s)', $post_type->labels->singular_name, $post_type->name );
	}

	public function format_label_unique( WP_Post_Type $post_type ): string {
		return sprintf( '%s (%s)', $post_type->labels->singular_name, $post_type->name );
	}

}