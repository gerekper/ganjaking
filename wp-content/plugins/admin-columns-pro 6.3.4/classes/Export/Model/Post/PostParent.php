<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class PostParent implements Service {

	public function get_value( $id ) {
		$parent_id = wp_get_post_parent_id( $id );

		return $parent_id
			? get_the_title( $parent_id )
			: '';
	}

}