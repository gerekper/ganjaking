<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class Permalink implements Service {

	public function get_value( $id ) {
		return urldecode( get_permalink( $id ) );
	}

}