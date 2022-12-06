<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use WP_User_Query;

class TotalSales extends AbstractModel {

	/**
	 * @var array
	 */
	private $status;

	public function __construct( array $status = [ 'wc-completed' ] ) {
		parent::__construct();

		$this->status = $status;
	}

	public function get_sorting_vars(): array {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		$status_where = ! empty( $this->status )
			? sprintf( " AND acsort_posts.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->status ) ) )
			: '';

		$sql_parts = [
			'select' => "
				SELECT acsort_pm1.meta_value as user_id, SUM( acsort_pm2.meta_value ) as total_sales",
			'from'   => "
				FROM {$wpdb->postmeta} as acsort_pm1",
			'join'   => [
				"INNER JOIN {$wpdb->postmeta} as acsort_pm2 
					ON acsort_pm1.post_id = acsort_pm2.post_id 
					AND acsort_pm2.meta_key = '_order_total'",
				"INNER JOIN {$wpdb->posts} as acsort_posts 
					ON acsort_pm1.post_id = acsort_posts.ID ",
			],
			'where'  => "    
				WHERE acsort_pm1.meta_key = '_customer_user' 
				AND acsort_posts.post_type = 'shop_order'
				{$status_where}
			",
			'group'  => [
				'GROUP BY user_id',
			],
		];

		$sub_query = $this->built_sql( $sql_parts );

		$query->query_from .= " LEFT JOIN ( {$sub_query} ) as acsort_user2 on {$wpdb->users}.ID = acsort_user2.user_id ";
		$query->query_orderby = sprintf( "ORDER BY %s",
			SqlOrderByFactory::create( 'total_sales', $this->get_order() )
		);
	}

	private function built_sql( array $parts ): string {
		$sql = '';

		foreach ( $parts as $part ) {
			if ( is_array( $part ) ) {
				$sql .= $this->built_sql( $part );
			} else {
				$sql .= ' ' . $part;
			}
		}

		return $sql;
	}

}