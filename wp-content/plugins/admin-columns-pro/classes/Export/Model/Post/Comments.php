<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class Comments implements Service {

	public function get_value( $id ) {
		return (string) wp_count_comments( $id )->total_comments;
	}

}