<?php
/**
 * Post by Search Control.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\QueryPost;

use UltimateElementor\Base\Module_Base;

use UltimateElementor\Modules\QueryPost\Controls\Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	const QUERY_CONTROL_ID = 'uael-query-posts';

	/**
	 * Module should load or not.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Constructer.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}

	/**
	 * Get Module Name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'query-control';
	}

	/**
	 * Register Control
	 *
	 * @since 1.1.0
	 */
	public function register_controls() {
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;

		$controls_manager->register( new Query() );

	}

	/**
	 * Get post title by ID
	 *
	 * @since 1.1.0
	 */
	public function get_posts_title_by_id() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'uael-post-nonce' ) ) {
			$ids = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : array();
		}

		$results = array();

		$query = new \WP_Query(
			array(
				'post_type'      => 'any',
				'post__in'       => $ids,
				'posts_per_page' => -1,
			)
		);

		foreach ( $query->posts as $post ) {
			$results[ $post->ID ] = $post->post_title;
		}

		// return the results in json.
		wp_send_json( $results );
	}

	/**
	 * Get post by search
	 *
	 * @since 1.1.0
	 */
	public function get_posts_by_query() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'uael-post-query' ) ) {
			$search_string = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : '';
			$req_post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'all';
		}

		$data   = array();
		$result = array();

		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$output   = 'names'; // names or objects, note names is the default.
		$operator = 'and'; // also supports 'or'.

		if ( 'all' === $req_post_type ) {
			$post_types = get_post_types( $args, $output, $operator );

			$post_types['Posts'] = 'post';
			$post_types['Pages'] = 'page';
		} else {
			$post_types[ $req_post_type ] = $req_post_type;
		}

		foreach ( $post_types as $key => $post_type ) {

			$data = array();

			add_filter( 'posts_search', array( $this, 'search_only_titles' ), 10, 2 );

			$query = new \WP_Query(
				array(
					's'              => $search_string,
					'post_type'      => $post_type,
					'posts_per_page' => - 1,
				)
			);

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$title  = get_the_title();
					$title .= ( 0 !== $query->post->post_parent ) ? ' (' . get_the_title( $query->post->post_parent ) . ')' : '';
					$id     = get_the_id();
					$data[] = array(
						'id'   => $id,
						'text' => $title,
					);
				}
			}

			if ( is_array( $data ) && ! empty( $data ) ) {
				$result[] = array(
					'text'     => $key,
					'children' => $data,
				);
			}
		}

		$data = array();

		wp_reset_postdata();

		// return the result in json.
		wp_send_json( $result );
	}

	/**
	 * Return search results only by post title.
	 * This is only run from get_posts_by_query()
	 *
	 * @param  (string)   $search   Search SQL for WHERE clause.
	 * @param  (WP_Query) $wp_query The current WP_Query object.
	 *
	 * @return (string) The Modified Search SQL for WHERE clause.
	 */
	public function search_only_titles( $search, $wp_query ) {
		if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
			global $wpdb;

			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';

			$search = array();

			foreach ( (array) $q['search_terms'] as $term ) {
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
			}

			if ( ! is_user_logged_in() ) {
				$search[] = "$wpdb->posts.post_password = ''";
			}

			$search = ' AND ' . implode( ' AND ', $search );
		}

		return $search;
	}

	/**
	 * Add actions
	 *
	 * @since 1.1.0
	 */
	protected function add_actions() {

		add_action( 'wp_ajax_uael_get_posts_by_query', array( $this, 'get_posts_by_query' ) );
		add_action( 'wp_ajax_uael_get_posts_title_by_id', array( $this, 'get_posts_title_by_id' ) );

	}
}
