<?php

namespace ACP\Export\Model\User;

use ACP\Export\Service;

class Posts implements Service {

	public function get_value( $id ) {
		return count_user_posts( $id );
	}

}