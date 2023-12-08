<?php

namespace ACA\MetaBox\Search\Comparison;

use ACP;

class MultiSelect extends Select {

	protected function get_meta_query( string $operator, ACP\Search\Value $value ): array {
		$comparison = ACP\Search\Helper\MetaQuery\SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

}