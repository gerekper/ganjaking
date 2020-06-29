<?php

namespace ACP\Search\Helper\Sql\Comparison;

class NotIn extends In {

	/**
	 * @inheritDoc
	 */
	public function is_negated() {
		return true;
	}

}