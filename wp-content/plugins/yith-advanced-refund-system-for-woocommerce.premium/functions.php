<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'ywcars_get_request_statuses' ) ) {
    function ywcars_get_request_statuses() {
        $request_statuses = array(
            'ywcars-new'        => esc_html_x( 'New refund request', 'Request status', 'yith-advanced-refund-system-for-woocommerce' ),
            'ywcars-processing' => esc_html_x( 'Processing', 'Request status', 'yith-advanced-refund-system-for-woocommerce' ),
            'ywcars-on-hold'    => esc_html_x( 'On hold', 'Request status', 'yith-advanced-refund-system-for-woocommerce' ),
            'ywcars-approved'   => esc_html_x( 'Approved', 'Request status', 'yith-advanced-refund-system-for-woocommerce' ),
            'ywcars-rejected'   => esc_html_x( 'Rejected', 'Request status', 'yith-advanced-refund-system-for-woocommerce' ),
            'trash'             => esc_html_x( 'Refund request in Trash', 'Request status', 'yith-advanced-refund-system-for-woocommerce' ),
        );
        return apply_filters( 'ywcars_request_statuses', $request_statuses );
    }
}

if ( ! function_exists( 'ywcars_get_request_status_by_key' ) ) {
    function ywcars_get_request_status_by_key( $status_key ) {
        $request_statuses = ywcars_get_request_statuses();
        return ! empty( $request_statuses[$status_key] ) ? $request_statuses[$status_key] : esc_html__( 'No status', 'yith-advanced-refund-system-for-woocommerce' );
    }
}

if ( ! function_exists( 'ywcars_get_requests_by_customer_id' ) ) {
    function ywcars_get_requests_by_customer_id( $customer_id ) {
        if ( empty( $customer_id ) ) {
            return false;
        }
        $request_statuses = ywcars_get_request_statuses();
        $args = array(
            'post_type'   => YITH_WCARS_CUSTOM_POST_TYPE,
            'post_status' => array_keys( $request_statuses ),
            'numberposts' => - 1,
            'fields'      => 'ids',
            'meta_query'  => array(
                array(
                    'key' => '_ywcars_customer_id',
                    'value' => $customer_id,
                    'compare' => '='
                )
            )
        );

        $request_ids = get_posts( $args );
        if ( empty( $request_ids ) ) {
            return false;
        } else {
            return $request_ids;
        }
    }
}