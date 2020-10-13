<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Coupons {

    public static function init() {
        // refund order item
        add_action( 'admin_post_warranty_send_coupon', 'Warranty_Coupons::send_coupon' );
    }

    public static function send_coupon() {
        global $wpdb;

        check_admin_referer( 'warranty_send_coupon' );

        $warranty_id    = absint( $_REQUEST['id'] );
        $is_ajax        = (isset( $_REQUEST['ajax'] )) ? true : false;
        $product_id     = get_post_meta( $warranty_id, '_product_id', true );
        $qty            = get_post_meta( $warranty_id, '_qty', true );
        $order_item_key = get_post_meta( $warranty_id, '_index', true );
        $product        = wc_get_product( $product_id );
        $order_id       = get_post_meta( $warranty_id, '_order_id', true );
        $order          = wc_get_order( $order_id );
        $email          = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_email' );

        $coupon_amount  = !empty( $_REQUEST['amount'] ) ? $_REQUEST['amount'] : wc_get_order_item_meta( $order_item_key, '_line_total', true );

        $coupon_code    = self::generate_coupon_code();
        $coupon_array   = array(
            'post_title'    => $coupon_code,
            'post_author'   => 1,
            'post_date'     => date("Y-m-d H:i:s"),
            'post_status'   => 'publish',
            'comment_status'=> 'closed',
            'ping_status'   => 'closed',
            'post_name'     => $coupon_code,
            'post_parent'   => 0,
            'menu_order'    => 0,
            'post_type'     => 'shop_coupon'
        );
        $coupon_id = wp_insert_post($coupon_array);

        update_post_meta($coupon_id, 'discount_type', 'fixed_cart');
        update_post_meta($coupon_id, 'coupon_amount', $coupon_amount);
        update_post_meta($coupon_id, 'individual_use', 'no');
        update_post_meta($coupon_id, 'product_ids', array());
        update_post_meta($coupon_id, 'exclude_product_ids', array());
        update_post_meta($coupon_id, 'usage_count', '0');
        update_post_meta($coupon_id, 'usage_limit', '1');
        update_post_meta($coupon_id, 'usage_limit_per_user', '1');
        update_post_meta($coupon_id, 'expiry_date', '');
        update_post_meta($coupon_id, 'free_shipping', 'no');
        update_post_meta($coupon_id, 'exclude_sale_items', 'no');
        update_post_meta($coupon_id, 'product_categories', array());
        update_post_meta($coupon_id, 'product_ids', serialize(array()));
        update_post_meta($coupon_id, 'exclude_product_categories', array());
        update_post_meta($coupon_id, 'exclude_product_ids', '');
        update_post_meta($coupon_id, 'minimum_amount', '');
        update_post_meta($coupon_id, 'maximum_amount', '');
        update_post_meta($coupon_id, 'customer_email', $email);

        $refunded_amount = get_post_meta( $warranty_id, '_refund_amount', true );

        if ( !$refunded_amount ) {
            $refunded_amount = 0;
        }
        $refunded_amount += $coupon_amount;

        $data = array(
            'refund_amount' => $refunded_amount,
            'coupon_sent'   => 'yes',
            'coupon_code'   => $coupon_code,
            'coupon_amount' => $coupon_amount,
            'coupon_date'   => current_time('mysql')
        );
        warranty_update_request( $warranty_id, $data );

        $return_message = __('Coupon sent', 'wc_warranty');

        warranty_send_emails( $warranty_id, 'coupon_sent' );

        if ( !$is_ajax ) {
            wp_redirect( 'admin.php?page=warranties&updated='. urlencode( $return_message ) );
            exit;
        }

        wp_send_json( array(
            'status'    => 'OK',
            'message'   => $return_message
        ) );
    }

    /**
     * Generate a random 8-character unique string that's to used as a coupon code
     * @return string
     */
    public static function generate_coupon_code() {
        global $wpdb;

        $chars = 'abcdefghijklmnopqrstuvwxyz01234567890';
        do {
            $code = '';
            for ($x = 0; $x < 8; $x++) {
                $code .= $chars[ rand(0, strlen($chars)-1) ];
            }

            $check = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_title = %s AND post_type = 'shop_coupon'", $code));

            if ($check == 0) {
                break;
            }
        } while (true);

        return $code;
    }

}

Warranty_Coupons::init();
