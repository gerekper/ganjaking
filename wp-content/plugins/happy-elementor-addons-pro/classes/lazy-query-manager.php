<?php
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

/**
 * Posts Query Controller class
 */
class Lazy_Query_Manager {

	/**
	 * Ajax action name
	 */
	const ACTION = 'ha_get_lazy_query_data';

	/**
	 * Taxonomy tems query
	 */
	const QUERY_TERMS = 'terms';

	/**
	 * Posts query
	 */
	const QUERY_POSTS = 'posts';

	/**
	 * Authors or users query
	 */
	const QUERY_AUTHORS = 'authors';

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_' . self::ACTION, [ __CLASS__, 'do_lazy_query' ] );
	}

	protected static function get_query_handler() {
		$query = isset( $_POST['query'] ) ? sanitize_text_field($_POST['query']) : self::QUERY_POSTS;
		$handlers = [
			self::QUERY_POSTS   => 'get_posts',
			self::QUERY_TERMS   => 'get_terms',
			self::QUERY_AUTHORS => 'get_authors',
		];

		return isset( $handlers[ $query ] ) ? $handlers[ $query ] : $handlers[ self::QUERY_POSTS ];
	}

	public static function do_lazy_query() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		try {
			if ( ! wp_verify_nonce( $nonce, self::ACTION ) ) {
				throw new \Exception( 'Invalid request' );
			}

			if ( ! current_user_can( 'edit_posts' ) ) {
				throw new \Exception( 'Unauthorized request' );
			}

			$handler = self::get_query_handler();
			$data = call_user_func( [ __CLASS__, $handler ] );
			wp_send_json_success( $data );
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		die();
	}

	protected static function get_search_term() {
		return isset( $_POST['search_term'] ) ? sanitize_text_field( $_POST['search_term'] ) : '';
	}

	protected static function get_post_type() {
		return isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
	}

	protected static function get_seleced_ids() {
		return isset( $_POST['ids'] ) ? ha_pro_sanitize_array_recursively($_POST['ids']) : [];
	}

	protected static function get_taxonomies() {
		return array_values( get_taxonomies( [ 'public' => true ] ) );
	}

	protected static function get_post_types() {
		return array_values( get_post_types( [ 'public' => true ] ) );
	}

	public static function get_taxonomy_label( $taxonomy = '' ) {
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		$taxonomies = array_column( $taxonomies, 'label', 'name' );

		return isset( $taxonomies[ $taxonomy ] ) ? $taxonomies[ $taxonomy ] : '';
	}

	public static function get_post_types_list() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$post_types = array_column( $post_types, 'label', 'name' );

		$ingore = [
			'elementor_library' => '',
			'attachment' => ''
		];

		$post_types = array_diff_key( $post_types, $ingore );

		$extra_types = [
			'manual_selection' => __( 'Manual Selection', 'happy-addons-pro' ),
		];

		$post_types = array_merge( $post_types, $extra_types );

		return $post_types;
	}

	public static function get_terms() {
		$include = self::get_seleced_ids();
		$search_term = self::get_search_term();
		$taxonomies = self::get_taxonomies();

		if ( self::get_post_type() ) {
			$post_taxonomies = get_object_taxonomies( self::get_post_type() );
			$taxonomies = array_intersect( $post_taxonomies, $taxonomies );
		}

		$data = [];

		if ( empty( $taxonomies ) ) {
			return $data;
		}

		$args = [
			'taxonomy' => $taxonomies,
			'hide_empty' => false,
		];

		if ( ! empty( $include ) ) {
			$args['include'] = $include;
		}

		if ( $search_term ) {
			$args['number'] = 20;
			$args['search'] = $search_term;
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return $data;
		}

		foreach ( $terms as $term ) {
			$label = $term->name;
			$taxonomy_name = self::get_taxonomy_label( $term->taxonomy );

			if ( $taxonomy_name ) {
				$label = "{$taxonomy_name}: {$label}";
			}

			$data[] = [
				'id' => $term->term_taxonomy_id,
				'text' => $label,
			];
		}

		return $data;
	}

	public static function get_authors() {
		$include = self::get_seleced_ids();
		$search_term = self::get_search_term();

		$data = [];
		$args = [
			'fields'  => ['ID', 'display_name'],
			'orderby' => 'display_name',
		];

		if ( ! empty( $include ) ) {
			$args['include'] = $include;
		}

		if ( $search_term ) {
			$args['number'] = 20;
			$args['search'] = "*$search_term*";
		}

		$users = get_users( $args );

		if ( empty( $users ) ) {
			return $data;
		}

		foreach ( $users as $user ) {
			$data[] = [
				'id' => $user->ID,
				'text' => $user->display_name,
			];
		}

		return $data;
	}

	public static function get_posts() {
		$include = self::get_seleced_ids();
		$search_term = self::get_search_term();

		$data = [];
		$args = [
			'numberposts' => 20
		];

		if ( self::get_post_type() ) {
			$args['post_type'] = self::get_post_type();
		} else {
			$args['post_type'] = self::get_post_types();
		}

		if ( ! empty( $include ) ) {
			$args['include'] = $include;
			$args['numberposts'] = count( $include );
		}

		if ( $search_term ) {
			$args['s'] = $search_term;
		}

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return $data;
		}

		foreach ( $posts as $post ) {
			$data[] = [
				'id' => $post->ID,
				'text' => strip_tags( $post->post_title ),
			];
		}

		return $data;
	}
}

Lazy_Query_Manager::init();
