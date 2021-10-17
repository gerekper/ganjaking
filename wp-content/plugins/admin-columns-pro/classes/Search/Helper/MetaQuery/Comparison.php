<?php

namespace ACP\Search\Helper\MetaQuery;

use ACP\Search\Value;

class Comparison {

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $operator;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * @param string $key
	 * @param string $operator
	 * @param Value  $value
	 */
	public function __construct( $key, $operator, Value $value ) {
		$this->key = $key;
		$this->operator = $operator;
		$this->value = $value;
	}

	/**
	 * @return array
	 */
	public function __invoke() {
		switch ( $this->value->get_type() ) {
			case Value::INT:
				$type = 'NUMERIC';

				break;
			case Value::DECIMAL:
				$type = 'DECIMAL';

				break;
			case Value::DATE:
				$type = 'DATE';

				break;
			default:
				$type = 'CHAR';
		}

		return [
			'key'     => $this->key,
			'value'   => $this->value->get_value(),
			'compare' => $this->operator,
			'type'    => $type,
		];
	}

}