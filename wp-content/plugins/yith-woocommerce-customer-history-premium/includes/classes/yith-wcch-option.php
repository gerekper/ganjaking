<?php

defined( 'ABSPATH' ) or exit;

/*
 *  YITH WooCommerce Customer History Session
 */

if ( ! class_exists( 'YITH_WCCH_Option' ) ) {

    class YITH_WCCH_Option {

        public $id              = 0;
        public $name     = '';
        public $sender_email    = '';
        public $user_id         = '';
        public $subject         = '';
        public $content         = '';
        public $reg_date        = '0000-00-00 00:00:00';
        public $del             = 0;

        /*
         *  Constructor
         */

        public function __construct( $id = 0 ) {

            global $wpdb;

            if ( $id > 0 ) {

                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}yith_wcch_emails WHERE id='$id'" );

                if ( isset( $row ) && $row->id == $id ) {

                    $this->id               = $row->id;
                    $this->sender_name      = $row->sender_name;
                    $this->sender_email     = $row->sender_email;
                    $this->user_id          = $row->user_id;
                    $this->subject          = $row->subject;
                    $this->content          = $row->content;
                    $this->reg_date         = $row->reg_date;
                    $this->del              = $row->del;

                }

            }
            
        }

        public static function send( $user_id, $subject, $content ) {

            if ( $user_id > 0 && $subject != '' && $content != '' ) {

                /*
                 *  WP Mail
                 */

                $user_info = get_userdata( $user_id );
                $user_email = $user_info->user_email;
                $headers = 'From: My Name <myname@example.com>' . "\r\n";

                wp_mail( $user_email, $subject, $content, $headers );

                /*
                 *  Database Insert
                 */

                global $wpdb;
                $wpdb->hide_errors();

                $sql = "INSERT INTO {$wpdb->prefix}yith_wcch_emails (id,sender_name,sender_email,user_id,subject,content,reg_date,del)
                    VALUES ('',$sender_name,$sender_email,$user_id,'$subject','$content',CURRENT_TIMESTAMP,'0')";
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

            $create = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}yith_wcch_emails (
                        id              BIGINT(20) NOT NULL AUTO_INCREMENT,
                        sender_name     VARCHAR(250),
                        sender_email    VARCHAR(250),
                        user_id         BIGINT(20),
                        subject         VARCHAR(250),
                        content         TEXT,
                        reg_date        TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                        del             TINYINT(1) NOT NULL DEFAULT '0',
                        PRIMARY KEY     (id)
                    ) $charset_collate;";
            $result = dbDelta( $create );

        }

    }

}