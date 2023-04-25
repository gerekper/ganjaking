<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_MS_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Multiple Shipping', 'wc_shipping_multiple_address' ) );

		$this->add_exporter( 'woocommerce-shipping-multiple-addresses-order-data', __( 'WooCommerce Multple Shipping Order Data', 'wc_shipping_multiple_address' ), array( $this, 'order_data_exporter' ) );
		$this->add_eraser( 'woocommerce-shipping-multiple-addresses-order-data', __( 'WooCommerce Multiple Shipping Order Data', 'wc_shipping_multiple_address' ), array( $this, 'order_data_eraser' ) );

		$this->add_exporter( 'woocommerce-shipping-multiple-addresses-customer-data', __( 'WooCommerce Multiple Shipping Customer Data', 'wc_shipping_multiple_address' ), array( $this, 'customer_data_exporter' ) );
		$this->add_eraser( 'woocommerce-shipping-multiple-addresses-customer-data', __( 'WooCommerce Multiple Shipping Customer Data', 'wc_shipping_multiple_address' ), array( $this, 'customer_data_eraser' ) );
	}

	/**
	 * Returns a list of orders that are using multiple shipping.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array WP_Post
	 */
	protected function get_s2ma_orders( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$order_query    = array(
			'meta_key'       => '_multiple_shipping',
			'meta_value'     => 'yes',
			'limit'          => 10,
			'page'           => $page,
		);

		if ( $user instanceof WP_User ) {
			$order_query['customer_id'] = (int) $user->ID;
		} else {
			$order_query['billing_email'] = $email_address;
		}

		return wc_get_orders( $order_query );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'wc_shipping_multiple_address' ), 'https://docs.woocommerce.com/document/privacy-shipping/#woocommerce-shipping-multiple-addresses' ) );
	}

	/**
	 * Handle exporting data for Orders.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function order_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();

		$orders = $this->get_s2ma_orders( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$packages = $order->get_meta( '_wcms_packages' );

				foreach ( $packages as $idx => $package ) {
					$products = $package['contents'];
					$address  = ( isset($package['full_address'] ) && ! empty( $package['full_address'] ) ) ? WC()->countries->get_formatted_address( $package['full_address'] ) : '';

					$data  = sprintf( __( 'Products listing for shipping address "%s": ', 'wc_shipping_multiple_address' ), $address );
					$data .= implode( ', ', array_map( function( $product ) {
						return get_the_title( $product['data']->id );
					}, $products ) );

					$order_note = $order->get_meta( '_note_' . $idx );

					if ( ! empty( $order_note ) ) {
						$data .= sprintf( __( '. Note: %s.', 'wc_shipping_multiple_address' ), $order_note );
					}

					$data_to_export[] = array(
						'group_id'    => 'woocommerce_orders',
						'group_label' => __( 'Orders', 'wc_shipping_multiple_address' ),
						'item_id'     => 'order-' . $order->get_id(),
						'data'        => array(
							array(
								'name'  => sprintf( __( 'Multiple Shipping package "%s"', 'wc_shipping_multiple_address' ), $idx ),
								'value' => $packages,
							),
						),
					);
				}

				$query = array(
					'post_type'  => 'order_shipment',
					'meta_key'   => 'post_parent',
					'meta_value' => $order->get_id(),
				);
				$shipment_data = get_posts( $query );

				foreach ( $shipment_data as $post ) {
					$data_to_export[] = array(
						'group_id'    => 'woocommerce_orders',
						'group_label' => __( 'Orders', 'wc_shipping_multiple_address' ),
						'item_id'     => 'order-' . $order->get_id(),
						'data'        => array(
							array(
								'name'  => sprintf( __( 'Multiple Shipping Order Shipment "%s"', 'wc_shipping_multiple_address' ), $post->ID ),
								'value' => $post->post_excerpt,
							),
						),
					);
				}
			}

			$done = 10 > count( $orders );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and exports customer data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function customer_data_exporter( $email_address, $page ) {
		$user           = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$data_to_export = array();

		if ( $user instanceof WP_User ) {
			$data_to_export[] = array(
				'group_id'    => 'woocommerce_customer',
				'group_label' => __( 'Customer Data', 'wc_shipping_multiple_address' ),
				'item_id'     => 'user',
				'data'        => array(
					array(
						'name'  => __( 'Multiple Shipping Addresses', 'wc_shipping_multiple_address' ),
						'value' => wp_json_encode( get_user_meta( $user->ID, 'wc_other_addresses', true ) ),
					),
				),
			);
		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * Finds and erases customer data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function customer_data_eraser( $email_address, $page ) {
		$page = (int) $page;
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$other_addresses = get_user_meta( $user->ID, 'wc_other_addresses', true );

		$items_removed  = false;
		$messages       = array();

		if ( ! empty( $other_addresses ) ) {
			$items_removed = true;
			delete_user_meta( $user->ID, 'wc_other_addresses' );
			$messages[] = __( 'Multiple Shipping User Data Erased.', 'wc_shipping_multiple_address' );
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => $messages,
			'done'           => true,
		);
	}

	/**
	 * Finds and erases order data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function order_data_eraser( $email_address, $page ) {
		$orders = $this->get_s2ma_orders( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $orders as $order ) {
			$order = wc_get_order( $order->get_id() );

			list( $removed, $retained, $msgs ) = $this->maybe_handle_order( $order );
			$items_removed  |= $removed;
			$items_retained |= $retained;
			$messages        = array_merge( $messages, $msgs );
		}

		// Tell core if we have more orders to work on still
		$done = count( $orders ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Handle eraser of data tied to Orders
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	protected function maybe_handle_order( $order ) {
		global $wpdb;

		$order_id          = $order->get_id();

		$packages          = $order->get_meta( '_shipping_packages' );
		$sess_item_address = $order->get_meta( '_shipping_addresses' );
		$sess_packages     = $order->get_meta( '_wcms_packages' );
		$ms_methods        = $order->get_meta( '_shipping_methods' );
		$sess_rates        = $order->get_meta( '_shipping_rates' );

		if ( empty( $packages ) && empty( $sess_item_address ) && empty( $sess_packages ) && empty( $ms_methods ) && empty( $sess_rates ) ) {
			return array( false, false, array() );
		}

		$query = array(
			'post_type'  => 'order_shipment',
			'meta_key'   => 'post_parent',
			'meta_value' => $order_id,
		);

		$shipment_data = get_posts( $query );

		foreach ( $shipment_data as $post ) {
			$wpdb->delete_post( $post->ID, true );
		}

		foreach ( $packages as $idx => $package ) {
			$order->delete_meta_data( '_note_' . $idx );
			$order->delete_meta_data( '_date_' . $idx );
		}

		$order->delete_meta_data( '_shipping_packages' );
		$order->delete_meta_data( '_shipping_addresses' );
		$order->delete_meta_data( '_wcms_packages' );
		$order->delete_meta_data( '_shipping_methods' );
		$order->delete_meta_data( '_shipping_rates' );
		$order->save();

		return array( true, false, array( __( 'Multiple Shipping Order Data Erased.', 'wc_shipping_multiple_address' ) ) );
	}
}

new WC_MS_Privacy();
