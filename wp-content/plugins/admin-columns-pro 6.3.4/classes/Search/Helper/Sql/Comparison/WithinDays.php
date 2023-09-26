<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Value;
use DateTime;
use Exception;

class WithinDays extends Between {

	/**
	 * @param Value $value
	 *
	 * @return Comparison
	 * @throws Exception
	 */
	public function bind_value( Value $value ) {
		$date = new DateTime();
		$date->modify( sprintf( '+%s days', $value->get_value() ) );
		$date->setTime( 23, 59 );
		$value_factory = new DateValueFactory( $value->get_type() );

		return parent::bind_value( $value_factory->create_range( new DateTime(), $date ) );
	}

}