<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\OptionsFactory;

use ACP\Helper\Select\PostType\GroupFormatter\Type;
use ACP\Helper\Select\PostType\Groups;
use ACP\Helper\Select\PostType\LabelFormatter\Name;
use ACP\Helper\Select\PostType\Options;

class PostType {

	public function create( array $args = [] ): Groups {
		$post_types = get_post_types( $args, 'objects' );

		return new Groups(
			new Options( $post_types, new Name() ),
			new Type()
		);
	}

}