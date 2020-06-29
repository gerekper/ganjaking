<?php
/**
 * Post Types class
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Post_Types_Feed' ) ) {
    /**
     * YITH WCGPF Post Type Feed
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Post_Types_Feed {


        /**
         * Feed Post Type
         *
         * @var string
         * @static
         */
        public static $feed = 'yith-wcgpf-feed';

        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Google_Product_Feed_Merchant
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return YITH_WCGPF_Post_Types_Feed instance
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
            add_action( 'init', array($this, 'register_post_types' ));
            add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ));
            add_action( 'edit_form_advanced', array( $this, 'add_return_to_list_button' ) );
            add_action('save_post',array($this,'save_post_data'));
        }

        /**
         * Register core post types.
         */
        public function register_post_types() {
            if ( post_type_exists( self::$feed ) ) {
                return;
            }

            do_action( 'yith_wcgpf_register_post_type_template_feed' );

            /*  Google Product Feed Templates  */

            $labels = array(
                'name'               => esc_html__( 'Make feed', 'yith-google-product-feed-for-woocommerce' ),
                'singular_name'      => esc_html__( 'Make feed', 'yith-google-product-feed-for-woocommerce' ),
                'add_new'            => esc_html__( 'Add feed', 'yith-google-product-feed-for-woocommerce' ),
                'add_new_item'       => esc_html__( 'Add new feed', 'yith-google-product-feed-for-woocommerce' ),
                'edit'               => esc_html__( 'Edit', 'yith-google-product-feed-for-woocommerce' ),
                'edit_item'          => esc_html__( 'Edit feed', 'yith-google-product-feed-for-woocommerce' ),
                'new_item'           => esc_html__( 'New feed', 'yith-google-product-feed-for-woocommerce' ),
                'view'               => esc_html__( 'View feed', 'yith-google-product-feed-for-woocommerce' ),
                'view_item'          => esc_html__( 'View feed', 'yith-google-product-feed-for-woocommerce' ),
                'search_items'       => esc_html__( 'Search feed', 'yith-google-product-feed-for-woocommerce' ),
                'not_found'          => esc_html__( 'No feed found', 'yith-google-product-feed-for-woocommerce' ),
                'not_found_in_trash' => esc_html__( 'No feed in trash', 'yith-google-product-feed-for-woocommerce' ),
                'parent'             => esc_html__( 'Parent feed', 'yith-google-product-feed-for-woocommerce' ),
                'menu_name'          => esc_html_x( 'YITH Feed', 'Admin menu name', 'yith-google-product-feed-for-woocommerce' ),
                'all_items'          => esc_html__( 'All Feeds', 'yith-google-product-feed-for-woocommerce' ),
            );

            $feed_post_type_args = array(
                'label'               => esc_html__( 'Feed', 'yith-google-product-feed-for-woocommerce' ),
                'labels'              => $labels,
                'description'         => esc_html__( 'This is where feed are stored.', 'yith-google-product-feed-for-woocommerce' ),
                'public'              => false,
                'show_ui'             => true,
                'capability_type'     => 'product',
                'map_meta_cap'        => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'show_in_menu'        => false,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => array( 'title' ),
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-edit',
            );

            register_post_type( self::$feed, apply_filters( 'yith_wcgpf_register_post_type_feed', $feed_post_type_args ) );

        }

        /**
         * Add custom metaboxes.
         */
        public function add_metaboxes($post)
        {
            add_meta_box( 'yith_wcgpf_feed_custom',  esc_html__( 'Configuration feed', 'yith-google-product-feed-for-woocommerce' ), array($this,'configuration_template_metabox'), 'yith-wcgpf-feed', 'normal', 'high' );
        }
        /**
         * Add template metabox .
         */
        function configuration_template_metabox($post) {
            
            if( file_exists( YITH_WCGPF_TEMPLATE_PATH . 'admin/make-tab/configuration-feed.php' ) ) {
                include_once( YITH_WCGPF_TEMPLATE_PATH . 'admin/make-tab/configuration-feed.php' );
            }
        }

        public function add_return_to_list_button() {
            global $post;

            if ( isset( $post ) && self::$feed === $post->post_type ) {
                $admin_url = admin_url( 'admin.php' );
                $params = array(
                    'page' => 'yith_wcgpf_panel',
                    'tab' => 'manage'
                );

                $list_url = apply_filters( 'yith_wcgpf_feed_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
                $button = sprintf( '<a class="button-secondary" href="%s">%s</a>', $list_url,
                    esc_html__( 'Back to the list of feeds',
                        'yith-google-product-feed-for-woocommerce' ) );
                echo $button;
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
            
            $values = array(
                'merchant' => $merchant,
                'post_id' => $post_id,
                'feed_type' => $feed_type,
                'template_feed' => $template_feed,
            );

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
            }
        }
    }
}