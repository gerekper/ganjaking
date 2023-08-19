<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Value;

class PaymentAmount extends ACP\Search\Comparison {

	public function __construct() {
		$operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::LT,
			ACP\Search\Operators::GT,
			ACP\Search\Operators::BETWEEN,
		] );

		parent::__construct( $operators, ACP\Search\Value::DECIMAL );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$comparison = ACP\Search\Helper\Sql\ComparisonFactory::create( 'payment_amount', $operator, $value );

		return ( new Bindings )->where( $comparison() );
	}

}