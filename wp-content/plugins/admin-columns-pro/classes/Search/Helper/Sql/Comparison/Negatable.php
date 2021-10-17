<?php

namespace ACP\Search\Helper\Sql\Comparison;

interface Negatable {

	/**
	 * @return bool
	 */
	public function is_negated();

}