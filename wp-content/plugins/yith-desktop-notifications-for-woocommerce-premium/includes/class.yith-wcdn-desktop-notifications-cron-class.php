<?php

/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCDN_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}


if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Cron' ) ) {
    /**
     * YITH_WCDN_Desktop_Notifications_Cron
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Desktop_Notifications_Cron
    {
        private static $_instance;

        // Singleton
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        public function __construct()
        {
            add_action('wp_loaded', array($this, 'set_cron'), 30);
            add_action('yith_wcdn_delete_notifications', array($this, 'delete_notifications'));
        }

        public function set_cron()
        {
            if (!wp_next_scheduled('yith_wcdn_delete_notifications')) {
                wp_schedule_event(time(), 'daily', 'yith_wcdn_delete_notifications');
            }
        }

        public function delete_notifications()
        {
            // Delete Notifications
            $instance = YITH_Desktop_Notifications()->register_notifications;
            $instance->delete_notifications();
        }
    }
}