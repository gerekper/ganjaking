<?php
/**
 * AJAX class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership Premium
 * @version 1.0.0
 * @since 1.2.9
 */


if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_AJAX' ) ) {
    /**
     * YITH WooCommerce Membership Premium AJAX
     */
    class YITH_WCMBS_AJAX {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCMBS_AJAX
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCMBS_AJAX
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * Constructor
         *
         * @return YITH_WCMBS_AJAX
         */
        public function __construct() {
            $ajax_actions = array(
                'json_search_posts',
            );

            foreach ( $ajax_actions as $ajax_action ) {
                add_action( 'wp_ajax_yith_wcmbs_' . $ajax_action, array( $this, $ajax_action ) );
                add_action( 'wp_ajax_nopriv_yith_wcmbs_' . $ajax_action, array( $this, $ajax_action ) );
            }
        }


        /**
         * Post Search
         */
        public function json_search_posts() {
            check_ajax_referer( 'search-posts', 'security' );

            $search_term = isset( $_REQUEST[ 'term' ][ 'term' ] ) ? $_REQUEST[ 'term' ][ 'term' ] : $_REQUEST[ 'term' ];

            $term = (string) wc_clean( stripslashes( $search_term ) );

            $exclude = array();
            $include = array();

            if ( empty( $term ) ) {
                die();
            }

            if ( !empty( $_GET[ 'exclude' ] ) ) {
                $exclude = array_map( 'intval', explode( ',', $_GET[ 'exclude' ] ) );
            }
            if ( !empty( $_GET[ 'include' ] ) ) {
                $include = array_map( 'intval', explode( ',', $_GET[ 'include' ] ) );
            }

            $post_type = !empty( $_REQUEST[ 'post_type' ] ) ? $_REQUEST[ 'post_type' ] : 'post';

            $found_posts = array();

            $args = array(
                'post_type'        => $post_type,
                'post_status'      => 'publish',
                'numberposts'      => -1,
                'orderby'          => 'title',
                'order'            => 'asc',
                'post_parent'      => 0,
                'suppress_filters' => 0,
                'include'          => $include,
                's'                => $term,
                'fields'           => 'ids',
                'exclude'          => $exclude,
            );

            $posts = get_posts( $args );

            if ( !empty( $posts ) ) {
                foreach ( $posts as $post_id ) {
                    if ( !current_user_can( 'read_product', $post_id ) ) {
                        continue;
                    }

                    $found_posts[ $post_id ] = rawurldecode( get_the_title( $post_id ) );
                }
            }
            $found_posts = apply_filters( 'yith_wcmbs_json_search_found_posts', $found_posts );

            wp_send_json( $found_posts );
        }
    }
}
