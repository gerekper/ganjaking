<?php

namespace ACA\ACF\Search\Comparison;

use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Value;

class Taxonomies extends Taxonomy {

	protected function get_meta_query( string $operator, Value $value ): array {
		$comparison = SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

}