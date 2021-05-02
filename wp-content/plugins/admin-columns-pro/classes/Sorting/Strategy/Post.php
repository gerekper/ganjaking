<?php

namespace ACP\Sorting\Strategy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Strategy;
use WP_Query;

class Post extends Strategy {

	/**
	 * @var WP_Query $wp_query
	 */
	private $wp_query;

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( AbstractModel $model, $post_type ) {
		parent::__construct( $model );

		$this->post_type = (string) $post_type;
	}

	public function manage_sorting() {
		add_action( 'pre_get_posts', [ $this, 'handle_sorting_request' ] );
	}

	/**
	 * @param WP_Query $wp_query
	 */
	private function set_wp_query( WP_Query $wp_query ) {
		$this->wp_query = $wp_query;
	}

	public function get_results( array $args = [] ) {
		return $this->get_posts( $args );
	}

	/**
	 * @return WP_Query
	 */
	public function get_query() {
		return $this->wp_query;
	}

	public function get_order() {
		return $this->wp_query->query['order'];
	}

	/**
	 * Get post ID's
	 *
	 * @param array $args
	 *
	 * @return array Array of post ID's
	 * @since 1.0.7
	 */
	protected function get_posts( array $args = [] ) {
		$query_vars = $this->wp_query ? $this->wp_query->query_vars : [];

		if ( ! isset( $query_vars['post_status'] ) || empty( $query_vars['post_status'] ) ) {
			$query_vars['post_status'] = [ 'any' ];
		}

		if ( isset( $query_vars['orderby'] ) ) {
			$query_vars['orderby'] = false;
		}

		$query_vars['post_status'] = apply_filters( 'acp/sorting/post_status', $query_vars['post_status'], $this );
		$query_vars['no_found_rows'] = 1;
		$query_vars['fields'] = 'ids';
		$query_vars['posts_per_page'] = -1;
		$query_vars['order'] = 'ASC';
		$query_vars['posts_per_archive_page'] = '';
		$query_vars['nopaging'] = true;

		return get_posts( array_merge( $query_vars, $args ) );
	}

	/**
	 * @return string
	 */
	public function get_post_type() {
		return (string) $this->wp_query->get( 'post_type' );
	}

	/**
	 * @return int
	 */
	public function get_author() {
		return (int) $this->wp_query->get( 'author' );
	}

	/**
	 * @rerturn array
	 */
	public function get_post_status() {
		$status = $this->wp_query->get( 'post_status' );

		if ( empty( $status ) ) {
			return [];
		}

		if ( false !== strpos( $status, ',' ) ) {
			return explode( ',', $status );
		}

		return [ $status ];
	}

	protected function get_pagination_per_page() {
		return (int) get_user_option( 'edit_' . $this->post_type . '_per_page' );
	}

	/**
	 * Handle the sorting request on the post-type listing screens
	 *
	 * @param WP_Query $query
	 *
	 * @since 1.0
	 */
	public function handle_sorting_request( WP_Query $query ) {
		if ( ! $query->is_main_query() || ! $query->get( 'orderby' ) ) {
			return;
		}

		if ( $query->get( 'post_type' ) !== $this->post_type ) {
			return;
		}

		if ( ! is_post_type_hierarchical( $this->post_type ) ) {
			$per_page = $this->get_pagination_per_page();

			if ( $per_page < 1 ) {
				$per_page = 20;
			}

			$query->set( 'posts_per_archive_page', $per_page );
			$query->set( 'posts_per_page', $per_page );
		}

		$this->set_wp_query( $query );

		foreach ( $this->model->get_sorting_vars() as $key => $value ) {
			if ( $this->is_universal_id( $key ) ) {
				$key = 'post__in';

				$query->set( 'orderby', $key );
			}

			if ( 'meta_query' === $key ) {
				$value = $this->add_meta_query( $value, $query->get( $key ) );
			}

			$query->set( $key, $value );
		}

	}

}