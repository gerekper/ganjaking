<?php

namespace ACP\Search\Helper\Sql\Comparison;

class NotContains extends Contains {

	public function is_negated() {
		return true;
	}

}