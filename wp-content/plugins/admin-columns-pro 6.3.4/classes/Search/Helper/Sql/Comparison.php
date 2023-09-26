<?php

namespace ACP\Search\Helper\Sql;

use ACP\Search\Value;

class Comparison extends Statement {

	/**
	 * @var string
	 */
	protected $column;

	/**
	 * @var string
	 */
	protected $operator;

	/**
	 * @param string $column
	 * @param string $operator
	 * @param Value  $value
	 */
	public function __construct( $column, $operator, Value $value ) {
		$this->column = $column;
		$this->operator = $operator;

		$this->bind_value( $value );

		parent::__construct( $this->get_statement() );
	}

	/**
	 * @return string
	 */
	protected function get_statement() {
		return sprintf( '%s %s ?',
			$this->column,
			$this->operator
		);
	}

	/**
	 * @return string
	 */
	public function __invoke() {
		return $this->prepare();
	}

}