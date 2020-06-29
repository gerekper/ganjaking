<?php

/**
 * Class Feed File
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Feed_File' ) ) {
    /**
     * YITH_WCGPF_Helper
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Feed_File
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCGPF_Feed_File
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCGPF_Feed_File
         * @since 1.0.0
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }


        /**
         * @type array
         */
        public $allowed_merchant = array();

        public function __construct( )
        {
            add_filter('yith_wcgpf_list_columns',array($this,'add_feed_file_url_column'));
            add_filter('yith_wcgpf_column_default',array($this,'add_feed_file_url_content'),10,3);

        }

        public function add_feed_file_url_column($columns){

            $columns['feed_file'] = __('Feed file','yith-google-product-feed-for-woocommerce');
            return $columns;

        }

        public function add_feed_file_url_content( $value,$item,$column_name ) {

            if ( 'feed_file' == $column_name ) {
                $feed = get_post_meta($item->ID,'yith_wcgpf_save_feed',true);
                return ( isset($feed['feed_file']) && $feed['feed_file'] ) ? '<a target="_blank" href="'.$feed['feed_file'].'">'.$feed['feed_file'].'</a>':'';
            } else {
                return $value;
            }
        }
    }
}