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


if ( !class_exists( 'YITH_WCDN_Register_Notifications' ) ) {
    /**
     * YITH_WCDN_Register_Notifications
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Register_Notifications {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Register_Notifications
         * @since 1.0.0
         */
        protected static $instance;

        public $table_name = '';


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCDN_Register_Notifications
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
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
        public function __construct() {
            global $wpdb;
            $this->table_name = $wpdb->prefix . YITH_WCDN_DB::$notification_table;
        }

        /**
         * @param  $notifications
         */
        public function add_notification($type,$is_parent_order = true ) {
            global $wpdb;
                $key_notification = uniqid();
                $type_notificacion = "placed";
                $data_notification = array();
                $data_notification['title'] = $type['title'];
                $data_notification['description'] = $type['description'];
                $data_notification['icon'] = $type['icon'];
                $data_notification['sound'] = $type['sound'];
                $data_notification['time_notification'] = $type['time_notification'];
                $role_user_notification = "NULL";
                $url_notificated = $type['url'];
                $vendors = (isset($type['vendors'])) ? $type['vendors']: "NULL";
                $user_notificated = array();
                $insert_query = "INSERT INTO $this->table_name (`key`, `notification`, `data`, `user_roles_to_notify`,`notified_users`,`url`,`vendors`) VALUES ('" . $key_notification . "', '" . $type_notificacion . "', '" . serialize($data_notification) . "' , '" . serialize($role_user_notification) . "' , '" . serialize($user_notificated) . "','" . $url_notificated . "','" . serialize($vendors) . "')";
                $wpdb->query( $insert_query );
        }
        
        /**
         *
         * @return array|null|object
         */
        public function get_notifications_by_user($user) {
            global $wpdb;
            $user_id = $user->ID;
            $datetime = 'NOW() - INTERVAL 15 MINUTE' ;
            $table = $wpdb->prefix . YITH_WCDN_DB::$notification_table;

            $where = "WHERE (notified_users NOT LIKE '%i:$user_id;%') AND (date >= '%\"$datetime\"%')";

            $results = $wpdb->get_results( "SELECT * FROM $table $where" );


            return $results;
        }


        /**
         * @param $auction_id
         *
         * @return array|null|object
         */
        public function update_notification($id,$user_notified) {
            global $wpdb;
            $wpdb->query("UPDATE $this->table_name SET `notified_users` = '".$user_notified."' WHERE `id` = '".$id."'");
        }

        public function delete_notifications() {
            global $wpdb;
            $query = "DELETE FROM $this->table_name WHERE date < (NOW() - INTERVAL 20 MINUTE)";
            return $wpdb->query($query);
        }

    }
}