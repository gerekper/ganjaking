<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'YITH_WC_Surveys_Multivendor_Compatibility' ) ) {
    /**
     * Multivendor Compatibility Class
     *
     * @class   YITH_WC_Surveys_Multivendor_Compatibility
     * @package Yithemes
     * @since   1.0.1
     * @author  Yithemes
     *
     */
    class YITH_WC_Surveys_Multivendor_Compatibility
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Surveys_Multivendor_Compatibility
         * @since 1.0.1
         */
        protected static $instance;

        /**
         * @var string survey post type
         */
        private $_surveys_post_type = 'yith_wc_surveys';

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Surveys_Multivendor_Compatibility
         * @since 1.0.1
         */
        public static function get_instance()
        {
            if(is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.1
         */
        public function __construct(){
            if ( ! YITH_WC_Surveys_Compatibility::has_multivendor_plugin() )
                return;

                $this->_vendor_taxonomy_name = YITH_Vendors()->get_taxonomy_name();

                /* Surveys filter for vendor*/
                add_action( 'yith_wc_surveys_table_init', array( $this, 'check_if_vendor_can_view_report' ) );
                add_filter( 'yith_wc_surveys_types', array( $this, 'get_surveys_type_for_vendors' ) ) ;
        }


        /**
         * check if vendor can view report
         * @author YIThemes
         * @since 1.0.1
         * @param YITH_WC_Surveys_Table $report_table
         */
        public function check_if_vendor_can_view_report( $report_table ){

            $survey_id = $report_table->survey_id;

            $vendor = yith_get_vendor( 'current', 'user' );

            if( $vendor && $vendor->is_valid() && $vendor->has_limited_access() ){

                if( has_term( $vendor->id, YITH_Vendor::$taxonomy, $survey_id ) )
                    return;

                wp_die( 'You do not have sufficient permissions to access this page.', 'ERROR' );
            }
        }

        /**
         * get survey type for vendors
         * @author YIThemes
         * @since 1.0.1
         * @param array $surveys_type
         * @return array
         */
        public function get_surveys_type_for_vendors( $surveys_type ){

            $vendor = yith_get_vendor( 'current', 'user' );

            if( $vendor && $vendor->is_valid() && $vendor->has_limited_access()  ) {

                $visible_option = array(
                    'product' => __('WooCommerce Product', 'yith-woocommerce-surveys'),
                    );
                return $visible_option;
            }
            return $surveys_type;
            }

    }
}

/**
 * Unique access to instance of YITH_WC_Surveys_Multivendor_Compatibility class
 *
 * @return YITH_WC_Surveys_Multivendor_Compatibility
 * @since 1.0.0
 */
function YITH_Surveys_Multivendor_Compatibility() {
    return YITH_WC_Surveys_Multivendor_Compatibility::get_instance();
}

YITH_Surveys_Multivendor_Compatibility();