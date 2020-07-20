<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH WooCommerce Ajax Search Premium
 * @version 1.2
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAS' ) ) {
	/**
	 * WooCommerce Ajax Search
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAS {
		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WCAS_VERSION;

		/**
		 * Plugin object.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $obj = null;

		/**
		 * Search string.
		 *
		 * @var string
		 */
		private $search_string = '';

		/**
		 * Search reverse string.
		 *
		 * @var string
		 */
		private $search_reverse_string = '';

		/**
		 * Search for order.
		 *
		 * @var string
		 */
		private $search_order = '';

		/**
		 * Post type to search.
		 *
		 * @var string
		 */
		private $post_type = 'any';

		/**
		 * Search options.
		 *
		 * @var array
		 */
		public $search_options = array();

		/**
		 * Flag var.
		 *
		 * @var bool
		 */
		private $ajax = false;

		/**
		 * Constructor
		 *
		 * @return mixed|YITH_WCAS_Admin|YITH_WCAS_Frontend
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->obj = false;

			// Load Plugin Framework.
			if ( ! isset( $_REQUEST['action'] ) || 'yith_ajax_search_products' !== sanitize_text_field( $_REQUEST['action'] ) ) {
				add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

				if ( is_admin() ) {
					$this->obj = new YITH_WCAS_Admin();
				} else {
					$this->obj = new YITH_WCAS_Frontend();
				}
			} else {
				include_once YITH_WCAS_DIR . 'plugin-fw/yit-woocommerce-compatibility.php';
			}

			// actions.
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'widgets_init', array( $this, 'registerWidgets' ) );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			add_action( 'wp', array( $this, 'remove_pre_get_posts' ) );

			if ( ! isset( $_REQUEST['wc-ajax'] ) ) {
				add_action( 'wp_ajax_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );
				add_action( 'wp_ajax_nopriv_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );
			} else {
				add_action( 'wc_ajax_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );
			}

			// YITH WooCommerce Brands Compatibility.
			add_filter( 'yith_wcas_search_options', array( $this, 'add_brands_search_option' ) );
			add_filter( 'yith_wcas_search_params', array( $this, 'add_brands_search_params' ) );

			// YITH WooCommerce Multi Vendor Premium Compatibility.
			if ( class_exists( 'YITH_Vendors' ) ) {
				add_filter( 'yith_wcas_search_options', array( $this, 'add_vendor_search_option' ) );
				add_filter( 'yith_wcas_search_params', array( $this, 'add_vendor_search_params' ) );
			}

			// register shortcode.
			add_shortcode( 'yith_woocommerce_ajax_search', array( $this, 'add_woo_ajax_search_shortcode' ) );

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once( YITH_WCAS_DIR . 'includes/compatibility/elementor/class.yith-wcas-elementor.php' );
			}

			return $this->obj;
		}


		/**
		 * Init method:
		 *  - default options
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function init() {

			$ordering_args = WC()->query->get_catalog_ordering_args( 'menu_order' );

			$search_by_cf = get_option( 'yith_wcas_cf_name' );
			if ( '' !== $search_by_cf ) {
				$search_by_cf = array_map( 'trim', explode( ',', $search_by_cf ) );
			}

			$this->search_options = apply_filters(
				'yith_wcas_search_params',
				array(
					'search_by_title'          => apply_filters( 'yith_wcas_search_in_title', get_option( 'yith_wcas_search_in_title', 'yes' ) ),
					'search_by_excerpt'        => apply_filters( 'yith_wcas_search_in_excerpt', get_option( 'yith_wcas_search_in_excerpt' ) ),
					'search_by_content'        => apply_filters( 'yith_wcas_search_in_content', get_option( 'yith_wcas_search_in_content' ) ),
					'search_by_cat'            => apply_filters( 'yith_wcas_search_in_product_categories', get_option( 'yith_wcas_search_in_product_categories' ) ),
					'search_by_tag'            => apply_filters( 'yith_wcas_search_in_product_tags', get_option( 'yith_wcas_search_in_product_tags' ) ),
					'search_by_sku'            => apply_filters( 'yith_wcas_search_by_sku', get_option( 'yith_wcas_search_by_sku' ) ),
					'search_by_sku_variations' => apply_filters( 'yith_wcas_search_by_sku_variations', get_option( 'yith_wcas_search_by_sku_variations' ) ),
					'search_by_author'         => apply_filters( 'yith_wcas_search_by_author', get_option( 'yith_wcas_search_in_author' ) ),
					'search_by_cf'             => ( is_array( $search_by_cf ) ) ? implode( "','", $search_by_cf ) : '',
					'posts_per_page'           => apply_filters( 'yith_wcas_search_posts_per_page', get_option( 'yith_wcas_posts_per_page' ) ),
					'orderby'                  => apply_filters( 'yith_wcas_search_orderby', $ordering_args['orderby'] ),
					'order'                    => apply_filters( 'yith_wcas_search_order', $ordering_args['order'] ),
					'like'                     => apply_filters( 'yith_wcas_search_with_like', false ),
					'hide_out_of_stock'        => apply_filters( 'yith_wcas_hide_out_of_stock', get_option( 'yith_wcas_hide_out_of_stock', 'no' ) ),
					'reverse'                  => apply_filters( 'yith_wcas_search_reverse', false ),
				)
			);

			if ( isset( $ordering_args['meta_key'] ) && '' !== $ordering_args['meta_key'] ) {
				$this->search_options['meta_key'] = apply_filters( 'yith_wcas_search_meta_key', $ordering_args['meta_key'] );
			}

		}

		/**
		 * Load Plugin Framework
		 *
		 * @return void
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Load template for [yith_woocommerce_ajax_search] shortcode
		 *
		 * @access public
		 *
		 * @param array $args Array of arguments.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function add_woo_ajax_search_shortcode( $args = array() ) {

			$template_default = get_option( 'yith_wcas_search_default_template', '' );

			$args     = shortcode_atts(
				array(
					'template' => $template_default,
					'class'    => '',
				),
				$args
			);
			$template = ! empty( $args['template'] ) ? '-wide' : '';
			unset( $args['template'] );
			ob_start();
			$wc_get_template = function_exists( 'wc_get_template' ) ? 'wc_get_template' : 'woocommerce_get_template';
			$wc_get_template( 'yith-woocommerce-ajax-search' . $template . '.php', $args, '', YITH_WCAS_DIR . 'templates/' );

			return ob_get_clean();
		}

		/**
		 * Load and register widgets
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function registerWidgets() {
			register_widget( 'YITH_WCAS_Ajax_Search_Widget' );
		}


		/**
		 * Extend join on query.
		 *
		 * @param string $join Join string.
		 *
		 * @return string
		 */
		function extend_search_join( $join ) {
			global $wpdb;

			if ( isset( $this->search_options['meta_key'] ) && ! empty( $this->search_options['meta_key'] ) ) {
				$join .= " LEFT JOIN {$wpdb->postmeta} as ywmk ON ( {$wpdb->posts}.ID = ywmk.post_id ) ";
			}

			if ( 'yes' === $this->search_options['search_by_author'] ) {
				$join .= " LEFT JOIN {$wpdb->users} as us ON ( {$wpdb->posts}.post_author = us.ID ) ";
			}

			// YITH WooCommerce Brands Compatibility.
			$search_by_brand = isset( $this->search_options['search_by_brand'] ) && 'yes' === $this->search_options['search_by_brand'];

			if ( 'yes' === $this->search_options['search_by_cat'] || 'yes' === $this->search_options['search_by_tag'] || $search_by_brand || apply_filters( 'yith_wcas_search_for_taxonomy', false ) ) {
				$join .= " LEFT JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id LEFT JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id LEFT JOIN {$wpdb->terms} tm ON tm.term_id = tt.term_id";
			}

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$join                        .= " LEFT JOIN {$wpdb->term_relationships} tr_v ON {$wpdb->posts}.ID = tr_v.object_id LEFT JOIN {$wpdb->term_taxonomy} tt_v ON ( tt_v.term_taxonomy_id=tr_v.term_taxonomy_id AND tt_v.taxonomy LIKE 'product_visibility' AND tt_v.term_taxonomy_id NOT IN ( " . $product_visibility_term_ids['exclude-from-search'] . " ) ) LEFT JOIN {$wpdb->terms} tm_v ON tm_v.term_id = tt_v.term_id";

			if ( '' !== $this->search_options['search_by_cf'] ) {
				$join .= " LEFT JOIN {$wpdb->postmeta} as cf1 ON ( {$wpdb->posts}.ID = cf1.post_id AND cf1.meta_key IN ('{$this->search_options['search_by_cf']}' ) ) ";
			}

			if ( 'yes' === $this->search_options['hide_out_of_stock'] && 'product' === $this->post_type ) {
				$join .= " INNER JOIN {$wpdb->postmeta} as ywpm_ous ON ( {$wpdb->posts}.ID = ywpm_ous.post_id AND  ywpm_ous.meta_key = '_stock_status' AND ywpm_ous.meta_value NOT IN ('outofstock') )";
			}

			return $join;
		}

		/**
		 * Extend search query
		 *
		 * @param string $where WHERE string.
		 *
		 * @return string
		 */
		public function extend_search_where( $where = '' ) {

			global $wpdb;

			$terms = array();

			//	$where
			if ( 'yes' === $this->search_options['search_by_cat'] ) {
				if ( 'product' === $this->post_type ) {
					$terms[] = 'product_cat';
				} else {
					$terms[] = 'category';
					$terms[] = 'product_cat';
				}
			}

			if ( 'yes' === $this->search_options['search_by_tag'] ) {
				if ( 'product' === $this->post_type ) {
					$terms[] = 'product_tag';
				} else {
					$terms[] = 'product_tag';
					$terms[] = 'post_tag';
				}
			}

			// YITH WooCommerce Brands Compatibility.
			if ( class_exists( 'YITH_WCBR' ) ) {
				if ( isset( $this->search_options['search_by_brand'] ) && 'yes' === $this->search_options['search_by_brand'] && 'product' === $this->post_type ) {
					if ( ! in_array( YITH_WCBR::$brands_taxonomy, $terms ) ) {
						$terms[] = YITH_WCBR::$brands_taxonomy;
					}
				}
			}

			// YITH WooCommerce Multi Vendor Compatibility.
			if ( function_exists( 'YITH_Vendors' ) ) {
				if ( isset( $this->search_options['search_by_vendor'] ) && 'yes' === $this->search_options['search_by_vendor'] && 'product' === $this->post_type ) {
					if ( ! in_array( YITH_Vendors()->get_taxonomy_name(), $terms ) ) {
						$terms[] = YITH_Vendors()->get_taxonomy_name();
					}
				}
			}

			// YITH WooCommerce Brands Compatibility.
			$terms = apply_filters( 'yith_wcas_search_taxonomy_terms', $terms );

			$reverse = $this->search_options['reverse'];

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$where                       = " AND (
				{$wpdb->posts}.ID NOT IN (
				SELECT object_id
				FROM {$wpdb->term_relationships}
				WHERE term_taxonomy_id IN (" . $product_visibility_term_ids['exclude-from-search'] . ")
			)
) AND {$wpdb->posts}.post_status = 'publish' ";

			$where .= ' AND (';

			if ( $this->search_options['like'] ) {

				$where .= $reverse ? '(' : '';

				if ( 'yes' === $this->search_options['search_by_title'] ) {
					$where .= "  ( LOWER( {$wpdb->posts}.post_title ) LIKE '" . $this->search_string . "') ";
					$where .= $reverse ? " OR ( LOWER( {$wpdb->posts}.post_title ) LIKE '" . $this->search_reverse_string . "') )" : '';
				} else {
					$where .= " 1=2 ";
				}

				if ( 'yes' === $this->search_options['search_by_excerpt'] ) {
					$where .= " OR ( LOWER({$wpdb->posts}.post_excerpt) LIKE '" . $this->search_string . "') ";
				}

				if ( 'yes' === $this->search_options['search_by_content'] ) {
					$where .= " OR ( LOWER({$wpdb->posts}.post_content) LIKE '" . $this->search_string . "')  ";
				}

				if ( '' !== $this->search_options['search_by_cf'] ) {
					$where .= " OR ( LOWER(cf1.meta_value) LIKE '{$this->search_string}' )  ";
				}

				$addor = true;

				if ( ! empty( $terms ) ) {
					$where .= ( $addor ) ? ' OR ' : '';
					$where .= " (( LOWER(tm.name) LIKE '" . $this->search_string . "' OR LOWER(tm.slug) LIKE '" . $this->search_string . "') AND tt.taxonomy IN ('" . implode( "','", $terms ) . "')) ";
				}

				$where .= ' ) ';
				if ( 'yes' === $this->search_options['search_by_author'] ) {
					$where .= " OR us.user_nicename LIKE '" . $this->search_string . "' ";
				}
			} else {

				$where .= $reverse ? '(' : '';
				if ( 'yes' === $this->search_options['search_by_title'] ) {
					$where .= "  ( LOWER( {$wpdb->posts}.post_title ) REGEXP  '" . $this->search_string . "') ";
					$where .= $reverse ? " OR ( LOWER( {$wpdb->posts}.post_title ) REGEXP '" . $this->search_reverse_string . "') )" : '';
				} else {
					$where .= " 1=2 ";
				}

				if ( 'yes' === $this->search_options['search_by_excerpt'] ) {
					$where .= " OR ( LOWER({$wpdb->posts}.post_excerpt) REGEXP '" . $this->search_string . "') ";
				}

				if ( 'yes' === $this->search_options['search_by_content'] ) {
					$where .= " OR ( LOWER({$wpdb->posts}.post_content) REGEXP '" . $this->search_string . "')  ";
				}

				if ( '' !== $this->search_options['search_by_cf'] ) {
					$where .= " OR ( LOWER(cf1.meta_value) REGEXP '{$this->search_string}' )  ";
				}

				$addor = true;

				if ( ! empty( $terms ) ) {
					$where .= ( $addor ) ? ' OR ' : '';
					$where .= " (( LOWER(tm.name) REGEXP '" . $this->search_string . "' OR LOWER(tm.slug) REGEXP '" . $this->search_string . "') AND tt.taxonomy IN ('" . implode( "','", $terms ) . "')) ";
				}

				$where .= ' ) ';

				if ( 'yes' === $this->search_options['search_by_author'] ) {
					$where .= " OR us.user_nicename REGEXP '" . $this->search_string . "' ";
				}
			}

			$allowed_post_type = array(
				'product',
			);

			if ( 'any' == get_option( 'yith_wcas_default_research', 'product' ) ) {

				$allowed_post_type = array_merge( $allowed_post_type, array( 'post', 'page' ) );
			}

			if ( 'yes' == get_option( 'yith_wcas_include_variations', 'no' ) ) {
				$allowed_post_type[] = 'product_variation';
			}

			$where .= " AND {$wpdb->posts}.post_type IN ('" . implode( "','", $allowed_post_type ) . "' )";

			$where = apply_filters( 'yith_wcas_search_where', $where, $this->search_string );

			return $where;
		}


		/**
		 * Change the query string.
		 *
		 * @param string $search_query Query string.
		 *
		 * @return mixed|void
		 */
		public function query_string_changes( $search_query ) {

			$string = apply_filters( 'yith_wcas_search_string_before_manipulation', $search_query );

			$string = preg_replace( '/\s+/', ' ', $string );
			$string = str_replace( '\\', '', $string );
			$string = str_replace( '\'', ' ', $string );

			if ( $this->search_options['like'] ) {

				$string = '%' . $string . '%';
				$string = str_replace( '&', '%', $string );
				$string = str_replace( '&amp;', '%', $string );
				$string = str_replace( ' ', '%', $string );
				$string = str_replace( '#039;', '%', $string );

			} else {
				$string = str_replace( '&#039;', '', $string );
				$string = str_replace( '[', '', $string );
				$string = str_replace( ']', '', $string );
				$string = str_replace( '{', '', $string );
				$string = str_replace( '}', '', $string );

				// search both or singular.
				if ( get_option( 'yith_wcas_search_type_more_words' ) === 'and' ) {
					$string = str_replace( '&', '', $string );
					$string = str_replace( '°', '', $string );
					$string = str_replace( ' ', '?(.*)', $string );
				} else {
					$string = str_replace( '&amp;', ' ', $string );
					$string = str_replace( '°', ' ', $string );
					$string = str_replace( ' ', '|', trim( $string ) );
				}
			}

			return apply_filters( 'yith_wcas_search_string_manipulation', $string );
		}

		/**
		 * Get reverse string.
		 *
		 * @param string $string Query string.
		 *
		 * @return string
		 */
		public function get_reverse_string( $string ) {
			$my_array   = str_word_count( $string, 1 );
			$reverse    = array_reverse( $my_array );
			$new_string = '';
			foreach ( $reverse as $rev ) {
				$new_string .= $rev . ' ';
			}

			return trim( $new_string );
		}


		/**
		 * @return string
		 */
		public function get_search_string() {
			return $this->search_string;
		}

		/**
		 * @return string
		 */
		public function get_ajax() {
			return $this->ajax;
		}

		/**
		 * Perform jax search products
		 */
		public function ajax_search_products() {
			$time_start         = getmicrotime();
			$transient_enabled  = get_option( 'yith_wcas_enable_transient', 'no' );
			$transient_duration = get_option( 'yith_wcas_transient_duration', 12 );

			$this->search_string = apply_filters( 'yith_wcas_ajax_search_products_search_query', ( trim( sanitize_text_field( $_REQUEST['query'] ) ) ) );
			$this->search_string = function_exists( 'mb_strtolower' ) ? mb_strtolower( $this->search_string, 'UTF-8' ) : strtolower( $this->search_string );
			$this->search_string = apply_filters( 'yith_wcas_ajax_lower_search_query', $this->search_string, $this->search_string );

			$have_results   = true;
			$transient_name = 'ywcas_' . ( isset( $_REQUEST['lang'] ) ? sanitize_text_field( $_REQUEST['lang'] ) . '_' : '' ) . $this->search_string;
			if ( 'no' === $transient_enabled || false === ( $suggestions = get_transient( $transient_name ) ) ) {

				// get the order by filter.
				$search_strings = $this->parse_search_string( $this->search_string );

				$this->search_order  = $this->parse_search_order( $this->search_string, $search_strings );
				$this->search_string = $this->query_string_changes( $this->search_string );

				$this->search_reverse_string = $this->get_reverse_string( $this->search_string );
				$this->search_reverse_string = $this->query_string_changes( $this->search_reverse_string );

				$post_type       = ( isset( $_REQUEST['post_type'] ) && 'any' === sanitize_text_field( $_REQUEST['post_type'] ) ) ? 'any' : 'product';
				$this->post_type = apply_filters( 'yith_wcas_ajax_search_products_post_type', esc_attr( $post_type ) );

				$suggestions = array();

				$args = array(
					'post_type'           => $this->post_type,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'orderby'             => $this->search_options['orderby'],
					'order'               => $this->search_options['order'],
					'posts_per_page'      => apply_filters( 'yith_wcas_ajax_search_products_posts_per_page', (int) $this->search_options['posts_per_page'] + 1 ),
					'suppress_filters'    => false,
				);

				if ( 'product' === $this->post_type ) {

					$product_visibility_term_ids = wc_get_product_visibility_term_ids();
					$args['tax_query'][]         = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['exclude-from-search'],
						'operator' => 'NOT IN',
					);

					/* perform the research if there's a request with a specific category */
					if ( isset( $_REQUEST['product_cat'] ) && ! empty( sanitize_text_field( $_REQUEST['product_cat'] ) ) ) {
						$args['tax_query'][] = array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'slug',
								'terms'    => $_REQUEST['product_cat'],
							),
						);
					}
				}

				add_filter( 'posts_where', array( $this, 'extend_search_where' ), 9 );
				add_filter( 'posts_join', array( $this, 'extend_search_join' ) );
				add_filter( 'posts_clauses', array( $this, 'search_posts_clauses' ), 10 );
				if ( apply_filters( 'yith_wcas_apply_orderby_filter', true, $args ) ) {
					add_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );
				}

				do_action( 'ywcas_before_do_the_request' );

				$results = apply_filters( 'ywrac_results', get_posts( $args ), sanitize_text_field( $_REQUEST['query'] ) );

				if ( count( $results ) < $this->search_options['posts_per_page'] ) {

					// collect the id of results.
					$is_posts = array();
					if ( $results ) {
						foreach ( $results as $key => $value ) {
							$is_posts[] = intval( $value->ID );
						}
					}

					$product_in     = $this->extend_to_sku();
					$product_by_sku = array();

					if ( ! empty( $product_in ) ) {
						$product_in       = array_map( 'intval', $product_in );
						$args['post__in'] = array_diff( $product_in, $is_posts );

						if ( $args['post__in'] ) {
							remove_filter( 'posts_where', array( $this, 'extend_search_where' ), 9 );
							remove_filter( 'posts_join', array( $this, 'extend_search_join' ) );
							remove_filter( 'posts_clauses', array( $this, 'search_posts_clauses' ) );
							remove_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );
							$product_by_sku = get_posts( $args );
						}
					}

					$results = array_merge( $results, $product_by_sku );
				}

				if ( ! empty( $results ) ) {

					$max_number   = apply_filters( 'yith_wcas_search_posts_per_page', get_option( 'yith_wcas_posts_per_page' ) );
					$have_results = ( ( count( $results ) - $max_number ) > 0 ) ? true : false;
					$i            = 0;
					$ids          = array();
					foreach ( $results as $post ) {
						if ( $i === intval( $max_number ) ) {
							break;
						}
						if ( ! in_array( $post->ID, $ids ) ) {
							$ids[] = $post->ID;
							if ( 'product' === $post->post_type ) {

								$product = wc_get_product( $post );

								if ( $product->is_visible() || apply_filters( 'ywcas_show_not_visible', false, $product ) ) {
									$i ++;
									$suggest = apply_filters(
										'yith_wcas_suggestion',
										array(
											'id'    => $product->get_id(),
											'value' => wp_strip_all_tags( $product->get_title() ),
											'url'   => $product->get_permalink(),
										),
										$product
									);

									if ( get_option( 'yith_wcas_show_thumbnail' ) === 'left' || get_option( 'yith_wcas_show_thumbnail' ) === 'right' ) {
										$thumb_size     = apply_filters( 'yith_wcas_thumbnail_size', 'shop_thumbnail' );
										$thumb          = $product->get_image( $thumb_size, array( 'class' => 'ywcas_img ' . esc_attr( 'align-' . get_option( 'yith_wcas_show_thumbnail' ) ) ) );
										$suggest['img'] = sprintf( '<div class="yith_wcas_result_image %s">%s</div>', esc_attr( 'align-' . get_option( 'yith_wcas_show_thumbnail' ) ), $thumb );
									}

									if ( ( $product->is_on_sale() && get_option( 'yith_wcas_show_sale_badge' ) !== 'no' ) || ( $product->is_featured() && get_option( 'yith_wcas_show_featured_badge' ) !== 'no' ) || ( ! $product->is_in_stock() && get_option( 'yith_wcas_show_outofstock_badge' ) !== 'no' ) ) {
										$suggest['div_badge_open'] = '<div class="badges">';
										if ( $product->is_on_sale() && get_option( 'yith_wcas_show_sale_badge' ) !== 'no' ) {
											$suggest['on_sale'] = '<span class="yith_wcas_result_on_sale">' . __( 'sale', 'yith-woocommerce-ajax-search' ) . '</span>';
										}

										if ( ! $product->is_in_stock() && get_option( 'yith_wcas_show_outofstock_badge' ) !== 'no' ) {
											$suggest['outofstock'] = '<span class="yith_wcas_result_outofstock">' . __( 'Out of stock', 'yith-woocommerce-ajax-search' ) . '</span>';
										}

										if ( $product->is_featured() && get_option( 'yith_wcas_show_featured_badge' ) !== 'no' && ! ( get_option( 'yith_wcas_hide_feature_if_on_sale' ) === 'yes' && $product->is_on_sale() ) ) {
											$suggest['featured'] = '<span class="yith_wcas_result_featured">' . __( 'featured', 'yith-woocommerce-ajax-search' ) . '</span>';
										}
										$suggest['div_badge_close'] = '</div>';
									}

									if ( get_option( 'yith_wcas_show_excerpt' ) !== 'no' ) {
										$short_description  = yit_get_prop( $product, 'short_description', true );
										$description        = yit_get_prop( $product, 'description', true );
										$excerpt            = ( '' !== $short_description ) ? $short_description : $description;
										$num_of_words       = ( get_option( 'yith_wcas_show_excerpt_num_words' ) ) ? get_option( 'yith_wcas_show_excerpt_num_words' ) : 10;
										$excerpt            = wp_strip_all_tags( strip_shortcodes( preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $excerpt ) ) );
										$suggest['excerpt'] = sprintf( '<p class="yith_wcas_result_excerpt">%s</p>', wp_trim_words( $excerpt, $num_of_words ) );
									}

									if ( get_option( 'yith_wcas_categories' ) === 'yes' ) {
										$categories = array();
										$terms      = get_the_terms( $product->get_id(), 'product_cat' );
										if ( $terms ) {
											foreach ( $terms as $term ) {
												$categories[] = $term->name;
											}
											$suggest['product_categories'] = sprintf( '<div class="yith_wcas_result_categories">%s</div>', implode( ', ', $categories ) );
										}
									}

									if ( get_option( 'yith_wcas_show_price' ) !== 'no' ) {
										$suggest['price'] = $product->get_price_html();
									}

									$suggestions[] = apply_filters( 'yith_wcas_suggestion_end', $suggest, $product );
								}
							} else {
								$suggest = apply_filters(
									'yith_wcas_suggestion',
									array(
										'id'    => $post->ID,
										'value' => wp_strip_all_tags( $post->post_title ),
										'url'   => get_permalink( $post->ID ),
									),
									$post
								);

								if ( has_post_thumbnail( $post->ID ) && ( get_option( 'yith_wcas_show_thumbnail' ) === 'left' || get_option( 'yith_wcas_show_thumbnail' ) === 'right' ) ) {
									$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
									if ( $thumb ) {
										$suggest['img'] = sprintf( '<div class="yith_wcas_result_image %s"><img src="%s" alt="%s"></div>', esc_attr( 'align-' . get_option( 'yith_wcas_show_thumbnail' ) ), $thumb['0'], $post->post_title );
									}
								}

								if ( get_option( 'yith_wcas_show_excerpt' ) !== 'no' ) {
									$excerpt            = ( '' !== $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
									$num_of_words       = ( get_option( 'yith_wcas_show_excerpt_num_words' ) ) ? get_option( 'yith_wcas_show_excerpt_num_words' ) : 10;
									$excerpt            = wp_strip_all_tags( strip_shortcodes( preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $excerpt ) ) );
									$suggest['excerpt'] = sprintf( '<p class="yith_wcas_result_excerpt">%s</p>', wp_trim_words( $excerpt, $num_of_words ) );
								}

								$suggestions[] = apply_filters( 'yith_wcas_suggestion_end', $suggest, $post );
							}
						}
					}
				} else {
					$have_results  = false;
					$suggestions[] = array(
						'id'    => - 1,
						'value' => get_option( 'yith_wcas_search_show_no_results_text' ) ? get_option( 'yith_wcas_search_show_no_results_text' ) : __( 'No results', 'yith-woocommerce-ajax-search' ),
						'url'   => '',
					);

				}
				wp_reset_postdata();

				if ( 'yes' === $transient_enabled ) {
					set_transient( $transient_name, $suggestions, $transient_duration * HOUR_IN_SECONDS );
				}
			}

			$suggestions = apply_filters( 'yith_wcas_suggestions', $suggestions );

			$time_end = getmicrotime();

			$time        = $time_end - $time_start;
			$suggestions = array(
				'results'     => $have_results,
				'suggestions' => $suggestions,
				'time'        => $time,
			);

			wp_send_json( $suggestions );

		}


		/**
		 * Alter the main query.
		 *
		 * @param string $q
		 */
		public function pre_get_posts( $q ) {

			global $wp_the_query;

			if ( ! is_admin() && ! empty( $wp_the_query->query_vars['s'] ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

				$pt              = isset( $wp_the_query->query_vars['post_type'] ) ? $wp_the_query->query_vars['post_type'] : 'any';
				$pt              = is_array( $pt ) ? implode( ',', $pt ) : $pt;
				$this->post_type = apply_filters( 'yith_wcas_ajax_search_products_post_type', esc_attr( $pt ) );

				$qv = apply_filters( 'yith_wcas_ajax_search_products_search_query', esc_attr( trim( $wp_the_query->query_vars['s'] ) ) );
				while ( substr( $qv, 0, 1 ) === '-' ) {
					$qv = substr( $qv, 1 );
				}

				// get the order by filter.
				$search_strings              = $this->parse_search_string( $qv );
				$this->search_order          = $this->parse_search_order( $qv, $search_strings );
				$this->search_string         = $this->query_string_changes( $qv );
				$this->search_reverse_string = $this->get_reverse_string( $qv );
				$this->search_reverse_string = $this->query_string_changes( $this->search_reverse_string );

				set_query_var( 's', $this->search_string );
				add_filter( 'posts_join', array( $this, 'search_post_join' ) );
				add_filter( 'posts_where', array( $this, 'search_post_where' ), 9 );
				if ( ! isset( $_GET['orderby'] ) ) {
					add_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );
				}
				add_filter( 'posts_clauses', array( $this, 'search_posts_clauses' ), 10 );

				set_query_var( 's', $qv );

			}
		}

		/**
		 * Extend orderby section of the main query.
		 *
		 * @param string $orderby
		 *
		 * @return string
		 */
		public function search_post_orderby( $orderby ) {

			return $this->search_order;
		}

		/**
		 * Extend join section of the main query.
		 *
		 * @param string $join
		 *
		 * @return string
		 */
		public function search_post_join( $join ) {
			$join = $this->extend_search_join( $join );

			return $join;
		}

		/**
		 * Extend where section of the main query.
		 *
		 * @param string $where Where string.
		 *
		 * @return string
		 */
		public function search_post_where( $where ) {

			if ( '' !== $where && apply_filters( 'yith_ajax_search_change_where', true ) ) {
				$where_array = array_filter( array_map( 'trim', explode( 'AND', $where ) ) );
				$where_array = apply_filters( 'yith_ajax_search_change_where_array', $where_array );
				$ands        = $where_array;
				$where       = '';
				foreach ( $ands as $key => $value ) {
					if ( strpos( $value, 'post_content' ) !== false ) {
						unset( $where_array[ $key ] );
					}

					$where .= ' AND ' . $value;
				}
			}

			global $wpdb;
			$this->ajax = true;
			$where      = $this->extend_search_where( $where );

			/* search by sku */
			$product_by_sku = $this->extend_to_sku();

			if ( ! empty( $product_by_sku ) ) {
				$where .= ' OR ' . $wpdb->posts . '.ID IN (' . implode( ',', $product_by_sku ) . ') ';
			}

			return $where;
		}

		/**
		 * Search post clauses.
		 *
		 * @param array $clauses post clauses.
		 *
		 * @return array
		 */
		public function search_posts_clauses( $clauses ) {
			global $wpdb;
			$clauses            = empty( $clauses ) ? array() : (array) $clauses;
			$clauses['groupby'] = "{$wpdb->posts}.ID";

			return $clauses;
		}


		/**
		 * Return a list of product id if the option search by sku is active
		 *
		 * @return array
		 * @since    1.3.0
		 * @author   Emanuela Castorina
		 * @internal param bool $only_visible
		 *
		 */
		public function extend_to_sku() {

			$product_in = array();

			$this->remove_pre_get_posts();

			remove_filter( 'posts_where', array( $this, 'extend_search_where' ), 9 );
			remove_filter( 'posts_join', array( $this, 'extend_search_join' ) );
			remove_filter( 'posts_groupby', array( $this, 'search_posts_clauses' ) );
			remove_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );

			if ( 'yes' === $this->search_options['search_by_sku'] ) {

				$args = array(
					'post_type'        => 'product',
					'fields'           => 'ids',
					'meta_query'       => array(
						array(
							'key'     => '_sku',
							'value'   => esc_sql( $this->search_string ),
							'compare' => apply_filters( 'ywcas_search_sku_by', 'LIKE' ),
						),
					),
					'suppress_filters' => false,
					'posts_per_page'   => - 1,
				);

				$product_visibility_term_ids = wc_get_product_visibility_term_ids();
				$args['tax_query'][]         = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['exclude-from-search'],
					'operator' => 'NOT IN',
				);

				$args = apply_filters( 'ywcas_extend_to_sku_before_query', $args, $this->search_string );

				$product_in = new WP_Query( $args );

				$product_in = $product_in->posts;

				if ( 'yes' === $this->search_options['search_by_sku_variations'] ) {
					$args['post_type'] = 'product_variation';
					$args['fields']    = 'id=>parent';
					$skus_posts        = new WP_Query( $args );
					$skus_posts        = $skus_posts->posts;
					$sku_to_id         = array();
					if ( $skus_posts ) {
						foreach ( $skus_posts as $skus_post ) {
							$sku_to_id[] = $skus_post->post_parent;
						}
					}
					$sku_to_id = array_filter( $sku_to_id );
				}

				if ( ! empty( $sku_to_id ) ) {
					$product_in = array_merge( $sku_to_id, $product_in );
				}
			}

			add_filter( 'posts_join', array( $this, 'search_post_join' ) );

			return $product_in;
		}


		/**
		 * Parse the search string
		 *
		 * @param string $s Query string.
		 *
		 * @return array
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
		protected function parse_search_string( $s ) {
			// added slashes screw with quote grouping when done early, so done later.
			$s = stripslashes( $s );
			$s = str_replace( array( "\r", "\n" ), '', $s );

			if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $s, $matches ) ) {
				$search_terms = $this->parse_search_terms( $matches[0] );
				// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
				if ( empty( $search_terms ) || count( $search_terms ) > 9 ) {
					$search_terms = array( $s );
				}
			} else {
				$search_terms = array( $s );
			}

			return $search_terms;
		}

		/**
		 * Parse the search terms
		 *
		 * @param array $terms Search terms.
		 *
		 * @return array
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
		protected function parse_search_terms( $terms ) {

			$checked = array();

			foreach ( $terms as $term ) {
				// keep before/after spaces when term is for exact match.
				if ( preg_match( '/^".+"$/', $term ) ) {
					$term = trim( $term, "\"'" );
				} else {
					$term = trim( $term, "\"' " );
				}

				// Avoid single A-Z.
				if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z]$/i', $term ) ) ) {
					continue;
				}

				$checked[] = $term;
			}

			return $checked;
		}

		/**
		 * Parse the search order
		 *
		 * @param string $s            Query string.
		 * @param array  $search_terms Terms od search.
		 *
		 * @return string
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
		protected function parse_search_order( $s, $search_terms ) {
			global $wpdb;

			$search_orderby = '';

			if ( get_option( 'yith_wcas_order_by_post_type' ) === 'yes' ) {
				$post_type_order = ( get_option( 'yith_wcas_order_by_post_type_select' ) === 'product' ) ? "'product', 'post', 'page'" : "'post', 'page', 'product'";
				$search_orderby  = apply_filters( 'yith_wcas_filter_by_post_type', ' FIELD(' . $wpdb->posts . '.post_type, ' . $post_type_order . ') ASC,  ', $s, $search_terms );
			}

			if ( isset( $this->search_options['meta_key'] ) && ! empty( $this->search_options['meta_key'] ) ) {
				$search_orderby .= 'ywmk.meta_value ' . $this->search_options['order'];
			} else {
				$search_orderby_title = array();
				foreach ( $search_terms as $term ) {
					$like                   = '%' . $wpdb->esc_like( $term ) . '%';
					$search_orderby_title[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $like );
				}

				if ( count( $search_terms ) > 0 && empty( $this->search_options['search_by_cf'] ) ) {

					$num_terms = count( $search_orderby_title );

					$like           = '%' . $wpdb->esc_like( $s ) . '%';
					$like2          = $wpdb->esc_like( $s ) . '%';
					$search_orderby .= '(CASE ';
					// sentence match in 'post_title'.
					$search_orderby .= $wpdb->prepare( "WHEN $wpdb->posts.post_title LIKE %s THEN 1 ", $like2 );
					$search_orderby .= $wpdb->prepare( "WHEN $wpdb->posts.post_title LIKE %s THEN 2 ", $like );

					// sanity limit, sort as sentence when more than 6 terms.
					// (few searches are longer than 6 terms and most titles are not).
					if ( $num_terms < 7 && count( $search_terms ) > 1 ) {
						// all words in title.
						$search_orderby .= 'WHEN ' . implode( ' AND ', $search_orderby_title ) . ' THEN 3 ';
						// any word in title, not needed when $num_terms == 1.
						if ( $num_terms > 1 ) {
							$search_orderby .= 'WHEN ' . implode( ' OR ', $search_orderby_title ) . ' THEN 4 ';
						}
					}

					if ( 'yes' === $this->search_options['search_by_tag'] ) {
						$search_orderby .= $wpdb->prepare( 'WHEN LOWER(tm.name) LIKE %s THEN 5 ', $wpdb->esc_like( $s ) );
						$search_orderby .= $wpdb->prepare( 'WHEN LOWER(tm.name) LIKE %s THEN 6 ', esc_sql( $like ) );
					}

					if ( 'yes' === $this->search_options['search_by_content'] ) {
						// sentence match in 'post_content'.
						$search_orderby .= $wpdb->prepare( "WHEN $wpdb->posts.post_content LIKE %s THEN 7 ", $like );
					}

					$search_orderby .= 'ELSE 8 END)';
				} else {
					// single word or sentence search.
					$search_orderby .= reset( $search_orderby_title ) . ' DESC';
				}
			}

			return apply_filters( 'ywcas_parse_search_order', $search_orderby, $s, $search_terms );

		}

		/**
		 * Remove Filter after the query
		 *
		 * @return void
		 *
		 * @since  1.3.6
		 * @author Emanuela Castorina
		 */
		public function remove_pre_get_posts() {
			global $wp_the_query;
			if ( ! is_admin() && ! empty( $wp_the_query->query_vars['s'] ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				remove_filter( 'posts_join', array( $this, 'search_post_join' ) );
				remove_filter( 'posts_where', array( $this, 'search_post_where' ), 9 );
				add_filter( 'posts_groupby', array( $this, 'search_posts_clauses' ) );
				remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			}

		}


		/* === YITH WooCommerce Brands Compatibility === */

		/**
		 * Filters search options, to add brands to search
		 *
		 * @param mixed $search_options Original array of options.
		 *
		 * @return mixed Filtered array of options
		 *
		 * @since  1.3.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
		 */
		public function add_brands_search_option( $search_options ) {
			if ( defined( 'YITH_WCBR' ) ) {
				$options_chunk_1 = array_splice( $search_options['search'], 0, 6 );
				$options_chunk_2 = $search_options['search'];

				$brand_option = array(
					'search_in_product_brands' => array(
						'name'    => __( 'Search in product brands', 'yith-woocommerce-ajax-search' ),
						'desc'    => __( 'Extend search in product brands' ),
						'id'      => 'yith_wcas_search_in_product_brands',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
				);

				$search_options['search'] = array_merge( $options_chunk_1, $brand_option, $options_chunk_2 );
			}

			return $search_options;
		}

		/**
		 * Filters search params, to add brands to search
		 *
		 * @param mixed $search_params Original array of params.
		 *
		 * @return mixed Filtered array of params
		 *
		 * @since  1.3.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
		 */
		public function add_brands_search_params( $search_params ) {
			if ( defined( 'YITH_WCBR' ) ) {
				$search_params['search_by_brand'] = apply_filters( 'yith_wcas_search_in_product_brands', get_option( 'yith_wcas_search_in_product_brands' ) );
			}

			return $search_params;
		}


		/* === YITH WooCommerce Multi Vendor Compatibility === */

		/**
		 * Filters search options, to add brands to search
		 *
		 * @param mixed $search_options Original array of options.
		 *
		 * @return mixed Filtered array of options
		 *
		 * @since  1.4.5
		 * @author Emanuela Castorina
		 */
		public function add_vendor_search_option( $search_options ) {

			$options_chunk_1 = array_splice( $search_options['search'], 0, 6 );
			$options_chunk_2 = $search_options['search'];

			$brand_option = array(
				'search_in_product_vendors' => array(
					'name'    => __( 'Search by vendor', 'yith-woocommerce-ajax-search' ),
					'desc'    => __( 'Extend search in vendors\' products' ),
					'id'      => 'yith_wcas_search_in_vendor',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
			);

			$search_options['search'] = array_merge( $options_chunk_1, $brand_option, $options_chunk_2 );

			return $search_options;
		}

		/**
		 * Filters search params, to add brands to search
		 *
		 * @param mixed $search_params Original array of params.
		 *
		 * @return mixed Filtered array of params.
		 *
		 * @since  1.4.5
		 * @author Emanuela Castorina
		 */
		public function add_vendor_search_params( $search_params ) {

			$search_params['search_by_vendor'] = apply_filters( 'yith_wcas_search_in_vendors', get_option( 'yith_wcas_search_in_vendor' ) );

			return $search_params;
		}

	}
}
