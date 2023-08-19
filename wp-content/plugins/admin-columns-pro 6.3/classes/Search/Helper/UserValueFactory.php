<?php

namespace ACP\Search\Helper;

use ACP\Search\Value;

class UserValueFactory {

	public function create_current_user( $type = Value::INT ) {
		return new Value(
			get_current_user_id(),
			$type
		);
	}

}