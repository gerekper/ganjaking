<?php

namespace ACP\Search\Helper\Sql\Comparison;

class NotLike extends Like {

	/**
	 * @inheritDoc
	 */
	public function is_negated() {
		return true;
	}

}