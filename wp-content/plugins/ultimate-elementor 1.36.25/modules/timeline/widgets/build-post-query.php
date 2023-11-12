<?php
/**
 * UAEL Build Post Query.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Timeline\Widgets;

use UltimateElementor\Classes\UAEL_Posts_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Posts
 */
class Build_Post_Query {

	/**
	 * Query object
	 *
	 * @since 1.5.2
	 * @var object $query
	 */
	public $query = '';

	/**
	 * Filter
	 *
	 * @since 1.5.2
	 * @var object $filter
	 */
	public static $filter = '';

	/**
	 * Settings
	 *
	 * @since 1.5.2
	 * @var object $settings
	 */
	public $settings = '';

	/**
	 * Initiator
	 *
	 * @param array  $settings Settimgs.
	 * @param string $filter Filter taxonomy.
	 */
	public function __construct( $settings, $filter ) {

		$this->settings = $settings;
		self::$filter   = str_replace( '.', '', $filter );
	}

	/**
	 * Get query products based on settings.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param array $settings Settings array for the widget.
	 * @since 1.5.2
	 * @access public
	 */
	public static function get_query_posts( $settings ) {

		$post_type = ( isset( $settings['post_type_filter'] ) && '' !== $settings['post_type_filter'] ) ? $settings['post_type_filter'] : 'post';

		$paged = self::get_paged();

		$query_args = array(
			'post_type'      => $post_type,
			'posts_per_page' => ( '' === $settings['posts_per_page'] ) ? -1 : $settings['posts_per_page'],
			'paged'          => $paged,
			'post_status'    => 'publish',
		);

		$query_args['orderby']             = $settings['orderby'];
		$query_args['order']               = $settings['order'];
		$query_args['ignore_sticky_posts'] = ( isset( $settings['ignore_sticky_posts'] ) && 'yes' === $settings['ignore_sticky_posts'] ) ? 1 : 0;

		if ( ! empty( $settings['post_filter'] ) ) {

			$query_args[ $settings['post_filter_rule'] ] = $settings['post_filter'];
		}

		if ( '' !== $settings['author_filter'] ) {

			$query_args[ $settings['author_filter_rule'] ] = $settings['author_filter'];
		}

		// Get all the taxanomies associated with the post type.
		$taxonomy = UAEL_Posts_Helper::get_taxonomy( $post_type );

		if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

			// Get all taxonomy values under the taxonomy.
			foreach ( $taxonomy as $index => $tax ) {

				if ( ! empty( $settings[ 'tax_' . $index . '_' . $post_type . '_filter' ] ) ) {

					$operator = $settings[ $index . '_' . $post_type . '_filter_rule' ];

					$query_args['tax_query'][] = array(
						'taxonomy' => $index,
						'field'    => 'slug',
						'terms'    => $settings[ 'tax_' . $index . '_' . $post_type . '_filter' ],
						'operator' => $operator,
					);
				}
			}
		}

		if ( '' !== self::$filter && '*' !== self::$filter ) {

			$query_args['tax_query'][0]['taxonomy'] = $settings[ 'tax_masonry_' . $post_type . '_filter' ];
			$query_args['tax_query'][0]['field']    = 'slug';
			$query_args['tax_query'][0]['terms']    = self::$filter;
			$query_args['tax_query'][0]['operator'] = 'IN';
		}

		if ( 0 < $settings['offset'] ) {

			/**
			 * Offser break the pagination. Using WordPress's work around
			 *
			 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			$query_args['offset_to_fix'] = $settings['offset'];
		}

		return apply_filters( '_uael_timeline_query_args', $query_args, $settings );
	}

	/**
	 * Set Query Posts.
	 *
	 * @since 1.5.2
	 * @access public
	 */
	public function query_posts() {

		$settings = $this->settings;

		if ( 'main' === $settings['query_type'] ) {

			global $wp_query;

			$main_query = clone $wp_query;

			$this->query = $main_query;

		} else {

			$query_args  = $this->get_query_posts( $settings );
			$this->query = new \WP_Query( $query_args );
		}
	}

	/**
	 * Returns the paged number for the query.
	 *
	 * @since 1.5.2
	 * @return int
	 */
	public static function get_paged() {

		global $wp_the_query, $paged;

		// Check the 'paged' query var.
		$paged_qv = $wp_the_query->get( 'paged' );

		if ( is_numeric( $paged_qv ) ) {
			return $paged_qv;
		}

		// Check the 'page' query var.
		$page_qv = $wp_the_query->get( 'page' );

		if ( is_numeric( $page_qv ) ) {
			return $page_qv;
		}

		// Check the $paged global?
		if ( is_numeric( $paged ) ) {
			return $paged;
		}

		return 0;
	}

	/**
	 * Render current query.
	 *
	 * @since 1.5.2
	 * @access protected
	 */
	public function get_query() {

		return $this->query;
	}
}
