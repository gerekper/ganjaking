<?php
/**
 * UAEL Posts Base Class.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\Widgets;

use UltimateElementor\Modules\Posts\Skins;
use UltimateElementor\Classes\UAEL_Posts_Helper;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Posts
 */
class Posts extends Posts_Base {

	/**
	 * Widget Slug
	 *
	 * @var widget_slug
	 */
	private static $widget_slug = null;

	/**
	 * Get Widget Slug.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_name() {

		if ( ! isset( self::$widget_slug ) ) {
			self::$widget_slug = parent::get_widget_slug( 'Posts' );
		}

		return self::$widget_slug;
	}

	/**
	 * Get Post Widget Title.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_title() {
		return parent::get_widget_title( 'Posts' );
	}

	/**
	 * Retrieve Widget icon.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Posts' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Posts' );
	}

	/**
	 * Register Skins.
	 *
	 * @since 1.29.0
	 * @access public
	 */
	public function register_skins() {
		$this->add_skin( new Skins\Skin_Classic( $this ) );
		$this->add_skin( new Skins\Skin_Event( $this ) );

		if ( UAEL_Helper::is_post_skin_active( 'Skin_Card' ) ) {
			$this->add_skin( new Skins\Skin_Card( $this ) );
		}

		if ( UAEL_Helper::is_post_skin_active( 'Skin_Feed' ) ) {
			$this->add_skin( new Skins\Skin_Feed( $this ) );
		}

		if ( UAEL_Helper::is_post_skin_active( 'Skin_News' ) ) {
			$this->add_skin( new Skins\Skin_News( $this ) );
		}

		if ( UAEL_Helper::is_post_skin_active( 'Skin_Business' ) ) {
			$this->add_skin( new Skins\Skin_Business( $this ) );
		}
	}

	/**
	 * Register controls.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore
		parent::register_controls();
	}

	/**
	 * Set Query Posts.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function query_posts_args() {

		$skin_id = $this->get_settings( '_skin' );
		$skin_id = str_replace( '-', '_', $skin_id );

		$query_args = self::get_query_posts( $skin_id, $this->get_settings() );

		$query_args['posts_per_page'] = ( '' === $this->get_current_skin()->get_instance_value( 'posts_per_page' ) ) ? -1 : $this->get_current_skin()->get_instance_value( 'posts_per_page' );
		$query_args['paged']          = $this->get_paged();

		if ( 'none' !== $this->get_current_skin()->get_instance_value( 'pagination' ) ) {
			$query_args['paged'] = $this->get_paged();
		} else {
			$query_args['paged'] = '1';
		}

		return ( $query_args );
	}

	/**
	 * Get query products based on settings.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param string $control_id Settings control id.
	 * @param array  $settings Settings array for the widget.
	 * @since 1.7.0
	 * @access public
	 */
	public static function get_query_posts( $control_id, $settings ) {

		if ( '' !== $control_id ) {
			$control_id = $control_id . '_';
		}

		if ( 'none' !== $settings[ $control_id . 'pagination' ] ) {
			$paged = self::get_paged();
		} else {
			$paged = '1';
		}

		$post_type = ( isset( $settings['post_type_filter'] ) && '' !== $settings['post_type_filter'] ) ? $settings['post_type_filter'] : 'post';

		$query_args = array(
			'post_type'        => $post_type,
			'posts_per_page'   => ( '' === $settings[ $control_id . 'posts_per_page' ] ) ? -1 : $settings[ $control_id . 'posts_per_page' ],
			'paged'            => $paged,
			'post_status'      => 'publish',
			'suppress_filters' => false,
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
				if ( ! empty( $settings[ 'tax_' . $index . '_filter' ] ) ) {
					$operator = $settings[ $index . '_filter_rule' ];

					$query_args['tax_query'][] = array(
						'taxonomy' => $index,
						'field'    => 'slug',
						'terms'    => $settings[ 'tax_' . $index . '_filter' ],
						'operator' => $operator,
					);
				}
			}
		}

		if ( 0 < $settings['offset'] ) {

			/**
			 * Offser break the pagination. Using WordPress's work around
			 *
			 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			$query_args['offset_to_fix'] = $settings['offset'];
		}

		return apply_filters( '_uael_posts_query_args', $query_args, $settings );
	}

	/**
	 * Set Query Posts.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function query_posts() {

		$settings = $this->get_settings();

		if ( 'main' === $settings['query_type'] ) {
			global $wp_query;

			$main_query = clone $wp_query;

			$this->query = $main_query;
		} else {
			$query_args = $this->query_posts_args();

			$this->query = new \WP_Query( $query_args );
		}
	}
}
