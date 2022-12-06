<?php

namespace ACA\WC\Search\User;

use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class OrderCount extends Comparison {

	/**
	 * @var array
	 */
	protected $status;

	public function __construct( array $status = [] ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::IS_EMPTY,
			Operators::BETWEEN,
		] );

		$this->status = $status;

		parent::__construct( $operators );
	}

	/**
	 * @inheritDoc
	 */
	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();

		if ( Operators::IS_EMPTY === $operator || ( Operators::EQ === $operator && 0 === (int) $value->get_value() ) ) {
			return $bindings->where( $wpdb->users . ".ID NOT IN( SELECT meta_value FROM wp_postmeta WHERE meta_key = '_customer_user' )" );
		}

		switch ( $operator ) {
			case Operators::LT;
				$having = sprintf( 'HAVING orders < %d', $value->get_value() );
				break;
			case Operators::GT;
				$having = sprintf( 'HAVING orders > %d', $value->get_value() );
				break;
			case Operators::BETWEEN:
				$values = $value->get_value();
				$having = sprintf( 'HAVING orders >= %d AND orders <= %s', $values[0], $values[1] );

				break;
			default:
				$having = sprintf( 'HAVING orders = %d', $value->get_value() );
		}

		$where = $this->status
			? sprintf( "AND p.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->status ) ) )
			: '';

		$sql = "SELECT pm.meta_value as user_id, count(*) as orders
				FROM {$wpdb->posts} as p
				INNER JOIN {$wpdb->postmeta} as pm 
					ON p.ID = pm.post_id
				WHERE post_type = 'shop_order'
					AND pm.meta_key = '_customer_user'
					AND p.post_status <> 'trash'
					AND p.post_status <> 'auto-draft'
					{$where}
				GROUP BY pm.meta_value
				{$having}";

		$user_ids = $wpdb->get_col( $sql );

		if ( empty( $user_ids ) ) {
			$user_ids = [ 0 ];
		}

		$user_ids = array_filter( $user_ids, 'is_numeric' );
		$where = $wpdb->users . '.ID IN( ' . implode( ',', $user_ids ) . ')';

		return $bindings->where( $where );
	}

}