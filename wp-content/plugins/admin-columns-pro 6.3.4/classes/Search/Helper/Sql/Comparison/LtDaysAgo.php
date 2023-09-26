<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Value;
use Exception;

class LtDaysAgo extends Between {

	/**
	 * @param Value $value
	 *
	 * @return Comparison
	 * @throws Exception
	 */
	public function bind_value( Value $value ) {
		$value_factory = new DateValueFactory( $value->get_type() );

		return parent::bind_value( $value_factory->create_less_than_days_ago( $value->get_value() ) );
	}

}