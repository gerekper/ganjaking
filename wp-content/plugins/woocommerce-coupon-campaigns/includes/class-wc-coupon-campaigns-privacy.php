<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Coupon_Campaigns_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( __( 'Coupon Campaigns', 'wc_coupon_campaigns' ) );

		$this->add_eraser( 'woocommerce-coupon-campaigns-order-data', __( 'WooCommerce Coupons Data', 'wc_coupon_campaigns' ), array( $this, 'order_data_eraser' ) );

		$this->add_exporter( 'woocommerce-coupon-campaigns-notes-data', __( 'WooCommerce Coupon Notes Data', 'wc_coupon_campaigns' ), array( $this, 'coupon_note_data_exporter' ) );
		$this->add_eraser( 'woocommerce-coupon-campaigns-notes-data', __( 'WooCommerce Coupon Notes Data', 'wc_coupon_campaigns' ), array( $this, 'coupon_note_data_eraser' ) );
	}

	/**
	 * Returns a list of orders that have post meta in coupons by Coupon Campaigns.
	 *
	 * @param string $email_address
	 * @param int    $page
	 *
	 * @return array WP_Post
	 */
	protected function get_orders_with_coupons( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$order_query = array(
			'limit' => 10,
			'page'  => $page,
		);

		if ( $user instanceof WP_User ) {
			$order_query['customer_id'] = (int) $user->ID;
		} else {
			$order_query['billing_email'] = $email_address;
		}

		$orders = wc_get_orders( $order_query );

		return array_filter(
			$orders,
			function( $order ) {
				$order_id = $order->get_id();

				$coupon_ids = array_map(
					function( $coupon ) {
						return $coupon->get_id();
					},
					$order->get_items( 'coupon' )
				);

				foreach ( $coupon_ids as $coupon_id ) {
					$coupon_orders = get_post_meta( $coupon_id, '_coupon_orders', true );

					if ( ! empty( $coupon_orders ) ) {
						return true;
					}
				}

				return false;
			}
		);
	}

	/**
	 * Returns a list of orders that have post meta in coupons by Coupon Campaigns.
	 *
	 * @param string $email_address
	 * @param int    $page
	 *
	 * @return array WP_Post
	 */
	protected function get_coupon_notes( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$comment_query = array(
			'number' => 10,
			'offset' => $page,
			'type'   => 'coupon_note',
		);

		if ( $user instanceof WP_User ) {
			$comment_query['user_id'] = (int) $user->ID;
		} else {
			$comment_query['author_email'] = $email_address;
		}

		return get_comments( $comment_query );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		/* translators: %s - marketplace link */
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'wc_coupon_campaigns' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-coupon-campaigns' ) );
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
		$orders = $this->get_orders_with_coupons( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $orders as $order ) {
			$order = wc_get_order( $order->get_id() );

			list( $removed, $retained, $msgs ) = $this->maybe_handle_order_coupons( $order );
			$items_removed                    |= $removed;
			$items_retained                   |= $retained;
			$messages                          = array_merge( $messages, $msgs );
		}

		// Tell core if we have more orders to work on still.
		$done = count( $orders ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Handle eraser of coupon data tied to Orders
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	protected function maybe_handle_order_coupons( $order ) {
		$order_id = $order->get_id();

		$coupon_ids = array_map(
			function( $coupon ) {
				return $coupon->get_id();
			},
			$order->get_items( 'coupon' )
		);

		// Assume no custom coupon data exists.
		$coupon_data = false;

		foreach ( $coupon_ids as $coupon_id ) {
			$coupon_orders = get_post_meta( $coupon_id, '_coupon_orders', true );

			if ( empty( $coupon_orders ) ) {
				continue;
			}

			unset( $coupon_orders[ $order_id ] );

			update_post_meta( $coupon_id, '_coupon_orders', $coupon_orders );

			$coupon_data = true;
		}

		if ( $coupon_data ) {
			return array( true, false, array( __( 'Coupon Campaigns Order Data Erased.', 'wc_coupon_campaigns' ) ) );
		}

		return array( false, false, array() );
	}

	/**
	 * Handle exporting data for notes.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function coupon_note_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();

		$notes = $this->get_coupon_notes( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $notes ) ) {
			foreach ( $notes as $note ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_coupon_notes',
					'group_label' => __( 'Coupon Campaigns Notes', 'wc_coupon_campaigns' ),
					'item_id'     => 'coupon-notes-' . $note->ID,
					'data'        => array(
						array(
							'name'  => __( 'Coupons comment ID', 'wc_coupon_campaigns' ),
							'value' => $note->ID,
						),
						array(
							'name'  => __( 'Coupons comment content', 'wc_coupon_campaigns' ),
							'value' => $note->comment_content,
						),
						array(
							'name'  => __( 'Coupons comment IP', 'wc_coupon_campaigns' ),
							'value' => $note->comment_author_IP,
						),
					),
				);
			}

			$done = 10 > count( $notes );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases notes data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function coupon_note_data_eraser( $email_address, $page ) {
		$notes = $this->get_coupon_notes( $email_address, 1 );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $notes as $note ) {
			wp_delete_comment( $note->ID, true );
			$items_removed |= true;
		}

		if ( $items_removed ) {
			$messages[] = __( 'Coupon Campaigns Order Data Erased.', 'wc_coupon_campaigns' );
		}

		// Tell core if we have more notes to work on still.
		$done = count( $notes ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}
}

new WC_Coupon_Campaigns_Privacy();
