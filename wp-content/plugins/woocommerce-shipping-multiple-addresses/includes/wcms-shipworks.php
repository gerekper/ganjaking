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
        $new_rows = array();

        foreach ( $rows as $i => $row ) {
			$shipments = WC_MS_Order_Shipment::get_shipment_objects_by_order( $row['ID'] );

            if ( count( $shipments ) > 0 ) {
                foreach ( $shipments as $shipment ) {
	                $order_note_count = count( wc_get_order_notes( array( 'order_id' => $shipment->get_id() ) ) );

                    $new_rows[] = array(
						'ID'                    => $shipment->get_id(),
	                    // WC always sets this to 1 when using the CPT data store for orders.
						'post_author'           => 1,
						'post_date'             => gmdate( 'Y-m-d H:i:s', $shipment->get_date_created( 'edit' )->getOffsetTimestamp() ),
						'post_date_gmt'         => gmdate( 'Y-m-d H:i:s', $shipment->get_date_created( 'edit' )->getTimestamp() ),
						'post_content'          => '',
						'post_title'            => sprintf( __( 'Shipment &ndash; %s', 'wc_shipping_multiple_address' ), date( _x( 'M d, Y @ H:i A', 'Order date parsed by date function', 'wc_shipping_multiple_address' ) ) ),
						'post_excerpt'          => $shipment->get_customer_note(),
						'post_status'           => $shipment->get_status(),
						'comment_status'        => 'open',
						'ping_status'           => 'closed',
						'post_password'         => $shipment->get_order_key(),
						'post_name'             => sprintf( __( 'shipment-%s', 'woocommerce-shipping-multiple-addresses' ), $shipment->get_id() ),
						'to_ping'               => '',
						'pinged'                => '',
						'post_modified'         => gmdate( 'Y-m-d H:i:s', $shipment->get_date_modified( 'edit' )->getOffsetTimestamp() ),
						'post_modified_gmt'     => gmdate( 'Y-m-d H:i:s', $shipment->get_date_modified( 'edit' )->getTimestamp() ),
						'post_content_filtered' => '',
						'post_parent'           => $shipment->get_parent_id(),
						'guid'                  => $shipment->get_view_order_url(),
						'menu_order'            => 0,
						'post_type'             => $shipment->get_type(),
						'post_mime_type'        => '',
	                    'comment_count'         => $order_note_count,
                    );
                }

                unset( $rows[ $i ] );

            }
        }

        $rows = array_merge( $rows, $new_rows );

        return $rows;
    }

	/**
	 * Add customer note into the order object.
	 *
	 * @param array $data Array of Order ID and customer note.
	 */
	public function customer_note_added( $data ) {
		$order = wc_get_order( $data['order_id'] );

		if ( false === $order || 'order_shipment' !== $order->get_type() ) {
			return;
		}

		$parent_order = wc_get_order( $order->get_parent_id() );

		if ( false === $parent_order ) {
			return;
		}

		$parent_order->add_order_note( $data['customer_note'], 1 );
	}
}

new WC_MS_Shipworks();
