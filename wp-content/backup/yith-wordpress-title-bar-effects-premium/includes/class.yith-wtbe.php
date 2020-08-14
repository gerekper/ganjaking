<?php
/**
 * Main class
 *
 * @author  YIThemes
 * @package YITH wordPress Title Bar Effects
 * @version 1.0.0
 */
if ( !defined( 'YITH_WTBE' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'YITH_WTBE' ) ) {
    class YITH_WTBE
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WTBE
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * The name for the plugin options
         *
         * @access public
         * @var string
         * @since 1.0.0
         */
        public $plugin_options = 'yit_ywtbe_options';


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

        /**
         * YITH_WTBE constructor.
         * @return mixed
         * @since 1.0.0
         * @author Alessio Torrisi <alessio.torrisi.91@gmail.com>
         */
        public function __construct()
        {
            add_action( 'plugins_loaded',array( $this, 'plugin_fw_loader' ),15 );
            add_action( 'wp_enqueue_scripts', array($this ,'register_general_scripts'),10);
            if (is_admin()){
                YITH_WTBE_Admin();
            }else{
                if( $this->get_option('yith-wtbe-enabled') == 'yes' ){
                    add_filter('yith_wtbe_title_bar',array($this,'get_title_bar'));
                    add_action( 'wp_enqueue_scripts', array($this ,'enqueue_frontend_scripts'),15);
                }
            }


        }


        /**
         * Load Plugin Framework
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( !defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( !empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Get options from db
         *
         * @access public
         * @since 1.0.0
         * @author  Alessio Torrisi
         * @author Alessio Torrisi <alessio.torrisi.91@gmail.com>
         * @param $option string
         * @return mixed
         */
        public function get_option( $option ) {
            // get all options
            $options = get_option( $this->plugin_options );

            if( isset( $options[ $option ] ) ) {
                return $options[ $option ];
            }

            return false;
        }


        /** Register general scripts
         *
         * @access public
         * @since 1.0.1
         * @author  Alessio Torrisi
         * @author Alessio Torrisi <alessio.torrisi.91@gmail.com>
         * @return void
         */
        public function register_general_scripts(){
            wp_register_script( 'yith_ywdpd_frontend', YITH_WTBE_ASSETS_URL . '/js/yith-wtbe-frontend.js', array( 'jquery'), YITH_WTBE_VERSION, true );
            wp_localize_script( 'yith_ywdpd_frontend', 'yith_wtbe_options', array(
                'animation'         =>  apply_filters('yith_wtbe_animation',$this->get_option('yith-wtbe-animation','typing')),
                'speed_animation'   =>  $this->get_option('yith-wtbe-speed-animation',500),
                'change_tab'        =>  $this->get_option('yith-wtbe-change-tab','yes'),
                'delay_start'       =>  $this->get_option('yith-wtbe-delay-start',500),
                'delay_stop'        =>  $this->get_option('yith-wtbe-delay-stop',60000),
                'delay_cycle'       =>  $this->get_option('yith-wtbe-delay-cycle',3000),
                'title_bar'         =>  apply_filters('yith_wtbe_title_bar', ''),
                'is_admin'          =>  is_admin()
            ) );

        }

        /** Enqueue frontend scripts
         *
         * @access public
         * @since 1.0.0
         * @author  Alessio Torrisi
         * @author Alessio Torrisi <alessio.torrisi.91@gmail.com>
         * @return void
         */
        public function enqueue_frontend_scripts(){
            wp_enqueue_script( 'yith_ywdpd_frontend');

        }


        /**  Get title bar to animate
         *
         * @access public
         * @since 1.0.0
         * @author  Alessio Torrisi
         * @author Alessio Torrisi <alessio.torrisi.91@gmail.com>
         *
         * @return string
         */
        public function get_title_bar(){
            global $post;
            if( ! is_object($post) ) return '';
            $post_title_bar = get_post_meta( $post->ID, '_yith_wtbe_title_bar',true );
            $general_title_bar = $this->get_option('yith-wtbe-title-bar');
            $title_bar = ( $post_title_bar && $post_title_bar != '' ) ? $post_title_bar : $general_title_bar;

            $post_title_bar_prefix = get_post_meta( $post->ID, '_yith_wtbe_title_bar_prefix',true );
            $general_title_bar_prefix = $this->get_option('yith-wtbe-title-bar-prefix');
            $title_bar_prefix = ( $post_title_bar_prefix && $post_title_bar_prefix != '' ) ? $post_title_bar_prefix : $general_title_bar_prefix;

            $post_title_bar_suffix = get_post_meta( $post->ID, '_yith_wtbe_title_bar_suffix',true );
            $general_title_bar_suffix = $this->get_option('yith-wtbe-title-bar-suffix');
            $title_bar_suffix = ( $post_title_bar_suffix && $post_title_bar_suffix != '' ) ? $post_title_bar_suffix : $general_title_bar_suffix;


            return $title_bar_prefix . $title_bar . $title_bar_suffix;
        }

    }
}

/**
 * Unique access to instance of yith_wtbe class
 *
 * @return \YITH_WTBE
 * @since 1.0.0
 */
function yith_wtbe() {
    return YITH_WTBE::get_instance();
}