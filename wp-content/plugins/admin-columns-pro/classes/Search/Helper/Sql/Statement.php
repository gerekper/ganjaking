<?php

namespace ACP\Search\Helper\Sql;

use ACP\Search\Value;
use LogicException;

class Statement {

	/**
	 * @var Value[]
	 */
	protected $values = [];

	/**
	 * @var string
	 */
	protected $statement;

	/**
	 * @var string
	 */
	protected $value_type;

	/**
	 * @param string $statement
	 */
	public function __construct( $statement ) {
		$this->statement = $statement;
	}

	/**
	 * @param Value $value
	 *
	 * @return $this
	 */
	public function bind_value( Value $value ) {
		$this->values[] = $value;

		return $this;
	}

	/**
	 * Prepare string for safe usage
	 * @return string
	 */
	public function prepare() {
		global $wpdb;

		if ( substr_count( $this->statement, '?' ) != count( $this->values ) ) {
			throw new LogicException( 'Amount of parameters and variables must be the same.' );
		}

		$statement = $this->statement;
		$values = [];

		foreach ( $this->values as $value ) {
			$type = $value->get_type() === Value::INT
				? '%d'
				: '%s';

			$statement = substr_replace(
				$statement,
				$type,
				strpos( $statement, '?' ),
				1
			);

			$values[] = $value->get_value();
		}

		return $wpdb->prepare( $statement, $values );
	}

}