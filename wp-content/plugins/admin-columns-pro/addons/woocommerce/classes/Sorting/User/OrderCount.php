<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use WP_User_Query;

class OrderCount extends AbstractModel {

	/**
	 * @var array
	 */
	private $status;

	public function __construct( array $status = [] ) {
		parent::__construct();

		$this->status = $status;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		$sql_parts = [
			'select' => '
				SELECT acsort_postmeta.meta_value as user_id, COUNT(acsort_postmeta.meta_value) as count',
			'from'   => "
				FROM {$wpdb->posts} as acsort_posts",
			'joins'  => [
				"INNER JOIN {$wpdb->postmeta} AS acsort_postmeta 
					ON acsort_posts.ID = acsort_postmeta.post_id 
					AND acsort_postmeta.meta_key = '_customer_user'",
			],
			'where'  => [
				"WHERE acsort_posts.post_type = 'shop_order'",
			],
			'group'  => "
				GROUP BY acsort_postmeta.meta_value",
		];

		if ( ! empty( $this->status ) ) {
			$sql_parts['where'][] = sprintf( " AND acsort_posts.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->status ) ) );
		}

		$sub_query = $this->built_sql( $sql_parts );

		$query->query_from .= " LEFT JOIN ( $sub_query ) as acsort_sub ON $wpdb->users.ID = acsort_sub.user_id";

		$query->query_orderby = sprintf( ' ORDER BY %s', SqlOrderByFactory::create( 'acsort_sub.count', $this->get_order(), [ 'cast_type' => CastType::SIGNED ] ) );
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