<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use DateTime;
use Exception;

class GtDaysAgo extends Comparison {

	public function __construct( $column, Value $value ) {
		parent::__construct( $column, Operators::LT, $value );
	}

	/**
	 * @param Value $value
	 *
	 * @return Comparison
	 * @throws Exception
	 */
	public function bind_value( Value $value ) {
		$date = new DateTime();
		$date->modify( sprintf( '-%s days', $value->get_value() ) );

		$value_factory = new DateValueFactory( $value->get_type() );

		return parent::bind_value( $value_factory->create_single_day( $date ) );
	}

}