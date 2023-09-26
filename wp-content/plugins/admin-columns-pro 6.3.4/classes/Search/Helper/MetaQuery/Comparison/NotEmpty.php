<?php

namespace ACP\Search\Helper\MetaQuery\Comparison;

use ACP\Search\Helper\MetaQuery;
use ACP\Search\Value;

class NotEmpty extends MetaQuery\Comparison {

	/**
	 * @param string $key
	 * @param Value  $value
	 */
	public function __construct( $key, Value $value ) {
		$value = new Value(
			'',
			$value->get_type()
		);

		parent::__construct( $key, '!=', $value );
	}

}