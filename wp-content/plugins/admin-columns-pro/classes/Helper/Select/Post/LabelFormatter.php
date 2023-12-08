<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Post;

use WP_Post;

interface LabelFormatter {

	public function format_label( WP_Post $post ): string;

	public function format_label_unique( WP_Post $post ): string;

}