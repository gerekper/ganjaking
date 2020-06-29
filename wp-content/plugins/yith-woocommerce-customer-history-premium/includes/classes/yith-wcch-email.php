<?php

defined( 'ABSPATH' ) or exit;

/*
 *  YITH WooCommerce Customer History Session
 */

if ( ! class_exists( 'YITH_WCCH_Email' ) ) {

    class YITH_WCCH_Email {

        public $id              = 0;
        public $sender_id       = '';
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
                    $this->sender_id        = $row->sender_id;
                    $this->user_id          = $row->user_id;
                    $this->subject          = $row->subject;
                    $this->content          = $row->content;
                    $this->reg_date         = $row->reg_date;
                    $this->del              = $row->del;

                }

            }
            
        }

        public static function send( $user_id, $subject, $content ) {

            global $wpdb;
            $wpdb->hide_errors();

            if ( $user_id > 0 && $subject != '' && $content != '' ) {

                /*
                 *  WP Mail
                 */
                
                /*
                $main_user = get_user_by( 'id', 1 )->data;
                $sender_name = get_option( 'yith-wcch-default-sender-name' ) ? get_option( 'yith-wcch-default-sender-name' ) : $main_user->display_name;
                $sender_email = get_option( 'yith-wcch-default-sender-email' ) ? get_option( 'yith-wcch-default-sender-email' ) : $main_user->user_email;
                */

                $sender_id = get_current_user_id();
                $sender = get_user_by( 'id', $sender_id );
                $sender_name = $sender->display_name;
                $sender_email = $sender->user_email;

                $user_info = get_userdata( $user_id );
                $user_email = $user_info->user_email;
                $headers = 'From: ' . $sender_name . ' <' . $sender_email . '>' . "\r\n";

                $success = wp_mail( $user_email, $subject, $content, $headers );

                /*
                 *  Database Insert
                 */

                if ( $success ) {

                    $sql = "INSERT INTO {$wpdb->prefix}yith_wcch_emails (id,sender_id,user_id,subject,content,reg_date,del)
                        VALUES ('','$sender_id','$user_id','$subject','$content',CURRENT_TIMESTAMP,'0')";
                    $result = $wpdb->query( $sql );

                    if ( $result ) { ?>

                        <div class="notice notice-success is-dismissible">
                            <p><?php echo __( 'Your email was sent successfully', 'yith-woocommerce-customer-history' ); ?></p>
                        </div>

                        <?php

                    } else { echo __( 'ERROR: there was a database problem', 'yith-woocommerce-customer-history' ); }

                } else { echo __( 'ERROR: "wp_mail" function problem', 'yith-woocommerce-customer-history' ); }

            }

        }

        public static function create_tables() {

            /*
             *  Check if dbDelta() exists
             */

            if ( ! function_exists( 'dbDelta' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            }

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $create = "CREATE TABLE {$wpdb->prefix}yith_wcch_emails (
                        id              BIGINT(20) NOT NULL AUTO_INCREMENT,
                        sender_id       BIGINT(20),
                        user_id         BIGINT(20),
                        subject         VARCHAR(250),
                        content         TEXT,
                        reg_date        TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                        del             TINYINT(1) NOT NULL DEFAULT '0',
                        PRIMARY KEY     (id)
                    ) $charset_collate;";
            
            dbDelta( $create );

        }

    }

}