<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Helper\UserValueFactory;
use ACP\Search\Value;

class CurrentUser extends Comparison {

	public function __construct( $column, Value $value ) {
		parent::__construct( $column, null, $value );
	}

	protected function get_statement() {
		return $this->column . ' = ?';
	}

	public function bind_value( Value $value ) {
		return parent::bind_value( ( new UserValueFactory() )->create_current_user() );
	}

}