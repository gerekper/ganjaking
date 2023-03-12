<?php

namespace ACP\Export\Model\Term;

use ACP\Export\Service;

class Posts implements Service {

	public function get_value( $id ) {
		return (string) get_term( $id )->count;
	}

}