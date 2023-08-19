<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Value;
use Exception;

class Today extends Between {

	/**
	 * @param Value $value
	 *
	 * @return Between
	 * @throws Exception
	 */
	public function bind_value( Value $value ) {
		$value_factory = new DateValueFactory( $value->get_type() );

		return parent::bind_value( $value_factory->create_range_today() );
	}

}