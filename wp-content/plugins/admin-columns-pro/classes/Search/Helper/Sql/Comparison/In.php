<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Value;
use LogicException;

class In extends Comparison
	implements Negatable {

	/**
	 * @param string $column
	 * @param Value  $value
	 */
	public function __construct( $column, Value $value ) {
		$operator = 'IN';

		if ( $this->is_negated() ) {
			$operator = 'NOT ' . $operator;
		}

		parent::__construct( $column, $operator, $value );
	}

	public function get_statement() {
		return $this->column . ' ' . $this->operator . ' (?)';
	}

	public function is_negated() {
		return false;
	}

	public function bind_value( Value $value ) {
		if ( ! is_array( $value->get_value() ) ) {
			throw new LogicException( 'Value can only be an array.' );
		}

		$type = $value->get_type();
		$values = $value->get_value();

		foreach ( $values as $new_value ) {
			parent::bind_value( new Value( $new_value, $type ) );
		}

		return $this;
	}

	public function prepare() {
		$this->statement = str_replace( '?', implode( ', ', array_fill( 0, count( $this->values ), '?' ) ), $this->statement );

		return parent::prepare();
	}

}