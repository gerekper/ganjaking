<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCP' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCP_WPML' ) ) {
    /**
     * Frontend class.
     * The class manage all the frontend behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCP_WPML {

        /**
         * @author Andrea Frascaspata
         *
         * @param        $string
         * @param string $name
         */
        public static function register_string( $string, $name = '' ) {

            if ( ! $name ) {
                $name = sanitize_title( $string );
            }

            yit_wpml_register_string( YITH_WCP_WPML_CONTEXT, '[' . YITH_WCP_SLUG . ']' . $name, $string );

        }

        public static function string_translate( $label ) {

            $name = sanitize_title( $label );

            return yit_wpml_string_translate( YITH_WCP_WPML_CONTEXT, '[' . YITH_WCP_SLUG . ']' . $name, $label );

        }

    }
}
