<?php
/**
 * Porto Infinite scroll
 *
 * @author     Porto Themes
 * @category   Library
 * @since      4.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Infinite_Scroll' ) ) :
	class Porto_Infinite_Scroll {

		private $loader_html = '<div class="bounce-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';

		private static $instance;

		private $is_infinite = false;

		private $post_type = 'post';

		private function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function init() {
			if ( is_admin() ) {
				return;
			}
			add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ), 99 );
			add_action( 'wp_head', array( $this, 'add_css' ), 99 );
		}

		public function is_infinite() {
			return $this->is_infinite;
		}

		private function check_if_infinite() {
			$is_infinite = false;
			if ( is_archive() || is_category() || is_home() ) {
				global $porto_settings;
				$post_type = get_post_type();
				if ( empty( $post_type ) ) {
					if ( is_home() || is_category() ) {
						$post_type = 'post';
					} elseif ( class_exists( 'Woocommerce' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
						$post_type = 'product';
					} elseif ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() ) {
						$post_type = 'portfolio';
					} elseif ( function_exists( 'is_porto_members_page' ) && is_porto_members_page() ) {
						$post_type = 'member';
					} elseif ( function_exists( 'is_porto_faqs_page' ) && is_porto_faqs_page() ) {
						$post_type = 'faq';
					} elseif ( function_exists( 'is_porto_events_page' ) && is_porto_events_page() ) {
						$post_type = 'event';
					} else {
						if ( is_post_type_archive( 'portfolio' ) ) {
							$post_type = 'portfolio';
						} elseif ( is_post_type_archive( 'member' ) ) {
							$post_type = 'member';
						} elseif ( is_post_type_archive( 'faq' ) ) {
							$post_type = 'faq';
						} elseif ( is_post_type_archive( 'event' ) ) {
							$post_type = 'event';
						} else {
							$term = get_queried_object();
							if ( $term && isset( $term->taxonomy ) ) {
								switch ( $term->taxonomy ) {
									case in_array( $term->taxonomy, porto_get_taxonomies( 'portfolio' ) ):
										$post_type = 'portfolio';
										break;
									case in_array( $term->taxonomy, porto_get_taxonomies( 'product' ) ):
										$post_type = 'product';
										break;
									case in_array( $term->taxonomy, porto_get_taxonomies( 'member' ) ):
										$post_type = 'member';
										break;
									case in_array( $term->taxonomy, porto_get_taxonomies( 'faq' ) ):
										$post_type = 'faq';
										break;
									case in_array( $term->taxonomy, porto_get_taxonomies( 'post' ) ):
										$post_type = 'post';
										break;
								}
							} elseif ( is_tag() ) {
								$post_type = 'post';
							}
						}
					}
				}

				$global_setting_name = ( 'post' == $post_type ? 'blog-infinite' : $post_type . '-infinite' );
				if ( ( 'post' == $post_type || 'portfolio' == $post_type || 'member' == $post_type ) && ( $term = get_queried_object() ) && isset( $term->term_id ) ) {
					$term_options = get_metadata( $term->taxonomy, $term->term_id, $post_type . '_options', true ) == $post_type . '_options' ? true : false;
					$is_infinite  = $term_options ? ( get_metadata( $term->taxonomy, $term->term_id, $post_type . '_infinite', true ) != $post_type . '_infinite' ? true : false ) : ( ! empty( $porto_settings[ $global_setting_name ] ) && 'ajax' != $porto_settings[ $global_setting_name ] ? $porto_settings[ $global_setting_name ] : false );
				} elseif ( $post_type ) {
					$is_infinite = isset( $porto_settings[ $global_setting_name ] ) ? ( $porto_settings[ $global_setting_name ] && 'ajax' != $porto_settings[ $global_setting_name ] ? $porto_settings[ $global_setting_name ] : false ) : $is_infinite;
				}
				$this->post_type = $post_type;
			}
			return $is_infinite;
		}

		public function add_css() {
			if ( $this->is_infinite ) {
				$post_type    = $this->post_type;
				$parent_class = '';
				if ( 'post' == $post_type ) {
					$parent_class = '.blog-posts ';
				} elseif ( 'portfolio' == $post_type ) {
					$parent_class = '.page-portfolios ';
				} elseif ( 'member' == $post_type ) {
					$parent_class = '.page-members ';
				} elseif ( 'faq' == $post_type ) {
					$parent_class = '.page-faqs ';
				} elseif ( 'product' == $post_type ) {
					$parent_class = '.products-container ';
				}
				echo '<style id="infinite-scroll-css">' . esc_html( $parent_class ) . '.pagination, ' . esc_html( $parent_class ) . '.page-links { display: none; }' . esc_html( $parent_class ) . ' { position: relative; }</style>';
			}
		}

		public function add_scripts() {
			$this->is_infinite = $this->check_if_infinite();

			if ( $this->is_infinite ) {
				$post_type = $this->post_type;
				if ( class_exists( 'Woocommerce' ) ) {
					$required = array( 'porto-woocommerce-theme' );
				} else {
					$required = array( 'imagesloaded', 'porto-theme' );
				}

				wp_enqueue_script( 'porto-jquery-infinite-scroll', PORTO_URI . '/js/libs/jquery.infinite-scroll.min.js', $required, '2.1.0', true );
				wp_enqueue_script( 'porto-infinite-scroll', PORTO_LIB_URI . '/infinite-scroll/infinite-scroll.min.js', array( 'porto-jquery-infinite-scroll' ), PORTO_VERSION, true );

				if ( 'post' == $post_type || 'portfolio' == $post_type ) {
					$item_selector = '.' . $post_type . 's-container .' . $post_type . ', .' . $post_type . 's-container .timeline-date';
				} elseif ( 'product' == $post_type ) {
					$item_selector = '.products-container:not(.is-shortcode) .' . $post_type;
				} else {
					$item_selector = '.' . $post_type . 's-container .' . $post_type;
				}

				global $wp_query;

				$builder_id   = porto_check_builder_condition( 'product' == $post_type ? 'shop' : 'archive' );
				$page_num     = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
				$page_link    = get_pagenum_link( 999999999 );
				$page_max_num = $wp_query->max_num_pages;
				$page_path    = str_replace( '999999999', '%cur_page%', add_query_arg( 'load_posts_only', $builder_id ? '2' : '1', $page_link ) );
				$page_path    = str_replace( '&#038;', '&amp;', $page_path );
				$page_path    = str_replace( '#038;', '&amp;', $page_path );

				$params = array(
					'post_type'       => $post_type,
					'item_selector'   => $item_selector,
					'loader_html'     => 'load_more' === $this->is_infinite ? '' : $this->loader_html,
					'page_path'       => esc_url( $page_path ),
					'cur_page'        => $page_num,
					'max_page'        => (int) $page_max_num,
					'pagination_type' => 'load_more' === $this->is_infinite ? 'load_more' : 'infinite_scroll',
					'loader_text'     => esc_html__( 'Loading...', 'porto' ),
				);

				wp_localize_script( 'porto-infinite-scroll', 'porto_infinite_scroll', apply_filters( 'porto_infinite_scroll_params', $params ) );
			} else {
				wp_register_script( 'porto-jquery-infinite-scroll', PORTO_URI . '/js/libs/jquery.infinite-scroll.min.js', array(), '2.1.0', true );
				wp_register_script( 'porto-infinite-scroll', PORTO_LIB_URI . '/infinite-scroll/infinite-scroll.min.js', array('porto-theme'), PORTO_VERSION, true );
			}
		}
	}

endif;

Porto_Infinite_Scroll::get_instance();
