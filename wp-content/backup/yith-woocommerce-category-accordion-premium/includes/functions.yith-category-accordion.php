<?php

if( !defined( 'ABSPATH' ) )
    exit;

if( ! function_exists( 'ywcca_json_search_wc_categories') ) {

    function ywcca_json_search_wc_categories( $x = '', $taxonomy_types = array('product_cat') ) {



            global $wpdb;
            $term = (string)urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
            $term = "%" . $term . "%";
            $query_cat = $wpdb->prepare("SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                          FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                          WHERE {$wpdb->term_taxonomy}.taxonomy IN ( %s ) AND {$wpdb->terms}.name LIKE %s",implode(',', $taxonomy_types), $term );

            $to_json = array();
            $product_categories = $wpdb->get_results(  $query_cat  );

            foreach ( $product_categories as $product_category ) {

                $to_json[$product_category->term_id] = "#" . $product_category->term_id . "-" . $product_category->name;
            }

            wp_send_json( $to_json );



    }
}

if( !function_exists( 'ywcca_json_search_wp_posts' ) ){

    function ywcca_json_search_wp_posts( $x='', $post_type=array('post') ){

            global $wpdb;

            $term = (string)urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
            $term = "%" . $term . "%";

            $query =    $wpdb->prepare("SELECT {$wpdb->posts}.ID, {$wpdb->posts}.post_title
                                        FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type IN (%s) AND {$wpdb->posts}.post_title LIKE %s", implode(",", $post_type),$term );

            $posts  =    $wpdb->get_results( $query );

            $to_json = array();

            foreach ( $posts as $post ) {

                $to_json[$post->ID] = $post->post_title;
            }

           wp_send_json( $to_json );

    }
}


add_action( 'wp_ajax_yith_category_accordion_json_search_wc_categories',  'ywcca_json_search_wc_categories', 10 );

if( !function_exists( 'ywcca_json_search_wp_categories' ) ){

    function ywcca_json_search_wp_categories(){
        ywcca_json_search_wc_categories('', array('category') );

    }
}
add_action( 'wp_ajax_yith_json_search_wp_categories',  'ywcca_json_search_wp_categories', 10 );



add_action( 'wp_ajax_yith_json_search_wp_posts', 'ywcca_json_search_wp_posts', 10 );


if( !function_exists( 'ywcca_json_search_wp_pages' ) ){
    function ywcca_json_search_wp_pages(){
        ywcca_json_search_wp_posts('', array('page') );

    }
}

add_action( 'wp_ajax_yith_json_search_wp_pages', 'ywcca_json_search_wp_pages', 10 );


if( !function_exists( 'yith_get_navmenu' ) ){

    function yith_get_navmenu(){

        $nav_menus  =   wp_get_nav_menus();
        $options    =   array();

        foreach( $nav_menus as $menu )
            $options[$menu->term_id]    =   $menu->name;

        return $options;
    }
}

