<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSWPMLSupport' ) ) {

    class RSWPMLSupport {

        public static function init() {
            add_action( 'wp_head' , array( __CLASS__ , 'register_user_lang' ) ) ;
            add_action( 'admin_init' , array( __CLASS__ , 'register_template_for_wpml' ) ) ;
        }

        public static function register_user_lang() {
            $Language = function_exists( 'icl_register_string' ) ? (isset( $_SESSION[ 'wpml_globalcart_language' ] ) ? $_SESSION[ 'wpml_globalcart_language' ] : ICL_LANGUAGE_CODE) : 'en' ;
            update_user_meta( get_current_user_id() , 'rs_wpml_lang' , $Language ) ;
        }

        // registering mail templates strings
        public static function register_template_for_wpml() {
            if ( ! function_exists( 'icl_register_string' ) )
                return ;

            global $wpdb ;
            $tablename = $wpdb->prefix . 'rs_templates_email' ;
            $re        = $wpdb->get_results( "SELECT * FROM $tablename" ) ;
            foreach ( $re as $each_template ) {
                $name_msg = 'rs_template_' . $each_template->id . '_message' ;
                icl_register_string( 'SUMO' , $name_msg , $each_template->message ) ; //for registering message
                $name_sub = 'rs_template_' . $each_template->id . '_subject' ;
                icl_register_string( 'SUMO' , $name_sub , $each_template->subject ) ; //for registering subject
            }
        }

        // getting the registered strings from wpml table
        public static function fp_wpml_text( $option_name , $language , $message ) {
            if ( ! function_exists( 'icl_register_string' ) )
                return $message ;

            if ( $language == 'en' )
                return $message ;

            global $wpdb ;
            $Data = $wpdb->get_results( $wpdb->prepare( " SELECT s.name, s.value, t.value AS translation_value, t.status
            FROM  {$wpdb->prefix}icl_strings s LEFT JOIN {$wpdb->prefix}icl_string_translations t ON s.id = t.string_id WHERE s.context = %s
            AND (t.language = %s OR t.language IS NULL)" , 'SUMO' , $language ) , ARRAY_A ) ;
            if ( ! srp_check_is_array( $Data ) )
                return $message ;

            $translated = $message ;
            foreach ( $Data as $each_entry ) {
                if ( $each_entry[ 'name' ] == $option_name )
                    $translated = $each_entry[ 'status' ] == '1' ? $each_entry[ 'translation_value' ] : $each_entry[ 'value' ] ;
            }
            return $translated ;
        }

    }

    RSWPMLSupport::init() ;
}