<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP;
use ACP\Search\Value;

class TableStorage extends ACP\Search\Comparison {

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var string
	 */
	protected $column;

	public function __construct( $operators, $table, $column, $value_type = null, ACP\Search\Labels $labels = null ) {
		$this->table = $table;
		$this->column = $column;

		parent::__construct( $operators, $value_type, $labels );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$binding = new ACP\Search\Query\Bindings\Post();
		$binding->where( 'ID in(' . $this->get_subquery( $operator, $value ) . ')' );

		return $binding;
	}

	protected function get_subquery( $operator, Value $value ) {
		$where = ACP\Search\Helper\Sql\ComparisonFactory::create( $this->column, $operator, $value );

		return "SELECT ID FROM {$this->table} WHERE " . $where->prepare();
	}

}