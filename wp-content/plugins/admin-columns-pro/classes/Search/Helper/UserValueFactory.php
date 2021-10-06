<?php

namespace ACP\Search\Helper;

use ACP\Search\Value;

class UserValueFactory {

	public function create_current_user() {
		return new Value(
			get_current_user_id(),
			Value::INT
		);
	}

}