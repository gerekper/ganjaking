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
 * @class      YITH_WCDN_Bookings_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.1
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCDN_Bookings_Compatibility' ) ) {

    class YITH_WCDN_Bookings_Compatibility
    {

        public function __construct()
        {
            add_filter('yith_wcdn_get_notification_type',array($this,'add_new_notification_type'));
            add_action('yith_wcbk_new_booking', array($this, 'yith_wcdn_create_booking_notification'));
        }

        public function add_new_notification_type($type_notification){
            $type_notification['new_booking'] = esc_html__('A new booking is placed','yith-desktop-notifications-for-woocommerce');
            $type_notification['new_request_booking'] = esc_html__('A new booking request is placed','yith-desktop-notifications-for-woocommerce');

            return $type_notification;
        }

        public function yith_wcdn_create_booking_notification($booking_id)
        {
            $booking = yith_get_booking( $booking_id );

            if( $booking ) {

                $notifications = get_option('yith-wcdn-desktop-notifications', array());

                do_action('yith_wcdn_before_booking_notify', $booking, $notifications);

                $allowed_booking = apply_filters('yith_wcdn_allow_booking_notify', true, $booking, $notifications);

                if ($allowed_booking) {

                    $url_booking = admin_url('post.php?post=' . absint($booking_id) . '&action=edit');
                    $notification_booking = array();

                    $placeholder = array(
                        "{booking_id}" => $booking_id,
                        "{username}" => get_user_meta($booking->user_id, 'nickname', true),
                        "{product_id}" => $booking->product_id,
                    );

                    if ( is_array( $notifications ) ) {
                        foreach ($notifications as $key => $type) {
                            foreach ($type as $notify => $notifytype) {
                                if ($notifytype == 'new_booking') {

                                    $description = strtr($type['description'], $placeholder);
                                    $type['description'] = $description;
                                    $type['url'] = $url_booking;
                                    $notification_booking[$key] = $type;
                                    continue 2;
                                } elseif ( $notifytype == 'new_request_booking' ) {

                                    $booking_request = get_post_meta($booking->product_id,'_yith_booking_request_confirmation',true);
                                    if( $booking_request ) {
                                        $description = strtr($type['description'], $placeholder);
                                        $type['description'] = $description;
                                        $type['url'] = $url_booking;
                                        $notification_booking[$key] = $type;
                                        continue 2;
                                    }
                                }
                            }
                        }
                        $instance = YITH_Desktop_Notifications()->register_notifications;
                        $instance->add_notification($notification_booking);
                    }

                }

                do_action('yith_wcdn_after_booking_notify', $booking, $notifications);
            }
        }
    }
}

return new YITH_WCDN_Bookings_Compatibility();