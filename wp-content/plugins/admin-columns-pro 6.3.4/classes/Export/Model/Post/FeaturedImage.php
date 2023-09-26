<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class FeaturedImage implements Service {

	public function get_value( $id ) {
		$attachment_id = get_post_thumbnail_id( $id );

		return $attachment_id
			? wp_get_attachment_url( $attachment_id )
			: '';
	}

}