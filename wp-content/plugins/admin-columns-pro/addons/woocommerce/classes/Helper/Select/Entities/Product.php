<?php

namespace ACA\WC\Helper\Select\Entities;

use AC;
use ACP\Helper\Select;
use ACP\Helper\Select\Value;
use WP_Query;

class Product extends Select\Entities\Post {

	/**
	 * @var WP_Query
	 */
	protected $query;

	/**
	 * @var array
	 */
	protected $search_fields = [];

	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Post();
		}

		$args = array_merge( [
			'post_type'     => 'product',
			'search_fields' => [ 'post_title', 'sku', 'ID' ],
		], $args );

		$this->search_fields = $args['search_fields'];

		add_filter( 'posts_join', [ $this, 'join_postmeta' ] );
		add_filter( 'posts_search', [ $this, 'add_search_fields' ], 30, 2 );
		add_filter( 'posts_groupby', [ $this, 'group_post_ids' ] );

		parent::__construct( $args, $value );
	}

	public function add_search_fields( $search_where, WP_Query $wp_query ) {
		global $wpdb;

		remove_filter( 'posts_search', __FUNCTION__ );

		// Empty search
		if ( ! $search_where ) {
			return $search_where;
		}

		$search_term = $wp_query->query_vars['s'];

		$like = '%' . $wpdb->esc_like( $search_term ) . '%';

		$where_parts = [];

		if ( in_array( 'post_title', $this->search_fields, true ) ) {
			$where_parts[] = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );
		}
		if ( in_array( 'sku', $this->search_fields, true ) ) {
			$where_parts[] = $wpdb->prepare( "acpm_sku.meta_value LIKE %s", $like );
		}
		if ( in_array( 'ID', $this->search_fields, true ) && is_numeric( $search_term ) ) {
			$where_parts[] = $wpdb->prepare( "{$wpdb->posts}.ID = %d", $search_term );
		}

		if ( $where_parts ) {
			$search_where = sprintf( " AND ( %s ) ", implode( ' OR ', $where_parts ) );
		}

		return $search_where;

	}

	public function join_postmeta( $join ) {
		global $wpdb;

		remove_filter( 'posts_join', __FUNCTION__ );

		if ( in_array( 'sku', $this->search_fields, true ) ) {
			$join .= " LEFT JOIN {$wpdb->postmeta} acpm_sku ON {$wpdb->posts}.ID = acpm_sku.post_id AND acpm_sku.meta_key = '_sku'";
		}

		return $join;
	}

	/**
	 * @return string
	 */
	public function group_post_ids() {
		global $wpdb;

		remove_filter( 'posts_groupby', __FUNCTION__ );

		return $wpdb->posts . '.ID';
	}

}