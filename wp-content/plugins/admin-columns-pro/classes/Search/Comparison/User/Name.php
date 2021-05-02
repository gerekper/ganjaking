<?php

namespace ACP\Search\Comparison\User;

use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Name extends Comparison {

	/**
	 * @var array
	 */
	private $meta_keys;

	/**
	 * @param array $meta_keys
	 */
	public function __construct( array $meta_keys ) {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
		] );

		$this->meta_keys = $meta_keys;

		parent::__construct( $operators );
	}

	public function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings();
		$bindings->meta_query(
			$this->get_meta_query( $operator, $value )
		);

		return $bindings;
	}

	protected function get_meta_query( $operator, Value $value ) {
		$meta_query = [
			'relation' => 'OR',
		];

		foreach ( $this->meta_keys as $key ) {
			$mq = ComparisonFactory::create(
				$key,
				$operator,
				$value
			);
			$meta_query[] = $mq();
		}

		return $meta_query;
	}

}