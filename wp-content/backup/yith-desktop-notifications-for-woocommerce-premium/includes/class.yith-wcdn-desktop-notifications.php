<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCDN_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Desktop_Notifications
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Desktop_Notifications' ) ) {
    /**
     * Class YITH_Desktop_Notifications
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Desktop_Notifications {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0
         */
        public $version = YITH_WCDN_VERSION;

        /**
         * Main Instance
         *
         * @var YITH_Desktop_Notifications
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main Admin Instance
         *
         * @var YITH_WCDN_Desktop_Notifications_Admin
         * @since 1.0
         */
        public $admin = null;


        /**
         * Main Product Instance
         *
         * @var YITH_Desktop_Notifications
         * @since 1.0
         */
        public $product = null;



        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct(){

            /* === Require Main Files === */
            $require = apply_filters( 'yith_wcdn_require_class',
                array(
                    'common'    => array(
                        'includes/class.yith-wcdn-desktop-notifications-ajax.php',
                        'includes/class.yith-wcdn-desktop-notifications-notify.php',
                        'includes/class.yith-wcdn-desktop-notifications-db.php',
                        'includes/class.yith-wcdn-desktop-notifications-register-notifications.php',
                        'includes/class.yith-wcdn-desktop-notifications-types.php',
                        'includes/class.yith-wcdn-desktop-notifications-cron-class.php',
                    ),
                    'admin'     => array(
                        'includes/class.yith-wcdn-desktop-notifications-admin.php',
                    ),
                    'frontend'  => array(

                    ),
                )
            );

            $this->_require( $require );

            $this->init_classes();
            
            /* === Load Plugin Framework === */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            /* == Plugins Init === */
            add_action( 'init', array( $this, 'init' ) );

        }

        /**
         * Main plugin Instance
         *
         * @return YITH_Desktop_Notifications
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        public function init_classes() {
            $this->register_notifications = YITH_WCDN_Register_Notifications::get_instance();
            $this->ajax = YITH_WCDN_Desktop_Notifications_Ajax::get_instance();
            $this->notify = YITH_WCDN_Desktop_Notifications_Notify::get_instance();
            $this->cron = YITH_WCDN_Desktop_Notifications_Cron::get_instance();
            $this->type = YITH_WCDN_Desktop_Notifications_Types::get_instance();
        }

        /**
         * Add the main classes file
         *
         * Include the admin and frontend classes
         *
         * @param $main_classes array The require classes file path
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         *
         * @return void
         * @access protected
         */
        protected function _require( $main_classes ) {
            foreach ( $main_classes as $section => $classes ) {
                foreach ( $classes as $class ) {
                    if ( 'common' == $section  || ( 'admin' == $section && is_admin() ) && file_exists( YITH_WCDN_PATH . $class ) ) {
                        require_once( YITH_WCDN_PATH . $class );
                    }
                }
            }
        }

        /**
         * Load plugin framework
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
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
         * Function init()
         *
         * Instance the admin or frontend classes
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         * @access protected
         */
        public function init() {
            if ( is_admin() ) {
                $this->admin = new YITH_WCDN_Desktop_Notifications_Admin();
            }
        }

    }
}