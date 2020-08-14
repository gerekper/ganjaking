<?php
!defined( 'YITH_WCBSL' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL_WPML_Integration' ) ) {
    /**
     * WPML Integrations class.
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.1.2
     */
    class YITH_WCBSL_WPML_Integration {

        /** @var  YITH_WCBSL_WPML_Integration */
        private static $_instance;

        /** @var string */
        public $current_language;

        /** @var string */
        public $default_language;

        /** @return YITH_WCBSL_WPML_Integration */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        private function __construct() {
            global $sitepress;
            if ( !empty( $sitepress ) ) {
                $this->_init_wpml_vars();
                //add_filter( 'woocommerce_reports_get_order_report_query', array( $this, 'filter_reports_query' ), 0 );

            }
        }

        private function _init_wpml_vars() {
            global $sitepress;
            $this->current_language = $sitepress->get_current_language();
            $this->default_language = $sitepress->get_default_language();
        }

        public function filter_reports_query( $query ) {
            return $query;
        }

        /**
         * get the id for the current language
         *
         * @param      $id
         * @param bool $return_original_if_missing
         *
         * @return int|null
         */
        public function get_current_language_id( $id, $return_original_if_missing = true ) {
            return $this->get_language_id( $id, $return_original_if_missing );
        }

        public function get_language_id( $id, $return_original_if_missing = true, $language = '' ) {
            $language = !!$language ? $language : $this->current_language;
            if ( function_exists( 'icl_object_id' ) ) {
                $id = icl_object_id( $id, get_post_type( $id ), $return_original_if_missing, $language );
            } else if ( function_exists( 'wpml_object_id_filter' ) ) {
                $id = wpml_object_id_filter( $id, get_post_type( $id ), $return_original_if_missing, $language );
            }

            return $id;
        }

        public function get_parent_id( $id ) {
            /** @var WPML_Post_Translation $wpml_post_translations */
            global $wpml_post_translations;
            if ( $wpml_post_translations && $parent_id = $wpml_post_translations->get_original_element( $id ) )
                $id = $parent_id;

            return $id;
        }
    }
}


/** @return YITH_WCBSL_WPML_Integration */
function YITH_WCBSL_WPML_Integration() {
    return YITH_WCBSL_WPML_Integration::get_instance();
}