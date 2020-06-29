<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Value;

class Posts extends Post {

	protected function get_meta_query( $operator, Value $value ) {
		$comparison = SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

}