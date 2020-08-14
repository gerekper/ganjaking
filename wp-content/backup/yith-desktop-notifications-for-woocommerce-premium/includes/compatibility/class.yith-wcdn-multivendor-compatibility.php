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
 * @class      YITH_WCDN_Multivendor_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCDN_Multivendor_Compatibility' ) ) {

    class YITH_WCDN_Multivendor_Compatibility
    {

        public function __construct()
        {
            add_filter('yith_wcdn_allow_order_notify', array($this, 'yith_wcdn_allow_order_notify'),10,3);
            add_action('yith_wcdn_after_order_notify', array($this,'notify_suborders'),10,2);
            add_action('yith_wcdn_after_order_notify_status_changed', array($this,'notify_suborders_status_changed'),10,4);
        }

        public function yith_wcdn_allow_order_notify($status,$order,$notifications) {

            $parent_or_admin_order = wp_get_post_parent_id( $order->get_id() ) == 0;

            return $parent_or_admin_order;
        }

        public function notify_suborders($order,$notifications) {

            $suborders = YITH_Vendors()->orders->get_suborder( $order->get_id() );
            $customer_user = yit_get_prop( $order, '_customer_user', true );
            $username = get_user_meta( $customer_user, 'nickname', true );

            $notification_processed = array();

            foreach ($notifications as $key => $type) {
                foreach ($type as $notify => $notifytype) {
                    if ($notifytype == 'placed') {

                        foreach ($suborders as $suborder_id) {

                            $suborder = wc_get_order($suborder_id);
                            //Placeholder for each suborders
                            $url_suborder = apply_filters('yith_wcdn_url_suborder',admin_url('post.php?post=' . absint($suborder_id) . '&action=edit'),$suborder_id,$order);
                            $placeholder = array(
                                "{order_id}" => $suborder_id,
                                "{order_total}" => $suborder->get_total(),
                                "{username}"    => $username,
                            );
                            $vendors_ids = array();
                            $vendor    = yith_get_vendor ( get_post($suborder->get_id())->post_author, 'user' );
                            $vendor_owner_user_id = $vendor->get_owner();
                            $vendors_ids[] = (int)$vendor_owner_user_id;

                            $description = strtr($type['description'],$placeholder );
                            $type['description'] = $description;
                            $type['url'] = $url_suborder;
                            $type['vendors'] = $vendors_ids;
                            $type['role_user'] = array('yith_vendor'); //This notification is only for yith_vendors

                            $notification_processed[$key.$suborder_id] = $type;
                        }

                        continue 2;
                    }
                }
            }
            $instance = YITH_Desktop_Notifications()->register_notifications;
            $instance->add_notification($notification_processed,false);

        }

        public function notify_suborders_status_changed($order,$notifications,$old_status,$new_status) {
            $suborders = YITH_Vendors()->orders->get_suborder( $order->get_id() );
            $customer_user = yit_get_prop( $order, '_customer_user', true );
            $username = get_user_meta( $customer_user, 'nickname', true );

            $notification_order_status_changed = array();

            foreach ($notifications as $key => &$type) {
                foreach ($type as $notify => $notifytype) {
                    if ($notifytype == 'status_changed') {
                        if (isset($type['specific_status']) && is_array($type['specific_status']) && in_array('wc-' . $new_status, $type['specific_status'])) {
                            foreach ($suborders as $suborder_id) {

                                $suborder = wc_get_order($suborder_id);
                                //Placeholder for each suborders
                                $url_suborder = admin_url('post.php?post=' . absint($suborder_id) . '&action=edit');
                                $placeholder = array(
                                    "{order_id}" => $suborder_id,
                                    "{order_total}" => $suborder->get_total(),
                                    "{username}" => $username,
                                    "{old_status}" => $old_status,
                                    "{new_status}" => $new_status,
                                );

                                $vendors_ids = array();
                                $vendor    = yith_get_vendor ( $suborder->post->post_author, 'user' );
                                $vendor_owner_user_id = $vendor->get_owner();
                                $vendors_ids[] = $vendor_owner_user_id;

                                $description = strtr($type['description'], $placeholder);
                                $type['description'] = $description;
                                $type['url'] = $url_suborder;
                                $type['vendors'] = $vendors_ids;
                                $type['role_user'] = array('yith_vendor'); //This notification is only for yith_vendors

                                $notification_order_status_changed[$key.$suborder_id] = $type;
                            }
                        }
                        continue 2;
                    }
                }
            }
            $instance = YITH_Desktop_Notifications()->register_notifications;
            $instance->add_notification($notification_order_status_changed,false);
        }
    }
}

return new YITH_WCDN_Multivendor_Compatibility();