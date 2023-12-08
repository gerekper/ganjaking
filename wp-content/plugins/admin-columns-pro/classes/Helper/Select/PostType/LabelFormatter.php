<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\PostType;

use WP_Post_Type;

interface LabelFormatter {

	public function format_label( WP_Post_Type $post_type ): string;

	public function format_label_unique( WP_Post_Type $post_type ): string;

}