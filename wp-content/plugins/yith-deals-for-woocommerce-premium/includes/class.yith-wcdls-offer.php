<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'YITH_WCDLS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}


/**
 *
 *
 * @class      YITH_WCDLS_Offer
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_WCDLS_Offer' ) ) {
    /**
     * YITH Deals for WooCommerce
     *
     * @since 1.0.0
     */
    class YITH_WCDLS_Offer
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDLS_Offer
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCDLS_Offer
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }


        /**
         * Payment Restriction Post Type
         *
         * @var string
         * @static
         */
        public static $rule = 'yith_wcdls_offer';
        public $post_type_name = 'yith_wcdls_offer';

        /**
         * Hook in methods.
         */
        public function __construct() {
            add_action( 'init', array($this, 'register_post_types' ));
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ),10,2 );
            add_action( 'save_post', array( $this, 'save_metabox' ), 10, 1 );
            add_action( 'edit_form_advanced', array( $this, 'add_return_to_list_button' ) );
        }

        /**
         * Register core post types.
         */
        public function register_post_types() {
            if ( post_type_exists( self::$rule ) ) {
                return;
            }

            do_action( 'yith_wcdls_register_post_type' );

            /* Deals  */

            $labels = array(
                'name'               => esc_html__( 'Deals', 'yith-deals-for-woocommerce' ),
                'singular_name'      => esc_html__( 'Deals', 'yith-deals-for-woocommerce' ),
                'add_new'            => esc_html__( 'Add new offer', 'yith-deals-for-woocommerce' ),
                'add_new_item'       => esc_html__( 'Add new offer', 'yith-deals-for-woocommerce' ),
                'edit'               => esc_html__( 'Edit', 'yith-deals-for-woocommerce' ),
                'edit_item'          => esc_html__( 'Edit Offer', 'yith-deals-for-woocommerce' ),
                'new_item'           => esc_html__( 'New Offer', 'yith-deals-for-woocommerce' ),
                'view'               => esc_html__( 'View Offer', 'yith-deals-for-woocommerce' ),
                'view_item'          => esc_html__( 'View Offer', 'yith-deals-for-woocommerce' ),
                'search_items'       => esc_html__( 'Search Offers', 'yith-deals-for-woocommerce' ),
                'not_found'          => esc_html__( 'No Offers found', 'yith-deals-for-woocommerce' ),
                'not_found_in_trash' => esc_html__( 'No Offers found in trash', 'yith-deals-for-woocommerce' ),
                'parent'             => esc_html__( 'Parent Offers', 'yith-deals-for-woocommerce' ),
                'menu_name'          => esc_html_x( 'YITH Offers', 'Admin menu name', 'yith-deals-for-woocommerce' ),
                'all_items'          => esc_html__( 'All YITH Offers', 'yith-deals-for-woocommerce' ),
            );

            $offer_args = array(
                'label'               => esc_html__( 'Deals', 'yith-deals-for-woocommerce' ),
                'labels'              => $labels,
                'description'         => esc_html__( 'This is where deals are stored.', 'yith-deals-for-woocommerce' ),
                'public'              => true,
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
                'supports'            => array( 'title','editor' ),
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-edit',
                //'show_in_rest' => true,

            );

            register_post_type( self::$rule, apply_filters( 'yith_wcdls_register_post_type_deals', $offer_args ) );

        }
        /**
         * Add style metabox custom post type.
         */
        public function add_meta_boxes( $post_type, $post ) {

            if ( $post_type && self::$rule  == $post_type ) {
                add_meta_box( 'wcdls-description-offer-metabox',
                    esc_html__( 'Offer settings', 'yith-deals-for-woocommerce' ),
                    array( $this, 'yith_wcdls_description_offer_metabox' ), self::$rule, 'normal', 'core'
                );

            }


        }

        /**
         * save_metabox
         *
         * Save post type data
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function save_metabox($post_id) {

            if(isset($_POST['yith_wcdls_description_offer'])) {
                update_post_meta($post_id ,'yith_wcdls_description_offer',$_POST['yith_wcdls_description_offer']);
            }

        }


        /*
         *
         * Description offer metabox
         *
         */
        public function yith_wcdls_description_offer_metabox($post) {
            if ( ! $post ) {
                return;
            }

            do_action('yith_wcdls_add_meta_boxes',$post);

        }

        /**
         * Add content in metabox.
         */
        public function add_return_to_list_button() {
            global $post;

            if ( isset( $post ) && self::$rule === $post->post_type ) {
                $admin_url = admin_url( 'admin.php' );
                $params = array(
                    'page' => 'yith_wcdls_panel_product_deals',
                    'tab' => 'deals'
                );

                $list_url = apply_filters( 'yith_wcdls_offer_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
                $button = sprintf( '<a class="button-secondary" href="%s">%s</a>', $list_url,
                    esc_html__( 'Back to Deals list',
                        'yith-deals-for-woocommerce' ) );
                echo $button;
            }
        }
    }
}

/**
 * Unique access to instance of YYITH_WCDLS_Offer class
 *
 * @return \YITH_WCDLS_Offer
 */
function YITH_WCDLS_Offer() {
    return YITH_WCDLS_Offer::get_instance();
}
