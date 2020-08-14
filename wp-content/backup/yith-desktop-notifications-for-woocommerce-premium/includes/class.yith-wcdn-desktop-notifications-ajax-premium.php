<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCDN_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Ajax_Premium' ) ) {
    /**
     * YITH_WCDN_Desktop_Notifications_Ajax
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Desktop_Notifications_Ajax_Premium extends YITH_WCDN_Desktop_Notifications_Ajax
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Desktop_Notifications_Ajax
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct()
        {
            add_action('wp_ajax_yith_wcdn_update_notifications', array($this, 'yith_wcdn_update_notifications'));
            add_action('wp_ajax_yith_wcdn_delete_notifications', array($this, 'yith_wcdn_delete_notifications'));
            add_action('wp_ajax_yith_wcdn_add_audio',array($this,'yith_wcdn_add_audio'));

            parent::__construct();
        }

        /**
         * Save notifications
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcdn_save_notification()
        {
            $global_desktop_notifications = get_option('yith-wcdn-desktop-notifications');
            $option_notification = array(
                'notification'  => $_POST['notification'],
                'title'         => ($_POST['title'] == '') ? __('Notification','yith-desktop-notifications-for-woocommerce'): $_POST['title'],
                'description'   => $_POST['description'],
                'role_user'     => $_POST['role_user'],
                'icon'          => apply_filters('yith_wcdn_change_protocol',$_POST['image']),
                'sound'         => apply_filters('yith_wcdn_change_protocol',$_POST['sound']),
                'time_notification' => $_POST['time_notification'],
                'active'        => 'yes',
                'url'           => NULL
            );
            
            if (isset($_POST['specific_status'])){
                $option_notification['specific_status'] = $_POST['specific_status'];
            }
            if (isset($_POST['products'])) {
                $option_notification['products'] = $_POST['products'];
            }
            $global_desktop_notifications[$_POST['key']] = $option_notification;

            update_option('yith-wcdn-desktop-notifications',$global_desktop_notifications);
            die();
        }

        /**
         * Update notifications
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcdn_update_notifications()
        {
            $global_desktop_notifications = get_option('yith-wcdn-desktop-notifications');

            if(array_key_exists($_POST['key'],$global_desktop_notifications)){
                $option_notification = array(
                    'notification'  => $_POST['notification'],
                    'title'         => ($_POST['title'] == '') ? __('Notification','yith-desktop-notifications-for-woocommerce'): $_POST['title'],
                    'description'   => $_POST['description'],
                    'role_user'     => $_POST['role_user'],
                    'icon'          => apply_filters('yith_wcdn_change_protocol',$_POST['image']),
                    'sound'         => apply_filters('yith_wcdn_change_protocol',$_POST['sound']),
                    'time_notification' => $_POST['time_notification'],
                    'active'        => 'yes',
                    'url'           => NULL
                );
                if (isset($_POST['specific_status'])) {
                    $option_notification['specific_status'] = $_POST['specific_status'];
                }

                if (isset($_POST['products'])) {
                    $option_notification['products'] = $_POST['products'];
                }
                
                $global_desktop_notifications[$_POST['key']] = $option_notification;
                update_option('yith-wcdn-desktop-notifications',$global_desktop_notifications);
            }
            die();
        }
        /**
         * Delete notifications
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcdn_delete_notifications()
        {
            $global_desktop_notifications = get_option('yith-wcdn-desktop-notifications');
            if(array_key_exists($_POST['key'],$global_desktop_notifications)){
                unset($global_desktop_notifications[$_POST['key']]);
                update_option('yith-wcdn-desktop-notifications',$global_desktop_notifications);
            }
            die();
        }

        /**
         * Get notifications to send the user
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcdn_display_notifications(){
            $current_user = wp_get_current_user();
            $id_current_user = $current_user->ID;
            $role_current_user = $current_user->roles;
            $user_notifications = array();
            $instance = YITH_Desktop_Notifications()->register_notifications;
            $notifications = $instance->get_notifications_by_user($current_user);
            if( $notifications && is_array($notifications) ) {
                foreach ($notifications as $notification) {

                    $roles = (array)unserialize($notification->user_roles_to_notify);
                    $user_notified = (array)unserialize($notification->notified_users);
                    $url = $notification->url;

                    foreach ($role_current_user as $role) {

                        if ((in_array($role, $roles) || in_array('ALL', $roles)) && !in_array($id_current_user, $user_notified)) {

                            if( isset( $notification->data ) ) {
                                $noti = (array)unserialize($notification->data);

                            }
                            $noti['url'] = $url;
                            array_push($user_notifications, $noti);
                            //Updated array user notified
                            array_push($user_notified, $id_current_user);
                            $instance->update_notification($notification->id, serialize($user_notified));
                            continue 2;

                        }
                    }
                }
            }
            wp_send_json($user_notifications);
        }
    }
}

