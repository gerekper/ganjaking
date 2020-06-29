<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WCOP_Survey_Post_Type' ) ){

    class YITH_WCOP_Survey_Post_Type{

        protected static $instance;
        public $post_type_name ;

        public function __construct(){

            $this->post_type_name = 'ywcpos_survey';
            //register post type and add metabox
            add_action( 'init', array( $this, 'register_post_type' ) );
            add_action( 'admin_init', array( $this, 'add_metaboxes' ) );
            add_action( 'admin_init', array( $this, 'add_capabilities' ) );

            //Custom Pending Order Survey Message
            add_filter( 'post_updated_messages', array($this, 'custom_pending_order_survey_messages' ) );

            add_filter( 'yit_fw_metaboxes_type_args', array($this, 'add_custom_pending_order_survey_metaboxes' ) );

            add_filter( 'template_include', array( $this, 'include_custom_template' ) );

        }


        /**
         * return single instance
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WCOP_Survey_Post_Type
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function add_metaboxes(){

            /**
             * @var $metaboxes array metabox_id, metabox_opt
             */
            $metaboxes	= array(
                'yit-pending-order-survey-metaboxes'        =>  'pending-survey-metaboxes-options.php',
                'yit-pending-order-survey-report-metaboxes' =>  'pending-survey-report-metaboxes-options.php'
             );

            if (!function_exists( 'YIT_Metabox' ) ) {
                require_once( YITH_WCPO_SURVEY_DIR.'plugin-fw/yit-plugin.php' );
            }

            foreach( $metaboxes as $key=> $metabox ) {
                $args = require_once( YITH_WCPO_SURVEY_TEMPLATE_PATH.'/metaboxes/'.$metabox );
                $box = YIT_Metabox( $key );
                $box->init( $args );
            }

        }
        /**
         * register post type
         * @author YIThemes
         * @since 1.0.0
         */
        public function register_post_type(){

            $args = apply_filters('yith_pending_order_survey_args_post_type', array(
                    'label' => $this->post_type_name,
                    'description' => __('YITH WooCommerce Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'labels' => $this->get_pending_order_survey_taxonomy_label(),
                    'supports' => array('title'),
                    'hierarchical' => false,
                    'public' => true,
                    'show_ui' => true,
                    'show_in_menu' => false,
                    'show_in_nav_menus' => false,
                    'show_in_admin_bar' => false,
                    'can_export' => true,
                    'has_archive' => false,
                    'exclude_from_search' => true,
                    'publicly_queryable' => true,
                    'rewrite' => array('slug' => 'pending-survey'),
                    'capabilities' => $this->get_capabilities(),
                )
            );


            register_post_type( $this->post_type_name, $args );

        }

        /**
         * get pending order survey capabilities
         * @author YIThemes
         * @since 1.0.0
         * @return array
         */
        public function get_capabilities(){

            $capability_type = 'pending_order_survey';
            $caps = array(
                'edit_post' => "edit_{$capability_type}",
                'read_post' => "read_{$capability_type}",
                'delete_post' => "delete_{$capability_type}",
                'edit_posts' => "edit_{$capability_type}s",
                'edit_others_posts' => "edit_others_{$capability_type}s",
                'publish_posts' => "publish_{$capability_type}s",
                'read_private_posts' => "read_private_{$capability_type}s",
                'read' => "read",
                'delete_posts' => "delete_{$capability_type}s",
                'delete_private_posts' => "delete_private_{$capability_type}s",
                'delete_published_posts' => "delete_published_{$capability_type}s",
                'delete_others_posts' => "delete_others_{$capability_type}s",
                'edit_private_posts' => "edit_private_{$capability_type}s",
                'edit_published_posts' => "edit_published_{$capability_type}s",
                'create_posts' => "edit_{$capability_type}s",
                'manage_posts' => "manage_{$capability_type}s",
            );

            return $caps;
        }

        /**
         * add capabilities for administrato and for shop_manager
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_capabilities(){

            $admin        = get_role( 'administrator' );
            $shop_manager = get_role( 'shop_manager' );

            $caps = $this->get_capabilities();

            foreach ( $caps as $key => $cap ) {

                $admin->add_cap( $cap );
                $shop_manager->add_cap( $cap );
            }
        }
        /**
         * Get the tab taxonomy label
         * @param   $arg string The string to return. Defaul empty. If is empty return all taxonomy labels
         * @author YIThemes
         * @since  1.0.0
         * @return Array taxonomy label
         *
         */
        protected function get_pending_order_survey_taxonomy_label($arg = '')
        {

            $label = apply_filters('yith_pending_order_survey_taxonomy_label', array(
                    'name' => _x('YITH WooCommerce Pending Order Survey', 'post type general name', 'yith-woocommerce-pending-order-survey'),
                    'singular_name' => _x('Pending Order Survey', 'post type singular name', 'yith-woocommerce-pending-order-survey'),
                    'menu_name' => __('Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'parent_item_colon' => __('Parent Item:', 'yith-woocommerce-pending-order-survey'),
                    'all_items' => __('All Pending Order Surveys', 'yith-woocommerce-pending-order-survey'),
                    'view_item' => __('View Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'add_new_item' => __('Add New Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'add_new' => __('Add New Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'edit_item' => __('Edit Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'update_item' => __('Update Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'search_items' => __('Search Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                    'not_found' => __('No Pending Order Survey found', 'yith-woocommerce-pending-order-survey'),
                    'not_found_in_trash' => __('No Pending Order Survey found in Trash', 'yith-woocommerce-pending-order-survey'),
                )
            );
            return !empty($arg) ? $label[$arg] : $label;
        }

        /**
         * Customize the messages for Pending Order Survey
         * @param $messages
         * @author Yithemes
         *
         * @return array
         * @fire post_updated_messages filter
         */
        public function custom_pending_order_survey_messages ( $messages ) {

            $singular_name  =   $this->get_pending_order_survey_taxonomy_label('singular_name');
            $messages[$this->post_type_name] =   array (

                0    =>  '',
                1    =>  sprintf(__('%s updated','yith-woocommerce-pending-order-survey') , $singular_name ) ,
                2    =>  __('Custom field updated', 'yith-woocommerce-pending-order-survey'),
                3    =>  __('Custom field deleted', 'yith-woocommerce-pending-order-survey'),
                4    =>  sprintf(__('%s updated','yith-woocommerce-pending-order-survey') , $singular_name ) ,
                5    =>  isset( $_GET['revision'] ) ? sprintf( __( 'Survey restored to version %s', 'yith-woocommerce-pending-order-survey' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6    =>  sprintf( __('%s published', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
                7    => sprintf( __('%s saved', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
                8    => sprintf( __('%s submitted', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
                9    => sprintf( __('%s', 'yith-woocommerce-pending-order-survey'), $singular_name ),
                10   =>  sprintf( __('%s draft updated', 'yith-woocommerce-pending-order-survey'), $singular_name )
            );

            return $messages;
        }

        public function add_custom_pending_order_survey_metaboxes( $args ){
            global $post;
           if( isset( $post ) && $this->post_type_name === $post->post_type ) {
               if ('pending_survey_type' == $args['type']) {
                   $args['basename'] = YITH_WCPO_SURVEY_DIR;
                   $args['path'] = 'metaboxes/types/';
               }

               if( 'pending_survey_report' == $args['type'] ){
                   $args['basename'] = YITH_WCPO_SURVEY_DIR;
                   $args['path'] = 'metaboxes/types/';
               }
           }

            return $args;
        }

        /**get all pending survey
         * @author YIThemes
         * @since 1.0.0
         * @param array $extra_param
         * @return array
         */
        public function get_pending_survey( $extra_param = array() ){

            $default = array(
                'posts_per_page' => -1,
                'post_type' => 'ywcpos_survey',
                'post_status' => 'any',
                'post_parent' => 0,
            );

            $author =  apply_filters( 'ywcpos_post_author', -1 );

            if( -1 !== $author )
                $args['author'] = $author;

            $args = array_merge( $default, $extra_param );
            $results = array();

            $query = new WP_Query( $args );
            if( $query->have_posts() ) {

                while( $query->have_posts() ) {

                    $query->the_post();
                    $results[] = $query->post->ID;
                }
            }

            wp_reset_query();
            wp_reset_postdata();

            return $results;



        }

        public function include_custom_template( $template ){

            if( 'ywcpos_survey'=== get_post_type() ){

                $template = YITH_WCPO_SURVEY_TEMPLATE_PATH.'single-pending-survey.php';

                if( isset( $_GET['order_id'] ) ){

                    global $post;

                    $order_id = $_GET['order_id'];
                    $orders = get_post_meta( $post->ID, '_ywcpos_orders', true );
                    $orders = empty( $orders )? array() : $orders;

                    if( in_array( $order_id, $orders ) ){

                        $template = YITH_WCPO_SURVEY_TEMPLATE_PATH.'pending-survey-not-avaible.php';
                    }
                }else{

                    if( !current_user_can('manage_options' ) )
                        wp_die( 'You do not have sufficient permissions to access this page.', 'ERROR' );
                }


            }

            return $template;
        }

    }
}
/** return Pending Order Survey PostType
 * @author YIThemes
 * @since 1.0.0
 * @return YITH_WCOP_Survey_Post_Type
 */
function YITH_Pending_Order_Survey_Type(){

    return YITH_WCOP_Survey_Post_Type::get_instance();
}