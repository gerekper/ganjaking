<?php

defined( 'ABSPATH' ) or exit;

/*
 *  YITH WooCommerce Customer History Session
 */

if ( ! class_exists( 'YITH_WCCH_Session' ) ) {

    class YITH_WCCH_Session {

        public $id              = 0;
        public $user_id         = '';
        public $ip              = '';
        public $url             = '';
        public $referer         = '';
        public $reg_date        = '0000-00-00 00:00:00';
        public $del             = 0;

        /*
         *  Constructor
         */

        public function __construct( $id = 0 ) {

            global $wpdb;

            if ( $id > 0 ) {

                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}yith_wcch_sessions WHERE id='$id'" );

                if ( isset( $row ) && $row->id == $id ) {

                    $this->id               = $row->id;
                    $this->user_id          = $row->user_id;
                    $this->ip               = $row->ip;
                    $this->url              = $row->url;
                    $this->referer          = $row->referer;
                    $this->reg_date         = $row->reg_date;
                    $this->del              = $row->del;

                }

            }
            
        }

        public static function insert( $user_id, $url ) {

            if ( $user_id >= 0 && $url != '' ) {

                global $wpdb;
                $wpdb->hide_errors();

                $ipaddress = '';
                if ( get_option('yith-wcch-save_user_ip') ) {
                    if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) )             { $ipaddress = $_SERVER['HTTP_CLIENT_IP']; }
                    else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )  { $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR']; }
                    else if ( isset( $_SERVER['HTTP_X_FORWARDED'] ) )      { $ipaddress = $_SERVER['HTTP_X_FORWARDED']; }
                    else if ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) )    { $ipaddress = $_SERVER['HTTP_FORWARDED_FOR']; }
                    else if ( isset( $_SERVER['HTTP_FORWARDED'] ) )        { $ipaddress = $_SERVER['HTTP_FORWARDED']; }
                    else if ( isset( $_SERVER['REMOTE_ADDR'] ) )           { $ipaddress = $_SERVER['REMOTE_ADDR']; }
                }

                $referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

                $timezone = 0;
                $reg_date = date( 'Y-m-d  H:i:s', time() + 3600 * $timezone );

                $sql = "INSERT INTO {$wpdb->prefix}yith_wcch_sessions (user_id,ip,url,referer,reg_date,del) VALUES ('$user_id','$ipaddress','$url','$referer','$reg_date','0')";
                $wpdb->query( $sql );

            }

        }

        public static function create_tables() {

            /*
             *  Check if dbDelta() exists
             */

            if ( ! function_exists( 'dbDelta' ) ) { require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); }

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $create = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}yith_wcch_sessions (
                        id              BIGINT(20) NOT NULL AUTO_INCREMENT,
                        user_id         BIGINT(20),
                        ip              VARCHAR(250),
                        url             VARCHAR(250),
                        referer         VARCHAR(250),
                        reg_date        TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                        del             TINYINT(1) NOT NULL DEFAULT '0',
                        PRIMARY KEY     (id)
                    ) $charset_collate";
            $result = $wpdb->query( $create );

        }

    }

}