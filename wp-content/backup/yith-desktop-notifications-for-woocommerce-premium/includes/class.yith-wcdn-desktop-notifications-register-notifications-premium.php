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


if ( !class_exists( 'YITH_WCDN_Register_Notifications_Premium' ) ) {
    /**
     * YITH_WCDN_Register_Notifications
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Register_Notifications_Premium extends YITH_WCDN_Register_Notifications {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Register_Notifications
         * @since 1.0.0
         */

        public $table_name = '';

        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct() {
            global $wpdb;
            $this->table_name = $wpdb->prefix . YITH_WCDN_DB::$notification_table;

            parent::__construct();
        }

        /**
         * @param  $notifications
         */
        public function add_notification($notifications,$is_parent_order = true ) {
            global $wpdb;
            foreach($notifications as $key =>$type) {

                $type_notificacion = $type['notification'];
                $key_notification = $key;
                $data_notification = array();
                $data_notification['title'] = $type['title'];
                $data_notification['description'] = $type['description'];
                $data_notification['icon'] = $type['icon'];
                $data_notification['sound'] = $type['sound'];
                $data_notification['time_notification'] = $type['time_notification'];
                if($is_parent_order &&  defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {
                    $key = array_search('yith_vendor', $type['role_user']);
                    if(isset($key) && !empty($key)) {
                        unset($type['role_user'][$key]);
                        $type['role_user'] = ($type['role_user']) ? $type['role_user'] : "NONE";
                    }
                }
                $role_user_notification = ($type['role_user']) ? $type['role_user'] : "ALL" ;
                $url_notificated = $type['url'];
                $vendors = (isset($type['vendors'])) ? $type['vendors']: "NULL";
                $user_notificated = array();
                
                if( $type['role_user'] != 'NONE' ) {
                    $insert_query = "INSERT INTO $this->table_name (`key`, `notification`, `data`, `user_roles_to_notify`,`notified_users`,`url`,`vendors`) VALUES ('" . $key_notification . "', '" . $type_notificacion . "', '" . serialize($data_notification) . "' , '" . serialize($role_user_notification) . "' , '" . serialize($user_notificated) . "','" . $url_notificated . "','" . serialize($vendors) . "')";
                    $wpdb->query($insert_query);
                }
            }
        }

        /**
         *
         * @return array|null|object
         */
        public function get_notifications_by_user($user) {
            global $wpdb;
            $user_id = $user->ID;
            $user_roles = $user->roles;
            $datetime = 'NOW() - INTERVAL 15 MINUTE' ;
            $table = $wpdb->prefix . YITH_WCDN_DB::$notification_table;


            $where      = '';
            $results = '';
            if ($user_roles) {
                $where = "WHERE (";
                $first = true;
                foreach ($user_roles as $user_role) {
                    if (!$first)
                        $where .= ' OR ';

                    $where .= "user_roles_to_notify LIKE '%\"$user_role\"%'";

                    $first = false;
                }

                $where .= " OR user_roles_to_notify LIKE '%ALL%' ";

                if (defined('YITH_WPV_PREMIUM') && YITH_WPV_PREMIUM) {

                    $where .= ") AND (notified_users NOT LIKE '%i:$user_id;%') AND (date >= '%$datetime%') AND (vendors LIKE '%NULL%')";
                    $where .= " OR (notified_users NOT LIKE '%i:$user_id;%') AND (date >= '%$datetime%') AND (vendors LIKE '%i:$user_id;%')";

                } else {
                    $where .= ") AND (notified_users NOT LIKE '%i:$user_id;%') AND (date >= '%$datetime%')";
                }

                $results = $wpdb->get_results("SELECT * FROM $table $where");

            }
            return $results;
        }
    }
}