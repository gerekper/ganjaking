<?php
declare( strict_types=1 );

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class Id implements Service {

	public function get_value( $id ) {
		return (string) $id;
	}

}