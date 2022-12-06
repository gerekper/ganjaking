<?php

namespace ACA\WC\Search\User;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class TotalSales extends Comparison {

	/**
	 * @var string[]
	 */
	private $statuses;

	public function __construct( $statuses ) {
		$operators = new Operators( [
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );

		$this->statuses = $statuses;

		parent::__construct( $operators, Value::INT );
	}

	/**
	 * @inheritDoc
	 */
	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();

		$user_ids = $this->get_user_ids( $operator, $value );
		$user_ids = array_filter( $user_ids, 'is_numeric' );

		// Force no results
		if ( ! $user_ids ) {
			$user_ids = [ 0 ];
		}

		return $bindings->where( sprintf( "{$wpdb->users}.ID IN( %s )", implode( ',', $user_ids ) ) );
	}

	/**
	 * @param string $operator
	 * @param Value  $value
	 *
	 * @return array
	 */
	protected function get_user_ids( $operator, $value ) {
		global $wpdb;

		$having = ComparisonFactory::create( 'total', $operator, $value );
		$status_where = ! empty( $this->statuses )
			? sprintf( "AND p.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->statuses ) ) )
			: '';

		$sql = "
				SELECT uo.user_id, SUM(uo.total) as total
				FROM (
					SELECT p.ID, pm.meta_value as user_id, pm2.meta_value as total
					FROM {$wpdb->posts} AS p
					JOIN {$wpdb->postmeta} as pm ON p.ID = pm.post_id AND pm.meta_key = '_customer_user'
					JOIN {$wpdb->postmeta} as pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_order_total'
					WHERE p.post_type = 'shop_order'
					{$status_where}
				) as uo
				GROUP BY uo.user_id
				HAVING {$having->prepare()}";

		return $wpdb->get_col( $sql );
	}

}