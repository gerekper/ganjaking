<?php
if ( !function_exists( 'yith_wcpsc_wpml_get_current_language_id' ) ) {
    function yith_wcpsc_wpml_get_current_language_id( $id ) {
        global $sitepress;
        if ( $sitepress ) {
            $language = $sitepress->get_current_language();
            if ( function_exists( 'icl_object_id' ) ) {
                $id = icl_object_id( $id, get_post_type( $id ), true, $language );
            } else if ( function_exists( 'wpml_object_id_filter' ) ) {
                $id = wpml_object_id_filter( $id, get_post_type( $id ), true, $language );
            }
        }
        return $id;
    }
}