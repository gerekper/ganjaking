<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Types' ) ) {
    /**
     * YITH_WCDN_Desktop_Notifications_Notify
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Desktop_Notifications_Types
    {
        protected static $instance;


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
            add_action('woocommerce_checkout_order_processed', array($this, 'yith_wcdn_order_processed'));
        }

        public function yith_wcdn_order_processed($order_id)
        {
            $notifications = get_option('yith-wcdn-desktop-notifications-free');
            $order = wc_get_order($order_id);

            do_action('yith_wcdn_before_order_notify', $order, $notifications);


                $url_order = admin_url('post.php?post=' . absint($order_id) . '&action=edit');
                $customer_user = yit_get_prop($order, '_customer_user', true);

                $placeholder = array(
                    "{order_id}" => $order_id,
                    "{order_total}" => $order->get_total(),
                    "{username}" => get_user_meta($customer_user, 'nickname', true),
                );

                $description = strtr($notifications['description'], $placeholder);
                $notifications['description'] = $description;
                $notifications['url'] = $url_order;
                $notification_processed = $notifications;


                $instance = YITH_Desktop_Notifications()->register_notifications;
                $instance->add_notification($notification_processed);

            do_action('yith_wcdn_after_order_notify',$order,$notifications);
        }

    }

}
