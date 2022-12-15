<?php
/**
 * Warranty_Query
 */
class Warranty_Query {

	/**
	 * Warranty_Query constructor.
	 */
	public function __construct() {
		add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );
		add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 2 );
	}

	/**
	 * Adds a JOIN clause to the wc_warranty_products table when searching for warranty requests
	 *
	 * @param string   $join SQL JOIN query.
	 * @param WP_Query $query SQL query.
	 *
	 * @return string
	 */
	public function posts_join( $join, $query ) {
		global $wpdb;

		if ( isset( $query->query['post_type'] ) && 'warranty_request' === $query->query['post_type'] ) {
			if ( ! empty( $query->query_vars['product_id'] ) || ! empty( $query->query_vars['item_index'] ) ) {
				$order_id = $query->query_vars['order_id'];
				$join    .= " LEFT JOIN {$wpdb->prefix}wc_warranty_products ON {$wpdb->prefix}wc_warranty_products.request_id = {$wpdb->posts}.ID ";
			}
		}

		return $join;
	}

	/**
	 * Adds a WHERE clause when searching for warranty requests
	 *
	 * @param string   $where SQL WHERE query.
	 * @param WP_Query $query SQL query.
	 *
	 * @return string
	 */
	public function posts_where( $where, $query ) {
		global $wpdb;

		if ( isset( $query->query['post_type'] ) && 'warranty_request' === $query->query['post_type'] ) {
			if ( ! empty( $query->query_vars['product_id'] ) ) {
				$where .= " AND {$wpdb->prefix}wc_warranty_products.product_id = " . absint( $query->query_vars['product_id'] ) . ' ';
			}

			if ( ! empty( $query->query_vars['item_index'] ) ) {
				$where .= " AND {$wpdb->prefix}wc_warranty_products.order_item_index = " . absint( $query->query_vars['item_index'] ) . ' ';
			}
		}

		return $where;
	}

}

new Warranty_Query();
