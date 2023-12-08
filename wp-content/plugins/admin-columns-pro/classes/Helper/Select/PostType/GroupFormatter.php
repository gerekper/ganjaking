<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\PostType;

use WP_Post_Type;

interface GroupFormatter {

	public function format( WP_Post_Type $post_type ): string;

}