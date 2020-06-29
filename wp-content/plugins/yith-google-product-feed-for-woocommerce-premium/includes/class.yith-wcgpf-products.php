<?php
/**
 * Product class
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Products' ) ) {
    /**
     * YITH_WCGPF_Products
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Products
    {
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Products
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Products instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
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
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        protected function __construct()
        {
            add_filter('yith_wcgpf_product_properties_wc',array($this,'add_wc_product_attributes'),10);
        }

        /**
         * return list of post type product and product_variation
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function get_products($filters="",$limit='',$offset='')
        {
            $params = array(
                'post_type' => array('product'),
                'posts_per_page' => 5,
                'fields'         => 'ids'
            );
            $posts = get_posts($params);

            
            return $posts ;
        }
    }
}