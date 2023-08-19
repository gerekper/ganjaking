<?php

namespace ACA\WC\Search\Meta\Date;

use ACP;

class Timestamp extends ACP\Search\Comparison\Meta\DateTime\Timestamp {

	public function __construct( $meta_key, $type ) {
		parent::__construct( $meta_key, $type );

		$this->operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::GT,
			ACP\Search\Operators::LT,
			ACP\Search\Operators::BETWEEN,
			ACP\Search\Operators::TODAY,
			ACP\Search\Operators::PAST,
			ACP\Search\Operators::FUTURE,
			ACP\Search\Operators::BETWEEN,
			ACP\Search\Operators::IS_EMPTY,
			ACP\Search\Operators::NOT_IS_EMPTY,
			ACP\Search\Operators::LT_DAYS_AGO,
			ACP\Search\Operators::GT_DAYS_AGO,
		] );
	}

}