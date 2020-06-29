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

if ( ! class_exists( 'YITH_Desktop_Notifications_Premium' ) ) {
    /**
     * Class YITH_Desktop_Notifications
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Desktop_Notifications_Premium extends YITH_Desktop_Notifications {

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
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct(){

            add_filter('yith_wcdn_require_class',array( $this, 'load_premium_classes' ) );
            add_action('plugins_loaded', array($this, 'init_compatibility_class' ),15);


            parent::__construct();
            
        }

        public function init_classes() {
            $this->register_notifications = YITH_WCDN_Register_Notifications_Premium::get_instance();
            $this->notifications = YITH_WCDN_Desktop_Notifications_Options::get_instance();
            $this->ajax = YITH_WCDN_Desktop_Notifications_Ajax_Premium::get_instance();
            $this->notify = YITH_WCDN_Desktop_Notifications_Notify::get_instance();
            $this->cron = YITH_WCDN_Desktop_Notifications_Cron::get_instance();
            $this->types = YITH_WCDN_Desktop_Notifications_Types_Premium::get_instance();
        }

        /**
         * Init compatibility class
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         *
         */
        public function init_compatibility_class() {
            $this->compatibility = YITH_WCDN_Compatibility::get_instance();
        }

        /**
         * Add premium files to Require array
         *
         * @param $require The require files array
         *
         * @return Array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         *
         */
        public function load_premium_classes( $require ){

            $common = array(
                'includes/class.yith-wcdn-desktop-notifications-options-premium.php',
                'includes/class.yith-wcdn-desktop-notifications-ajax-premium.php',
                'includes/class.yith-wcdn-desktop-notifications-register-notifications-premium.php',
                'includes/class.yith-wcdn-desktop-notifications-types-premium.php',
                'includes/compatibility/class.yith-wcdn-compatibility.php'
            );
            $require['admin'][]   = 'includes/class.yith-wcdn-desktop-notifications-admin-premium.php';
            $require['common']    = array_merge($require['common'],$common);

            return $require;
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
                $this->admin = new YITH_WCDN_Desktop_Notifications_Admin_Premium();
            }
        }
        
    }
}