<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWGC_Plugin_FW_Loader' ) ) {

    /**
     *
     * @class   YWGC_Plugin_FW_Loader
     *
     * @since   1.0.0
     * @author  Lorenzo Giuffrida
     */
    class YWGC_Plugin_FW_Loader {

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        /**
         * @var $_premium string Premium tab template file name
         */
        protected $_premium = 'premium.php';

        /**
         * @var string the YITH plugin stats page
         */
        protected $_status_page = 'status.php';

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin official documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin panel page
         */
        protected $_panel_page = 'yith_woocommerce_gift_cards_panel';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-gift-cards/';

        /**
         * @var string Official plugin support page
         */
        protected $_support = 'https://yithemes.com/my-account/support/dashboard/';

        /**
         * Single instance of the class
         *
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct() {

            $this->plugin_fw_loader();
            /**
             * Register actions and filters to be used for creating an entry on YIT Plugin menu
             */
            add_action( 'admin_init', array( $this, 'register_pointer' ) );

            //  Add stylesheets and scripts files
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            //  Show plugin premium tab
            add_action( 'yith_gift_cards_tab_premium', array( $this, 'premium_tab' ) );
            /**
             * register plugin to licence/update system
             */
            $this->licence_activation();

	        add_action( 'woocommerce_admin_field_update-cron', array( $this, 'show_update_cron_field' ) );


        }

        /**
         * Load YIT core plugin
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( ! empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

	    /**
	     * show the custom woocommerce field
	     * @since 3.1.15
	     *
	     * @param array $option
	     */
	    public function show_update_cron_field( $option ) {

		    $option['option'] = $option;

		    wc_get_template( '/admin/update-cron.php', $option, '', YITH_YWGC_TEMPLATES_DIR );
	    }


	    /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs['gift-cards'] = esc_html__( 'Dashboard', 'yith-woocommerce-gift-cards' );
            $admin_tabs['general']  = esc_html__( 'General', 'yith-woocommerce-gift-cards' );
            $admin_tabs['design'] = esc_html__( 'Style', 'yith-woocommerce-gift-cards' );
            $admin_tabs['gift-cards-category'] = esc_html__( 'Image categories', 'yith-woocommerce-gift-cards' );
            $admin_tabs['gift_this_product'] = esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' );
            $admin_tabs['recipient_delivery'] = esc_html__( 'Recipient & delivery', 'yith-woocommerce-gift-cards' );
            $admin_tabs['cart_checkout'] = esc_html__( 'Cart & checkout', 'yith-woocommerce-gift-cards' );
//            $admin_tabs['gift-cards-products'] = esc_html__( 'Products', 'yith-woocommerce-gift-cards' );

            if ( ! defined( 'YITH_YWGC_PREMIUM' ) ) {
                $admin_tabs['premium-landing'] = esc_html__( 'Premium version', 'yith-woocommerce-gift-cards' );
            }

            $capability = get_option( 'ywgc_allow_shop_manager', 'no' ) == "yes" ? 'manage_woocommerce' : apply_filters('yith_wcgc_plugin_settings_capability', 'manage_options');

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => 'Gift Cards',
                'menu_title'       => 'Gift Cards',
                'capability'       => $capability,
                'parent'           => '',
                'class'            => yith_set_wrapper_class(),
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => apply_filters('yith_wcgc_admin_tabs_control', $admin_tabs),
                'options-path'     => YITH_YWGC_DIR . 'plugin-options',
            );

            /* === Fixed: not updated theme  === */
            if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {

                require_once( YITH_YWGC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

        }

        /**
         * Premium Tab Template
         *
         * Load the premium tab template on admin page
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         */
        public function premium_tab() {
            $premium_tab_template = YITH_YWGC_TEMPLATES_DIR . 'admin/' . $this->_premium;
            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }

        public function register_pointer() {

            $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

            if ( $is_ajax ){
                return;
            }

            if ( ! class_exists( 'YIT_Pointers' ) ) {
                include_once( 'plugin-fw/lib/yit-pointers.php' );
            }

            $premium_message = defined( 'YITH_YWGC_PREMIUM' )
                ? ''
                : esc_html__( 'YITH WooCommerce Gift Cards is available in an outstanding premium version with many new options, discover it now.', 'yith-woocommerce-gift-cards' ) .
                ' <a href="' . $this->get_premium_landing_uri() . '">' . esc_html__( 'Premium version', 'yith-woocommerce-gift-cards' ) . '</a>';

            $args[] = array(
                'screen_id'  => 'plugins',
                'pointer_id' => 'yith_woocommerce_gift_cards',
                'target'     => '#toplevel_page_yit_plugin_panel',
                'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
                    esc_html__( 'YITH WooCommerce Gift Cards', 'yith-woocommerce-gift-cards' ),
                    esc_html__( 'In the YITH Plugins tab you can find YITH WooCommerce Gift Cards options.<br> From this menu you can access all the settings of your active YITH plugins.', 'yith-woocommerce-gift-cards' ) . '<br>' . $premium_message
                ),
                'position'   => array( 'edge' => 'left', 'align' => 'center' ),
                'init'       => defined( 'YITH_YWGC_PREMIUM' ) ? YITH_YWGC_INIT : YITH_YWGC_FREE_INIT,
            );

            YIT_Pointers()->register( $args );
        }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri() {
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
        }

        //region    ****    licence related methods ****

        /**
         * Add actions to manage licence activation and updates
         */
        public function licence_activation() {
            if ( ! defined( 'YITH_YWGC_PREMIUM' ) ) {
                return;
            }

            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation() {

            if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once YITH_YWGC_DIR . '/plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_YWGC_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
            }

            YIT_Plugin_Licence()->register( YITH_YWGC_INIT, YITH_YWGC_SECRET_KEY, YITH_YWGC_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates() {
            if ( ! class_exists( 'YIT_Upgrade' ) ) {
                require_once YITH_YWGC_DIR . '/plugin-fw/lib/yit-upgrade.php';
            }
            YIT_Upgrade()->register( YITH_YWGC_SLUG, YITH_YWGC_INIT );
        }
        //endregion
    }
}
YWGC_Plugin_FW_Loader::get_instance();
