<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_Product_Slider_Type_Premium') ){

    class YITH_Product_Slider_Type_Premium extends  YITH_Product_Slider_Type{


        public function __construct()
        {
            parent::__construct();

            add_action( 'admin_init', array( $this, 'add_tab_metabox') );
            add_filter( 'manage_edit-' . $this->_post_type_name . '_columns', array( $this, 'edit_columns' ) );
            add_action( 'manage_' . $this->_post_type_name . '_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
            //Custom Tab Message
            add_filter( 'post_updated_messages', array($this, 'custom_tab_messages' ) );
            //register metabox to tab manager
            add_filter( 'yit_fw_metaboxes_type_args', array($this, 'add_custom_product_slider_metaboxes' ) );
        }

        public static function get_instance()
        {
            if (is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * add_tab_metabox
         * Register metabox for product slider
         * @author YITHEMES
         * @since 1.0.0
         */
        public function  add_tab_metabox() {

            $args	=	include_once( YWCPS_INC . '/metaboxes/product_slider-metabox.php');

            if (!function_exists( 'YIT_Metabox' ) ) {
                require_once( YWCPS_DIR.'plugin-fw/yit-plugin.php' );
            }
            $metabox    =   YIT_Metabox('yit-product-slider-setting');
            $metabox->init($args);

        }

        /** Edit Columns Table
         * @param $columns
         * @return mixed
         */
        function edit_columns( $columns ) {

            $columns = apply_filters('yith_add_column_prod_slider', array(
                    'cb' => '<input type="checkbox" />',
                    'title' => __('Title', 'yith-woocommerce-product-slider-carousel'),
                    'shortcode' => __('Shortcode', 'yith-woocommerce-product-slider-carousel'),
                    'date' => __('Date', 'yith-woocommerce-product-slider-carousel'),
                )
            ) ;

            return $columns;
        }

        /**
         * Print the content columns
         * @param $column
         * @param $post_id
         */
        public function custom_columns( $column, $post_id ) {

            switch( $column ) {
                case 'shortcode' :
                    echo '[yith_wc_productslider id='.$post_id.']';
                    break;
            }
        }

        public function add_custom_product_slider_metaboxes( $args ){


            $custom_types = array(
                'custom_checkbox',
                'select-group',
            );
            if( in_array( $args['type'], $custom_types ) ){
                $args['basename']   = YWCPS_DIR;
                $args['path']       = 'metaboxes/types/';
            }


            return $args;
        }

        /**
         * Customize the messages for Sliders
         * @param $messages
         * @author YITHEMES
         *
         * @return array
         * @fire post_updated_messages filter
         */
        public function custom_tab_messages ( $messages ) {

            $singular_name  =   $this->get_tab_taxonomy_label('singular_name');
            $messages[$this->_post_type_name] =   array (

                0    =>  '',
                1    =>  sprintf(__('%s updated','yith-woocommerce-product-slider-carousel') , $singular_name ) ,
                2    =>  __('Custom field updated', 'yith-woocommerce-product-slider-carousel'),
                3    =>  __('Custom field deleted', 'yith-woocommerce-product-slider-carousel'),
                4    =>  sprintf(__('%s updated','yith-woocommerce-product-slider-carousel') , $singular_name ) ,
                5    =>  isset( $_GET['revision'] ) ? sprintf( __( 'Product Slider Carousel restored to version %s', 'yith-woocommerce-product-slider-carousel' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6    =>  sprintf( __('%s published', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
                7    => sprintf( __('%s saved', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
                8    => sprintf( __('%s submitted', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
                9    => sprintf( __('%s', 'yith-woocommerce-product-slider-carousel'), $singular_name ),
                10   =>  sprintf( __('%s draft updated', 'yith-woocommerce-product-slider-carousel'), $singular_name )
            );


            return $messages;
        }
    }
}