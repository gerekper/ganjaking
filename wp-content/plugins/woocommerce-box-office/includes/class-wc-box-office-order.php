<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Order {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add ticket info to order item meta.
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			add_action( 'woocommerce_new_order_item', array( $this, 'add_order_item_meta' ), 50, 3 );
		} else {
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta' ), 50, 2 );
		}

		add_filter( 'woocommerce_attribute_label', array( $this, 'filter_order_item_meta_ticket' ), 10, 2 );

		// Filter displayed order items meta from WC_Order.
		add_filter( 'woocommerce_order_items_meta_display', array( $this, 'filter_order_items_meta_display' ) );

		// Process completed orders.
		add_action( 'woocommerce_order_status_processing', array( $this, 'publish_tickets' ), 10, 1 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'publish_tickets' ), 10, 1 );

		// Proces orders that used to be completed or processing and now are on-hold or pending.
		add_action( 'woocommerce_order_status_processing_to_on-hold', array( $this, 'maybe_unpublish_tickets' ), 10, 1 );
		add_action( 'woocommerce_order_status_completed_to_on-hold', array( $this, 'maybe_unpublish_tickets' ), 10, 1 );
		add_action( 'woocommerce_order_status_processing_to_pending', array( $this, 'maybe_unpublish_tickets' ), 10, 1 );
		add_action( 'woocommerce_order_status_completed_to_pending', array( $this, 'maybe_unpublish_tickets' ), 10, 1 );

		// When an order is cancelled/fully refunded, trash the tickets.
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'trash_tickets' ), 10, 1 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'trash_tickets' ), 10, 1 );
		add_action( 'woocommerce_order_status_pending_to_failed', array( $this, 'trash_tickets' ), 10, 1 );
		add_action( 'woocommerce_order_status_on-hold_to_failed', array( $this, 'trash_tickets' ), 10, 1 );

		// Status transitions.
		add_action( 'before_delete_post', array( $this, 'delete_tickets' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_tickets' ) );
		add_action( 'untrash_post', array( $this, 'untrash_tickets' ) );

		// Display purchased tickets.
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'order_details_ticket_list' ), 10, 1 );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'order_email_ticket_list' ), 10, 1 );

		// Alter the order again items to include all tickets
		add_action( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_add_tickets' ), 10, 3 );
		// Filter order items that will be send to gateway.
		add_action( 'woocommerce_before_pay_action', array( $this, 'filter_order_items_meta_to_gateway' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'filter_order_items_meta_to_gateway' ) );

		// Barcodes in checkout for purchased tickets.
		add_action( 'woocommerce_after_order_notes', array( $this, 'maybe_create_barcode_fields' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'maybe_process_barcode_fields' ) );

		// Logic to not strip HTML tags in the order item meta so that ticket HTML is properly shown
		add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'process_ticket_display_meta' ), 10, 3 );
	}

	/**
	 * Add ticket meta as order item meta.
	 *
	 * @param mixed $item_id  Item ID.
	 * @param mixed $values   Item meta.
	 * @param mixed $order_id Order ID.
	 */
	public function add_order_item_meta( $item_id, $values, $order_id = 0 ) {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			$ticket_meta = ! empty( $values->legacy_values['ticket'] )
				? $values->legacy_values['ticket']
				: array();
			$qty = ! empty( $values->legacy_values['quantity'] )
				? $values->legacy_values['quantity']
				: array();
		} else {
			$ticket_meta = ! empty( $values['ticket'] )
				? $values['ticket']
				: array();
			$qty = ! empty( $values['quantity'] )
				? $values['quantity']
				: 0;
		}

		if ( empty( $ticket_meta['fields'] ) ) {
			return;
		}

		// Make sure number of tickets to create matches with quantity in cart.
		if ( count( $ticket_meta['fields'] ) > $qty ) {
			// Remove extra tickets from last position in case number of tickets
			// higher than quantity in cart.
			$ticket_meta['fields'] = array_slice( $ticket_meta['fields'], 0, $qty );
		} elseif ( count( $ticket_meta['fields'] ) < $qty ) {
			// add_to_cart may replaces item meta if generate_cart_id generates
			// the same id because the same meta. See issue #60.
			//
			// Duplicate first ticket.
			$ticket_to_copy = current( $ticket_meta['fields'] );
			if ( $ticket_to_copy ) {
				for ( $i = count( $ticket_meta['fields'] ); $i < $qty; $i++ ) {
					$ticket_meta['fields'][] = $ticket_to_copy;
				}
			}
		}

		// Get all tickets related to the order.
		$tickets         = $this->get_tickets_by_order( $order_id );
		$order           = wc_get_order( $order_id );
		$billing_address = $order->get_address( 'billing' );
		$ticket_fields   = wc_box_office_get_product_ticket_fields( (int) $ticket_meta['product_id'] );

		foreach ( $ticket_meta['fields'] as $index => $fields ) {

			/**
			 * Populate values from billing information if autofill is enabled on ticket field.
			 *
			 * @see https://github.com/woocommerce/woocommerce-box-office/issues/318
			 */
			foreach ( $fields as $field_key => $field_value ) {
				$field_option = $ticket_fields[ $field_key ];
				$autofill     = str_replace( 'billing_', '', $field_option['autofill'] );
				if (
					empty( $field_value ) &&
					'none' !== $field_option['autofill'] &&
					isset( $billing_address[ $autofill ] ) &&
					! empty( $billing_address[ $autofill ] )
				) {
					// For Radio, Select and Checkbox fields, only populate value it is if available in given options.
					if ( in_array( $field_option['type'], array( 'radio', 'select', 'checkbox' ), true ) && ! empty( $field_option['options'] ) ) {
						if ( in_array( $billing_address[ $autofill ], explode( ',', $field_option['options'] ), true ) ) {
							$fields[ $field_key ] = $billing_address[ $autofill ];
						}
					} else {
						$fields[ $field_key ] = $billing_address[ $autofill ];
					}
				}
			}

			// Check if a ticket was already created at some point, and trash it (since it will be re-created).
			foreach ( $tickets as $ticket ) {
				$ticket            = new WC_Box_Office_Ticket( $ticket );
				$product_id        = (int) $ticket_meta['product_id'];
				$ticket_product_id = (int) $ticket->product_id;
				if ( is_a( $ticket->product, 'WC_Product_Variable' ) ) {
					$product_id        = (int) $ticket_meta['variation_id'];
					$ticket_product_id = (int) $ticket->variation_id;
				}

				if (
					array_keys( $ticket->fields ) === array_keys( $fields ) &&
					$ticket_product_id === $product_id
				) {
					wp_delete_post( $ticket->id );
				}
			}

			$ticket_data = array_merge(
				$ticket_meta,
				array(
					'fields'        => $fields,
					'order_item_id' => $item_id,
				)
			);
			$ticket = new WC_Box_Office_Ticket( $ticket_data );
			$ticket->create( 'pending' );

			wc_add_order_item_meta(
				$item_id,
				sprintf( '_ticket_id_for_%1$s_%2$s', $ticket_meta['key'], $index ),
				$ticket->id
			);

			wc_add_order_item_meta(
				$item_id,
				// wp_kses_post removed data attribute, so we use class to
				// store ticket-id. This span will be turned into link to
				// edit ticket in edit order.
				sprintf( '<span class="order-item-meta-ticket ticket-id-%d">%s%d</span>', $ticket->id, wcbo_get_ticket_title_prefix(), $index + 1 ),
				wc_box_office_get_ticket_description( $ticket->id, 'list' )
			);
		}
	}

	/**
	 * Update order item meta with ticket info specified by `$ticket_id`.
	 *
	 * The order item meta contains ticket fields information which is stored
	 * as raw HTML string. The meta key contains HTML string too but with ticket
	 * ID. When ticket is updated either via admin or front-end, updated info
	 * should be propagated to order item meta too.
	 *
	 * @since 1.1.4
	 * @version 1.1.4
	 *
	 * @see https://github.com/woocommerce/woocommerce-box-office/issues/180.
	 *
	 * @param int $ticket_id Ticket ID.
	 *
	 * @return bool True if updated successfully.
	 */
	public function update_item_meta_from_ticket( $ticket_id ) {
		if ( 'event_ticket' !== get_post_type( $ticket_id ) ) {
			return false;
		}

		$order_id = get_post_meta( $ticket_id, '_order', true );
		$order    = wc_get_order( $order_id );
		if ( ! $order ) {
			return false;
		}

		$product_id = get_post_meta( $ticket_id, '_product_id', true );
		if ( ! $product_id ) {
			return false;
		}

		$updated = false;

		$is_pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
		foreach ( $order->get_items() as $item_id => $item ) {
			$item_product_id = $is_pre_wc_30 ? $item['product_id'] : $item->get_product_id();
			if ( (int) $product_id !== (int) $item_product_id ) {
				continue;
			}

			$meta_data = $is_pre_wc_30 ? $item['item_meta'] : $item->get_meta_data();
			foreach ( $meta_data as $key => $meta_value ) {
				$meta_key = $is_pre_wc_30 ? $key : $meta_value->key;
				if ( strpos( $meta_key, 'order-item-meta-ticket ticket-id-' . $ticket_id ) !== false ) {
					$updated = wc_update_order_item_meta(
						$item_id,
						$meta_key,
						wc_box_office_get_ticket_description( $ticket_id, 'list' )
					);
				}
			}
		}

		return $updated;
	}

	public function filter_order_item_meta_ticket( $label, $name ) {
		if ( strpos( $name, 'class="order-item-meta-ticket' ) !== false ) {
			return $name;
		}

		return $label;
	}

	/**
	 * Removed order items meta that contains ticket fields in markup. It won't
	 * nicely displayed in PayPal.
	 *
	 * @see https://github.com/woothemes/woocommerce-box-office/issues/106
	 *
	 * @param string $output Output of items meta to display
	 *
	 * @return string Returns empty string if output contains markup of ticket fields
	 */
	public function filter_order_items_meta_display( $output ) {
		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			return $output;
		}

		if ( ! WOOCOMMERCE_CHECKOUT ) {
			return $output;
		}

		// At this point dashses were replaced with spaces by WC.
		if ( strpos( $output, '<span class="order item meta ticket' ) !== false ) {
			$output = '';
		}

		return $output;
	}

	/**
	 * Set ticket to publish.
	 *
	 * @param  integer $order_id Order ID
	 * @return void
	 */
	public function publish_tickets( $order_id = 0 ) {
		global $wpdb;

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$processed = $order->get_meta( '_tickets_processed', true );

		if ( 'yes' === $processed ) {
			return;
		}

		$payment_method = is_callable( array( $order, 'get_payment_method' ) )
			? $order->get_payment_method()
			: $order->payment_method;

		// Don't publish ticket for COD while still in processing.
		if ( $order->has_status( 'processing' ) && 'cod' === $payment_method ) {
			return;
		}

		$tickets = array();
		foreach ( $order->get_items() as $order_item_id => $item ) {
			if ( 'line_item' == $item['type'] ) {
				$tickets = array_merge( $tickets, $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_ticket_order_item_id' AND meta_value = %d", $order_item_id ) ) );
			}
		}

		if ( empty( $tickets ) ) {
			return;
		}

		foreach ( $tickets as $ticket_id ) {
			wp_update_post( array( 'ID' => $ticket_id, 'post_status' => 'publish' ) );
		}

		$order->update_meta_data( '_tickets_processed', 'yes' );
		$order->save();

		// Send email to each email contact in each ticket.
		WCBO()->components->cron->schedule_send_email_after_tickets_published( time(), $tickets );
	}

	/**
	 * Set ticket to pending.
	 *
	 * Changes the status for all the tickets from the order to pending.
	 *
	 * @param integer $order_id Order ID.
	 * @return void
	 */
	public function maybe_unpublish_tickets( $order_id = 0 ) {
		global $wpdb;

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		$tickets = array();
		foreach ( $order->get_items() as $order_item_id => $item ) {
			if ( 'line_item' === $item['type'] ) {
				$tickets = array_merge( $tickets, $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_ticket_order_item_id' AND meta_value = %d", $order_item_id ) ) );
			}
		}

		if ( empty( $tickets ) ) {
			return;
		}

		foreach ( $tickets as $ticket_id ) {
			wp_update_post( array( 'ID' => $ticket_id, 'post_status' => 'pending' ) );
		}

		// Reset the processing status so after order status is changed again the ticket status will be updated correctly.
		$order->update_meta_data( '_tickets_processed', 'no' );
		$order->save();
	}

	/**
	 * Permanently delete tickets related to the order being deleted.
	 *
	 * @param int $order_id ID of order being deleted
	 */
	public function delete_tickets( $order_id ) {
		$this->_apply_func_to_order_tickets( $order_id, 'wp_delete_post', array( true ) );
	}

	/**
	 * Trash tickets related to the order being deleted/cancelled.
	 *
	 * @param int $order_id ID of order being trashed
	 */
	public function trash_tickets( $order_id ) {
		$this->_apply_func_to_order_tickets( $order_id, 'wp_trash_post' );
	}

	/**
	 * Untrash tickets related to the order being deleted.
	 *
	 * @param int $order_id ID of order being untrashed
	 */
	public function untrash_tickets( $order_id ) {
		$this->_apply_func_to_order_tickets( $order_id, 'wp_untrash_post' );
	}

	/**
	 * Apply callable to tickets in order.
	 *
	 * @param int   $order_id      Order ID
	 * @param mixed $callable      Callable func
	 * @param array $callable_args Args to be passed to callable after ticket_id
	 */
	private function _apply_func_to_order_tickets( $order_id, $callable, $callable_args = array() ) {
		if ( $order_id > 0 && 'shop_order' === get_post_type( $order_id ) ) {
			global $wpdb;

			$order   = wc_get_order( $order_id );
			$tickets = array();

			foreach ( $order->get_items() as $order_item_id => $item ) {
				if ( 'line_item' == $item['type'] ) {
					$tickets = array_merge( $tickets, $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_ticket_order_item_id' AND meta_value = %d", $order_item_id ) ) );
				}
			}

			foreach ( $tickets as $ticket_id ) {
				// Ticket ID will be the first arg.
				array_unshift( $callable_args, $ticket_id );
				call_user_func_array( $callable, $callable_args );
			}
		}
	}

	/**
	 * Display order tickets list.
	 *
	 * @param  object $order Order object
	 * @return void
	 */
	public function order_details_ticket_list( $order = false ) {
		if ( ! $order ) {
			return;
		}
		echo do_shortcode( '[order_tickets order_id="' . esc_attr( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id() ) . '" fields_format="list"]' );
	}

	/**
	 * Display order ticket in order email.
	 *
	 * @param  object $order Order object
	 * @return void
	 */
	public function order_email_ticket_list( $order = false ) {
		$this->order_details_ticket_list( $order );
	}

	/**
	 * Get tickets purchased in order.
	 *
	 * @param  integer $order_id Order ID
	 * @param  string  $amount   Number of tickets to fetch
	 * @return array             Array of ticket posts
	 */
	public function get_tickets_by_order( $order_id = 0, $amount = 'all' ) {

		if ( ! $order_id ) {
			return array();
		}

		if ( 'all' === $amount ) {
			$amount = -1;
		}

		$args = apply_filters( 'woocommerce_box_office_order_tickets_query', array(
			'post_type'      => 'event_ticket',
			'post_status'    => array( 'publish', 'pending' ),
			'posts_per_page' => $amount,
			'meta_query' => array(
				array(
					'key'   => '_order',
					'value' => $order_id,
				),
			),
		), $order_id );

		return get_posts( $args );
	}

	/**
	 * Ensure that tickets are added to the order again action by the user.
	 *
	 * @since 1.0.0
	 *
	 * @param array $cart_item_data
	 * @param array $order_item
	 * @param WC_Order $original_order
	 * @return array $order_meta
	 */
	public function order_again_add_tickets( $cart_item_data, $order_item, $original_order ) {

		if ( ! wc_box_office_is_product_ticket( $order_item['product_id'] ) ) {

			return $cart_item_data;

		}

		$new_order_ticket_fields = array();
		$original_order_tickets = $this->get_tickets_by_order( version_compare( WC_VERSION, '3.0', '<' ) ? $original_order->id : $original_order->get_id() );

		foreach ( $original_order_tickets as $ticket ) {
			//add custom ticket related fields
			$new_order_ticket_fields[] = maybe_unserialize( $ticket->post_content );

		}

		// add variation if it exists
		$variation_id = $order_item['variation_id'] ? $order_item['variation_id'] : '';

		// setup the cart item date with ticket related fields
		$cart_item_data['ticket'] = array(
			'product_id'   => $order_item['product_id'],
			'variation_id' => $variation_id,
			'fields'       => $new_order_ticket_fields,
		);

		return $cart_item_data;

	}

	/**
	 * Don't send order items meta to gateway if order has ticket product.
	 *
	 * @see https://github.com/woothemes/woocommerce-box-office/issues/137
	 * @since 1.1.0
	 *
	 * @param int|WC_Order Order ID or order object
	 */
	public function filter_order_items_meta_to_gateway( $order ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order );
		}
		if ( ! $order ) {
			return;
		}

		$has_ticket_product = false;
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( wc_box_office_is_product_ticket( $product ) ) {
				$has_ticket_product = true;
				break;
			}
		}

		if ( $has_ticket_product ) {
			add_filter( 'woocommerce_order_items_meta_display', '__return_empty_string' );
		}
	}

	/**
	 * Create barcode fields in chekcout form.
	 *
	 * The fields contain 2 * N fields, where N is number of purchased tickets.
	 * Each field represent barcode text for a ticket.
	 *
	 * @since 1.1.1
	 */
	public function maybe_create_barcode_fields() {
		if ( ! WCBO()->components->ticket_barcode->is_available() ) {
			return;
		}

		$fields = $this->_get_barcode_fields_for_checkout();

		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $key => $field ) {
			foreach ( $field as $f ) {
				$barcode_text = WCBO()->components->ticket_barcode->generate_barcode_text_for_ticket();

				echo sprintf(
					'<input type="hidden" name="%1$s" value="%2$s" />',
					esc_attr( $f['text'] ),
					esc_attr( $barcode_text )
				);
			}
		}
	}

	/**
	 * Get barcode fields to be injected in checkout form.
	 *
	 * @since 1.1..1
	 *
	 * @return array Barcode fields
	 */
	protected function _get_barcode_fields_for_checkout() {
		$fields = array();
		foreach ( WC()->cart->get_cart() as $key => $values ) {
			$ticket_key = '';
			if ( ! empty( $values['ticket']['key'] ) ) {
				$ticket_key = $values['ticket']['key'];
			} else {
				continue;
			}

			$fields[ $ticket_key ] = array();
			foreach ( $values['ticket']['fields'] as $idx => $__ ) {
				$fields[ $ticket_key ][ $idx ] = array(
					'text'  => sprintf( 'ticket_barcodes[%1$s][%2$s][text]', $ticket_key, $idx ),
				);
			}
		}

		return $fields;
	}

	/**
	 * Maybe process barcode fields.
	 *
	 * This will process barcode text injected in checkout form.
	 * Barcode data will be saved as ticket meta.
	 *
	 * @since 1.1.1
	 *
	 * @param int $order_id Order ID
	 */
	public function maybe_process_barcode_fields( $order_id ) {
		if ( empty( $_POST['ticket_barcodes'] ) ) {
			return;
		}

		global $wpdb;

		foreach ( $_POST['ticket_barcodes'] as $key => $tickets ) {
			foreach ( $tickets as $index => $barcode ) {
				$meta_key  = sprintf( '_ticket_id_for_%1$s_%2$s', $key, $index );
				$ticket_id = absint( $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = %s", $meta_key ) ) );

				if ( ! $ticket_id ) {
					continue;
				}

				update_post_meta( $ticket_id, '_barcode_text', $barcode['text'] );
			}
		}
	}

	/**
	 * Make sure display value contains correct HTML contents for tickets.
	 * We need to do this after https://github.com/woocommerce/woocommerce/pull/17821.
	 *
	 * @param string        $display_value
	 * @param WC_Meta_Data  $meta
	 * @param WC_Order_Item $order_item
	 *
	 * @return string
	 */
	public function process_ticket_display_meta( $display_value, $meta, $order_item ) {
		if ( ! is_callable( array( $order_item, 'get_product' ) ) ) {
			return $display_value;
		}

		$product = $order_item->get_product();

		if ( ! wc_box_office_is_product_ticket( $product ) ) {
			return $display_value;
		}

		return $meta->value;
	}
}
