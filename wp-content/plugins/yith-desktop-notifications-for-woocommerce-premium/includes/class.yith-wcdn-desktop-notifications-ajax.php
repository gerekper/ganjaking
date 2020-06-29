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

if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Ajax' ) ) {
    /**
     * YITH_WCDN_Desktop_Notifications_Ajax
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Desktop_Notifications_Ajax
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDN_Desktop_Notifications_Ajax
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCDN_Desktop_Notifications_Ajax
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
        public function __construct()
        {
            add_action('wp_ajax_yith_wcdn_save_notifications', array($this, 'yith_wcdn_save_notification'));
            add_action('wp_ajax_yith_wcdn_display_notifications', array($this, 'yith_wcdn_display_notifications'));
        }

        /**
         * Save notifications
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcdn_save_notification()
        {
            $option_notification = array(
                'title'         => ($_POST['title'] == '') ? esc_html__('Notification','yith-desktop-notifications-for-woocommerce'): $_POST['title'],
                'description'   => $_POST['description'],
                'icon'          => $_POST['image'],
                'sound'         => $_POST['sound'],
                'time_notification' => $_POST['time_notification'],
                'active'        => 'yes',
                'url'           => NULL
            );


            update_option('yith-wcdn-desktop-notifications-free',$option_notification);
            die();
        }
        
        /**
         * Get notifications to send the user
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcdn_display_notifications(){
            $user_notifications = array();
            if ( current_user_can('manage_options') ) {
                $current_user = wp_get_current_user();
                $id_current_user = $current_user->ID;
                $instance = YITH_Desktop_Notifications()->register_notifications;
                $notifications = $instance->get_notifications_by_user($current_user);
                foreach ($notifications as $notification) {
                    $user_notified = (array)unserialize($notification->notified_users);
                    $url = $notification->url;
                    if (!in_array($id_current_user, $user_notified)) {
                        $noti = (array)unserialize($notification->data);
                        $noti['url'] = $url;
                        array_push($user_notifications, $noti);
                        //Updated array user notified
                        array_push($user_notified, $id_current_user);
                        $instance->update_notification($notification->id, serialize($user_notified));
                        continue;
                    }
                }
            }
            wp_send_json($user_notifications);
        }
    }
}

