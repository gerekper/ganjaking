<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH Composite Products for WooCommerce
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCP' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCP_Admin' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCP_Admin {

        /* @var YIT_Plugin_Panel_WooCommerce */
        protected $_panel;

        /**
         * @var string Main Panel Option
         */
        protected $_main_panel_option;

        /**
         * @var string The panel page
         */
        protected $_panel_page = 'yith_wcp_panel';

        /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-composite-products';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-composite-products';

        /**
         * @var string Official live demo
         */
        protected $_premium_live = 'http://plugins.yithemes.com/yith-woocommerce-composite-products';


        /**
         * @var YITH_WCP
         */
        protected $_wcp_object = null;

        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        public function __construct( $wcp_object ) {

            $this->_wcp_object = $wcp_object;

            //Actions
            add_action( 'init', array( $this, 'init' ) );

            add_action( 'admin_menu', array( $this, 'register_panel' ), 5) ;

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

            /* Plugin Informations */
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCP_DIR . '/' . basename( YITH_WCP_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );


            // Allows the selection of the 'composite product' type.
            add_filter( 'product_type_options', array( $this, 'add_composite_type_options' ) );

            /* Creates the admin panel tabs. */
            add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'composite_write_panel_tabs' ), 99 );

            // Creates the admin Components and Dependencies panels.
            add_action( 'woocommerce_product_data_panels', array( $this, 'composite_write_panel' ), 99 );

            add_action( 'yith_woocommerce_component_edit_admin_html', array( $this, 'composite_layout_options' ), 10, 3 );

            add_action( 'yith_woocommerce_component_edit_admin_html', array( $this, 'composite_list_options' ), 15, 3 );

            add_action( 'yith_woocommerce_dependencies_edit_admin_html', array( $this, 'dependencies_list_options' ), 15, 5 );

            // Allows the selection of the 'composite product' type.
            add_filter( 'product_type_selector', array( $this, 'add_composite_type' ) );

            // Processes and saves the necessary post metas from the selections made above.
            add_action( 'woocommerce_process_product_meta_yith-composite', array( $this, 'process_composite_meta' ) );

            // Ajax save composite config.
            add_action( 'wp_ajax_ywcp_components_save', array( $this, 'ajax_components_save' ) );

            // Ajax add component button.
            add_action( 'wp_ajax_ywcp_ajax_add_component', array( $this, 'ajax_add_component' ) );

            // Ajax add component button.
            add_action( 'wp_ajax_ywcp_ajax_load_components', array( $this, 'ajax_load_component_list' ) );

            // Ajax save composite config.
            add_action( 'wp_ajax_ywcp_dependencies_save', array( $this, 'ajax_dependencies_save' ) );

            // Ajax add dependencies button.
            add_action( 'wp_ajax_ywcp_ajax_add_dependence', array( $this, 'ajax_add_dependence' ) );

            // Ajax add component button.
            add_action( 'wp_ajax_ywcp_ajax_load_dependencies', array( $this, 'ajax_load_dependence_list' ) );

            // Copy components page
            add_action( 'admin_menu', array( $this, 'copy_components_page' ) );

            // Order Admin Table
            if ( get_option('yith_wcp_remove_composite_product_quantity_from_order_table') == 'yes' ) {
                add_filter( 'woocommerce_admin_order_item_count' , array( $this, 'change_order_item_number' ) , 10 , 2 );
            }
            add_filter( 'woocommerce_admin_html_order_item_class', array( $this, 'admin_html_order_item_class' ), 10, 3 );

            // YITH WCP Loaded
            do_action( 'yith_wcp_admin_loaded' );
        }

        /**
         * Init method:
         *  - default options
         *
         * @access public
         * @since 1.0.0
         */
        public function init() { }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = array(
                'general'       => __( 'General', 'yith-composite-products-for-woocommerce' ),
            );


            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => _x( 'Composite products', 'yith plugin title' , 'yith-composite-products-for-woocommerce' ),
                'menu_title'       => _x( 'Composite products', 'yith plugin title' , 'yith-composite-products-for-woocommerce' ),
                'capability'       => 'manage_options',
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'links'            => $this->get_panel_sidebar_links(),
                'admin-tabs'       => apply_filters( 'yith-wcp-admin-tabs', $admin_tabs ),
                'options-path'     => YITH_WCP_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( YITH_WAPO_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

        }

        /**
         * @return array
         */
        public function get_panel_sidebar_links() {
            return array(
                array(
                    'url' => $this->_official_documentation,
                    'title' => __( 'Plugin Documentation' , 'yith-composite-products-for-woocommerce' ),
                ),
                array(
                    'url' => 'https://yithemes.com/my-account/support/dashboard',
                    'title' => __( 'Support platform' , 'yith-composite-products-for-woocommerce' ),
                ),
                array(
                    'url' => $this->_official_documentation.'/changelog',
                    'title' => 'Changelog ( '.$this->_wcp_object->version.' )',
                )
            );
        }


        /**
         * Enqueue admin styles and scripts
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function enqueue_styles_scripts() {

            global $pagenow;

            if( apply_filters( 'ywcp_enable_admin_enqueue_style'  , (  $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'edit.php' ) , $pagenow ) ) {

                $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                // style.css

                wp_register_style( 'yith_wcp_admin', YITH_WCP_ASSETS_URL . '/css/yith-wcp-admin.css' , false, YITH_WCP_VERSION );

                wp_enqueue_style( 'yith_wcp_admin' );

                // JS

                wp_register_script( 'yith_wc_composite_writepanel', YITH_WCP_ASSETS_URL . '/js/yith-wcp-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'wc-admin-meta-boxes' ), YITH_WCP_VERSION );

                // Get admin screen id.
                $screen    = get_current_screen();
                $screen_id = $screen ? $screen->id : '';

                if ( in_array( $screen_id, array( 'product' ) ) ) {
                    wp_enqueue_script( 'yith_wc_composite_writepanel' );
                }

            }
        }

        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         * @return mixed
         * @use plugin_action_links_{$plugin_file_name}
         */
        public function action_links( $links ) {
            $premium_live_text =  __( 'Live demo', 'yith-composite-products-for-woocommerce' );
            $links[]           = '<a href="' . $this->_premium_live . '" target="_blank">' . $premium_live_text . '</a>';

            return $links;
        }

        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0
         * @author   Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         * @use plugin_row_meta
         */
        public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

            if ( ( defined( 'YITH_WCP_INIT' ) && YITH_WCP_INIT == $plugin_file )  ) {
                $plugin_meta[] = '<a href="' . $this->_official_documentation . '" target="_blank">' . __( 'Plugin documentation', 'yith-composite-products-for-woocommerce' ) . '</a>';
            }
            return $plugin_meta;
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_activation() {

            if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once( YITH_WCP_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
                require_once( YITH_WCP_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
            }

            YIT_Plugin_Licence()->register( YITH_WCP_INIT, YITH_WCP_SECRET_KEY, YITH_WCP_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_updates() {

            if( ! class_exists( 'YIT_Plugin_Licence' ) ){
                require_once( YITH_WCP_DIR . 'plugin-fw/lib/yit-upgrade.php' );
            }

            YIT_Upgrade()->register( YITH_WCP_SLUG, YITH_WCP_INIT );
        }

        /* ========= EDIT PRODUCT OPTION ======== */

        /**
         * Adds the 'composite product' type to the menu.
         *
         * @param  array 	$options
         * @return array
         */
        public function add_composite_type( $options ) {

            $options[ 'yith-composite' ] = _x( 'Composite product', 'dropdown admin edit title product type', 'yith-composite-products-for-woocommerce' );

            return $options;
        }


        /**
         * @param $options
         *
         * @return mixed
         */
        public function add_composite_type_options( $options ) {

            $options['ywcp_virtual'] = array(
                'id'            => '_ywcp_virtual',
                'wrapper_class' => 'show_if_yith-composite',
                'label'         => __( 'Virtual', 'yith-composite-products-for-woocommerce' ),
                'description'   => __( 'Virtual products are intangible and are not shipped.', 'yith-composite-products-for-woocommerce' ),
                'default'       => 'no'
            );
            $options['ywcp_downloadable'] = array(
                'id'            => '_ywcp_downloadable',
                'wrapper_class' => 'show_if_yith-composite',
                'label'         => __( 'Downloadable', 'yith-composite-products-for-woocommerce' ),
                'description'   => __( 'Downloadable products give access to a file upon purchase.', 'yith-composite-products-for-woocommerce' ),
                'default'       => 'no'
            );
            $options[ 'ywcp_options_product_per_item_shipping' ] = array(
                'id'            => '_ywcp_options_product_per_item_shipping',
                'wrapper_class' => 'show_if_yith-composite',
                'label'         => __( 'Per-Item Shipping', 'yith-composite-products-for-woocommerce' ),
                'description'   => __( 'If your Composite product consists of items that are assembled or packaged together, leave this box un-checked and define the shipping properties
of the entire Composite below. If, however, the contents of the Composite product  are shipped individually, check this option to retain their
original shipping weight and dimensions. <strong>Per-Item Shipping</strong> should also be checked if all composite items are virtual.', 'yith-composite-products-for-woocommerce' ),
                'default'       => 'no'
            );
            $options[ 'ywcp_options_product_per_item_pricing' ] = array(
                'id'            => '_ywcp_options_product_per_item_pricing',
                'wrapper_class' => 'show_if_yith-composite ywcp_per_item_pricing',
                'label'         => __( 'Per-Item Pricing', 'yith-composite-products-for-woocommerce' ),
                'description'   => __( 'When <strong>Per-Item Pricing</strong> is checked, the Composite product will be priced according to the cost of its contents. In this case the regular price will be a fixed cost added to the total price', 'yith-composite-products-for-woocommerce' ),
                'default'       => 'no'
            );

            return $options;
        }

        /**
         * Adds the Composite Product write panel tabs.
         *
         * @return string
         */
        public function composite_write_panel_tabs() {

            echo '<li class="ywcp_tab ywcp_tab_component linked_product_options show_if_yith-composite"><a href="#ywcp_tab_component"><span>'.__( 'Components', 'yith-composite-products-for-woocommerce' ).'</span></a></li>';
            echo '<li class="ywcp_tab ywcp_tab_dependecies linked_product_options show_if_yith-composite"><a href="#ywcp_tab_dependecies"><span>'.__( 'Dependecies', 'yith-composite-products-for-woocommerce' ).'</span></a></li>';
        }

        /**
         *
         */
        public function composite_write_panel() {

            global $post, $wpdb;

            // Components

            $wcp_data = get_post_meta( $post->ID, '_ywcp_component_data_list' );

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-component-options.php' );

            // Dependencies

            $wcp_data_dependencies = get_post_meta( $post->ID, '_ywcp_dependencies_data_list' );

            $wcp_data_dependencies_component_list_data = get_post_meta( $post->ID, '_ywcp_dependencies_component_data_options' );

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-dependencies-options.php' );

        }

        /**
         * @param $wcp_data
         * @param $post_id
         * @param $wpdb
         */
        public function composite_layout_options( $wcp_data, $post_id , $wpdb ) {

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-component-layouts-options.php' );

        }

        /**
         * @param $wcp_data
         * @param $post_id
         * @param $wpdb
         */
        public function composite_list_options(  $wcp_data, $post_id , $wpdb ) {

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-component-list-options.php' );

        }

        /**
         * @param $wcp_data
         * @param $wcp_data_dependencies
         * @param $post_id
         * @param $wpdb
         */
        public function dependencies_list_options( $wcp_data , $wcp_data_dependencies, $wcp_data_dependencies_component_list_data , $post_id , $wpdb ) {

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-dependencies-list-options.php' );

        }

        public function process_composite_meta( $post_id ) {

            $this->save_meta( $post_id );

            $this->save_components( $post_id, $_POST );

            $this->save_dependencies( $post_id, $_POST );

        }

        private function save_meta( $post_id ) {

            // Virtual.
            if ( isset( $_POST[ '_ywcp_virtual' ] ) ) {
                update_post_meta( $post_id, '_ywcp_virtual', 'yes' );
            } else {
                update_post_meta( $post_id, '_ywcp_virtual', 'no' );
            }

            // Downloadable.
            if ( isset( $_POST[ '_ywcp_downloadable' ] ) ) {
                update_post_meta( $post_id, '_ywcp_downloadable', 'yes' );
            } else {
                update_post_meta( $post_id, '_ywcp_downloadable', 'no' );
            }

            // Per-Item Pricing.
            if ( isset( $_POST[ '_ywcp_options_product_per_item_pricing' ] ) ) {
                update_post_meta( $post_id, '_ywcp_options_product_per_item_pricing', 'yes' );
            } else {
                update_post_meta( $post_id, '_ywcp_options_product_per_item_pricing', 'no' );
            }

            // Per-Item Shipping.
            if ( isset( $_POST[ '_ywcp_options_product_per_item_shipping' ] ) ) {
                update_post_meta( $post_id, '_ywcp_options_product_per_item_shipping', 'yes' );
            } else {
                update_post_meta( $post_id, '_ywcp_options_product_per_item_shipping', 'no' );
            }

        }

        /**
         * Handles saving composite config via ajax.
         *
         * @return void
         */
        public function ajax_components_save() {

            parse_str( $_POST[ 'data' ], $ywcp_post_data );

            $post_id = absint( $_POST[ 'post_id' ] );

            $this->save_components( $post_id, $ywcp_post_data );

            die;
        }

        /**
         * @param $post_id
         * @param $ywcp_post_data
         * @return bool
         */
        public function save_components( $post_id, $ywcp_post_data ) {

            // Layout Options

            $ywcp_layout_options = 'list';

            if ( isset( $ywcp_post_data[ '_ywcp_layout_options' ] ) ) {
                $ywcp_layout_options = stripslashes( $ywcp_post_data[ '_ywcp_layout_options' ] );
            }

            update_post_meta( $post_id, '_ywcp_layout_options', $ywcp_layout_options );

            $ywcp_layout_options_product_list_position = 'cascading';

            if ( isset( $ywcp_post_data[ '_ywcp_layout_options_product_list_position' ] ) ) {
                $ywcp_layout_options_product_list_position = stripslashes( $ywcp_post_data[ '_ywcp_layout_options_product_list_position' ] );
            }

            update_post_meta( $post_id, '_ywcp_layout_options_product_list_position', $ywcp_layout_options_product_list_position );

            // component options

            $ywcp_component_data_list = isset( $ywcp_post_data['ywcp_component_data'] ) ? $ywcp_post_data['ywcp_component_data'] : array();

            foreach ( $ywcp_component_data_list as $key => &$ywcp_component_data_item ) {
                $ywcp_component_data_item['apply_discount_to_sale_price']  = isset( $ywcp_component_data_item['apply_discount_to_sale_price'] ) ? true : false;
                $ywcp_component_data_item['thumb']                         = isset( $ywcp_component_data_item['thumb'] ) ? true : false;
                $ywcp_component_data_item['required']                      = isset( $ywcp_component_data_item['required'] ) ? true : false;
                $ywcp_component_data_item['sold_individually']             = isset( $ywcp_component_data_item['sold_individually'] ) ? true : false;
                $ywcp_component_data_item['exclusive']                     = isset( $ywcp_component_data_item['exclusive'] ) ? true : false;
                $ywcp_component_data_item['option_type_product_id_values'] = isset( $ywcp_component_data_item['option_type_product_id_values'] ) ? $ywcp_component_data_item['option_type_product_id_values'] : '';
                $ywcp_component_data_item['option_type_cat_id_values']     = isset( $ywcp_component_data_item['option_type_cat_id_values'] ) ? $ywcp_component_data_item['option_type_cat_id_values'] : '';
                $ywcp_component_data_item['option_type_tag_id_values']     = isset( $ywcp_component_data_item['option_type_tag_id_values'] ) ? $ywcp_component_data_item['option_type_tag_id_values'] : '';
                $ywcp_component_data_item['option_style']                  = empty( $ywcp_component_data_item['option_style'] ) ? 'dropdowns' : $ywcp_component_data_item['option_style'];
            }

            update_post_meta( $post_id, '_ywcp_component_data_list', $ywcp_component_data_list );

            return true;
        }

        /**
         *
         */
        public function ajax_add_component( ) {

            $post_id = absint( $_POST[ 'post_id' ] );

            $this->composite_list_options_sigle_item( '' , array() , $post_id  );

            die();
        }


        /**
         * @param $post_id
         * @param $wcp_data
         */
        public function load_component_list( $post_id , $wcp_data ) {

            if( isset( $wcp_data ) && $post_id > 0 ) {

                foreach ( $wcp_data as $key => $wcp_data_item ) {
                    if( ! empty( $wcp_data_item ) ) {
                        $this->composite_list_options_sigle_item( $key , $wcp_data_item, $post_id ) ;
                    }

                }

            }

        }

        /**
         *
         */
        public function ajax_load_component_list() {

            $post_id = $_REQUEST['post_id'];

            $wcp_data = get_post_meta( $post_id, '_ywcp_component_data_list' );

            $wcp_data = $wcp_data[0];

            $this->load_component_list( $post_id, $wcp_data );

            die;
        }

        /**
         * @param $wcp_data_single_item
         * @param $post_id
         */
        public function composite_list_options_sigle_item(  $wcp_data_key , $wcp_data_single_item, $post_id ) {

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-component-list-options-single-item.php' );

        }

        public function ajax_dependencies_save() {

            parse_str( $_POST[ 'data' ], $ywcp_post_data );

            $post_id = absint( $_POST[ 'post_id' ] );

            $this->save_dependencies( $post_id, $ywcp_post_data );

            die;
        }

        /**
         * @param $post_id
         * @param $ywcp_post_data
         *
         * @return bool
         */
        public function save_dependencies( $post_id, $ywcp_post_data ) {

            // dependece options

            $ywcp_dependencies_data_list = isset( $ywcp_post_data['ywcp_dependencies_data'] ) ? $ywcp_post_data['ywcp_dependencies_data'] : array();

            update_post_meta( $post_id, '_ywcp_dependencies_data_list', $ywcp_dependencies_data_list );

            // dependece components details

            $ywcp_dependencies_component_data_list = isset( $ywcp_post_data['ywcp_dependencies_component_data_options'] ) ? $ywcp_post_data['ywcp_dependencies_component_data_options'] : array();

            update_post_meta( $post_id, '_ywcp_dependencies_component_data_options', $ywcp_dependencies_component_data_list );

            return true;
        }

        /**
         *
         */
        public function ajax_add_dependence() {
            $post_id = absint( $_POST['post_id'] );
            $wcp_data = get_post_meta( $post_id, '_ywcp_component_data_list' );
            $wcp_data = isset( $wcp_data[0] ) ? $wcp_data[0] : '';
            $this->dependence_list_options_sigle_item( '', array(), $post_id, $wcp_data , array() );
            die();
        }

        /**
         * @param $post_id
         * @param $wcp_data_dependencies
         * @param $wcp_data
         * @param $wcp_data_dependencies_components_list_data
         */
        public function load_dependence_list( $post_id, $wcp_data_dependencies, $wcp_data, $wcp_data_dependencies_components_list_data ) {

            if ( isset( $wcp_data_dependencies ) && $post_id > 0 ) {

                foreach ( $wcp_data_dependencies as $key => $wcp_data_item ) {
                    if ( !empty( $wcp_data_item ) ) {
                        $this->dependence_list_options_sigle_item( $key, $wcp_data_item, $post_id, $wcp_data, $wcp_data_dependencies_components_list_data );
                    }

                }

            }

        }

        /**
         *
         */
        public function ajax_load_dependence_list() {

            $post_id = $_REQUEST['post_id'];

            // dependencies data

            $wcp_data_dependencies = get_post_meta( $post_id, '_ywcp_dependencies_data_list' );

            $wcp_data_dependencies = $wcp_data_dependencies[0];

            $wcp_data_dependencies_component_list_data = get_post_meta( $post_id, '_ywcp_dependencies_component_data_options' );

            $wcp_data_dependencies_component_list_data = $wcp_data_dependencies_component_list_data[0];

            // component data

            $wcp_data = get_post_meta( $post_id, '_ywcp_component_data_list' );

            $wcp_data = $wcp_data[0];

            // load data

            $this->load_dependence_list( $post_id, $wcp_data_dependencies , $wcp_data , $wcp_data_dependencies_component_list_data );

            die;
        }

        /**
         * @param $wcp_data_single_item
         * @param $post_id
         */
        public function dependence_list_options_sigle_item(  $wcp_data_key , $wcp_data_dependence_single_item , $post_id , $wcp_data , $wcp_data_dependencies_components_list_data ) {

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-dependencies-list-options-single-item.php' );

        }

        /**
         * @param $wcp_data_key
         * @param $wcp_data_single_item
         * @param $post_id
         * @param $item_index
         */
        public function dependence_list_options_sigle_item_components_item(  $wcp_data_key , $wcp_data_single_item , $post_id , $item_index , $wcp_data_dependencies_components_list_data_item ) {

            include( YITH_WCP_TEMPLATE_ADMIN_PATH . 'yith-wcp-edit-dependencies-list-options-single-item-component-item.php' );

        }

        /**
         * @param      $index
         * @param      $name
         * @param bool $multiple
         *
         * @return string
         */
        public static function getSettingsEditorName( $index , $base_name, $name , $multiple = false ) {

            return $base_name.'['.$index.']['.$name.']'.( $multiple ? '[]' : '' );

        }

        /**
         * @param        $index
         * @param        $name
         * @param        $list
         * @param        $value
         * @param string $class
         */
        public static function printSettingsDropdown( $index, $base_name , $name , $list , $value , $class='' ){

            echo '<select class="select short '.$class.'" name="'.YITH_WCP_Admin::getSettingsEditorName( $index , $base_name, $name ).'">';

            foreach( $list as $key => $item ) {
                echo '<option value="'.$key.'" '.selected( $value , $key , false ).'>'.$item.'</option>';
            }

            echo '</select>';

        }

        /**
         * @param int   $id
         * @param int   $tabs
         * @param array $categories_array
         */
        public static function echo_product_categories_childs_of( $id = 0, $tabs = 0, $categories_array = array() ) {

            if( ! is_array( $categories_array ) ) {
                $categories_array = array();
            }

            $categories = get_categories( array( 'taxonomy'=>'product_cat', 'parent'=>$id, 'orderby'=>'name', 'order'=>'ASC' ) );

            foreach ( $categories as $key => $value ) {
                echo '<option value="' . $value->slug . '" ' . ( in_array( $value->slug, $categories_array ) ? 'selected="selected"' : '' ) . '>' . str_repeat( '&#8212;', $tabs ) . ' ' . $value->name . '</option>';
                $childs = get_categories( array( 'taxonomy'=>'product_cat', 'parent'=>$value->term_id, 'orderby'=>'name', 'order'=>'ASC' ) );
                if ( count( $childs ) > 0 ) { self::echo_product_categories_childs_of( $value->term_id, $tabs + 1, $categories_array ); }
            }
        }

        /**
         * @param int   $tabs
         * @param array $tags_array
         */
        public static function echo_product_tags_childs_of( $tabs = 0, $tags_array = array() ) {

            if( ! is_array( $tags_array ) ) {
                $tags_array = array();
            }

            $tags = get_terms( array(
                'taxonomy' => 'product_tag',
                'hide_empty' => false,
            ) );

            foreach ( $tags as $key => $value ) {
                echo '<option value="' . $value->slug . '" ' . ( in_array( $value->slug, $tags_array ) ? 'selected="selected"' : '' ) . '>' . str_repeat( '&#8212;', $tabs ) . ' ' . $value->name . '</option>';
            }
        }

        /**
         * @param       $post_id
         * @param       $wcp_data_single_item
         * @param array $options_value
         */
        public static function echo_product_chosen_list( $post_id, $wcp_data_single_item, $options_value = array() ) {
            $args = YITH_WCP()->getProductsQueryArgs( $post_id, $wcp_data_single_item );
            $loop = new WP_Query( $args );
            if ( $loop->have_posts() ) {
                while ( $loop->have_posts() ) : $loop->the_post();
                    global $product;
                    if ( isset( $product ) && is_object( $product ) ) {
                        if ( ! $product->is_purchasable() ) { return; }
                        $post_id = yit_get_base_product_id( $product );
                        $title =  $product->get_title();
                        if ( $product->get_type() == 'variable' ) {
                            $title_all = $title . ': '. __( 'All variations', 'yith-composite-products-for-woocommerce' );
                            self::printSelectOptionValue( $post_id, $options_value, $title_all );
                            $variations = self::get_product_variations_chosen_list( $post_id );
                            foreach ( $variations as $variation_id ) {
                                $title_variation = $title . ': ' . self::get_product_variation_title( $variation_id );
                                self::printSelectOptionValue( $variation_id, $options_value, $title_variation );
                            }
                        } else { self::printSelectOptionValue( $post_id, $options_value, $title ); }
                    }
                endwhile;
            }
            wp_reset_postdata();
        }

        /**
         * @param $post_id
         * @param $options_value
         * @param $title
         */
        private static function printSelectOptionValue( $post_id , $options_value , $title ) {
            echo '<option value="' . $post_id. '" ' . ( in_array( $post_id, $options_value ) ? 'selected="selected"' : '' ) . '>' . '#' . $post_id . ' ' . $title . '</option>';
        }

        /**
         * @param $item_id
         *
         * @return array
         */
        private static function get_product_variations_chosen_list( $item_id ) {

            $variations = array();

            if( $item_id ) {

                $args = array(
                    'post_type'   => 'product_variation',
                    'post_status' => array( 'publish' ),
                    'numberposts' => -1,
                    'orderby'     => 'menu_order',
                    'order'       => 'asc',
                    'post_parent' => $item_id,
                    'fields'      => 'ids'
                );

                $variations = get_posts( $args );

            }

            return $variations;
        }

        /**
         * @param $variation_id
         *
         * @return bool
         */
        private static function get_product_variation_title( $variation_id ) {

            if ( is_object( $variation_id ) ) {
                $variation = $variation_id;
            } else {
                $variation = wc_get_product( $variation_id );
            }

            if ( ! $variation ) {
                return false;
            }

            return  $description = wc_get_formatted_variation( true );
        }

        public static function getComponentDependenceActionTypeList( ) {

            $list_option_type = array(
                'do'       => _x( 'DO', 'admin action type' , 'yith-composite-products-for-woocommerce' ),
                'if'       => _x( 'IF', 'admin action type' , 'yith-composite-products-for-woocommerce' ),
            );

            return $list_option_type;

        }

        /**
         * @param $required
         *
         * @return array
         */
        public static function getComponentDependenceSelectionTypeList( $required ) {

            $list_option_type = array(
                'do_nothing'       => _x( 'No action' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
                'not_selected'     => _x( 'No product is selected' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
                'is_selected'      => _x( 'Any product is selected' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
                'selection_is'     => _x( 'Selection is' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
                'selection_is_not' => _x( 'Selection is not' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
            );

            if ( $required ) {
                unset( $list_option_type['not_selected'] );
            }

            return $list_option_type;

        }

        /**
         * @param $required
         *
         * @return array
         */
        public static function getComponentDependenceDoTypeList( $required ) {

            $list_option_type = array(
                'do_nothing'       => _x( 'Nothing' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
                'selection_is'     => _x( 'Force selection to' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
                'selection_is_not' => _x( 'Exclude' , 'admin selection type' , 'yith-composite-products-for-woocommerce' ),
                'hided'            => _x( 'Hide Component' , 'admin selection type' , 'yith-composite-products-for-woocommerce' )
            );

            if ( $required ) {
                unset( $list_option_type['hided'] );
            }

            return $list_option_type;

        }

        /**
         * @return mixed
         */
        public static function getWooCommerceOrderOptions() {

            return apply_filters( 'woocommerce_default_catalog_orderby_options', array(
                'menu_order' => __( 'Default sorting (custom ordering + name)', 'yith-composite-products-for-woocommerce' ),
                'popularity' => __( 'Popularity (sales)', 'yith-composite-products-for-woocommerce' ),
                'rating'     => __( 'Average Rating', 'yith-composite-products-for-woocommerce' ),
                'date'       => __( 'Sort by date', 'yith-composite-products-for-woocommerce' ),
                'price'      => __( 'Sort by price', 'yith-composite-products-for-woocommerce' ),
            ) );

        }

        /**
         * @param $html
         * @param $the_order
         * @return mixed
         */
        public function change_order_item_number( $html , $the_order ) {
            $order_count = $the_order->get_item_count();
            $items = $the_order->get_items();
            foreach ( $items as $item ) {
                $is_composite_product = isset( $item['item_meta']['_yith_wcp_component_data'][0] );
                if ( $is_composite_product && isset( $item['item_meta']['_qty'][0] ) ) {
                    $qty = intval( $item['item_meta']['_qty'][0] );
                    $order_count -= $qty;
                }
            }
            $html = sprintf( _n( '%d item', '%d items', $order_count, 'yith-composite-products-for-woocommerce' ), $order_count );
            return $html;
        }

        /**
         * @return string
         */
        function admin_html_order_item_class( $class, $item, $order ) {
            if ( is_object( $item ) && ! empty( $item ) ) {
                if ( isset( $item->get_data()['meta_data'][0] ) && is_object( $item->get_data()['meta_data'][0] ) && ! empty( $item->get_data()['meta_data'][0] ) ) {
                    return $class . $item->get_data()['meta_data'][0]->get_data()['key'];
                }
            }
            return $class;
        }

        public function copy_components_page() {
            $page = add_submenu_page(
                null,
                __( 'Copy components', 'yith-woocommerce-product-add-ons' ),
                __( 'Copy components', 'yith-woocommerce-product-add-ons' ),
                'manage_woocommerce',
                'ywcp_copy_components',
                array( $this, 'copy_components' )
            );
        }

        public function copy_components() {
            load_template( YITH_WCP_DIR . '/templates/admin/yith-wcp-copy-components.php' );
        }

    }
}
