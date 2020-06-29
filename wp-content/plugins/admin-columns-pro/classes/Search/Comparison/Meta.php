<?php

namespace ACP\Search\Comparison;

use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

abstract class Meta extends Comparison {

	/**
	 * @var string
	 */
	protected $meta_key;

	/**
	 * @var string
	 */
	protected $meta_type;

	/**
	 * Meta constructor.
	 *
	 * @param Operators $operators
	 * @param string    $meta_key
	 * @param string    $meta_type
	 * @param string    $type
	 * @param Labels    $labels
	 */
	public function __construct( $operators, $meta_key, $meta_type, $type = null, $labels = null ) {
		$this->meta_key = $meta_key;
		$this->meta_type = $meta_type;

		parent::__construct( $operators, $type, $labels );
	}

	/**
	 * @return string
	 */
	protected function get_meta_key() {
		return $this->meta_key;
	}

	/**
	 * @return string
	 */
	public function get_meta_type() {
		return $this->meta_type;
	}

	/**
	 * @inheritDoc
	 */
	public function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings();
		$bindings->meta_query(
			$this->get_meta_query( $operator, $value )
		);

		return $bindings;
	}

	/**
	 * Template function that should work most of the cases
	 *
	 * @param string $operator
	 * @param Value  $value
	 *
	 * @return array
	 */
	protected function get_meta_query( $operator, Value $value ) {
		$comparison = ComparisonFactory::create(
			$this->meta_key,
			$operator,
			$value
		);

		return $comparison();
	}

}