<?php
/**
 * WooCommerce List and Grid View Support
 */

add_action( 'wp_enqueue_scripts', 'yith_wcan_wc_list_grid_support', 20 );

if( ! function_exists( 'yith_wcan_wc_list_grid_support' ) ){
    function yith_wcan_wc_list_grid_support(){
        $is_cookie_set_to_list = ! empty( $_COOKIE['gridcookie'] ) && 'list' == $_COOKIE['gridcookie'];
        $is_list_default_view  = empty( $_COOKIE['gridcookie'] ) && 'list' == get_option( 'wc_glt_default', 'grid' );

        if( $is_cookie_set_to_list || $is_list_default_view ) {
            $handle = 'yith-wcan-script';
            $js = 'jQuery(document).on( "yith-wcan-ajax-filtered", function(){ jQuery("ul.products").addClass("list"); } );';
            wp_add_inline_script( $handle, $js );
        }
    }
}

