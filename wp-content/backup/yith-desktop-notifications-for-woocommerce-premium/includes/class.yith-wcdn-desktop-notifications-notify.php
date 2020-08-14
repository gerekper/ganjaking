<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Notify' ) ) {
    /**
     * YITH_WCDN_Desktop_Notifications_Notify
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Desktop_Notifications_Notify
    {
        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Desktop_Notifications_Notify
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCDN_Desktop_Notifications_Notify
         * @since 1.0.0
         */
        public static function get_instance()
        {
            $self = __CLASS__ . (class_exists(__CLASS__ . '_Premium') ? '_Premium' : '');

            if (is_null($self::$instance)) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }

        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        private function __construct()
        {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_styles_scripts'), 11);
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles_scripts'), 11);
        }

        public function enqueue_styles_scripts()
        {
            if (apply_filters('yith_wcdn_load_script',__return_true()) ) {

                wp_enqueue_script('yith_wcdn_notify', YITH_WCDN_ASSETS_URL . 'js/wcdn-notify.js', array('jquery', 'jquery-ui-sortable'), YITH_WCDN_VERSION, false);
                $array = array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'time_check' => get_option('yith_wcdn_settings_check_new_notification') * 1000, //60000 miliseconds = 1 min
                    'looping_sound' => get_option('yith_wcdn_settings_looping_sound')
                );
                wp_localize_script('yith_wcdn_notify', 'yith_wcdn', $array);
            }
        }
    }

}

