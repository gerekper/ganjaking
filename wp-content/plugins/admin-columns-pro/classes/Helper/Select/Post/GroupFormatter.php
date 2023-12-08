<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Post;

use WP_Post;

interface GroupFormatter {

	public function format( WP_Post $post ): string;

}