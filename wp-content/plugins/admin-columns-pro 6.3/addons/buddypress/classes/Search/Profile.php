<?php

namespace ACA\BP\Search;

use ACA\BP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Profile extends Comparison {

	/**
	 * @var string
	 */
	protected $field;

	/**
	 * Profile constructor.
	 *
	 * @param Operators $operators
	 * @param string    $field
	 * @param string    $value
	 * @param Labels    $labels
	 */
	public function __construct( $operators, $field, $value, $labels = null ) {
		$this->field = $field;

		parent::__construct( $operators, $value, $labels );
	}

	/**
	 * @inheritDoc
	 */
	public function create_query_bindings( $operator, Value $value ) {
		global $wpdb, $bp;

		if ( $operator === Operators::IS_EMPTY ) {
			return $this->create_empty_bindings();
		}

		$bindings = new Bindings();
		$alias = 'bpx' . uniqid();

		$where = ComparisonFactory::create(
			$alias . '.value',
			$operator,
			$value
		)->prepare();

		$bindings->join( " INNER JOIN {$bp->profile->table_name_data} AS " . $alias . ' ON ( ' . $alias . ".user_id = {$wpdb->users}.ID )" );
		$bindings->where( '(' . $wpdb->prepare( $alias . '.field_id = %d', $this->field ) . ' AND ' . $where . ')' );

		return $bindings;
	}

	private function create_empty_bindings() {
		global $wpdb, $bp;

		$bindings = new Bindings();
		$alias_first = 'xpdf' . uniqid();
		$alias_second = 'xpds' . uniqid();

		$join = " LEFT JOIN {$bp->profile->table_name_data} AS {$alias_first} ON ( {$wpdb->users}.ID = {$alias_first}.user_id )";
		$join .= $wpdb->prepare( " AND {$alias_first}.field_id = %d", $this->field );
		$join .= " LEFT JOIN {$bp->profile->table_name_data} AS {$alias_second} ON ( {$wpdb->users}.ID = {$alias_second}.user_id )";

		$bindings->join( $join );
		$bindings->where( $wpdb->prepare( "( {$alias_first}.user_id IS NULL OR ( {$alias_second}.field_id = %d AND {$alias_second}.value IN ( '0', 'no', 'false', 'off', '' ) ) )", $this->field ) );

		return $bindings;
	}

}