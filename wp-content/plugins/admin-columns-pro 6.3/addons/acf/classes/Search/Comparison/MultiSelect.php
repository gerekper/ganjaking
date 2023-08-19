<?php

namespace ACA\ACF\Search\Comparison;

use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Value;

class MultiSelect extends Select {

	protected function get_meta_query( $operator, Value $value ) {
		$comparison = SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

}