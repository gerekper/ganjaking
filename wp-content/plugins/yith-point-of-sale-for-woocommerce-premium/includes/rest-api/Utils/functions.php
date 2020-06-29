<?php

namespace YITH\POS\RestApi;

defined( 'ABSPATH' ) || exit;


function get_sql_clauses_for_filters( $query_args, $table_name, $maybe_include_order_check = false ) {

    $clauses = (object) array(
        'from'  => '',
        'where' => '',
    );

    $meta_value = 0;
    $meta_key   = '';

    $register = isset( $query_args[ 'register' ] ) ? absint( $query_args[ 'register' ] ) : 0;
    $store    = $query_args[ 'store' ] ? absint( $query_args[ 'store' ] ) : 0;

    if ( $register ) {
        $meta_value = $register;
        $meta_key   = '_yith_pos_register';
    } elseif ( $store ) {
        $meta_value = $store;
        $meta_key   = '_yith_pos_store';
    } elseif ( $maybe_include_order_check ) {
        $meta_value = 1;
        $meta_key   = '_yith_pos_order';
    }

    if ( $meta_value && $meta_key ) {
        global $wpdb;
        $post_meta      = $wpdb->postmeta;
        $post_meta_name = 'pm_filters_clause';

        $clauses->from  = " JOIN {$post_meta} as {$post_meta_name} ON {$post_meta_name}.post_id = {$table_name}.order_id";
        $clauses->where = " {$post_meta_name}.meta_key = '{$meta_key}' AND {$post_meta_name}.meta_value = '{$meta_value}'";
    }
    return $clauses;
}