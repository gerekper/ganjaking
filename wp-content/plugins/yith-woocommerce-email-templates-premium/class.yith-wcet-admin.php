<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Email Templates
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCET' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCET_Admin' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCET_Admin {

        /**
         * Single instance of the class
         *
         * @var YITH_WCET_Admin
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * @var $_panel Object
         */
        protected $_panel;

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-email-templates/';

        /**
         * @var string Quick View panel page
         */
        protected $_panel_page = 'yith_wcet_panel';

        /**
         * @var string Doc url
         */
        public $doc_url = 'http://yithemes.com/docs-plugins/yith-woocommerce-email-templates/';

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCET_Admin | YITH_WCET_Admin_Premium
         * @since 1.2.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.2.0
         */
        protected function __construct() {

            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            //Add action links
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCET_DIR . '/' . basename( YITH_WCET_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

            add_action( 'init', array( $this, 'post_type_register' ) );
            add_action( 'save_post', array( $this, 'metabox_save' ) );

            add_filter( 'woocommerce_email_settings', array( $this, 'email_extra_settings' ) );
            add_filter( 'yith_wcet_panel_settings_options', array( $this, 'add_email_extra_settings_in_tab_settings' ) );

            // Premium Tabs
            add_action( 'yith_wcet_premium_tab', array( $this, 'show_premium_tab' ) );
        }

        /**
         * This function copy the mail extra settings in the plugin settings tab
         *
         * @access public
         * @since  1.0.0
         */
        public function add_email_extra_settings_in_tab_settings( $settings ) {
            $settings[ 'settings' ]   = $this->email_extra_settings( $settings[ 'settings' ] );
            $settings[ 'settings' ][] = array(
                'type' => 'sectionend',
                'id'   => 'yith-wcet-email-extra-settings'
            );

            return $settings;
        }

        /**
         * Add Email extra settings in woocommerce email settings
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function email_extra_settings( $settings ) {
            $templates_array = array(
                'default' => __( 'Default', 'yith-woocommerce-email-templates' )
            );

            $args      = array(
                'posts_per_page' => -1,
                'post_type'      => 'yith-wcet-etemplate',
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post_status'    => 'publish',
                'fields'         => 'ids'
            );
            $templates = get_posts( $args );
            foreach ( $templates as $template_id ) {
                $templates_array[ $template_id ] = get_the_title( $template_id );
            }

            $settings[] = array(
                'title' => __( 'YITH WooCommerce Email Settings', 'yith-woocommerce-email-templates' ),
                'type'  => 'title',
                'desc'  => __( 'Select templates for email', 'yith-woocommerce-email-templates' ),
                'id'    => 'yith_wcet_email_extra_settings'
            );

            $settings[] = array(
                'id'       => 'yith-wcet-email-template',
                'name'     => __( 'Email Template', 'yith-woocommerce-email-templates' ),
                'type'     => 'select',
                'desc_tip' => __( 'Select the email template that you want to use for your emails!', 'yith-woocommerce-email-templates' ),
                'class'    => 'email_type wc-enhanced-select',
                'options'  => $templates_array,
                'default'  => 'default'
            );

            $settings[] = array(
                'type' => 'sectionend',
                'id'   => 'yith_wcet_email_extra_settings'
            );

            return $settings;
        }

        /**
         * Register Email Template custom post type with options metabox
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function post_type_register() {
            $labels = array(
                'name'               => __( 'Email Templates', 'yith-woocommerce-email-templates' ),
                'singular_name'      => __( 'Email Template', 'yith-woocommerce-email-templates' ),
                'add_new'            => __( 'Add Email Template', 'yith-woocommerce-email-templates' ),
                'add_new_item'       => __( 'Add New Email Template', 'yith-woocommerce-email-templates' ),
                'edit_item'          => __( 'Edit Email Template', 'yith-woocommerce-email-templates' ),
                'view_item'          => __( 'View Email Template', 'yith-woocommerce-email-templates' ),
                'not_found'          => __( 'Email template not found', 'yith-woocommerce-email-templates' ),
                'not_found_in_trash' => __( 'Email template not found in trash', 'yith-woocommerce-email-templates' )
            );

            $args = array(
                'labels'               => $labels,
                'public'               => false,
                'show_ui'              => true,
                'menu_position'        => 10,
                'exclude_from_search'  => true,
                'capability_type'      => 'post',
                'map_meta_cap'         => true,
                'rewrite'              => true,
                'has_archive'          => true,
                'hierarchical'         => false,
                'show_in_nav_menus'    => false,
                'menu_icon'            => 'dashicons-email-alt',
                'supports'             => array( 'title' ),
                'register_meta_box_cb' => array( $this, 'register_metabox' )
            );

            register_post_type( 'yith-wcet-etemplate', $args );
        }

        /**
         * register Email Template metabox
         *
         * @return void
         */
        public function register_metabox() {
            add_meta_box( 'yith-wcet-metabox', __( 'Template Options', 'yith-woocommerce-email-templates' ), array( $this, 'metabox_render' ), 'yith-wcet-etemplate', 'normal', 'high' );
        }

        /**
         * render Email Template metabox
         *
         * @param $post WP_Post
         * @return void
         */
        public function metabox_render( $post ) {

            $meta = get_post_meta( $post->ID, '_template_meta', true );

            $default = array(
                'txt_color_default'  => '#000000',
                'txt_color'          => '#000000',
                'bg_color_default'   => '#F5F5F5',
                'bg_color'           => '#F5F5F5',
                'base_color_default' => '#2470FF',
                'base_color'         => '#2470FF',
                'body_color_default' => '#FFFFFF',
                'body_color'         => '#FFFFFF',
                'logo_url'           => '',
                'custom_logo_url'    => get_option( 'yith-wcet-custom-default-header-logo' ),
                'page_width'         => '800',
            );

            $args = wp_parse_args( $meta, $default );

            $args = apply_filters( 'yith_wcet_metabox_options_content_args', $args );

            yith_wcet_metabox_options_content( $args );
        }

        /**
         * metabox save
         *
         * @param $post_id
         */
        public function metabox_save( $post_id ) {
            if ( !empty( $_POST[ '_template_meta' ] ) ) {
                $meta[ 'txt_color' ]  = ( !empty( $_POST[ '_template_meta' ][ 'txt_color' ] ) ) ? $_POST[ '_template_meta' ][ 'txt_color' ] : '';
                $meta[ 'bg_color' ]   = ( !empty( $_POST[ '_template_meta' ][ 'bg_color' ] ) ) ? $_POST[ '_template_meta' ][ 'bg_color' ] : '';
                $meta[ 'base_color' ] = ( !empty( $_POST[ '_template_meta' ][ 'base_color' ] ) ) ? $_POST[ '_template_meta' ][ 'base_color' ] : '';
                $meta[ 'body_color' ] = ( !empty( $_POST[ '_template_meta' ][ 'body_color' ] ) ) ? $_POST[ '_template_meta' ][ 'body_color' ] : '';
                $meta[ 'logo_url' ]   = ( !empty( $_POST[ '_template_meta' ][ 'logo_url' ] ) ) ? $_POST[ '_template_meta' ][ 'logo_url' ] : '';
                $meta[ 'page_width' ] = ( !empty( $_POST[ '_template_meta' ][ 'page_width' ] ) ) ? $_POST[ '_template_meta' ][ 'page_width' ] : '800';
                update_post_meta( $post_id, '_template_meta', $meta );
            }
        }

        /**
         * Action Links
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         * @return   mixed Array
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @since    1.0
         */
        public function action_links( $links ) {
            return yith_add_action_links( $links, $this->_panel_page, defined( 'YITH_WCET_PREMIUM' ) );
        }

        /**
         * plugin_row_meta
         * add the action links to plugin admin page
         *
         * @param $row_meta_args
         * @param $plugin_meta
         * @param $plugin_file
         * @return   array
         * @since    1.0
         * @use      plugin_row_meta
         */
        public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
            $init = defined( 'YITH_WCET_FREE_INIT' ) ? YITH_WCET_FREE_INIT : YITH_WCET_INIT;

            if ( $init === $plugin_file ) {
                $row_meta_args[ 'slug' ]       = YITH_WCET_SLUG;
                $row_meta_args[ 'is_premium' ] = defined( 'YITH_WCET_PREMIUM' );
            }

            return $row_meta_args;
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs_free = array(
                'settings' => __( 'Settings', 'yith-woocommerce-email-templates' ),
                'premium'  => __( 'Premium Version', 'yith-woocommerce-email-templates' )
            );

            $admin_tabs = apply_filters( 'yith_wcet_settings_admin_tabs', $admin_tabs_free );

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'class'            => yith_set_wrapper_class(),
                'page_title'       => 'WooCommerce Email Templates',
                'menu_title'       => 'Email Templates',
                'capability'       => 'manage_options',
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WCET_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if ( !class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

            add_action( 'woocommerce_admin_field_yith_wcet_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
        }

        public function admin_enqueue_scripts() {
            wp_register_style( 'yith-wcet-admin-styles', YITH_WCET_ASSETS_URL . '/css/admin.css', array(), YITH_WCET_VERSION );

            $screen     = get_current_screen();
            $metabox_js = defined( 'YITH_WCET_PREMIUM' ) ? 'metabox_options_premium.js' : 'metabox_options.js';

            if ( defined( 'YITH_WCET_PREMIUM' ) ) {
                wp_register_style( 'yith-wcet-popup', YITH_WCET_ASSETS_URL . '/css/yith-wcet-popup.css', array(), YITH_WCET_VERSION );
                wp_register_script( 'yith-wcet-popup', YITH_WCET_ASSETS_URL . '/js/yith-wcet-popup.js', array( 'jquery', 'jquery-blockui' ), YITH_WCET_VERSION );

            }

            if ( 'yith-wcet-etemplate' == $screen->id ) {
                wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
                wp_enqueue_style( 'yith-wcet-admin-styles' );
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker' );
                wp_enqueue_script( 'jquery-ui-tabs' );
                wp_enqueue_style( 'jquery-ui-style-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css' );

                $metabox_options_depts = array( 'jquery', 'wp-color-picker' );
                $metabox_options_depts = !defined( 'YITH_WCET_PREMIUM' ) ? $metabox_options_depts : array_merge( $metabox_options_depts, array( 'jquery-blockui', 'codemirror', 'yith-wcet-popup' ) );

                wp_enqueue_script( 'yith_wcet_metabox_options', YITH_WCET_ASSETS_URL . '/js/' . $metabox_js, $metabox_options_depts, YITH_WCET_VERSION, true );
                wp_localize_script( 'yith_wcet_metabox_options', 'ajax_object', array( 'assets_url' => YITH_WCET_ASSETS_URL, 'wp_ajax_url' => admin_url( 'admin-ajax.php' ) ) );

                if ( defined( 'YITH_WCET_PREMIUM' ) ) {
                    wp_enqueue_style( 'yith-wcet-popup' );
                }
            }
            wp_register_script( 'yith-wcet-admin-js', YITH_WCET_ASSETS_URL . '/js/admin.js', array( 'select2' ), YITH_WCET_VERSION, true );

            if ( strpos( $screen->id, 'page_yith_wcet_panel' ) > 0 || strpos( $screen->id, '_page_wc-settings' ) ) {
                wp_enqueue_style( 'yith-wcet-admin-styles' );
                wp_enqueue_script( 'yith-wcet-admin-js' );
            }
        }

        /**
         * Show premium landing tab
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_premium_tab() {
            $landing = YITH_WCET_TEMPLATE_PATH . '/premium.php';
            file_exists( $landing ) && require( $landing );
        }

        /**
         * Get the premium landing uri
         *
         * @return  string The premium landing link
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @since   1.0.0
         */
        public function get_premium_landing_uri() {
            return $this->_premium_landing;
        }
    }
}

/**
 * Unique access to instance of YITH_WCET_Admin class
 *
 * @return YITH_WCET_Admin || YITH_WCET_Admin_Premium
 * @since                   1.2.0
 */
function YITH_WCET_Admin() {
    return YITH_WCET_Admin::get_instance();
}
