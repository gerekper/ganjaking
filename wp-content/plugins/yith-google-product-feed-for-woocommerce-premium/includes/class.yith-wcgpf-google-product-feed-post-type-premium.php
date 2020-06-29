<?php
/**
 * Post Types Premium class
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Post_Types_Feed_Premium' ) ) {
    /**
     * YITH WCGPF Post Type Feed
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Post_Types_Feed_Premium extends YITH_WCGPF_Post_Types_Feed {

        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Post_Types_Feed_Premium
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return YITH_WCGPF_Post_Types_Feed_Premium instance
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
         * @return YITH_WCGPF_Post_Types_Feed_Premium
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct() {
            parent::__construct();
        }


        /**
         * Add template metabox .
         */
        function configuration_template_metabox($post) {

            if( file_exists( YITH_WCGPF_TEMPLATE_PATH . 'admin/make-tab/configuration-feed-premium.php' ) ) {
                include_once( YITH_WCGPF_TEMPLATE_PATH . 'admin/make-tab/configuration-feed-premium.php' );
            }
        }


        /**
         * Save post data.
         */
        public function save_post_data($post_id) {

            if(!isset($_POST['yith-merchant']) || !isset($_POST['yith-feed-type']) ){
                return;
            }


            $merchant = $_POST['yith-merchant'];
            $feed_type = $_POST['yith-feed-type'];
            $template_feed = 'personalized';
            $categories_selected = isset( $_POST['yith-feed-category'] ) ? $_POST['yith-feed-category']: false;
            $tags_selected = isset(  $_POST['yith-feed-tags'] ) ?  $_POST['yith-feed-tags'] : false;
            $include_products = isset(  $_POST['yith-feed-include-product'] ) ?  $_POST['yith-feed-include-product'] : false;
            $exclude_products = isset(  $_POST['yith-feed-exclude-product'] ) ?  $_POST['yith-feed-exclude-product'] : false;

            $filters = array(
                'categories_selected' => $categories_selected,
                'tags_selected' => $tags_selected,
                'include_products' => $include_products,
                'exclude_products' => $exclude_products,
            );

            $values = array(
                'merchant' => $merchant,
                'post_id' => $post_id,
                'feed_type' => $feed_type,
                'template_feed' => $template_feed,
            );
            
            $values = array_merge($values,$filters);
            
            //Feed template
            $attributes = isset($_POST['yith-wcgpf-attributes']) ? $_POST['yith-wcgpf-attributes'] : 0 ;
            $prefix = isset($_POST['yith_wcgpf_prexif']) ? $_POST['yith_wcgpf_prexif'] : '';
            $value = isset($_POST['yith-wcgpf-value']) ? $_POST['yith-wcgpf-value'] : '';
            $suffix = isset($_POST['yith_wcgpf_sufix']) ? $_POST['yith_wcgpf_sufix'] : '';

            $count  = count($attributes);
            for ( $i = 0; $i < $count; $i++ ) {
                if ( '' != $attributes[$i] ) {
                    $feed_tamplate[$i]['attributes'] = $attributes[$i];
                    $feed_tamplate[$i]['prefix'] = $prefix[$i];
                    $feed_tamplate[$i]['value'] = $value[$i];
                    $feed_tamplate[$i]['suffix'] = $suffix[$i];
                }
            }

            $feed_tamplate = array(
                'feed_template' =>$feed_tamplate,
            );
            $values = array_merge($values,$feed_tamplate);

            if ( !empty( $feed_tamplate ) && !empty( $values )) {

                $functions =  YITH_Google_Product_Feed()->functions;
                $feed = $functions->create_feed($merchant,$values);
                $values['feed_url'] = $feed;


                update_post_meta( $post_id, 'yith_wcgpf_save_feed', $values );

                do_action( 'yith_wcgpf_save_feed_file',$post_id,$values );

            }
        }

        public function add_metaboxes($post)
        {
            parent::add_metaboxes($post);
            add_meta_box( 'yith_wcgpf_filters_and_conditions',  esc_html__( 'Filters and conditions', 'yith-google-product-feed-for-woocommerce' ), array($this,'filters_and_conditions_metabox'), 'yith-wcgpf-feed', 'side', 'low' );
        }

        function filters_and_conditions_metabox($post) {
            if( file_exists( YITH_WCGPF_TEMPLATE_PATH . 'admin/make-tab/filter-and-conditions.php' ) ) {
                include_once( YITH_WCGPF_TEMPLATE_PATH . 'admin/make-tab/filter-and-conditions.php' );
            }
        }
    }
}