<?php

namespace ACP\Search\Helper\MetaQuery\Comparison;

use ACP\Search\Helper\MetaQuery;
use ACP\Search\Value;

class IsEmpty extends MetaQuery\Comparison {

	/**
	 * @param string $key
	 * @param Value  $value
	 */
	public function __construct( $key, Value $value ) {
		$value = new Value(
			'',
			$value->get_type()
		);

		parent::__construct( $key, 'NOT EXISTS', $value );
	}

	public function __invoke() {
		return [
			'relation' => 'OR',
			[
				'key'   => $this->key,
				'value' => $this->value->get_value(),
			],
			[
				'key'     => $this->key,
				'compare' => $this->operator,
			],
		];
	}

}