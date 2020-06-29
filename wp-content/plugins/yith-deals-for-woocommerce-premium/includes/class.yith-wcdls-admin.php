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
 * @class      YITH_Deals_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_Deals_Admin' ) ) {
    /**
     * Class YITH_Deals_Admin
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Deals_Admin
    {

        /**
         * @var Panel object
         */
        protected $_panel = null;


        /**
         * @var Panel page
         */
        protected $_panel_page = 'yith_wcdls_panel_product_deals';

        /**
         * @var bool Show the premium landing page
         */
        public $show_premium_landing = true;

        /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = 'http://docs.yithemes.com/yith-deals-for-woocommerce/';

        /**
         * @var string
         */
        protected $_premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-deals/';

        /**
         * Single instance of the class
         *
         * @var \YITH_Deals_Admin
         * @since 1.0.0
         */
        protected static $instance;


        public $product_meta_array = array();

        /**
         * Returns single instance of the class
         *
         * @return \YITH_Deals_Admin
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
        public function __construct()
        {
            /* === Register Panel Settings === */
            add_action('admin_menu', array($this, 'register_panel'), 5);

            add_action( 'yith_wcdls_deals_tab', array( $this, 'show_deals_tab' ) );
            add_action( 'yith_wcdls_premium_tab', array( $this, 'show_premium_landing' ) );
            
            // Enqueue Scripts
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));


            //Custom tinymce button
            add_action('admin_head', array( $this, 'tc_button' ) );

            /* === Show Plugin Information === */
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCDLS_PATH . '/' . basename( YITH_WCDLS_FILE ) ), array( $this, 'action_links' ) );
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
        public function register_panel()
        {

            if (!empty($this->_panel)) {
                return;
            }

            $admin_tabs = apply_filters('yith_wcdls_admin_tabs', array(
                    'settings' => esc_html__('Settings', 'yith-deals-for-woocommerce'),
                    'deals' => esc_html__('Deals','yith-deals-for-woocommerce'),
                )
            );

            if( $this->show_premium_landing ){
                $admin_tabs['premium'] = esc_html__( 'Premium Version', 'yith-deals-for-woocommerce' );
            }

	        $capability = get_option('yith_wcdls_settings_tab_allow_shop_manager') != 'no' ? 'manage_woocommerce' : 'manage_options';


	        $args = array(
                'create_menu_page' => true,
                'parent_slug' => '',
                'page_title' => 'Deals',
                'menu_title' => 'Deals',
                'capability' => $capability,
                'parent' => '',
                'parent_page' => 'yith_plugin_panel',
                'page' => $this->_panel_page,
                'admin-tabs' => $admin_tabs,
                'options-path' => YITH_WCDLS_OPTIONS_PATH,
                'links' => $this->get_sidebar_link(),
                'class'            => yith_set_wrapper_class(),
            );


            /* === Fixed: not updated theme/old plugin framework  === */
            if (!class_exists('YIT_Plugin_Panel_WooCommerce')) {
                require_once('plugin-fw/lib/yit-plugin-panel-wc.php');
            }


            $this->_panel = new YIT_Plugin_Panel_WooCommerce($args);

            add_action('woocommerce_admin_field_yith_deals_upload', array($this->_panel, 'yit_upload'), 10, 1);
        }

        /**
         * Show the premium landing
         *
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function show_premium_landing(){
            if( file_exists( YITH_WCDLS_TEMPLATE_PATH . 'premium/premium.php' )&& $this->show_premium_landing ){
                require_once( YITH_WCDLS_TEMPLATE_PATH . 'premium/premium.php' );
            }
        }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri()
        {
            return defined('YITH_REFER_ID') ? $this->_premium_landing_url . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing_url.'?refer_id=1030585';
        }

        /**
         * Sidebar links
         *
         * @return   array The links
         * @since    1.2.1
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_sidebar_link()
        {
            $links = array(
                array(
                    'title' => esc_html__('Plugin documentation', 'yith-deals-for-woocommerce'),
                    'url' => $this->_official_documentation,
                ),
                array(
                    'title' => esc_html__('Help Center', 'yith-deals-for-woocommerce'),
                    'url' => 'http://support.yithemes.com/hc/en-us/categories/202568518-Plugins',
                ),
                array(
                    'title' => esc_html__('Support platform', 'yith-deals-for-woocommerce'),
                    'url' => 'https://yithemes.com/my-account/support/dashboard/',
                ),
                array(
                    'title' => sprintf('%s (%s %s)', esc_html__('Changelog', 'yith-deals-for-woocommerce'), esc_html__('current version', 'yith-deals-for-woocommerce'), YITH_WCDLS_VERSION),
                    'url' => 'https://yithemes.com/docs-plugins/yith-woocommerce-deals/changelog',
                ),
            );

            return $links;
        }


        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Admin
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */

        public function enqueue_scripts()
        {
            global $post;

            wp_register_style('yith_wcdls_admincss', YITH_WCDLS_ASSETS_URL . 'css/wcdls-admin.css', YITH_WCDLS_VERSION);
            if ( is_admin() && ( is_page('yith_wcdls_panel_product_deals') || isset( $post ) && 'yith_wcdls_offer' == $post->post_type ) ) {
                wp_enqueue_style('yith_wcdls_admincss');
            }
            do_action('yith_wcdls_enqueue_scripts');
        }

        /**
         * Show the deals tab
         *
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function show_deals_tab() {

            if( file_exists( YITH_WCDLS_TEMPLATE_PATH . 'admin/wcdls-deals-tab.php' ) ){
                wc_get_template('admin/wcdls-deals-tab.php', array(), '', YITH_WCDLS_TEMPLATE_PATH);
            }
        }

        /**
         * Add a new button to tinymce
         *
         * @return   void
         * @since    1.0
         * @author   Emanuela Castorina
         */
        public function tc_button() {

            if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
                return;
            }

            $post_type = '';

            if( isset( $_GET['post'] ) ){
                $post_type = get_post_type( $_GET['post']);
            }elseif( isset( $_GET['post_type'] )  ){
                $post_type = $_GET['post_type'];
            }

            if( $post_type != YITH_WCDLS_Offer()->post_type_name ){
                return;
            }

            if ( get_user_option( 'rich_editing' ) == 'true' ) {
                add_filter( "mce_external_plugins", array( $this, 'add_tinymce_plugin' ) );
                add_filter( "mce_buttons", array( $this, 'register_tc_button' ) );
                add_filter( 'mce_external_languages', array( $this, 'add_tc_button_lang' ) );
            }
        }


        /**
         * Add plugin button to tinymce from filter mce_external_plugins
         *
         * @return   void
         * @since    1.0
         * @author   Emanuela Castorina
         */
        function add_tinymce_plugin( $plugin_array ) {
            $plugin_array['yith_wcdls_button'] = YITH_WCDLS_ASSETS_URL . '/js/tinymce/text-editor.js';
            return $plugin_array;
        }

        /**
         * Register the custom button to tinymce from filter mce_buttons
         *
         * @return   void
         * @since    1.0
         * @author   Emanuela Castorina
         */
        function register_tc_button( $buttons ) {
            array_push( $buttons, "yith_wcdls_button" );
            return $buttons;
        }

        /**
         * Add multilingual to mce button from filter mce_external_languages
         *
         * @return   void
         * @since    1.0
         * @author   Emanuela Castorina
         */
        function add_tc_button_lang( $locales ) {
            $locales ['tc_button'] = YITH_WCDLS_PATH . 'includes/tinymce/tinymce-plugin-langs.php';
            return $locales;
        }

        /**
         * Action links
         *
         *
         * @return void
         * @since    1.0.3
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
         * @since    1.0.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCDLS_FREE_INIT' ) {
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_WCDLS_SLUG;
            }

            return $new_row_meta_args;
        }

    }
}

