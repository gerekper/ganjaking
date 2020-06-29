<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Value;
use Exception;

class Past extends Comparison {

	public function __construct( $column, Value $value ) {
		parent::__construct( $column, null, $value );
	}

	protected function get_statement() {
		return $this->column . ' < ?';
	}

	/**
	 * @param Value $value
	 *
	 * @return Comparison
	 * @throws Exception
	 */
	public function bind_value( Value $value ) {
		$value_factory = new DateValueFactory( $value->get_type() );

		return parent::bind_value( $value_factory->create_today() );
	}

}