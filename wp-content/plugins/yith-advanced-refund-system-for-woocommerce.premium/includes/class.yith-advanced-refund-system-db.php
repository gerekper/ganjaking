<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_ARS_DB' ) ) {
    /**
     * YITH Advanced Refund System Database
     *
     * @since 1.0.0
     */
    class YITH_ARS_DB {

        /**
         * DB version
         *
         * @var string
         */
        public static $version = '1.0.0';

        public static $ywcars_messages_table = 'ywcars_messages';
        public static $ywcars_messagemeta_table = 'ywcars_messagemeta';


        /**
         * Constructor
         *
         * @return YITH_ARS_DB
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

            $current_version = get_option( 'ywcars_db_version' );

            if ( $force || $current_version != self::$version ) {
                $wpdb->hide_errors();

                $messages_table  = $wpdb->prefix . self::$ywcars_messages_table;
                $messagemeta_table  = $wpdb->prefix . self::$ywcars_messagemeta_table;
                $charset_collate = $wpdb->get_charset_collate();

                $sql_messages = "CREATE TABLE IF NOT EXISTS $messages_table (
                    ID bigint(20) unsigned NOT NULL auto_increment,
                    request bigint(20) unsigned NOT NULL default '0',
                    date datetime NOT NULL default '0000-00-00 00:00:00',
                    message longtext NOT NULL,
                    author bigint(20) unsigned NOT NULL default '0',
                    PRIMARY KEY (ID)
                    ) $charset_collate;";

                $sql_messagesmeta = "CREATE TABLE IF NOT EXISTS $messagemeta_table (
                    meta_id bigint(20) unsigned NOT NULL auto_increment,
                    message bigint(20) unsigned NOT NULL default '0',
                    meta_key varchar(255) default NULL,
                    meta_value longtext,
                    PRIMARY KEY (meta_id)
                    ) $charset_collate;";

                if ( ! function_exists( 'dbDelta' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                }
                dbDelta( $sql_messages );
                dbDelta( $sql_messagesmeta );
                update_option( 'ywcars_db_version', self::$version );
            }
        }

    }
}