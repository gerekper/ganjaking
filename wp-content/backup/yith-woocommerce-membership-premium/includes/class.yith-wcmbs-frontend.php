<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Frontend {

        /**
         * Single instance of the class
         *
         * @var YITH_WCMBS_Frontend
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCMBS_Frontend
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }


        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        protected function __construct() {
            // add frontend css
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );


            // Filter Post, Pages and product
            add_action( 'pre_get_posts', array( $this, 'hide_not_allowed_posts' ) );
            add_filter( 'the_posts', array( $this, 'filter_posts' ) );
            add_filter( 'get_pages', array( $this, 'filter_posts' ) );

            // Filter nav menu
            add_filter( 'wp_nav_menu_objects', array( $this, 'filter_nav_menu_pages' ), 10, 2 );
            // Filter next and previous post link
            add_filter( 'get_next_post_where', array( $this, 'filter_adiacent_post_where' ), 10, 3 );
            add_filter( 'get_previous_post_where', array( $this, 'filter_adiacent_post_where' ), 10, 3 );
        }

        /**
         * Filter Adiacent Posts (next and previous)
         *
         * @param string $where
         * @param bool   $in_same_term
         * @param array  $excluded_terms
         *
         * @access public
         * @since  1.0.0
         *
         * @return string
         */
        public function filter_adiacent_post_where( $where, $in_same_term, $excluded_terms ) {
            $current_user_id      = get_current_user_id();
            $non_allowed_post_ids = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( $current_user_id );

            if ( !empty( $non_allowed_post_ids ) )
                $where .= " AND p.ID NOT IN (" . implode( $non_allowed_post_ids, ',' ) . ')';

            return $where;
        }

        /**
         * Filter Nav Menu Pages
         *
         * @param $items array
         * @param $args  array
         *
         * @access public
         * @since  1.0.0
         *
         * @return array
         */
        public function filter_nav_menu_pages( $items, $args ) {
            $current_user_id      = get_current_user_id();
            $non_allowed_post_ids = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( $current_user_id );
            foreach ( $items as $key => $post ) {
                if ( in_array( absint( $post->object_id ), $non_allowed_post_ids ) ) {
                    unset( $items[ $key ] );
                }
            }

            return $items;
        }

        /**
         * Filter pre get posts Query
         *
         * @param $query WP_Query
         *
         * @access public
         * @since  1.0.0
         */
        public function hide_not_allowed_posts( $query ) {
            $suppress_filter = isset( $query->query[ 'yith_wcmbs_suppress_filter' ] ) ? $query->query[ 'yith_wcmbs_suppress_filter' ] : false;
            if ( !$suppress_filter ) {
                $current_user_id      = get_current_user_id();
                $non_allowed_post_ids = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( $current_user_id );
                if ( !empty( $non_allowed_post_ids ) )
                    $query->set( 'post__not_in', (array)$non_allowed_post_ids );
            }
        }

        /**
         * Filter posts
         *
         * @param array $posts
         *
         * @return array
         *
         * @access public
         * @since  1.0.0
         */
        public function filter_posts( $posts ) {
            $current_user_id = get_current_user_id();
            foreach ( $posts as $post_key => $post ) {
                if ( !YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID ) ) {
                    unset( $posts[ $post_key ] );
                }
            }

            return $posts;
        }


        public function enqueue_scripts() {
            $premium_suffix = defined( 'YITH_WCMBS_PREMIUM' ) ? '_premium' : '';
            wp_enqueue_style( 'yith-wcmbs-frontent-styles', YITH_WCMBS_ASSETS_URL . '/css/frontend' . $premium_suffix . '.css' );

            wp_enqueue_style( 'yith-wcmbs-membership-statuses', YITH_WCMBS_ASSETS_URL . '/css/membership-statuses.css' );

        }
    }
}
/**
 * Unique access to instance of YITH_WCMBS_Frontend class
 *
 * @return YITH_WCMBS_Frontend|YITH_WCMBS_Frontend_Premium
 * @since 1.0.0
 */
function YITH_WCMBS_Frontend() {
    return YITH_WCMBS_Frontend::get_instance();
}