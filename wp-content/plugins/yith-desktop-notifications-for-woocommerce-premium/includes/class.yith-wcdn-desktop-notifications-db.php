<?php
/**
 * DB class
 *
 * @author  Yithemes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCDN_DB' ) ) {
    /**
     * YITH Desktop Notifications for WooCommerce
     *
     * @since 1.0.0
     */
    class YITH_WCDN_DB {

        /**
         * DB version
         *
         * @var string
         */
        public static $version = '1.0.7';

        public static $notification_table = 'yith_wcdn_desktop_notification';


        /**
         * Constructor
         *
         * @return YITH_WCDN_DB
         */
        private function __construct() {
        }

        public static function install() {
            self::create_db_table();
        }

        /**
         * create table for Notes
         *
         * @param bool $force
         */
        public static function create_db_table( $force = false ) {
            global $wpdb;

            $current_version = get_option( 'yith_wcdn_db_version' );
            if ( $force || $current_version != self::$version ) {
                $wpdb->hide_errors();

                $table_name      = $wpdb->prefix . self::$notification_table;
                $charset_collate = $wpdb->get_charset_collate();

                $sql
                    = "CREATE TABLE $table_name (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `key` varchar(255) NOT NULL,
                    `notification` varchar(255) NOT NULL,
                    `data` longtext,
                    `user_roles_to_notify` longtext,
                    `notified_users` longtext,
                    `vendors` longtext,
                    `url` longtext,
                    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                    ) $charset_collate;";

                if ( !function_exists( 'dbDelta' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                }
                dbDelta( $sql );
                update_option( 'yith_wcdn_db_version', self::$version );
            }
        }

    }
}