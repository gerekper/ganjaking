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
 * @class      YITH_WCGPF_Feed_Functions
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Feed_Functions' ) ) {

    class YITH_WCGPF_Feed_Functions {
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Feed_Functions
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;
        
        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Feed_Functions instance
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
         * Get list of products
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_products() {
            $products = YITH_Google_Product_Feed()->products;
            return $products->get_products();
        }

        /**
         * Create Feed
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function create_feed($merchant,$type)
        {
            $feed_url = array('yith_wcgpf_feed' => $type, 'merchant' => $merchant);
            $feed_url = add_query_arg($feed_url, home_url());
            return $feed_url;
        }
    }


}