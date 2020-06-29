<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Advanced_Refund_System_Request_Manager_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Advanced_Refund_System_Request_Manager_Premium' ) ) {
    /**
     * Class YITH_Advanced_Refund_System_Request_Manager_Premium
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Advanced_Refund_System_Request_Manager_Premium extends YITH_Advanced_Refund_System_Request_Manager {
        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         */
        public function __construct() {
	        parent::__construct();
	        add_action( 'wp_ajax_ywcars_create_coupon', array( $this, 'create_coupon' ) );

        }

	    public function create_coupon() {

		    check_ajax_referer( 'create-coupon', 'security' );

		    $request_id = ! empty( $_POST['ywcars_request_id'] ) ? $_POST['ywcars_request_id'] : false;
		    $amount     = ! empty( $_POST['amount'] ) ? $_POST['amount'] : false;

		    try {
			    if ( ! current_user_can( 'publish_shop_coupons' ) ) {
				    throw new Exception( esc_html__( 'You do not have permission to create coupons.', 'yith-advanced-refund-system-for-woocommerce' ) );
			    }

			    $request = new YITH_Refund_Request( $request_id );
			    if ( ! $request->exists() ) {
				    throw new Exception( esc_html__( 'Refund request does not exist.', 'yith-advanced-refund-system-for-woocommerce' ) );
			    }

			    $code = get_option( 'yith_wcars_coupon_code' );

			    if ( ! $code ) {
				    $code = 'refund_{request_id}';
			    }

			    // Get customer email
			    $order = wc_get_order( $request->order_id );
			    if ( ! $order ) {
				    throw new Exception( esc_html__( 'Order does not exist.', 'yith-advanced-refund-system-for-woocommerce' ) );
			    }
			    $email = $order instanceof WC_Data ? $order->get_billing_email() : $order->billing_email;

			    $coupon_code = str_replace(
				    array( '{customer_email}', '{request_id}', '{coupon_amount}', '{order_number}' ),
				    array( $email, $request_id, $amount, $request->order_id ),
				    $code
			    );


			    $new_coupon = array(
				    'post_title'   => $coupon_code,
				    'post_content' => '',
				    'post_status'  => 'publish',
				    'post_author'  => get_current_user_id(),
				    'post_type'    => 'shop_coupon',
				    'post_excerpt' => ''
			    );

			    $id = wp_insert_post( $new_coupon, true );

			    if ( is_wp_error( $id ) ) {
				    throw new Exception( esc_html__( 'Error: invalid data when creating coupon.', 'yith-advanced-refund-system-for-woocommerce' ) );
			    }

			    global $wpdb;
			    if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
				    $coupon_code = apply_filters( 'woocommerce_coupon_code', $coupon_code );
				    $wpdb->update( $wpdb->posts, array( 'post_title' => $coupon_code ), array( 'ID' => $id ) );

				    $coupon_found = $wpdb->get_var(
					    $wpdb->prepare(
						    " SELECT $wpdb->posts.ID FROM $wpdb->posts WHERE $wpdb->posts.post_type = 'shop_coupon'
			                          AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_title = '%s'
			                          AND $wpdb->posts.ID != %s", $coupon_code, $id
					    )
				    );
			    } else {
				    $coupon_code  = wc_format_coupon_code( $coupon_code );
				    $coupon_found = wc_get_coupon_id_by_code( $coupon_code, $id );
			    }

			    if ( $coupon_found ) {
				    $coupon_code = $coupon_code . '_' . uniqid();
				    $coupon_code = apply_filters( 'woocommerce_coupon_code', $coupon_code );
				    $wpdb->update( $wpdb->posts, array( 'post_title' => $coupon_code ), array( 'ID' => $id ) );
				    clean_post_cache( $id );
			    }

			    // Expiration date
			    $expiry_date_ndays = get_option( 'yith_wcars_expiry_date', 0 );
			    $expiry_date = $expiry_date_ndays ? strtotime( sprintf( '+%d days', $expiry_date_ndays ) ) : '';
			    $date_expires = $expiry_date ? date( 'Y-m-d', $expiry_date ) : '';

			    update_post_meta( $id, 'discount_type', 'fixed_cart' );
			    update_post_meta( $id, 'coupon_amount', wc_format_decimal( $amount ) );
			    update_post_meta( $id, 'individual_use', 'yes' );
			    update_post_meta( $id, 'product_ids', '' );
			    update_post_meta( $id, 'exclude_product_ids', '' );
			    update_post_meta( $id, 'usage_limit', 1 );
			    update_post_meta( $id, 'usage_limit_per_user', 1 );
			    update_post_meta( $id, 'limit_usage_to_x_items', 0 );
			    update_post_meta( $id, 'usage_count', 0 );
			    update_post_meta( $id, 'date_expires', $date_expires );
			    update_post_meta( $id, 'expiry_date', $expiry_date );
			    update_post_meta( $id, 'free_shipping', 'no' );
			    update_post_meta( $id, 'product_categories',  array() );
			    update_post_meta( $id, 'exclude_product_categories',  array() );
			    update_post_meta( $id, 'exclude_sale_items', 'no' );
			    update_post_meta( $id, 'minimum_amount', '' );
			    update_post_meta( $id, 'maximum_amount', '' );
			    update_post_meta( $id, 'customer_email', array( $email ) );
			    update_post_meta( $id, 'ywcars_coupon', 'yes' );

			    $request->coupon_id = $id;
			    $request->set_coupon_offered();
			    wp_send_json_success();
		    } catch ( Exception $e ) {
			    wp_send_json_error( array( 'error' => $e->getMessage() ) );
		    }
	    }

    }
}