<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Surveys_Compatibility' ) ){

    class YITH_WC_Surveys_Compatibility{

        /**
         * @var YITH_WC_Surveys_Compatibility static instance
         */
        protected static $instance;

        public function __construct(){
            $this->include_compatibility_files();
        }

        /**
         * return single instance
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WC_Surveys_Compatibility
         */
        public static function get_instance()
        {
            if(is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Include compatibility files
         *
         * @access public
         * @since  1.0.0
         */
        private function include_compatibility_files() {
            $compatibility_dir = YITH_WC_SURVEYS_INC . 'classes/compatibility/';

            $files = array(
                $compatibility_dir . 'class.yith-wc-surveys-multivendor-compatibility.php'
            );

            foreach ( $files as $file ) {
                file_exists( $file ) && require_once( $file );
            }

        }

        /**
         * Check if user has YITH Multivendor Premium plugin
         *
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @since  1.0
         * @return bool
         */
        static function has_multivendor_plugin() {
            return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, apply_filters( 'yith_wcpsc_multivendor_min_version', '1.7.1' ), '>=' );
        }
    }
}
/**
 * @return YITH_WC_Surveys_Compatibility
 */
function YITH_Surveys_Compatibility(){

    return YITH_WC_Surveys_Compatibility::get_instance();
}