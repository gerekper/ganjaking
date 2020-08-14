<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_WCGPF_Merchant_Google
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Merchant_Google' ) ) {

    class YITH_WCGPF_Merchant_Google {
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Merchant_Google
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Merchant_Google instance
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

        public function __construct()
        {

        }
        /**
         * Get google_rows();
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function google_rows(){
            $default_rows = array(
                array(
                    'attributes' => 'id',
                    'type' => 'attributes',
                    'value' => 'id',
                ),
                array(
                    'attributes' => 'title',
                    'type' => 'attributes',
                    'value' => 'title',
                ),
                array(
                    'attributes' => 'description',
                    'type' => 'attributes',
                    'value' => 'description'
                ),
                array(
                    'attributes' => 'link',
                    'type' => 'attributes',
                    'value' => 'link',
                ),
                array(
                    'attributes' => 'image_link',
                    'type' => 'attributes',
                    'value' => 'image_link',
                ),
                array(
                    'attributes' => 'availability',
                    'type' => 'attributes',
                    'value' => 'availability',
                ),
                array(
                    'attributes' => 'price',
                    'type' => 'attributes',
                    'value' => 'price',
                ),
                array(
                    'attributes' => 'google_product_category',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_google_product_category',
                ),
                array(
                    'attributes' => 'brand',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_brand',
                ),
                array(
                    'attributes' => 'gtin',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_gtin',
                ),
                array(
                    'attributes' => 'mpn',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_mpn',
                ),
                array(
                    'attributes' => 'condition',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_condition',
                ),
                array(
                    'attributes' => 'item_group_id',
                    'type' => 'attributes',
                    'value' => 'item_group_id'
                ),
                array(
                    'attributes' => 'adult',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_adult',
                ),
            );
            return apply_filters('yith_wcgpf_google_default_rows',$default_rows);
        }
    }
}