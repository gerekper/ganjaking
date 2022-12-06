<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

abstract class OrderDate extends AbstractModel {

	/**
	 * @var string
	 */
	private $status;

	public function __construct( array $status = [ 'wc-completed' ] ) {
		parent::__construct();

		$this->status = $status;
	}

	abstract protected function get_order_by(): string;

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		$where_status = $this->status
			? sprintf( " AND acsort_orders.post_status IN ( %s )", $this->esc_sql_array( $this->status ) )
			: '';

		$query->query_from .= " 
					LEFT JOIN {$wpdb->postmeta} AS acsort_postmeta 
						ON {$wpdb->users}.ID = acsort_postmeta.meta_value
						AND acsort_postmeta.meta_key = '_customer_user'
					LEFT JOIN {$wpdb->posts} AS acsort_orders
						ON acsort_orders.ID = acsort_postmeta.post_id
						AND acsort_orders.post_type = 'shop_order'
						{$where_status}
					LEFT JOIN $wpdb->postmeta AS acsort_order_postmeta
						ON acsort_orders.ID = acsort_order_postmeta.post_id
						AND acsort_order_postmeta.meta_key = '_completed_date'
					";

		$query->query_orderby = sprintf( "
					GROUP BY {$wpdb->users}.ID
					ORDER BY %s
				", $this->get_order_by() );

	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}