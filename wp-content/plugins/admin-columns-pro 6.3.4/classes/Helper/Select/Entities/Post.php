<?php

namespace ACP\Helper\Select\Entities;

use AC;
use ACP\Helper\Select\Value;
use WP_Query;

class Post extends AC\Helper\Select\Entities
	implements AC\Helper\Select\Paginated {

	/**
	 * @var WP_Query
	 */
	protected $query;

	/**
	 * @var array
	 */
	protected $search_fields = [];

	/**
	 * @param array                  $args
	 * @param AC\Helper\Select\Value $value
	 */
	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Post();
		}

		$args = array_merge( [
			'posts_per_page' => 30,
			'post_type'      => 'any',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'paged'          => 1,
			's'              => null,
			'post_status'    => 'any',
			'search_fields'  => [ 'post_title', 'ID' ],
		], $args );

		$this->search_fields = $args['search_fields'];

		add_filter( 'posts_search', [ $this, 'set_search_fields' ], 20, 2 );

		$this->query = new WP_Query( $args );

		parent::__construct( $this->query->get_posts(), $value );
	}

	public function set_search_fields( $search_where, WP_Query $wp_query ) {
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
		if ( in_array( 'post_content', $this->search_fields, true ) ) {
			$where_parts[] = $wpdb->prepare( "{$wpdb->posts}.post_content LIKE %s", $like );
		}
		if ( in_array( 'post_excerpt', $this->search_fields, true ) ) {
			$where_parts[] = $wpdb->prepare( "{$wpdb->posts}.post_excerpt LIKE %s", $like );
		}
		if ( in_array( 'ID', $this->search_fields, true ) && is_numeric( $search_term ) ) {
			$where_parts[] = $wpdb->prepare( "{$wpdb->posts}.ID = %d", $search_term );
		}

		if ( $where_parts ) {
			$search_where = sprintf( " AND ( %s ) ", implode( ' OR ', $where_parts ) );
		}

		return $search_where;

	}

	public function get_total_pages() {
		$per_page = $this->query->get( 'posts_per_page' );

		return ceil( $this->query->found_posts / $per_page );
	}

	public function get_page() {
		return $this->query->get( 'paged' );
	}

	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

}