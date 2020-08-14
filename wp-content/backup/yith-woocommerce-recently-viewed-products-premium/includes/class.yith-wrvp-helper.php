<?php
/**
 * Helper class
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WRVP_Helper' ) ) {
	/**
	 * YITH WooCommerce Recently Viewed Products Helper Class
	 *
	 * @since 1.4.2
	 */
	class YITH_WRVP_Helper {

        /**
         * Get the id of the most viewed category
         *
         * @access public
         * @since 1.4.2
         * @param array $products_list
         * @return string | boolean if not found
         * @author Francesco Licandro
         */
        static function most_viewed_cat( $products_list = array() ){
            $categories = array();

            foreach( $products_list as $product_id ) {
                $terms = wp_get_post_terms( $product_id, 'product_cat' );

                foreach( $terms as $term ) {
                    if( ! isset( $term->term_id ) )
                        continue;

                    if( ! array_key_exists( $term->term_id, $categories ) ) {
                        $categories[ $term->term_id ] = 1;
                    } else {
                        $categories[ $term->term_id ] = ++$categories[ $term->term_id ];
                    }
                }
            }

            if( empty( $categories ) ) {
                return false;
            }

            // order array, last is most viewed
            natsort($categories);
            // get keys
            $categories = array_keys( $categories );
            // then return last
            return array_pop( $categories );
        }

        /**
         * Get list of similar products based on user chronology
         *
         * @access public
         * @since 1.0.0
         * @param array $cats_array
         * @param string $similar_type
         * @param array $products_list
         * @return mixed
         * @author Francesco Licandro
         */
        static function get_similar_products( $cats_array = array(), $similar_type = '', $products_list = array() ) {

            global $wpdb;

            $excluded = array( 0 );
            if( is_product() ) {
                global $product;
                $excluded[] = $product->get_id();
            }

            if( ! $similar_type ) {
                $similar_type = get_option( 'yith-wrvp-type-similar-products', 'both' );
            }
            $tags_array = array();

            // set cat
            if( empty( $cats_array ) && ( $similar_type == 'both' ||  $similar_type == 'cats' ) ) {
                $cats_array = self::get_list_terms( 'product_cat', false, $products_list );
            }
            // set tag
            if( $similar_type == 'both' ||  $similar_type == 'tags' ) {
                $tags_array = self::get_list_terms( 'product_tag', false, $products_list );
            }

            // return array() if cats and tags are empty
            if( empty( $cats_array ) && empty( $tags_array ) ) {
                return array();
            }

            // let's plugin filter args
            $cats_array = apply_filters( 'yith_wrvp_products_cats_array', $cats_array );
            $tags_array = apply_filters( 'yith_wrvp_products_tags_array', $tags_array );
            $excluded 	= apply_filters( 'yith_wrvp_excluded_products', $excluded );

            $query = self::build_query( $cats_array, $tags_array, $excluded );
            $products = $wpdb->get_col( implode( ' ', $query ) );

            return $products;
        }

        /**
         * Get products terms
         *
         * @access public
         * @since 1.0.0
         * @param string $term_name
         * @param boolean $with_name
         * @param array $products_list
         * @return array
         * @author Francesco Licandro
         */
        static function get_list_terms( $term_name, $with_name = false, $products_list = array() ){

            if( empty( $products_list ) ){
                return array();
            }

            $terms_list = array();

            foreach( $products_list as $product_id ) {
                // get terms
                $terms = wp_get_post_terms( $product_id, $term_name );

                foreach( $terms as $term ) {
                    if( isset( $term->term_id ) && ! in_array( $term->term_id, $terms_list ) ) {

                        if( $with_name ) {
                            $terms_list[$term->term_id] = $term->name;
                        } else {
                            $terms_list[] = intval($term->term_id);
                        }
                    }
                }
            }

            return $terms_list;
        }

        /**
         * Query build for get similar products
         *
         * @access public
         * @since 1.0.0
         * @param $cats_array
         * @param $tags_array
         * @param $excluded
         * @return array
         * @author Francesco Licandro
         */
        static function build_query( $cats_array, $tags_array, $excluded ) {

            global $wpdb;

            $query           = array();
            $query['fields'] = "SELECT DISTINCT ID FROM {$wpdb->posts} p";
            $query['join']   = " INNER JOIN {$wpdb->postmeta} pm ON ( pm.post_id = p.ID )";
            $query['join']  .= " INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)";
            $query['join']  .= " INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";
            $query['join']  .= " INNER JOIN {$wpdb->terms} t ON (t.term_id = tt.term_id)";

            if ( get_option( 'yith-wrvp-hide-out-of-stock' ) === 'yes' ) {
                $query['join'] .= " INNER JOIN {$wpdb->postmeta} pm2 ON ( pm2.post_id = p.ID AND pm2.meta_key='_stock_status' )";
            }

            $query['where']  = " WHERE 1=1";
            $query['where'] .= " AND p.post_status = 'publish'";
            $query['where'] .= " AND p.post_type = 'product'";
            $query['where'] .= " AND p.ID NOT IN ( " . implode( ',', $excluded ) . " )";

            if ( get_option( 'yith-wrvp-hide-out-of-stock' ) === 'yes' ) {
                $query['where'] .= " AND pm2.meta_value = 'instock'";
            }

            $rel = 'AND';
            if( array_filter( $cats_array ) && ! empty( $cats_array ) ) {
                $query['where'] .= " AND ( tt.taxonomy = 'product_cat' AND t.term_id IN ( " . implode(',', $cats_array ) . " ) )";
                $rel = 'OR';
            }
            if( array_filter( $tags_array ) && ! empty( $tags_array ) ) {
                $query['where'] .= " {$rel} ( ( tt.taxonomy = 'product_tag' AND t.term_id IN ( " . implode(',', $tags_array) . " ) )";
                $query['where'] .= " AND p.ID NOT IN ( " . implode(',', $excluded) . " ) )";
            }

            $query['group'] = "";

            return apply_filters( 'yith_wrvp_main_query_array', $query, $cats_array, $tags_array, $excluded );
        }
	}
}