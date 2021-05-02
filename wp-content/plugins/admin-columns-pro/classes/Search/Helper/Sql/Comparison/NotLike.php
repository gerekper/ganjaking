<?php

namespace ACP\Search\Helper\Sql\Comparison;

class NotLike extends Like {

	public function is_negated() {
		return true;
	}

}