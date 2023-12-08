<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Post\GroupFormatter;

use ACP\Helper\Select\Post\GroupFormatter;
use WP_Post;

class PostType implements GroupFormatter {

	public function format( WP_Post $post ): string {
		$post_type_object = get_post_type_object( $post->post_type );

		return $post_type_object->labels->singular_name ?? $post->post_type;
	}

}