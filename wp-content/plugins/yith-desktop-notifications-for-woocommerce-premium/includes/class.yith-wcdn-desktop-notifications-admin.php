<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCDN_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCDN_Desktop_Notifications_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Admin' ) ) {
    /**
     * Class YITH_WCDN_Desktop_Notifications_Admin
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCDN_Desktop_Notifications_Admin {

        /**
         * @var Panel object
         */
        protected $_panel = null;


        /**
         * @var Panel page
         */
        protected $_panel_page = 'yith_wcdn_panel_desktop_notifications';

        /**
         * @var bool Show the premium landing page
         */
        public $show_premium_landing = true;

        /**
         * @var string Official plugin documentation
         */

        protected $_official_documentation = 'https://docs.yithemes.com/yith-desktop-notifications-for-woocommerce/';
        /**
         * @var string
         */
        protected $_premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-desktop-notifications/';

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Desktop_Notifications_Admin
         * @since 1.0.0
         */
        protected static $instance;

        
        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCDN_Desktop_Notifications_Admin
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
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct() {
            /* === Register Panel Settings === */
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            /* === Premium Tab === */
            add_action( 'yith_wcdn_premium_tab', array( $this, 'show_premium_landing' ) );

            // Enqueue Scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11);
            //Custom tab
            add_action( 'yith_wcdn_notifications_tab', array( $this, 'notifications_tab' ), 10, 2 );
            add_action( 'yith_wcdn_print_notifications', array( $this, 'load_notifications' ), 10 );

            /* === Show Plugin Information === */
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCDN_PATH . '/' . basename( YITH_WCDN_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = apply_filters( 'yith_wcdn_admin_tabs', array(
                    'settings'   => esc_html__( 'Settings', 'yith-desktop-notifications-for-woocommerce' ),
                    'notifications' => esc_html__( 'Notifications', 'yith-desktop-notifications-for-woocommerce' )
                )
            );

            if( $this->show_premium_landing ){
                $admin_tabs['premium'] = esc_html__( 'Premium Version', 'yith-desktop-notifications-for-woocommerce' );
            }

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => 'Desktop Notifications',
                'menu_title'       => 'Desktop Notifications',
                'capability'       => 'manage_options',
                'parent'           => 'yith-woocommerce-desktop-notifications',
                'parent_page'      => 'yith_plugin_panel',
                'class'            => yith_set_wrapper_class(),
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WCDN_OPTIONS_PATH,
                'links'            => $this->get_sidebar_link()
            );


            /* === Fixed: not updated theme/old plugin framework  === */
            if ( !class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }


            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

            add_action( 'woocommerce_admin_field_yith_desktop_notifications_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
        }


        /**
         * Show the premium landing
         *
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function show_premium_landing(){
            if( file_exists( YITH_WCDN_TEMPLATE_PATH . 'premium/premium.php' )&& $this->show_premium_landing ){
                require_once( YITH_WCDN_TEMPLATE_PATH . 'premium/premium.php' );
            }
        }

        /**
         * Sidebar links
         *
         * @return   array The links
         * @since    1.2.1
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_sidebar_link() {
            $links = array(
                array(
                    'title' => esc_html__( 'Plugin documentation', 'yith-desktop-notifications-for-woocommerce' ),
                    'url'   => $this->_official_documentation,
                ),
                array(
                    'title' => esc_html__( 'Help Center', 'yith-desktop-notifications-for-woocommerce' ),
                    'url'   => 'https://support.yithemes.com/hc/en-us/categories/202568518-Plugins',
                ),
                array(
                    'title' => esc_html__( 'Support platform', 'yith-desktop-notifications-for-woocommerce' ),
                    'url'   => 'https://yithemes.com/my-account/support/dashboard/',
                ),
                /*array(
                    'title' => sprintf( '%s (%s %s)', esc_html__( 'Changelog', 'yith-desktop-notifications-for-woocommerce' ), esc_html__( 'current version', 'yith-desktop-notifications-for-woocommerce' ), YITH_WCDN_VERSION ),
                    'url'   => 'https://yithemes.com/docs-plugins/yith-woocommerce-multi-step-checkout/07-changelog-premium.html',
                ),*/
            );

            return $links;
        }



        /**
         * Enqueue styles and scripts
         *
         * @access public
         * @return void
         * @since 1.0.0
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function enqueue_styles_scripts() {

            wp_register_style( 'yith_wcdn_backend', YITH_WCDN_ASSETS_URL . 'css/backend.css', YITH_WCDN_VERSION );
            wp_register_script( 'yith_wcdn_admin', YITH_WCDN_ASSETS_URL . 'js/wcdn-admin.js', array( 'jquery','jquery-ui-sortable','wc-enhanced-select' ), YITH_WCDN_VERSION, true );


            wp_localize_script( 'yith_wcdn_admin', 'yith_wcdn_admin', apply_filters( 'yith_wcdn_admin_localize',array(
                'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
            )));

            if ( is_admin() ){
                wp_enqueue_style('yith_wcdn_backend');
                wp_enqueue_script('yith_wcdn_admin');
            }

        }

        /**
         * Print notifications table
         *
         * @access public
         * @param array $options
         * @return void
         * @since 1.0.0
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */

        public function notifications_tab(){
            if( isset( $_GET['page'] ) && $_GET['page'] == $this->_panel_page
                && file_exists( YITH_WCDN_TEMPLATE_PATH . 'admin/notifications-panel.php' ) ) {
                $type = $_GET['tab'];
                include_once( YITH_WCDN_TEMPLATE_PATH . 'admin/notifications-panel.php' );
            }
        }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri()
        {
            return defined('YITH_REFER_ID') ? $this->_premium_landing_url . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing_url.'?refer_id=1030585';
        }

        /**
         * @param $type
         */
        public function load_notifications( $type ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] == $this->_panel_page
                && file_exists( YITH_WCDN_TEMPLATE_PATH . 'admin/'.$type.'-load-options.php' ) ) {
                $db_value = get_option('yith-wcdn-desktop-notifications-free');
                include_once( YITH_WCDN_TEMPLATE_PATH . 'admin/'.$type.'-load-options.php' );
            }
        }

        /**
         * Action links
         *
         *
         * @return void
         * @since    1.2.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, false );
            return $links;
        }
        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.2.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCDN_FREE_INIT' ) {
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_WCDN_SLUG;
            }

            return $new_row_meta_args;
        }
    }
}