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

if ( !class_exists( 'YITH_WCDN_Desktop_Notifications_Types_Premium' ) ) {
    /**
     * YITH_WCDN_Desktop_Notifications_Notify
     *
     * @since 1.0.0
     */
    class YITH_WCDN_Desktop_Notifications_Types_Premium extends YITH_WCDN_Desktop_Notifications_Types
    {
        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct()
        {
            add_action( 'woocommerce_order_refunded',array($this,'yith_wcdn_order_refunded'));
            add_action('woocommerce_product_set_stock_status', array($this, 'yith_wcdn_product_out_of_stock'), 10, 2);
            add_action('woocommerce_order_status_changed',array($this,'yith_wcdn_order_status_changed'),10,3);
            add_action('woocommerce_checkout_order_processed',array($this,'yith_wcdn_product_sold'));
            add_action('woocommerce_low_stock', array($this,'yith_wcdn_low_stock'));
            parent::__construct();
        }
        
        public function yith_wcdn_order_processed($order_id)
        {
            $notifications = get_option('yith-wcdn-desktop-notifications');
            $order = wc_get_order($order_id);
            
            do_action('yith_wcdn_before_order_notify', $order, $notifications);

            if ( $notifications && is_array( $notifications ) ) {

                //Multivendor compatibilities
                $allowed_order = apply_filters('yith_wcdn_allow_order_notify', true, $order, $notifications);

                if ($allowed_order) {

                    $url_order = admin_url('post.php?post=' . absint($order_id) . '&action=edit');
                    $notification_processed = array();
                    $customer_user = yit_get_prop($order, '_customer_user', true);

                    $placeholder = apply_filters('yith_wcdn_order_processed_params', array(
                        "{order_id}" => $order_id,
                        "{order_total}" => $order->get_total(),
                        "{username}" => get_user_meta($customer_user, 'nickname', true),
                    ), $order);

                    foreach ($notifications as $key => $type) {
                        foreach ($type as $notify => $notifytype) {
                            if ($notifytype == 'placed') {

                                $description = strtr($type['description'], $placeholder);
                                $type['description'] = $description;
                                $type['url'] = $url_order;
                                $notification_processed[$key] = $type;
                                continue 2;
                            }
                        }
                    }
                    $instance = YITH_Desktop_Notifications()->register_notifications;
                    $instance->add_notification($notification_processed);
                }
            }

            do_action('yith_wcdn_after_order_notify',$order,$notifications);
        }

        public function yith_wcdn_product_out_of_stock($product_id, $status)
        {
            if ( $status == 'outofstock' ) {

                $url_product = admin_url('post.php?post=' . absint($product_id) . '&action=edit');
                $notifications = get_option('yith-wcdn-desktop-notifications');
                $notification_product_out_of_stock = array();
                $product = wc_get_product($product_id);

                $placeholder = array(
                    "{product_id}" => $product_id,
                    "{product_name}" => $product->get_title(),
                );

                if ( $notifications && is_array( $notifications ) ) {

                    foreach ($notifications as $key => &$type) {

                        foreach ($type as $notify => $notifytype) {

                            if ($notifytype == 'out_of_stock') {

                                $description = strtr($type['description'], $placeholder);
                                $type['description'] = $description;
                                $type['url'] = $url_product;
                                $notification_product_out_of_stock[$key] = $type;

                                continue 2;
                            }
                        }
                    }
                    $instance = YITH_Desktop_Notifications()->register_notifications;
                    $instance->add_notification($notification_product_out_of_stock);
                }
            }
        }

        public function yith_wcdn_low_stock($product) {
            $url_product = admin_url('post.php?post=' . absint($product->get_id()) . '&action=edit');
            $notifications = get_option('yith-wcdn-desktop-notifications');
            $notification_product_out_of_stock = array();


            $placeholder = array(
                "{product_id}" => $product->get_id(),
                "{product_name}" => $product->get_title(),
            );

            if ( $notifications && is_array( $notifications ) ) {

                foreach ($notifications as $key => &$type) {

                    foreach ($type as $notify => $notifytype) {

                        if ($notifytype == 'low_stock') {

                            $description = strtr($type['description'], $placeholder);
                            $type['description'] = $description;
                            $type['url'] = $url_product;
                            $notification_product_out_of_stock[$key] = $type;

                            continue 2;
                        }
                    }
                }
                $instance = YITH_Desktop_Notifications()->register_notifications;
                $instance->add_notification($notification_product_out_of_stock);
            }
        }

        public function yith_wcdn_order_refunded($order_id) {
            $url_order = admin_url('post.php?post=' . absint($order_id) . '&action=edit');
            $notifications = get_option('yith-wcdn-desktop-notifications');
            $notification_order_refunded = array();

            $order = wc_get_order($order_id);
            $customer_user = yit_get_prop( $order, '_customer_user', true );

            $placeholder = array(
                "{order_id}" => $order,
                "{order_total}" => $order->get_total(),
                "{username}"    => get_user_meta( $customer_user, 'nickname', true ),
            );

            if ( $notifications && is_array( $notifications ) ) {

                foreach ($notifications as $key => &$type) {

                    foreach ($type as $notify => $notifytype) {

                        if ($notifytype == 'refund') {

                            $description = strtr($type['description'], $placeholder);
                            $type['description'] = $description;
                            $type['url'] = $url_order;
                            $notification_order_refunded[$key] = $type;

                            continue 2;
                        }
                    }
                }
                $instance = YITH_Desktop_Notifications()->register_notifications;
                $instance->add_notification($notification_order_refunded);
            }

        }

        public function yith_wcdn_order_status_changed($order_id, $old_status, $new_status) {


            $url_order = admin_url('post.php?post=' . absint($order_id) . '&action=edit');
            $notifications = get_option('yith-wcdn-desktop-notifications');
            $notification_order_refunded = array();
            $order = wc_get_order($order_id);
            $customer_user = yit_get_prop( $order, '_customer_user', true );

            do_action('yith_wcdn_before_order_notify_status_changed', $order, $notifications);

            if ( $notifications && is_array( $notifications ) ) {


                //Multivendor compatibilities
                $allowed_order = apply_filters('yith_wcdn_allow_order_notify', true, $order, $notifications, $old_status, $new_status);

                if ($allowed_order) {

                    $placeholder = array(
                        "{order_id}" => $order_id,
                        "{order_total}" => $order->get_total(),
                        "{username}" => get_user_meta($customer_user, 'nickname', true),
                        "{old_status}" => $old_status,
                        "{new_status}" => $new_status,
                    );

                    foreach ($notifications as $key => &$type) {

                        foreach ($type as $notify => $notifytype) {

                            if ($notifytype == 'status_changed') {

                                if (isset($type['specific_status']) && is_array($type['specific_status']) && in_array('wc-' . $new_status, $type['specific_status'])) {

                                    $description = strtr($type['description'], $placeholder);
                                    $type['description'] = $description;
                                    $type['url'] = $url_order;
                                    $notification_order_refunded[$key] = $type;
                                }

                                continue 2;
                            }
                        }
                    }
                    $instance = YITH_Desktop_Notifications()->register_notifications;
                    $instance->add_notification($notification_order_refunded);
                }

            }

            do_action('yith_wcdn_after_order_notify_status_changed', $order, $notifications, $old_status, $new_status);

        }

        public function yith_wcdn_product_sold($order_id)
        {

            $url_order = admin_url('post.php?post=' . absint($order_id) . '&action=edit');
            $notifications = get_option('yith-wcdn-desktop-notifications');
            $notification_product_sold = array();
            $order = wc_get_order($order_id);
            $customer_user = yit_get_prop($order, '_customer_user', true);
            $products_order = $order->get_items();
            $products_order_id = array();
            foreach ($products_order as $product) {

                $products_order_id[] = $product['product_id'];
            }

            $placeholder = array(
                "{order_id}" => $order_id,
                "{username}" => get_user_meta($customer_user, 'nickname', true),
            );

            if ( $notifications && is_array( $notifications ) ) {

                foreach ($notifications as $key => &$type) {
                    foreach ($type as $notify => $notifytype) {
                        if ($notifytype == 'sold') {

                            if (isset($type['products']) && !empty($type['products'])) {

                                if (version_compare(WC()->version, '2.7.0', '>=')) {
                                    $product_ids = $type['products'];
                                } else {
                                    $product_ids = array_filter(array_map('absint', explode(',', $type['products'])));
                                }

                                $name_products = '';
                                $in_array = false;
                                foreach ($product_ids as $product) {
                                    if (in_array($product, $products_order_id)) {
                                        $name_products = $name_products . ' ' . wc_get_product($product)->get_title();
                                        $in_array = true;
                                    }
                                }
                                if (apply_filters('yith_wcdn_in_array', $in_array)) {
                                    $placeholder['{products_sold}'] = apply_filters('yith_wcdn_name_products', $name_products, $products_order_id, $product_ids);
                                    $description = strtr($type['description'], $placeholder);
                                    $type['description'] = $description;
                                    $type['url'] = $url_order;
                                    $notification_product_sold[$key] = $type;
                                    $in_array = false;
                                }
                            }

                            continue 2;
                        }
                    }
                }
            }
            $instance = YITH_Desktop_Notifications()->register_notifications;
            $instance->add_notification($notification_product_sold);

        }

    }
}