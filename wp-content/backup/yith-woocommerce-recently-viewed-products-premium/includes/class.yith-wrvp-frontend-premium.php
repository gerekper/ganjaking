<?php
/**
 * Frontend Premium class
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WRVP_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WRVP_Frontend_Premium extends YITH_WRVP_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WRVP_Frontend_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Page id
		 *
		 * @var \YITH_WRVP_Frontend_Premium
		 * @since 1.0.0
		 */
		protected $_recently_viewed_page;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WRVP_VERSION;

		/**
		 * The name of meta purchased products
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_meta_purchased_products = 'yith_wrvp_purchased_products';

		/**
		 * The name of filter by categories action
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_filter_cat_action = 'ywrvp_filter_by_cat_action';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WRVP_Frontend_Premium
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct();

			$this->_recently_viewed_page = get_option( 'yith-wrvp-page-id' );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ), 10 );

			add_action( 'wp_login', array( $this, 'init_products_list' ), 10, 2 );

			// remove products form list
			add_action( 'init', array( $this, 'remove_product' ), 5 );

			// action in recently viewed page
			add_action( 'template_redirect', array( $this, 'page_actions' ) );

			add_action( 'init', array( $this, 'create_meta_purchased_products' ), 10 );
			add_action( 'init', array( $this, 'remove_products' ), 20 );

			add_action( 'woocommerce_order_status_completed', array( $this, 'update_meta_purchased_products' ), 99, 1 );
			add_action( 'woocommerce_order_status_processing', array( $this, 'update_meta_purchased_products' ), 99, 1 );

			add_shortcode( 'yith_recenlty_viewed_page', array( $this, 'recently_viewed_page' ) );
			add_shortcode( 'yith_most_viewed_products', array( $this, 'most_viewed_products' ) );
		}

		/**
		 * Recently viewed products page actions
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function page_actions() {

			if( ! is_page( $this->_recently_viewed_page ) ){
				return;
			}

			// remove link
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'add_remove_link' ), 5 );
			// filter by cat
			add_action( 'yith_wrvp_before_products_loop', array( $this, 'filter_by_cat_template' ), 10 );
		}

		/**
		 * Enqueue scripts
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_script(){

			$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_register_script( 'yith-wrvp-frontend', YITH_WRVP_ASSETS_URL . '/js/yith-wrvp-frontend'.$min.'.js', array( 'jquery' ), YITH_WRVP_VERSION, true );
			wp_register_style( 'yith-wrvp-frontend', YITH_WRVP_ASSETS_URL . '/css/yith-wrvp-frontend.css', array(), YITH_WRVP_VERSION );

			//slider jquery plugin
			wp_register_script( 'slick', YITH_WRVP_ASSETS_URL . '/js/slick.min.js', array('jquery'), YITH_WRVP_VERSION, true );
			wp_register_style( 'ywrvp_slick', YITH_WRVP_ASSETS_URL . '/css/slick.css', array(), YITH_WRVP_VERSION );

			wp_enqueue_script( 'yith-wrvp-frontend' );
			wp_enqueue_style( 'yith-wrvp-frontend' );
			wp_enqueue_script( 'slick' );
			wp_enqueue_style( 'ywrvp_slick' );

			wp_localize_script( 'yith-wrvp-frontend', 'ywrvp', array(
				'ajaxurl' 		    => get_permalink( $this->_recently_viewed_page ),
				'products_selector' => apply_filters( 'yith_wrvp_products_selector', '.products' )
			));
		}

		/**
		 * Init cookie for users after a login action
		 *
		 * @access public
		 * @param $username
		 * @param $user
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function init_products_list( $username, $user ) {

			$this->_user_id = $user->data->ID;

			//exit if admin
			if( user_can( $this->_user_id, 'administrator' ) ) {
				return;
			}

			$meta_products_list = get_user_meta( $this->_user_id, $this->_meta_products_list, true );
			$this->_products_list = isset( $_COOKIE[$this->_cookie_name] ) ? unserialize( $_COOKIE[ $this->_cookie_name ] ) : array();

			if( ! empty( $meta_products_list ) ) {
				// merge with cookie value
				foreach( $meta_products_list as $key => $value ) {
					if( in_array( $value, $this->_products_list ) ){
						continue;
					}

					$this->_products_list[ $key ] = $value;
				}
			}

			// remove general cookie
			setcookie( $this->_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false, true );

			// then save
			$this->set_cookie_meta();
		}

		/**
		 * Create an user meta with all purchased products
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function create_meta_purchased_products(){

			// check first if option is enabled
			if( get_option( 'yith-wrvp-excluded-purchased' ) != 'yes' ){
				return;
			}

			$meta = get_user_meta( $this->_user_id, $this->_meta_purchased_products, true );

			if( $meta ) {
				return;
			}
			// if meta not exists, init for customer

			$purchased = $this->get_purchased_products();

			update_user_meta( $this->_user_id, $this->_meta_purchased_products, $purchased );
		}

		/**
		 * Get purchased products for user
		 *
		 * @access protected
		 * @since 1.0.0
		 * @return array
		 * @author Francesco Licandro
		 */
		protected function get_purchased_products() {

			global $wpdb;

			$user = $this->_user_id;

			$query           = array();
			$query['fields'] = "SELECT DISTINCT a.meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta a";
			$query['join']   = " LEFT JOIN {$wpdb->prefix}woocommerce_order_items b ON ( b.order_item_id = a.order_item_id )";
			$query['join']   .= " LEFT JOIN {$wpdb->postmeta} c ON ( c.post_id = b.order_id )";
			$query['join']   .= " LEFT JOIN {$wpdb->posts} d ON ( d.ID = c.post_id )";

			$query['where'] = " WHERE a.meta_key = '_product_id'";
			$query['where'] .= " AND c.meta_key = '_customer_user' AND c.meta_value = {$user}";
			$query['where'] .= " AND d.post_status IN ( 'wc-processing', 'wc-completed' )";

			$results = $wpdb->get_col( implode( ' ', $query ) );

			return $results;
		}

		/**
		 * Update user purchased products
		 *
		 * @access public
		 * @since 1.0.0
		 * @param $order_id
		 * @author Francesco Licandro
		 */
		public function update_meta_purchased_products( $order_id ) {

			// first check option
			if( get_option( 'yith-wrvp-excluded-purchased' ) != 'yes' ){
				return;
			}

			// get order
			$order = wc_get_order( $order_id );

			$items = $order->get_items();
			$to_exclude = array();

			foreach( $items as $item ) {
				if( $item['type'] == 'line_item' && isset( $item['item_meta'] ) ){
					$to_exclude[] = intval( $item['item_meta']['_product_id'][0] );
				}
			}

			$user_id = yit_get_prop( $order, 'customer_user', true );
			$excluded_list = get_user_meta( $user_id, $this->_meta_purchased_products, true );
			$excluded_list = empty( $excluded_list ) ? array() : $excluded_list;

			foreach( $to_exclude as $exclusion ) {
				if( ! in_array( $exclusion, $excluded_list ) ) {
					$excluded_list[] = $exclusion;
				}
			}

			update_user_meta( $user_id, $this->_meta_purchased_products, $excluded_list );
		}

		/**
		 * Shortcode similar products
		 *
		 * @access public
		 * @since 1.0.0
		 * @param mixed $atts
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function similar_products( $atts ) {

			global $product, $woocommerce_loop;

			$atts = shortcode_atts(array(
				'num_post' 			=> get_option( 'yith-wrvp-num-tot-products', '6' ),
				'order' 			=> get_option( 'yith-wrvp-order-products', 'rand' ),
				'title'				=> get_option( 'yith-wrvp-section-title', '' ),
				'slider'			=> get_option( 'yith-wrvp-slider', 'yes' ),
				'autoplay'			=> get_option( 'yith-wrvp-slider-autoplay', 'yes' ),
				'autoplay_speed'    => '3000',
				'prod_type'			=> get_option( 'yith-wrvp-type-products', 'similar' ),
				'similar_type'		=> get_option( 'yith-wrvp-type-similar-products', 'both' ),
				'cat_most_viewed' 	=> get_option( 'yith-wrvp-cat-most-viewed', 'no' ),
				'view_all'			=> get_option( 'yith-wrvp-view-all-text', '' ),
				'view_all_link'		=> '',
				'num_columns' 		=> get_option( 'yith-wrvp-num-visible-products', '4' ),
				'cats_id'			=> '',
				'class'             => ''
			), $atts );

			// extract $atts
			extract( $atts );

			$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts' => 1,
				'no_found_rows' => 1,
				'posts_per_page' => $num_post,
				'order' => 'DESC'
			);

			if( $cat_most_viewed == 'yes' ) {
				// get cat id
				$category = $this->most_viewed_cat();
				$cats_id = array( $category );
			}
			elseif( $cats_id ) {
				$cats_id = explode(',', $cats_id );
				$cats_id = array_filter( $cats_id );
			}
			else {
				$cats_id = array();
			}

			$products_list = array();
			if( $prod_type == 'similar' ) {
				$products_list = $this->get_similar_products( $cats_id, $similar_type );
			}
			else {
				$products_list = array_reverse( array_values( $this->_products_list ) );
				// set tax query
				if( ! empty( $cats_id ) ) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'product_cat',
							'field' => 'id',
							'terms' => $cats_id,
							'operator' => 'IN'
						)
					);
				}
			}

			// remove current product from products list
			if( $product && is_product() ) {
				if( ( $key = array_search( $product->get_id(), $products_list ) ) !== false ) {
					unset( $products_list[ $key ] );
				}
			}

			// also set variable for shop
			$page_url = $view_all_link ? esc_url( $view_all_link ) : get_permalink( $this->_recently_viewed_page );
			$page_url = apply_filters( 'yith_wrvp_view_all_link', $page_url, $atts );

			// set post__in param with products list
			$args['post__in'] = apply_filters( 'yith_wrvp_product_list_shortcode', $products_list, $atts, $cats_id );

			if( empty( $args['post__in'] ) ) {
				$no_found = $this->get_not_found_html();
				return apply_filters( 'yith_wrvp_shortcode_return_html', $no_found, $this->_products_list, $this );
			}

			// hide free
			if( get_option( 'yith-wrvp-hide-free' ) == 'yes' ) {
				$args['meta_query'][] = array(
					'key'     => '_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'DECIMAL'
				);
			}

			// visibility meta query
            $args = yit_product_visibility_meta( $args );

			if( get_option( 'yith-wrvp-hide-out-of-stock' ) == 'yes' ) {
				$args['meta_query'][] = array(
					'key' 		=> '_stock_status',
					'value' 	=> 'instock',
					'compare' 	=> '='
				);
			}

			switch( $order ) {
				case 'sales':
					$args['meta_key']   = 'total_sales';
					$args['orderby']    = 'meta_value_num';
					break;
				case 'newest':
					$args['orderby']    = 'date';
					break;
				case 'high-low':
					$args['meta_key']   = '_price';
					$args['orderby']    = 'meta_value_num';
					break;
				case 'low-high':
					$args['meta_key']   = '_price';
					$args['orderby']    = 'meta_value_num';
					$args['order']      = 'ASC';
					break;
                case 'viewed':
                    $args['orderby']    = 'post__in';
                    break;
				default:
					$args['orderby']    = 'rand';
					break;
			}

			$args = apply_filters('yith_wrvp_shortcode_query_args', $args );

			// TRANSIENT
			$transient_name = 'yith_wrvp_' . md5( 'Similar: ' . json_encode( $args ) );
			if ( false === ( $products_ids = yith_wrvp_get_transient( $transient_name ) ) ) {
				$args['fields'] = 'ids';
				$products_ids = get_posts( $args );
                yith_wrvp_set_transient( $transient_name, $products_ids, WEEK_IN_SECONDS );
			}

			ob_start();

			if ( ! empty( $products_ids ) ) {
				// set main query
				$products = new WP_Query();
				$products->init();
				$products->query = $products->query_vars = wp_parse_args( $args );
				$products->posts = array_map( 'get_post', $products_ids );
				$products->found_posts = $products->post_count = count( $products->posts );
				update_post_caches( $products->posts, 'product' );

				// templare args
				$templates_args = apply_filters( 'yith_wrvp_templates_query_args', array(
					'products'	=> $products,
					'title'		=> $title,
					'slider'	=> $slider,
					'autoplay'	=> $autoplay,
					'page_url'  => $page_url,
					'view_all'  => $view_all,
					'columns' 	=> $num_columns,
					'class'		=> $class,
					'autoplay_speed' => $autoplay_speed
				));

				wc_get_template( 'ywrvp-loop-template.php', $templates_args, '', YITH_WRVP_DIR . 'templates/' );
			}

			$content = ob_get_clean();

			wp_reset_postdata();

			return apply_filters( 'yith_wrvp_shortcode_return_html', $content, $this->_products_list, $this );
		}

		/**
		 * Add remove link for product in list
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function add_remove_link() {

			global $product;

			$query_args = array(
				'_yith_remove_product' => $product->get_id()
			);

			$url = esc_url_raw( add_query_arg( $query_args, get_permalink( get_option( 'yith-wrvp-page-id' ) ) ) );

			echo '<a href="' . $url  .'" class="remove-product">X ' . esc_html__( 'Remove', 'yith-woocommerce-recently-viewed-products' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Remove products from product list and update cookie and meta
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function remove_product() {

			if( ! isset( $_GET['_yith_remove_product'] ) ) {
				return;
			}

			$id = intval( $_GET['_yith_remove_product'] );

			if( ( $key = array_search( $id, $this->_products_list ) ) !== false ) {
				unset( $this->_products_list[ $key ] );
			}

			// set meta and cookie with new products list
			$this->set_cookie_meta();

			// the redirect to shop
			$url = esc_url_raw( remove_query_arg( array( '_yith_remove_product', '_yith_nonce' ) ) );
			wp_safe_redirect( $url );
			exit;
		}


		/**
		 * Add categories filter in custom shop page
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function filter_by_cat_template() {

			$categories = $this->get_list_terms( 'product_cat', true );

			$args = apply_filters( 'yith-wrvp-filter-by-cat-args', array(
				'categories' => $categories
			));

			wc_get_template( 'ywrvp-loop-filter.php', $args, '', YITH_WRVP_DIR . 'templates/' );
		}

		/**
		 * Update products list with purchased products and expired
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function remove_products() {

			if( ! $this->_user_id || empty( $this->_products_list ) ){
				return;
			}

			$duration = get_option( 'yith-wrvp-cookie-time' );
			$expiration = time() - (86400 * $duration);

			// purchased products
			$purchased = get_option( 'yith-wrvp-excluded-purchased' ) == 'yes' ? get_user_meta( $this->_user_id, $this->_meta_purchased_products, true ) : array();

			// remove
			foreach( $this->_products_list as $key => $product_id ) {

				if( $key < $expiration ) {
					unset( $this->_products_list[$key] );
					continue;
				}

				if( ! empty( $purchased ) ) {
					foreach( $purchased as $item ) {
						if( $product_id == $item ) {
							unset( $this->_products_list[ $key ]);
						}
					}
				}
			}

			// save new list
			$this->set_cookie_meta();
		}

		/**
		 * Get products list
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function get_the_products_list(){
			return $this->_products_list;
		}

		/**
		 * Get the id of the most viewed category
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $products_list
		 * @return string | boolean if not found
		 * @author Francesco Licandro
		 */
		public function most_viewed_cat( $products_list = array() ){

			if( empty( $products_list ) ) {
				$products_list = $this->_products_list;
			}

            return YITH_WRVP_Helper::most_viewed_cat( $products_list );
		}

		/**
		 * Recently viewed page shortcode
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param array $atts
		 * @return mixed
		 */
		public function recently_viewed_page( $atts ){

			global $wp_query;

			$atts = shortcode_atts(array(
				'columns' 		=> get_option( 'yith-wrvp-num-visible-products', '4' ),
			), $atts );

			// extract $atts
			extract( $atts );

			$products_list = $this->_products_list;

			if( empty( $products_list ) ){
				return $this->get_not_found_html();
			}

			$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

			$args = apply_filters('yith_recently_viewed_page_query_args', array(
				'post_type' => 'product',
				'posts_per_page' => apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
				'paged'	=> $paged,
				'post__in' => $products_list
			));

			if( isset( $_GET[ 'ywrvp_cat_id' ] ) && $_GET['ywrvp_cat_id'] ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'id',
						'terms' => intval( $_GET[ 'ywrvp_cat_id' ] )
					)
				);
			}

			$wp_query = new WP_Query( $args );

			ob_start();

			if ( $wp_query->have_posts() ) {
				// template args
				$templates_args = array(
					'products'	=> $wp_query,
					'title'		=> '',
					'slider'	=> 'no',
					'autoplay'	=> 'no',
					'autoplay_speed'    => '3000',
					'page_url'  => '',
					'view_all'  => '',
					'class'		=> 'in-page',
					'columns'	=> $columns
				);

				?>
				<div class="woocommerce">
					<?php

                    if( function_exists( 'wc_print_notices' ) ) {
	                    wc_print_notices();
                    }

					do_action( 'yith_wrvp_before_products_loop' );

					wc_get_template( 'ywrvp-loop-template.php', $templates_args, '', YITH_WRVP_DIR . 'templates/' );

					woocommerce_pagination();

					do_action( 'yith_wrvp_after_products_loop' );

					?>
				</div>
				<?php
			}

			$content = ob_get_clean();

			wp_reset_query();

			return $content;
		}

		/**
		 * Get not found product html message
		 *
		 * @author Francesco Licandro
		 * @since 1.1.0
		 * @return string
		 */
		public function get_not_found_html(){
		    $message = get_option( 'yith-wrvp-nofound-msg' ,'');
		    if( !empty( $message)) {
			    return '<p class="woocommerce-info">' . $message . '</p>';
		    }else{
		        return '';
            }
		}

		/**
         * Shortcode that show products based on views globally
         *
         * @since 1.5.0
         * @author Francesco Licandro
         * @param array $atts
         * @return mixed
         */
		public function most_viewed_products( $atts ) {
            $atts = shortcode_atts(array(
                'num_post' 			=> get_option( 'yith-wrvp-num-tot-products', '6' ),
                'title'				=> __( 'Most Viewed Products', 'yith-woocommerce-recently-viewed-products' ),
                'slider'			=> get_option( 'yith-wrvp-slider', 'yes' ),
                'autoplay'			=> get_option( 'yith-wrvp-slider-autoplay', 'yes' ),
                'autoplay_speed'    => '3000',
                'num_columns' 		=> get_option( 'yith-wrvp-num-visible-products', '4' ),
                'class'             => '',
                'cats_id'			=> '',
                'category'          => '' // deprecated, to remove
            ), $atts );

            // extract $atts
            extract( $atts );

            $args = array(
                'post_type'             => 'product',
                'post_status'           => 'publish',
                'ignore_sticky_posts'   => 1,
                'no_found_rows'         => 1,
                'posts_per_page'        => $num_post,
                'meta_key'              => '_ywrvp_views',
                'orderby'               => 'meta_value_num',
                'order'                 => 'DESC',
            );

            if( ! empty( $category ) ) {
                if( $category == 'current' ) {
                    global $product;
                    $terms = $product ? get_the_terms( $product->get_id(), 'product_cat' ) : '';
                    foreach( $terms as $term) {
                        $category_slug[] = $term->term_id;
                        break;
                    }
                } else {
                    $category_slug = explode( ',', $category );
                }

                $args['tax_query'] = array(
                    array (
                        'taxonomy'  => 'product_cat',
                        'field'     => 'slug',
                        'terms'     => $category_slug,
                        'operator'  => 'IN'
                    ),
                );
            }
            elseif( ! empty( $cats_id ) ) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => $cats_id,
                        'operator' => 'IN'
                    )
                );
            }

            // hide free
            if( get_option( 'yith-wrvp-hide-free' ) == 'yes' ) {
                $args['meta_query'][] = array(
                    'key'     => '_price',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'DECIMAL'
                );
            }

            // visibility meta query
            $args = yit_product_visibility_meta( $args );

            if( get_option( 'yith-wrvp-hide-out-of-stock' ) == 'yes' ) {
                $args['meta_query'][] = array(
                    'key' 		=> '_stock_status',
                    'value' 	=> 'instock',
                    'compare' 	=> '='
                );
            }

            $args = apply_filters( 'yith_wrvp_shortcode_most_viewed_query_args', $args );

            // set main query
            $products = new WP_Query( $args );

            ob_start();

            if ( ! empty( $products ) ) {

                // templare args
                $templates_args = apply_filters( 'yith_wrvp_templates_most_viewed_query_args', array(
                    'products'	=> $products,
                    'title'		=> $title,
                    'slider'	=> $slider,
                    'autoplay'	=> $autoplay,
                    'columns' 	=> $num_columns,
                    'class'		=> $class,
                    'autoplay_speed' => $autoplay_speed
                ));

                wc_get_template( 'ywrvp-most-viewed-template.php', $templates_args, '', YITH_WRVP_DIR . 'templates/' );
            }

            $content = ob_get_clean();

            wp_reset_postdata();

            return apply_filters( 'yith_wrvp_shortcode_most_viewed_return_html', $content, $this->_products_list, $this );
        }
	}
}
/**
 * Unique access to instance of YITH_WRVP_Frontend_Premium class
 *
 * @return \YITH_WRVP_Frontend_Premium
 * @since 1.0.0
 */
function YITH_WRVP_Frontend_Premium(){
	return YITH_WRVP_Frontend_Premium::get_instance();
}