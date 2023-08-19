<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class Title implements Service {

	public function get_value( $id ) {
		return get_the_title( (int) $id );
	}

}