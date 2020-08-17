<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the frontend behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Frontend {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /**
         * @var array deprecated array from WC_QUERY
         * @since version 3.0
         */
        public $filtered_product_ids_for_taxonomy   = array();
        public $layered_nav_product_ids             = array();
        public $unfiltered_product_ids              = array();
        public $filtered_product_ids                = array();
        public $layered_nav_post__in                = array();

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct( $version ) {

            $theme_support = apply_filters( 'yith_wcan_theme_use_wp_the_query_object', array(
                    'porto'
                )
            );

            $current_theme = strtolower( wp_get_theme()->Template );
            $is_qTranlateX_enabled = class_exists( 'QTX_Translator' );

            if( in_array( $current_theme, $theme_support ) || $is_qTranlateX_enabled ){
                add_filter( 'yith_wcan_use_wp_the_query_object', '__return_true' );
            }

            $this->version = $version;
            $is_ajax_navigation_active = is_active_widget( false, false, 'yith-woo-ajax-navigation', true );

            //Actions
            if( $is_ajax_navigation_active ) {
                add_filter( 'woocommerce_is_layered_nav_active', '__return_true' );
            }

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

            add_action( 'body_class', array( $this, 'body_class' ) );

            add_filter( 'the_posts', array( $this, 'the_posts' ), 15, 2 );

            add_filter( 'woocommerce_layered_nav_link', 'yit_plus_character_hack', 99 );

            add_filter( 'woocommerce_is_filtered', 'yit_is_filtered_uri', 20 );

            // YITH WCAN Loaded
            do_action( 'yith_wcan_loaded' );
        }

        public function layered_navigation_array_for_wc_older_26(){
            if( YITH_WCAN()->is_wc_older_2_6 ){
                $this->filtered_product_ids_for_taxonomy   = WC()->query->filtered_product_ids_for_taxonomy;
                $this->layered_nav_product_ids             = WC()->query->layered_nav_product_ids;
                $this->unfiltered_product_ids              = WC()->query->unfiltered_product_ids;
                $this->filtered_product_ids                = WC()->query->filtered_product_ids;
                $this->layered_nav_post__in                = WC()->query->layered_nav_post__in;
            }
        }

        /**
         * Select the correct query object
         *
         * @access public
         * @param WP_Query|bool $query (default: false)
         * @return the query object
         */
        public function select_query_object( $current_wp_query ){
            /**
             * For WordPress 4.7 Must use WP_Query object
             */
            global $wp_the_query;
            $use_wp_query = YITH_WCAN()->is_wp_older_4_7 ? false : true;
            return apply_filters( 'yith_wcan_use_wp_the_query_object', $use_wp_query ) ? $wp_the_query->query : $current_wp_query->query;
        }

        /**
         * Hook into the_posts to do the main product query if needed - relevanssi compatibility.
         *
         * @access public
         * @param array $posts
         * @param WP_Query|bool $query (default: false)
         * @return array
         */
        public function the_posts( $posts, $query = false ) {
            if( YITH_WCAN()->is_wc_older_2_6 ){
                add_action( 'wp', array( $this, 'layered_navigation_array_for_wc_older_26' ) );
            }

            else{

            	$queried_object = get_queried_object();

            	if( ! empty( $queried_object ) && ( is_shop() || is_product_taxonomy() || ! apply_filters( 'yith_wcan_is_search', is_search() ) ) ){
		            $filtered_posts   = array();
		            $queried_post_ids = array();

		            $problematic_theme = array(
			            'basel',
			            'ux-shop',
			            'aardvark'
		            );

		            $wp_theme       = wp_get_theme();
		            $template_name  = $wp_theme->get_template();

		            /**
		             * Support for Flatsome Theme lower then 3.6.0
		             */
		            if( 'flatsome' == $template_name && version_compare( '3.6.0', $wp_theme->Version, '<' ) ){
			            $problematic_theme[] = 'flatsome';
		            }

		            $is_qTranslateX_and_yit_core_1_0_0 = class_exists( 'QTX_Translator' ) && defined('YIT_CORE_VERSION') && '1.0.0' == YIT_CORE_VERSION;
		            $is_problematic_theme = in_array( $template_name, $problematic_theme );

		            if( $is_qTranslateX_and_yit_core_1_0_0 || $is_problematic_theme || class_exists( 'SiteOrigin_Panels' ) ){
			            add_filter( 'yith_wcan_skip_layered_nav_query', '__return_true' );
		            }

		            $query_filtered_posts = $this->layered_nav_query();

		            foreach ( $posts as $post ) {

			            if ( in_array( $post->ID, $query_filtered_posts ) ) {
				            $filtered_posts[]   = $post;
				            $queried_post_ids[] = $post->ID;
			            }
		            }

		            $query->posts       = $filtered_posts;
		            $query->post_count  = count( $filtered_posts );

		            // Get main query
		            $current_wp_query = $this->select_query_object( $query );

		            if( is_array( $current_wp_query ) ){
			            // Get WP Query for current page (without 'paged')
			            unset( $current_wp_query['paged'] );
		            }

		            else {
			            $current_wp_query = array();
		            }

		            // Ensure filters are set
		            $unfiltered_args = array_merge(
			            $current_wp_query,
			            array(
				            'post_type'              => 'product',
				            'numberposts'            => - 1,
				            'post_status'            => 'publish',
				            'meta_query'             => is_object( $current_wp_query ) ? $current_wp_query->meta_query : array(),
				            'fields'                 => 'ids',
				            'no_found_rows'          => true,
				            'update_post_meta_cache' => false,
				            'update_post_term_cache' => false,
				            'pagename'               => '',
				            'wc_query'               => 'get_products_in_view', //Only for WC <= 2.6.x
				            'suppress_filters'       => true,
			            )
		            );

		            $hide_out_of_stock_items = apply_filters( 'yith_wcan_hide_out_of_stock_items', 'yes' == get_option( 'woocommerce_hide_out_of_stock_items' ) ? true : false );

		            if( $hide_out_of_stock_items ){
			            $unfiltered_args['meta_query'][] = array(
				            'key' => '_stock_status',
				            'value' => 'instock',
				            'compare' => 'AND'
			            );
		            }

		            $unfiltered_args = apply_filters( 'yith_wcan_unfiltered_args', $unfiltered_args );
		            $this->unfiltered_product_ids = apply_filters( 'yith_wcan_unfiltered_product_ids', get_posts( $unfiltered_args ), $query, $current_wp_query );
		            $this->filtered_product_ids   = $queried_post_ids;

		            // Also store filtered posts ids...
		            if ( sizeof( $queried_post_ids ) > 0 ) {
			            $this->filtered_product_ids = array_intersect( $this->unfiltered_product_ids, $queried_post_ids );
		            } else {
			            $this->filtered_product_ids = $this->unfiltered_product_ids;
		            }

		            if ( sizeof( $this->layered_nav_post__in ) > 0 ) {
			            $this->layered_nav_product_ids = array_intersect( $this->unfiltered_product_ids, $this->layered_nav_post__in );
		            } else {
			            $this->layered_nav_product_ids = $this->unfiltered_product_ids;
		            }
            	}
            }
            return $posts;
        }

        /**
         * Enqueue frontend styles and scripts
         *
         * @access public
         * @return void
         * @since  1.0.0
         */
        public function enqueue_styles_scripts() {
            if ( yith_wcan_can_be_displayed() ) {
                $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                wp_enqueue_style( 'yith-wcan-frontend', YITH_WCAN_URL . 'assets/css/frontend.css', false, $this->version );
                wp_enqueue_script( 'yith-wcan-script', YITH_WCAN_URL . 'assets/js/yith-wcan-frontend' . $suffix . '.js', array( 'jquery' ), $this->version, true );

	            $custom_style     = yith_wcan_get_option( 'yith_wcan_custom_style', '' );
	            $current_theme    = function_exists( 'wp_get_theme' ) ? wp_get_theme() : null;
	            $current_template = $current_theme instanceof WP_Theme ? $current_theme->get_template() : '';

                ! empty( $custom_style ) && wp_add_inline_style( 'yith-wcan-frontend', sanitize_text_field( $custom_style ) );

                $args = apply_filters( 'yith_wcan_ajax_frontend_classes', array(
                        'container'             => yith_wcan_get_option('yith_wcan_ajax_shop_container', '.products'),
                        'pagination'            => yith_wcan_get_option('yith_wcan_ajax_shop_pagination', 'nav.woocommerce-pagination'),
                        'result_count'          => yith_wcan_get_option('yith_wcan_ajax_shop_result_container', '.woocommerce-result-count'),
                        'wc_price_slider'       => array(
                            'wrapper'   => '.price_slider',
                            'min_price' => '.price_slider_amount #min_price',
                            'max_price' => '.price_slider_amount #max_price',
                        ),
                        'is_mobile'             => wp_is_mobile(),
                        'scroll_top'            => yith_wcan_get_option('yith_wcan_ajax_scroll_top_class', '.yit-wcan-container'),
                        'scroll_top_mode'       => yith_wcan_get_option( 'yith_wcan_scroll_top_mode', 'mobile' ),
                        'change_browser_url'    => 'yes' == yith_wcan_get_option( 'yith_wcan_change_browser_url', 'yes' ) ? true : false,
                        /* === Avada Theme Support === */
                        'avada'                 => array(
                            'is_enabled' => class_exists( 'Avada' ),
                            'sort_count' => 'ul.sort-count.order-dropdown'
                        ),
                        /* Flatsome Theme Support */
                        'flatsome'              => array(
                            'is_enabled'         => function_exists( 'flatsome_option' ),
                            'lazy_load_enabled'  => get_theme_mod( 'lazy_load_images' )
                        ),
		                /* === YooThemes Theme Support === */
                        'yootheme' => array(
	                        'is_enabled' => 'yootheme' === $current_template
                        ),
                    )
                );

                wp_localize_script( 'yith-wcan-script', 'yith_wcan', apply_filters( 'yith-wcan-frontend-args', $args ) );
            }
        }
        
        /**
         * Layered Nav post filter.
         *
         * @param array $filtered_posts
         * @return array
         */
        public function layered_nav_query( $filtered_posts  = array() ) {
            if( apply_filters( 'yith_wcan_skip_layered_nav_query', false ) ){
                return $filtered_posts;
            }

            $_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
            $is_product_taxonomy = false;
            if( is_product_taxonomy() ){
                $is_product_taxonomy = array(
                    'taxonomy'  => get_queried_object()->taxonomy,
                    'terms'     => get_queried_object()->slug,
                    'field'     => YITH_WCAN()->filter_term_field
                );
            }

            if ( sizeof( $_chosen_attributes ) > 0 ) {

                $matched_products   = array(
                    'and' => array(),
                    'or'  => array()
                );
                $filtered_attribute = array(
                    'and' => false,
                    'or'  => false
                );

                foreach ( $_chosen_attributes as $attribute => $data ) {
                    $matched_products_from_attribute = array();
                    $filtered = false;

                    if ( sizeof( $data['terms'] ) > 0 ) {
                        foreach ( $data['terms'] as $value ) {

                            $args = array(
                                'post_type' 	=> 'product',
                                'numberposts' 	=> -1,
                                'post_status' 	=> 'publish',
                                'fields' 		=> 'ids',
                                'no_found_rows' => true,
                                'suppress_filters'       => true,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' 	=> $attribute,
                                        'terms' 	=> $value,
                                        'field' 	=> YITH_WCAN()->filter_term_field
                                    )
                                ),
                            );

                            $args = yit_product_visibility_meta( $args );

                            if( $is_product_taxonomy ){
                                $args['tax_query'][] = $is_product_taxonomy;
                            }

                            //TODO: Increase performance for get_posts()
                            $post_ids = apply_filters( 'woocommerce_layered_nav_query_post_ids', get_posts( $args ), $args, $attribute, $value );

                            if ( ! is_wp_error( $post_ids ) ) {

                                if ( sizeof( $matched_products_from_attribute ) > 0 || $filtered ) {
                                    $matched_products_from_attribute = $data['query_type'] == 'or' ? array_merge( $post_ids, $matched_products_from_attribute ) : array_intersect( $post_ids, $matched_products_from_attribute );
                                } else {
                                    $matched_products_from_attribute = $post_ids;
                                }

                                $filtered = true;
                            }
                        }
                    }

                    if ( sizeof( $matched_products[ $data['query_type'] ] ) > 0 || $filtered_attribute[ $data['query_type'] ] === true ) {
                        $matched_products[ $data['query_type'] ] = ( $data['query_type'] == 'or' ) ? array_merge( $matched_products_from_attribute, $matched_products[ $data['query_type'] ] ) : array_intersect( $matched_products_from_attribute, $matched_products[ $data['query_type'] ] );
                    } else {
                        $matched_products[ $data['query_type'] ] = $matched_products_from_attribute;
                    }

                    $filtered_attribute[ $data['query_type'] ] = true;

                    $this->filtered_product_ids_for_taxonomy[ $attribute ] = $matched_products_from_attribute;
                }

                // Combine our AND and OR result sets
                if ( $filtered_attribute['and'] && $filtered_attribute['or'] )
                    $results = array_intersect( $matched_products[ 'and' ], $matched_products[ 'or' ] );
                else
                    $results = array_merge( $matched_products[ 'and' ], $matched_products[ 'or' ] );

                if ( $filtered ) {

                    $this->layered_nav_post__in   = $results;
                    $this->layered_nav_post__in[] = 0;

                    if ( sizeof( $filtered_posts ) == 0 ) {
                        $filtered_posts   = $results;
                        $filtered_posts[] = 0;
                    } else {
                        $filtered_posts   = array_intersect( $filtered_posts, $results );
                        $filtered_posts[] = 0;
                    }

                }
            }

            else {

                $args = array(
                    'post_type'        => 'product',
                    'numberposts'      => - 1,
                    'post_status'      => 'publish',
                    'fields'           => 'ids',
                    'no_found_rows'    => true,
                    'suppress_filters' => true,
                    'tax_query'        => array(),
                    'meta_query' => array()
                );

                if( $is_product_taxonomy ){
                    $args['tax_query'][] = $is_product_taxonomy;
                }

                if( isset( $_GET['min_price'] ) && isset( $_GET['max_price'] ) ){
                	$min_price = sanitize_text_field( $_GET['min_price'] );
                	$max_price = sanitize_text_field( $_GET['max_price'] );
                	$args['meta_query'][] =  array(
		                'key' => '_price',
		                'value' => array($min_price, $max_price),
		                'compare' => 'BETWEEN',
		                'type' => 'NUMERIC'
	                );
                }

                $args = yit_product_visibility_meta( $args );

                global $wp_query;
                $queried_object = function_exists( 'get_queried_object' ) && is_callable( array( $wp_query, 'get_queried_object' ) ) ? get_queried_object() : false;

                $taxonomy   = $queried_object && property_exists( $queried_object, 'taxonomy' ) ? $queried_object->taxonomy : false;
                $slug       = $queried_object && property_exists( $queried_object, 'slug' ) ? $queried_object->slug : false;
                
                //TODO: Increase performance for get_posts()
                $post_ids = apply_filters( 'woocommerce_layered_nav_query_post_ids', get_posts( $args ), $args, $taxonomy, $slug );

                if( ! is_wp_error( $post_ids ) ){
                    $this->layered_nav_post__in   = $post_ids;
                    $this->layered_nav_post__in[] = 0;    

                    if ( sizeof( $filtered_posts ) == 0 ) {
                        $filtered_posts   = $post_ids;
                        $filtered_posts[] = 0;
                    } 

                    else {
                        $filtered_posts   = array_intersect( $filtered_posts, $post_ids );
                        $filtered_posts[] = 0;
                    }
                }
            }

            return (array) $filtered_posts;
        }


        /**
         * Add a body class(es)
         *
         * @param $classes The classes array
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         * @return array
         */
        public function body_class( $classes ) {
            $classes[] = apply_filters( 'yith_wcan_body_class',  'yith-wcan-free' );
            return $classes;
        }
    }
}
