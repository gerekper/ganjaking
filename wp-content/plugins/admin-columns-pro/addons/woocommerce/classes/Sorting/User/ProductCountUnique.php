<?php

namespace ACA\WC\Sorting\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

class ProductCountUnique extends AbstractModel {

	/**
	 * @var array
	 */
	private $status;

	public function __construct( array $status = null ) {
		parent::__construct();

		if ( null === $status ) {
			$status = [ 'wc-completed' ];
		}

		$this->status = $status;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		$order = esc_sql( $this->get_order() );
		$where = $this->status
			? sprintf( "AND acsort_posts.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->status ) ) )
			: '';

		$query->query_fields .= ", COUNT( DISTINCT acsort_orderitemmeta.meta_value ) AS acsort_product_count";
		$query->query_from .= " 
				LEFT JOIN {$wpdb->postmeta} AS acsort_postmeta ON acsort_postmeta.meta_value = {$wpdb->users}.ID 
				    AND acsort_postmeta.meta_key = '_customer_user'
				LEFT JOIN {$wpdb->posts} AS acsort_posts ON acsort_postmeta.post_id = acsort_posts.ID
					AND acsort_posts.post_type = 'shop_order'
					{$where}
				LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_orderitems ON acsort_orderitems.order_id = acsort_posts.ID 
				    AND acsort_orderitems.order_item_type = 'line_item'
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS acsort_orderitemmeta ON acsort_orderitems.order_item_id = acsort_orderitemmeta.order_item_id 
					AND acsort_orderitemmeta.meta_key = '_product_id'
		";

		$query->query_orderby = "
					GROUP BY {$wpdb->users}.ID
					ORDER BY acsort_product_count $order
				";
	}

}