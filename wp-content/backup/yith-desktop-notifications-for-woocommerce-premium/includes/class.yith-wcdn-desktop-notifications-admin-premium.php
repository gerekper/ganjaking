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

if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Admin_Premium' ) ) {
    /**
     * Class YITH_WCDN_Desktop_Notifications_Admin_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCDN_Desktop_Notifications_Admin_Premium extends YITH_WCDN_Desktop_Notifications_Admin {

        /**
         * @var Panel object
         */
        protected $_panel = null;


        /**
         * @var Panel page
         */
        protected $_panel_page = 'yith_wcdn_panel_desktop_notifications';

        /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = 'https://yithemes.com/docs-plugins/yith-woocommerce-multi-step-checkout/';

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Desktop_Notifications_Admin_Premium
         * @since 1.0.0
         */
        protected static $instance;

        
        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct() {
            /* === Register Panel Settings === */
            $this->show_premium_landing = false;
            add_filter('yith_wcdn_admin_tabs', array( $this, 'register_admin_tabs' ));

            /* Register plugin to licence/update system */
            add_action('wp_loaded', array($this, 'register_plugin_for_activation'), 99);
            add_action('admin_init', array($this, 'register_plugin_for_updates'));

            //custom tab
            add_action( 'yith_wcdn_upload_tab', array( $this, 'upload_tab' ),10,2);



            parent:: __construct();
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
        public function register_admin_tabs($admin_tabs) {

            $admin_tabs['upload'] =  esc_html__('Upload','yith-desktop-notifications-for-woocommerce');


            return $admin_tabs;
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
                /*array(
                    'title' => esc_html__( 'Plugin documentation', 'yith-desktop-notifications-for-woocommerce' ),
                    'url'   => $this->_official_documentation,
                ),*/
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
       
            wp_register_style( 'yith_wcdn_backend_premium', YITH_WCDN_ASSETS_URL . 'css/backend-premium.css', YITH_WCDN_VERSION );
            wp_register_script( 'yith_wcdn_admin_premium', YITH_WCDN_ASSETS_URL . 'js/wcdn-admin-premium.js', array( 'jquery','jquery-ui-sortable','wc-enhanced-select' ), YITH_WCDN_VERSION, true );
            wp_register_script( 'yith_wcdn_media_lib', YITH_WCDN_ASSETS_URL . 'js/wcdn-media-lib-uploader.js', array( 'jquery'), YITH_WCDN_VERSION, true );



            wp_localize_script( 'yith_wcdn_admin_premium', 'yith_wcdn_admin', apply_filters( 'yith_wcdn_admin_localize',array(
                'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
                'vendor_active'           => !!defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM,
            )));

            if ( is_admin() ){
                wp_enqueue_style('yith_wcdn_backend_premium');
                wp_enqueue_script('yith_wcdn_admin_premium');
                wp_enqueue_script('yith_wcdn_media_lib');
            }

        }

        /**
         * Print upload table
         *
         * @access public
         * @param array $options
         * @return void
         * @since 1.0.0
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function upload_tab(){
            if( isset( $_GET['page'] ) && $_GET['page'] == $this->_panel_page
                && file_exists( YITH_WCDN_TEMPLATE_PATH . 'admin/upload-panel.php' ) ) {
                $type = $_GET['tab'];
                include_once( YITH_WCDN_TEMPLATE_PATH . 'admin/upload-panel.php' );
            }
        }

        /**
         * @param $type
         */
        public function load_notifications( $type ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] == $this->_panel_page
                && file_exists( YITH_WCDN_TEMPLATE_PATH . 'admin/'.$type.'-load-options-premium.php' ) ) {
            ?>
                <p id="yith_button_new_notification">
                    <a href="" id="yith-wcdn-add-section-button" class="button-secondary" data-section_id="yit_wcdn_options_<?php echo $type ?>-rules" data-action="yit_wcdn_add_section" data-type="<?php echo $type ?>" data-section_name="yit_wcdn_options[<?php echo $type ?>-rules]"><?php _e( 'Add new notification', 'yith-desktop-notifications-for-woocommerce' ) ?></a>
                    <span class="yith-wcdn-error-input-section"></span>
                </p>
            <?php
                $db_value = get_option('yith-wcdn-desktop-notifications');
                include_once( YITH_WCDN_TEMPLATE_PATH . 'admin/'.$type.'-load-options-premium.php' );
            }
        }


        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    1.0.0
         * @author   Carlos Rodríguez <carlos.rodriguez@youtinspiration.it>
         */
        public function register_plugin_for_activation()
        {
            if (!class_exists('YIT_Plugin_Licence')) {
                require_once YITH_WCDN_PATH . '/plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_WCDN_PATH . '/plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register(YITH_WCDN_INIT, YITH_WCDN_SECRETKEY, YITH_WCDN_SLUG);

        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if (!class_exists('YIT_Upgrade')) {
                require_once(YITH_WCDN_PATH . '/plugin-fw/lib/yit-upgrade.php');
            }
            YIT_Upgrade()->register(YITH_WCDN_SLUG, YITH_WCDN_INIT);
        }


        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.2.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCDN_INIT' ) {
            $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
                $new_row_meta_args['is_premium'] = true;
            }

            return $new_row_meta_args;
        }
        /**
         * Regenerate auction prices
         *
         * Action Links
         *
         * @return void
         * @since    1.2.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, true );
            return $links;
        }

    }
}