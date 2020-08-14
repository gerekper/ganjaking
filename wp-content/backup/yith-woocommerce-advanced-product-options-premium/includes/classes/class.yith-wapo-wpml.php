<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WAPO' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WAPO_WPML' ) ) {
    /**
     * Frontend class.
     * The class manage all the frontend behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WAPO_WPML {

        /**
         * @author Andrea Frascaspata
         * @param        $string
         * @param string $name
         */
        public static function register_string( $string, $name = '' ) {
            if ( ! $name ) { $name = sanitize_title( $string ); }
            $name_slug = substr( '['. YITH_WAPO_LOCALIZE_SLUG .']' . $name, 0, 150 );
            yit_wpml_register_string( YITH_WAPO_WPML_CONTEXT, $name_slug, $string );
        }

        public static function string_translate( $label, $name ='' ) {
            if ( ! $name ) { $name = sanitize_title( $label ); }
            $name_slug = substr( '['. YITH_WAPO_LOCALIZE_SLUG .']' . $name, 0, 150 );
            return yit_wpml_string_translate( YITH_WAPO_WPML_CONTEXT, $name_slug, $label );
        }

        /**
         *
         * @author Andrea Frascaspata
         * @param $title
         * @param $description
         * @param $options
         *
         */
        public static function register_option_type( $title, $description, $options ) {

            YITH_WAPO_WPML::register_string( $title );
            YITH_WAPO_WPML::register_string( $description );

            // options

            if ( isset( $options ) ) {

                $options = maybe_unserialize( $options );

                if ( ! is_array( $options ) || ! ( isset( $options['label'] ) ) || count( $options['label'] ) <= 0 ) {
                    return;
                }

                $options['label']       = array_map( 'stripslashes', $options['label'] );
                $options['description'] = array_map( 'stripslashes', $options['description'] );
                $options['placeholder'] = array_map( 'stripslashes', $options['placeholder'] );
                $options['tooltip']     = array_map( 'stripslashes', $options['tooltip'] );

                for ( $i = 0; $i < count( $options['label'] ); $i ++ ) {

                    YITH_WAPO_WPML::register_string( $options['label'][$i] );
                    YITH_WAPO_WPML::register_string( $options['description'][$i] );
                    YITH_WAPO_WPML::register_string( $options['placeholder'][$i] );
                    YITH_WAPO_WPML::register_string( $options['tooltip'][$i] );

                }

            }

        }

    }
}
