<?php

namespace ACP\Search\Comparison\Post;

use AC\MetaType;
use ACP\Search\Comparison;

class FeaturedImage extends Comparison\Meta\Image {

	public function __construct( $post_type ) {
		parent::__construct( '_thumbnail_id', MetaType::POST, $post_type );
	}

}