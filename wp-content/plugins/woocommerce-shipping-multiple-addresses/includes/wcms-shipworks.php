<?php

if (! defined('ABSPATH')) {
    exit;
}

class WC_MS_Shipworks {

    public function __construct() {
        add_filter( 'se_woocommerce_order_rows', array( $this, 'send_split_orders' ), 10, 2 );
        add_filter( 'sc_orders_rows', array( $this, 'send_split_orders' ), 10, 2 );
        add_filter( 'woocommerce_new_customer_note', array( $this, 'customer_note_added' ), 1 );
    }

    public function send_split_orders( $rows, $orders ) {
        global $wpdb;

        $new_rows = array();

        foreach ( $rows as $i => $row ) {
            $ids = WC_MS_Order_Shipment::get_by_order( $row['ID'] );

            if ( count( $ids ) > 0 ) {
                foreach ( $ids as $id ) {
                    $shipment = $wpdb->get_row( $wpdb->prepare(
                        "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID = %d",
                        $id
                    ), ARRAY_A );

                    $new_rows[] = $shipment;
                }

                unset( $rows[ $i ] );

            }
        }

        $rows = array_merge( $rows, $new_rows );

        return $rows;
    }

    public function customer_note_added( $data ) {
        global $wpdb;

        $post = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE id = %d", $data['order_id'] ));

        if ( $post && $post->post_type == 'order_shipment' ) {
            $parent_id = $post->post_parent;

            $is_customer_note = intval( 1 );

            if ( isset( $_SERVER['HTTP_HOST'] ) )
                $comment_author_email 	= sanitize_email( strtolower( esc_html__( 'WooCommerce', 'wc_shipping_multiple_address' ) ) . '@' . str_replace( 'www.', '', sanitize_text_field( $_SERVER['HTTP_HOST'] ) ) );
            else
                $comment_author_email 	= sanitize_email( strtolower( esc_html__( 'WooCommerce', 'wc_shipping_multiple_address' ) ) . '@noreply.com' );

            $comment_post_ID 		= $parent_id;
            $comment_author 		= __( 'WooCommerce', 'wc_shipping_multiple_address' );
            $comment_author_url 	= '';
            $comment_content 		= $data['customer_note'];
            $comment_agent			= 'WooCommerce';
            $comment_type			= 'order_note';
            $comment_parent			= 0;
            $comment_approved 		= 1;
            $commentdata 			= compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved' );

            $comment_id = wp_insert_comment( $commentdata );

            add_comment_meta( $comment_id, 'is_customer_note', $is_customer_note );
        }
    }
}

new WC_MS_Shipworks();
