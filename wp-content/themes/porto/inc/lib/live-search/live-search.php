<?php
/**
 * Porto Live Search
 *
 * @author     Porto Themes
 * @category   Library
 * @since      4.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Live_Search' ) ) :

	class Porto_Live_Search {

		public function __construct() {
			global $porto_settings;

			if ( porto_is_amp_endpoint() ) {
				return;
			}

			if ( ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) || ! isset( $porto_settings['search-live'] ) || ! $porto_settings['search-live'] ) {
				return;
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'add_script' ) );
			add_action( 'wp_ajax_porto_ajax_search_posts', array( $this, 'ajax_search' ) );
			add_action( 'wp_ajax_nopriv_porto_ajax_search_posts', array( $this, 'ajax_search' ) );
		}

		public function add_script() {
			wp_enqueue_script( 'porto-live-search', PORTO_LIB_URI . '/live-search/live-search.min.js', array( 'jquery-core' ), PORTO_VERSION, true );
			wp_localize_script(
				'porto-live-search',
				'porto_live_search',
				array(
					'nonce' => wp_create_nonce( 'porto-live-search-nonce' ),
				)
			);
		}

		public function ajax_search() {
			check_ajax_referer( 'porto-live-search-nonce', 'nonce' );
			global $porto_settings;

			$query  = apply_filters( 'porto_ajax_search_query', sanitize_text_field( $_REQUEST['query'] ) );
			$posts  = array();
			$result = array();
			$args   = array(
				's'                   => $query,
				'orderby'             => '',
				'post_status'         => 'publish',
				'posts_per_page'      => 50,
				'ignore_sticky_posts' => 1,
				'post_password'       => '',
				'suppress_filters'    => false,
			);

			if ( ! isset( $_REQUEST['post_type'] ) || empty( $_REQUEST['post_type'] ) || 'product' == $_REQUEST['post_type'] ) {
				if ( class_exists( 'Woocommerce' ) ) {
					$posts = $this->search_products( 'product', $args );
					$search_by = isset( $porto_settings['search-by'] ) ? $porto_settings['search-by'] : array();

					if ( in_array( 'sku', $search_by ) ) {
						$posts = array_merge( $posts, $this->search_products( 'sku', $args ) );
					}
					if ( in_array( 'product_tag', $search_by ) ) {
						$posts = array_merge( $posts, $this->search_products( 'tag', $args ) );
					}

				}
				if ( ! isset( $_REQUEST['post_type'] ) || empty( $_REQUEST['post_type'] ) ) {
					$posts = array_merge( $posts, $this->search_posts( $args, $query ) );
				}
			} else {
				$posts = $this->search_posts( $args, $query, array( sanitize_text_field( $_REQUEST['post_type'] ) ) );
			}

			foreach ( $posts as $post ) {
				if ( class_exists( 'Woocommerce' ) && ( 'product' === $post->post_type || 'product_variation' === $post->post_type ) ) {
					$product       = wc_get_product( $post );
					$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ) );

					$result[] = array(
						'type'  => 'Product',
						'id'    => $product->get_id(),
						'value' => $product->get_title(),
						'url'   => esc_url( $product->get_permalink() ),
						'img'   => esc_url( $product_image[0] ),
						'price' => $product->get_price_html(),
					);
				} else {
					$result[] = array(
						'type'  => get_post_type( $post->ID ),
						'id'    => $post->ID,
						'value' => get_the_title( $post->ID ),
						'url'   => esc_url( get_the_permalink( $post->ID ) ),
						'img'   => esc_url( get_the_post_thumbnail_url( $post->ID, 'thumbnail' ) ),
						'price' => '',
					);
				}
			}

			wp_send_json( array( 'suggestions' => $result ) );
		}

		private function search_posts( $args, $query, $post_type = array( 'post', 'page', 'portfolio', 'event' ) ) {
			$args['s']         = $query;
			$args['post_type'] = apply_filters( 'porto_ajax_search_post_type', $post_type );
			$args              = $this->search_add_category_args( $args );

			$search_query   = http_build_query( $args );
			$search_funtion = apply_filters( 'porto_ajax_search_function', 'get_posts', $search_query, $args );

			return ( 'get_posts' === $search_funtion || ! function_exists( $search_funtion ) ? get_posts( $args ) : $search_funtion( $search_query, $args ) );
		}

		private function search_products( $search_type, $args ) {
			$args['post_type']  = 'product';
			$args['meta_query'] = WC()->query->get_meta_query(); // WPCS: slow query ok.
			$args               = $this->search_add_category_args( $args );

			switch ( $search_type ) {
				case 'product':
					$args['s'] = apply_filters( 'porto_ajax_search_products_query', sanitize_text_field( $_REQUEST['query'] ) );
					break;
				case 'sku':
					$query                = apply_filters( 'porto_ajax_search_products_by_sku_query', sanitize_text_field( $_REQUEST['query'] ) );
					$args['s']            = '';
					$args['post_type']    = array( 'product', 'product_variation' );
					$args['meta_query'][] = array(
						'key'   => '_sku',
						'value' => $query,
					);
					break;
				case 'tag':
					$args['s']           = '';
					$args['product_tag'] = apply_filters( 'porto_ajax_search_products_by_tag_query', sanitize_text_field( $_REQUEST['query'] ) );
					break;
			}

			$search_query   = http_build_query( $args );
			$search_funtion = apply_filters( 'porto_ajax_search_function', 'get_posts', $search_query, $args );

			return 'get_posts' === $search_funtion || ! function_exists( $search_funtion ) ? get_posts( $args ) : $search_funtion( $search_query, $args );
		}

		private function search_add_category_args( $args ) {
			if ( isset( $_REQUEST['cat'] ) && $_REQUEST['cat'] && '0' != $_REQUEST['cat'] ) {
				if ( 'product' == $porto_settings['search-type'] ) {
					$args['tax_query']   = array();
					$args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $_REQUEST['cat'] ),
					);
				} elseif ( 'post' == $porto_settings['search-type'] ) {
					$args['category'] = sanitize_text_field( $_REQUEST['cat'] );
				} elseif ( 'portfolio' == $porto_settings['search-type'] ) {
					$args['tax_query']   = array();
					$args['tax_query'][] = array(
						'taxonomy' => 'portfolio_cat',
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $_REQUEST['cat'] ),
					);
				}
			}
			return $args;
		}
	}
	new Porto_Live_Search;
endif;
