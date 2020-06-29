<?php
/**
 *
 * @author  YIThemes
 * @package YITH WordPress Title Bar Effects
 * @version 1.0.0
 */
if ( !defined( 'YITH_WTBE' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'YITH_WTBE_Admin' ) ) {
    class YITH_WTBE_Admin{
        /**
         * Single instance of the class
         *
         * @var \YITH_WTBE
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        /**
        * @var string Official plugin documentation
        */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-wordpress-title-bar-effects/' ;

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_landing_url = 'https://yithemes.com/themes/plugins/yith-wordpress-title-bar-effects/' ;

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live_demo = 'https://plugins.yithemes.com/yith-wordpress-title-bar-effects/' ;



        /**
         * @var string panel page
         */
        protected $_panel_page = 'yith_wtbe_panel';
        

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WTBE
         * @since 1.0.0
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        public function __construct()
        {
            
            add_action( 'admin_menu', array( $this, 'register_panel' ),5 );
            add_action( 'add_meta_boxes', array($this,'register_meta_boxes') );
            add_action( 'save_post',array($this,'save_post_meta') );

            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WTBE_DIR . '/' . basename( YITH_WTBE_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
            
            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Torrisi Alessio <alessio.torrisi.91@gmail.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = array(
                'settings'  =>  __( 'Settings', 'yith-wordpress-title-bar-effects' ),
            );

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => 'YITH WordPress Title Bar Effects',
                'menu_title'       => 'Title Bar Effects',
                'capability'       => 'manage_options',
                'page'             => $this->_panel_page,
                'parent'           => 'ywtbe',
                'parent_page'      => 'yith_plugin_panel',
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WTBE_DIR . '/plugin-options',
                'class'            => yith_set_wrapper_class()
            );

            /* === Fixed: not updated theme  === */
            if ( !class_exists( 'YIT_Plugin_Panel' ) ) {
                require_once( YITH_WTBE_DIR.'/plugin-fw/lib/yit-plugin-panel.php' );
            }
            
            $this->_panel = new YIT_Plugin_Panel( $args );
        }

          /**
         * add custom action links
         * @author YITHEMES
         * @since 1.0.0
         * @param $links
         * @return array
         */
        public function action_links( $links ){

            $links = yith_add_action_links( $links, $this->_panel_page, false );
            return $links;
        }

        /**
         * add custom plugin meta
         * @author YITHEMES
         * @since 1.0.0
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         * @return array
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WTBE_INIT' ) {
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug']      = 'yith-wordpress-title-bar-effects';
                $new_row_meta_args['is_premium'] = true;
            }

            return $new_row_meta_args;
        }

        /**
         *  Register metabox Title Bar Effects into post, page and product
         *
         * @return   void
         * @since    1.0
         * @author   Torrisi Alessio <alessio.torrisi.91@gmail.com>
         */
        public function register_meta_boxes() {
            add_meta_box(
                'yith_wtbe_title_bar_mb',
                __( 'YITH WordPress Title Bar Effects','yith-wordpress-title-bar-effects' ),
                array($this,'print_post_meta'),
                array('post','page','product'),
                'normal',
                'default'
            );
        }



        /**
         *  Print post meta in metabox Title Bar effects
         *
         * @return   void
         * @since    1.0
         * @author   Torrisi Alessio <alessio.torrisi.91@gmail.com>
         */
        public function print_post_meta() {
            global $post;
            $title = get_post_meta( $post->ID, '_yith_wtbe_title_bar',true );
            $title_prefix = get_post_meta( $post->ID, '_yith_wtbe_title_bar_prefix',true );
            $title_suffix = get_post_meta( $post->ID, '_yith_wtbe_title_bar_suffix',true );
            echo sprintf( __('%1$sTitle displayed on the tab when the animation is applied%2$s','yith-wordpress-title-bar-effects'),'<p>','</p>');
            echo '<input id="yith_wptbe_title_bar" name="yith_wptbe_title_bar" value="'.$title.'" style="width: 100%; padding:10px">';
            echo sprintf( __('%1$sTitle prefix%2$s','yith-wordpress-title-bar-effects'),'<p>','</p>');
            echo '<input id="yith_wptbe_title_bar_prefix" name="yith_wptbe_title_bar_prefix" value="'.$title_prefix.'" style="width: 100%; padding:10px">';
            echo sprintf( __('%1$sTitle suffix%2$s','yith-wordpress-title-bar-effects'),'<p>','</p>');
            echo '<input id="yith_wptbe_title_bar_suffix" name="yith_wptbe_title_bar_suffix" value="'.$title_suffix.'" style="width: 100%; padding:10px">';

        }


        /** Save post meta
         *
         * @return   void
         * @since    1.0
         * @author   Torrisi Alessio <alessio.torrisi.91@gmail.com>
         */
        public function save_post_meta(){
            global $post;
            if( isset( $_POST['yith_wptbe_title_bar'] ) ){
                update_post_meta($post->ID,'_yith_wtbe_title_bar', $_POST['yith_wptbe_title_bar']);
            }
            if( isset( $_POST['yith_wptbe_title_bar_prefix'] ) ){
                update_post_meta($post->ID,'_yith_wtbe_title_bar_prefix', $_POST['yith_wptbe_title_bar_prefix']);
            }
            if( isset( $_POST['yith_wptbe_title_bar_suffix'] ) ){
                update_post_meta($post->ID,'_yith_wtbe_title_bar_suffix', $_POST['yith_wptbe_title_bar_suffix']);
            }
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    1.0.0
         * @author   Alessio Torrisi <alessio.torrisi@yourinspiration.it>
         */
        public function register_plugin_for_activation() {
            if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once( YITH_WTBE_DIR . 'plugin-fw/lib/yit-plugin-licence.php' );
            }

            YIT_Plugin_Licence()->register( YITH_WTBE_INIT, YITH_WTBE_SECRET_KEY, YITH_WTBE_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    1.0.0
         * @author   Alessio Torrisi <alessio.torrisi@yourinspiration.it>
         */
        public function register_plugin_for_updates() {
            if ( ! class_exists( 'YIT_Upgrade' ) ) {
                require_once( YITH_WTBE_DIR . 'plugin-fw/lib/yit-upgrade.php' );
            }

            YIT_Upgrade()->register( YITH_WTBE_SLUG, YITH_WTBE_INIT );
        }

    }
}


/**
 * Unique access to instance of YITH_WTBE_Admin class
 *
 * @return \YITH_WTBE_Admin
 * @since 1.0.0
 */
function YITH_WTBE_Admin() {
    return YITH_WTBE_Admin::get_instance();
}