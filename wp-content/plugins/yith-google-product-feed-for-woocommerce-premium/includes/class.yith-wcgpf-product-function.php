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
 * @class      YITH_WCGPF_Product_Functions
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Product_Functions' ) ) {

    class YITH_WCGPF_Product_Functions
    {
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Product_Functions
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        public $create_feed = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Product_Functions instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function get_instance()
        {
            $self = __CLASS__ . (class_exists(__CLASS__ . '_Premium') ? '_Premium' : '');

            if (is_null($self::$_instance)) {
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
         * Get product condition
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function condition() {
            $condition = array(
                'new'         => esc_html__( 'New', 'yith-google-product-feed-for-woocommerce' ),
                'refurbished' =>  esc_html__( 'Refurbished', 'yith-google-product-feed-for-woocommerce' ),
                'used'        => esc_html__( 'Used', 'yith-google-product-feed-for-woocommerce' ),
            );

            return apply_filters('yith_wcgpf_get_product_condition',$condition);
        }
        
        /**
         * Get adult status
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function adult() {
            $adult = array(
                'yes'         => esc_html__( 'Yes', 'yith-google-product-feed-for-woocommerce' ),
                'no' =>  esc_html__( 'No', 'yith-google-product-feed-for-woocommerce' ),
            );

            return apply_filters('yith_wcgpf_adult_option',$adult);
        }
        
        /**
         * Get google product category
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function google_category($is_global='') {
            $language           = get_locale();

            $google_categories = get_transient( 'yith_wcgpf_google_categories_' . $language );
            if ( !$google_categories ) {

                $language_suffix    = str_replace( '_','-', $language );

                switch( $language_suffix ) {
                    case 'pt-PT' :
                        $language_suffix = 'pt-BR';
                        break;
                    case 'nb-NO' :
                        $language_suffix = 'no-NO';
                        break;

                    case 'en' :
                        $language_suffix = 'en-US';
                }

                $google_categories      = array();
                $google_categories_url  = apply_filters('yith_wcgpf_get_google_categories_url',"https://www.google.com/basepages/producttype/taxonomy-with-ids.$language_suffix.txt");
                $remote_file            = wp_remote_get ( $google_categories_url );

                if ( !is_wp_error( $remote_file ) && isset( $remote_file[ 'response' ][ 'code' ] ) && '200' == $remote_file[ 'response' ][ 'code' ] ) {
                    $categories_txt     = $remote_file[ 'body' ];
                    $categories_lines   = preg_split( '/$\R?^/m', $categories_txt );

                    if ( count( $categories_lines ) > 0 ) {
                        unset( $categories_lines[ 0 ] );
                        foreach ( $categories_lines as $categories_line ) {
                            $category_info  = explode( ' - ', $categories_line );
                            $id             = $category_info[ 0 ];
                            $value          = $category_info[ 1 ];

                            $google_categories[ $id ] = apply_filters('yith_wcgpf_google_category_value',$value,$id);
                        }
                    }
                    set_transient( 'yih_wcgpf_google_categories_' . $language, $google_categories, 10 * DAY_IN_SECONDS );
                }

                if(!empty($is_global)) {
                    $default = array('99999' => esc_html__('General field','yith-google-product-feed-for-woocommerce'));
                    $google_categories = $default + $google_categories;
                }

                return apply_filters('yith_wcgpf_google_product_category_option',$google_categories);
            }
        }
    }
}

