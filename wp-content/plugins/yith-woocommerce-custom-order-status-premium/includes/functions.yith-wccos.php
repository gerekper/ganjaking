<?php
if ( !function_exists( 'yith_wccos_get_statuses' ) ) {
    function yith_wccos_get_statuses( $args = array() ) {
        $default_args = array(
            'posts_per_page' => -1,
            'post_type'      => 'yith-wccos-ostatus',
            'post_status'    => 'publish',
            'fields'         => 'ids',
        );

        $args = wp_parse_args( $args, $default_args );

        $statuses = get_posts( $args );

        return !!$statuses ? $statuses : array();
    }
}
if ( !function_exists( 'yith_wccos_get_recipients' ) ) {
    function yith_wccos_get_recipients( $id ) {
        $status_type = get_post_meta( $id, 'status_type', true );
        if ( 'custom' === $status_type || !$status_type ) {
            $recipients = get_post_meta( $id, 'recipients', true );
        } else {
            $recipients = array();
        }

        return !!$recipients && is_array( $recipients ) ? $recipients : array();
    }
}

if ( !function_exists( 'yith_wccos_get_allowed_recipients' ) ) {
    function yith_wccos_get_allowed_recipients() {
        $recipients = array(
            'admin'        => __( 'Administrator', 'yith-woocommerce-custom-order-status' ),
            'customer'     => __( 'Customer', 'yith-woocommerce-custom-order-status' ),
            'custom-email' => __( 'Custom Email Address', 'yith-woocommerce-custom-order-status' )
        );

        return apply_filters( 'yith_wccos_get_allowed_recipients', $recipients );
    }
}

if ( !function_exists( 'yith_wccos_is_true' ) ) {
    function yith_wccos_is_true( $value ) {
        return true === $value || 1 === $value || '1' === $value || 'yes' === $value || 'true' === $value;
    }
}