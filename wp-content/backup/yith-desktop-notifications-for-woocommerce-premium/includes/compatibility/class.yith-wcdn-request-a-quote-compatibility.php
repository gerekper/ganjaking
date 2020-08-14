<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCDN_RequestaQuote_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.1
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCDN_RequestaQuote_Compatibility' ) ) {

    class YITH_WCDN_RequestaQuote_Compatibility
    {

        public function __construct()
        {
            add_filter('yith_wcdn_get_notification_type',array($this,'add_new_notification_type'));
            add_action('ywraq_after_create_order', array($this, 'yith_wcdn_create_quote_order'), 10, 3);
        }

        public function add_new_notification_type($type_notification){
            $type_notification['new_quote'] = esc_html__('A quote is placed','yith-desktop-notifications-for-woocommerce');
            return $type_notification;
        }

        public function yith_wcdn_create_quote_order($order_id, $post, $req)
        {
            $notifications = get_option('yith-wcdn-desktop-notifications',array());
            $order = wc_get_order($order_id);

            do_action('yith_wcdn_before_quote_notify', $order, $notifications);

            //Multivendor compatibilities
            $allowed_order = apply_filters('yith_wcdn_allow_quote_notify',true, $order, $notifications);

            if ($allowed_order) {

                $url_order = admin_url('post.php?post=' . absint($order_id) . '&action=edit');
                $notification_quote = array();
                $customer_user = yit_get_prop($order, '_customer_user', true);

                $placeholder = array(
                    "{quote_id}" => $order_id,
                    "{username}" => get_user_meta($customer_user, 'nickname', true),
                );
                if ( is_array( $notifications ) ) {
                    foreach ($notifications as $key => $type) {
                        foreach ($type as $notify => $notifytype) {
                            if ($notifytype == 'new_quote') {

                                $description = strtr($type['description'], $placeholder);
                                $type['description'] = $description;
                                $type['url'] = $url_order;
                                $notification_quote[$key] = $type;
                                continue 2;
                            }
                        }
                    }
                    $instance = YITH_Desktop_Notifications()->register_notifications;
                    $instance->add_notification($notification_quote);
                }
            }

            do_action('yith_wcdn_after_quote_notify',$order,$notifications);
        }
    }
}

return new YITH_WCDN_RequestaQuote_Compatibility();