<?php
/**
 * Option class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

/*
 *  
 */

if ( ! class_exists( 'YITH_WAPO_Option' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WAPO_Option {


        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        public function __construct() {

        }

        /**
         * @param $single_type
         * @param $value
         * @param $field_name
         *
         * @author Andrea Frascaspata
         * @return int
         */
        public static function getOptionDataByValueKey( $single_type, $value , $field_name ) {

            $options = maybe_unserialize( $single_type->options );

            return stripslashes( $options[$field_name][$value] );

        }

        /**
         * @param $single_type
         * @param $value
         * @param $field_name
         *
         * @author Andrea Frascaspata
         * @return int
         */
        public static function getOptionDataByValueRadio( $single_type, $value , $field_name ) {

            $options = maybe_unserialize( $single_type->options );

            return stripslashes( $options[$field_name][$value] );

        }

        /**
         * @param $single_type
         * @param $value
         * @param $field_name
         *
         * @author Andrea Frascaspata
         * @return mixed
         */
        public static function getOptionDataByValueSelect( $single_type, $value , $field_name ) {

            $options = maybe_unserialize( $single_type->options );

            return stripslashes( $options[$field_name][$value] );

        }

        /**
         * @param $single_type
         * @param $values
         * @param $field_name
         *
         * @author Andrea Frascaspata
         * @return mixed
         */
        public static function getOptionDataByValueLabels( $single_type, $values , $field_name ) {

            $options = maybe_unserialize( $single_type->options );

            $index = -1;
            foreach( $values as $value  ){
                if( $value != '' ) {
                    $index = $value;
                    break;
                }
            }

            if ( $index >= 0 ) {
                if ( isset( $options[$field_name][$index] ) ) {
                    return stripslashes( $options[$field_name][$index] );
                } else {
                    return false;
                }
            } else {
                return false;
            }

        }

        public static function getOptionDataByValueMultipleLabels( $single_type, $selected_value , $field_name ) {

            $options = maybe_unserialize( $single_type->options );

            if( $selected_value >= 0 ) {
                return stripslashes( $options[$field_name][$selected_value] );
            } else {
                return false;
            }
        }

    }

}