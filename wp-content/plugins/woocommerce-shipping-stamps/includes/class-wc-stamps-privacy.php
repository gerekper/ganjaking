<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Stamps_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Stamps', 'woocommerce-shipping-stamps' ) );

		$this->add_exporter( 'woocommerce-stamps-order-data', __( 'WooCommerce Stamps Order Data', 'woocommerce-shipping-stamps' ), array( $this, 'order_data_exporter' ) );

		$this->add_eraser( 'woocommerce-stamps-order-data', __( 'WooCommerce Stamps Data', 'woocommerce-shipping-stamps' ), array( $this, 'order_data_eraser' ) );

		$this->add_exporter( 'woocommerce-stamps-label-data', __( 'WooCommerce Stamps Label Data', 'woocommerce-shipping-stamps' ), array( $this, 'label_data_exporter' ) );

		$this->add_eraser( 'woocommerce-stamps-label-data', __( 'WooCommerce Stamps Label Data', 'woocommerce-shipping-stamps' ), array( $this, 'label_data_eraser' ) );
	}

	/**
	 * Returns a list of orders.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array WP_Post
	 */
	protected function get_orders( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$order_query = array(
			'limit'          => 10,
			'page'           => $page,
		);

		if ( $user instanceof WP_User ) {
			$order_query['customer_id'] = $user->ID;
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
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-shipping-stamps' ), 'https://docs.woocommerce.com/document/privacy-shipping/#woocommerce-shipping-stamps' ) );
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
		$orders = $this->get_orders( $email_address, (int) $page );

		$data_to_export = array();
		$done = true;

		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_orders',
					'group_label' => __( 'Orders', 'woocommerce-shipping-stamps' ),
					'item_id'     => 'order-' . $order->get_id(),
					'data'        => array(
						array(
							'name'  => __( 'Stamps last label tx id', 'woocommerce-shipping-stamps' ),
							'value' => $order->get_meta( '_last_label_tx_id' ),
						),
						array(
							'name'  => __( 'Stamps response', 'woocommerce-shipping-stamps' ),
							'value' => $order->get_meta( '_stamps_response' ),
						),
						array(
							'name'  => __( 'Stamps hash', 'woocommerce-shipping-stamps' ),
							'value' => $order->get_meta( '_stamps_hash' ),
						),
						array(
							'name'  => __( 'Stamps verified address hash', 'woocommerce-shipping-stamps' ),
							'value' => $order->get_meta( '_stamps_verified_address_hash' ),
						),
					),
				);
			}

			$done = 10 > count( $orders );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases order data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function order_data_eraser( $email_address, $page ) {
		$orders = $this->get_orders( $email_address, (int) $page );

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
	 *
	 * @return array
	 */
	protected function maybe_handle_order( $order ) {
		$label_tx_id  = $order->get_meta( '_last_label_tx_id' );
		$response     = $order->get_meta( '_stamps_response' );
		$hash         = $order->get_meta( '_stamps_hash' );
		$address_hash = $order->get_meta( '_stamps_verified_address_hash' );

		if ( empty( $label_tx_id ) && empty( $response ) && empty( $hash ) && empty( $address_hash ) ) {
			return array( false, false, array() );
		}

		$order->delete_meta_data( '_last_label_tx_id' );
		$order->delete_meta_data( '_stamps_response' );
		$order->delete_meta_data( '_stamps_hash' );
		$order->delete_meta_data( '_stamps_verified_address_hash' );

		$order->save();

		return array( true, false, array() );
	}

	/**
	 * Gets all the label ids associated with order.
	 *
	 * @return array $label_ids
	 */
	public function get_order_label_ids( $order_id ) {
		$label_ids = get_posts( array(
			'posts_per_page' => -1,
			'post_type'      => 'wc_stamps_label',
			'fields'         => 'ids',
			'post_parent'    => $order_id,
		) );

		return $label_ids;
	}

	/**
	 * Handle exporting data for Labels.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function label_data_exporter( $email_address, $page = 1 ) {
		$data_to_export = array();

		$orders = $this->get_orders( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$label_ids = $this->get_order_label_ids( $order->get_id() );

				if ( ! empty( $label_ids ) ) {
					foreach ( $label_ids as $id ) {
						$data_to_export[] = array(
							'group_id'    => 'woocommerce_stamp_labels',
							'group_label' => __( 'Stamps Label', 'woocommerce-shipping-stamps' ),
							'item_id'     => 'stamps-label-' . $id,
							'data'        => array(
								array(
									'name'  => __( 'Stamps label tx id', 'woocommerce-shipping-stamps' ),
									'value' => get_post_meta( $id, 'StampsTxID', true ),
								),
							),
						);
					}
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
	 * Finds and erases label data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function label_data_eraser( $email_address, $page ) {
		$orders = $this->get_orders( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $orders as $order ) {
			$order = wc_get_order( $order->get_id() );

			list( $removed, $retained, $msgs ) = $this->maybe_handle_label( $order );
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
	protected function maybe_handle_label( $order ) {
		$order_id  = $order->get_id();
		$label_ids = $this->get_order_label_ids( $order_id );

		if ( empty( $label_ids ) || false === $label_ids ) {
			return array( false, false, array() );
		}

		foreach ( $label_ids as $id ) {
			delete_post_meta( $id, 'StampsTxID' );
		}

		return array( true, false, array( __( 'Stamps personal data erased.', 'woocommerce-shipping-stamps' ) ) );
	}
}

new WC_Stamps_Privacy();
