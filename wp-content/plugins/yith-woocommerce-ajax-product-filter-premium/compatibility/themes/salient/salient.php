<?php
/* === YITH WCAN and Salient Theme Integration === */

add_action( 'wp_enqueue_scripts', 'yith_wcan_salient_style', 20 );
add_action( 'admin_init', 'yith_wcan_salient_support', 20 );

if( ! function_exists( 'yith_wcan_salient_style' ) ){
    function yith_wcan_salient_style(){
        // Style
        wp_enqueue_style( 'yith-wcan-salient', YITH_WCAN_URL . 'compatibility/themes/salient/salient.css', array( 'yith-wcan-frontend' ), YITH_WCAN()->version  );}
}

if( ! function_exists( 'yith_wcan_salient_support' ) ){
    function yith_wcan_salient_support(){
        $options = get_option( 'yit_wcan_options' );
        if( 'h4' != $options['yith_wcan_ajax_widget_title_class'] ){
            $options['yith_wcan_ajax_widget_title_class'] = 'h4';
            update_option( 'yit_wcan_options', $options );
        }
    }
}