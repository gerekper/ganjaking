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
 * @class      YITH_WCGPF_Feed_Functions_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Feed_Functions_Premium' ) ) {

    class YITH_WCGPF_Feed_Functions_Premium extends YITH_WCGPF_Feed_Functions {
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Feed_Functions_Premium
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Feed_Functions_Premium instance
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
         * Get list of merchant
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function merchant() {
            $merchants = array(
                "google"    => "Google Shopping",
            );

            return apply_filters('yith_wcgpf_feed_google_merchants',$merchants);
        }

        /**
         * Get list of fyle type
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */

        public function type_file() {
            $type_file = array(
                'xml' => 'XML',
                'txt' => 'TXT'
            );
            return apply_filters('yith_wcgpf_feed_google_type_file',$type_file);
        }

        /**
         * Get list of template created
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_list_template() {

            $defaults = apply_filters( 'yith_wcgpf_get_templates_merchant',array(
                'posts_per_page' => -1, //default -1
                'post_type' => 'yith-wcgpf-template',
            ));

            $params = wp_parse_args( $defaults );
            $results = get_posts( $params );
            $list_template = array(
                'default' =>esc_html__('Default','yith-google-product-feed-for-woocommerce'),
            );
            foreach ($results as $key){
                $list_template[$key->ID] = $key->post_title;
            }
            return apply_filters('yith_wcgpf_get_list_template',$list_template);
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
        public function create_feed($merchant,$values) {
            $feed_url = array( 'yith_wcgpf_feed' => $values['feed_type'], 'feed_id' => $values['post_id'], 'merchant' => $values['merchant'] );
            $feed_url = add_query_arg( $feed_url, home_url());
            return $feed_url;
        }


    }


}