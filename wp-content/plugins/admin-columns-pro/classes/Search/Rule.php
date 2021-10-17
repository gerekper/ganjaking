<?php

namespace ACP\Search;

final class Rule {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $operator;

	/**
	 * @var Value
	 */
	private $value;

	/**
	 * @param string $name
	 * @param string $operator
	 * @param Value  $value
	 */
	public function __construct( $name, $operator, Value $value ) {
		$this->name = $name;
		$this->operator = $operator;
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_operator() {
		return $this->operator;
	}

	/**
	 * @return Value
	 */
	public function get_value() {
		return $this->value;
	}

}