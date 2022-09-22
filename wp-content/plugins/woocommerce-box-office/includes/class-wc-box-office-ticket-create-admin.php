<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Create_Admin {

	/**
	 * Required fields.
	 *
	 * @var array
	 */
	private $_fields = array();

	/**
	 * Errors.
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * Clean data after validated.
	 *
	 * @var array
	 */
	private $_clean_data = array();

	/**
	 * Cache current product.
	 *
	 * @var WC_Product
	 */
	private $_current_product;

	/**
	 * Cache current variation_id.
	 *
	 * @var int
	 */
	private $_current_variation_id;

	/**
	 * Cache current product variation.
	 *
	 * @var array
	 */
	private $_current_variation = array();

	/**
	 * Cache current order.
	 *
	 * @var WC_Order
	 */
	private $_current_order;

	/**
	 * Created tickets.
	 *
	 * @var array
	 */
	private $_created_tickets;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_fields = array(
			'customer_id' => array(
				'sanitize' => 'absint',
				'required' => true,
			),
			'create_order_method' => array(
				'sanitize'      => array( $this, '_sanitize_create_order_method' ),
				'required'      => true,
				'validate'      => array( $this, '_validate_create_order_method' ),
				'error_message' => __( 'Please choose create order method', 'woocommerce-box-office' ),
			),
			'ticket_order_id' => array(
				'sanitize'      => 'absint',
				'required'      => array( $this, '_maybe_require_order_id' ),
				'validate'      => array( $this, '_validate_existing_order' ),
				'error_message' => __( 'Invalid order ID provided', 'woocommerce-box-office' ),
			),
			'quantity' => array(
				'sanitize'      => 'absint',
				'required'      => true,
				'validate'      => array( $this, '_validate_not_zero' ),
				'error_message' => __( 'Ticket quantity must be greater than zero', 'woocommerce-box-office' ),
			),
			'product_id' => array(
				'sanitize'      => 'absint',
				'required'      => true,
				'validate'      => array( $this, '_validate_product' ),
				'error_message' => __( 'Please choose ticket-enabled product', 'woocommerce-box-office' ),
			),
			'ticket_fields' => array(
				'required' => array( $this, '_require_it_after_step_one' ),
				'validate' => array( $this, '_validate_ticket_fields' ),
			),
		);
	}

	/**
	 * Render the create ticket admin page.
	 *
	 * @param array $posted_data Posted data
	 *
	 * @return void
	 */
	public function render( $posted_data = array() ) {
		$this->errors = array();
		$ticket_form  = null;
		$step         = $this->_get_step( $posted_data );

		try {
			$processed = $this->_process_posted_data( $posted_data, $step );
			$step      = $processed ? $step + 1 : $step;
		} catch ( Exception $e ) {
			$this->_errors[] = $e->getMessage();
		}

		switch ( $step ) {
			case 2:
				$ticket_form = new WC_Box_Office_Ticket_Form(
					$this->_current_product ? $this->_current_product : wc_get_product( $posted_data['product_id'] ),
					! empty( $posted_data['ticket_fields'] ) ? $posted_data['ticket_fields'] : null
				);
				break;
			case 3:
				if ( $this->_current_order ) {
					$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $this->_current_order->id : $this->_current_order->get_id();
					$order_url = admin_url( 'post.php?post=' . $order_id . '&action=edit' );
				}
		}

		$template = sprintf( '%sincludes/views/admin/create-ticket-step-%d.php', WCBO()->dir, $step );
		require_once( $template );
	}

	/**
	 * Get step from posted data.
	 *
	 * @param array $posted_data Posted data
	 *
	 * @return int Step
	 */
	private function _get_step( $posted_data ) {
		$step = 1;
		if ( ! empty( $posted_data['create_ticket_step'] ) ) {
			$step = absint( $posted_data['create_ticket_step'] );
		}

		if ( ! in_array( $step, array( 1, 2 ) ) ) {
			$step = 1;
		}

		return $step;
	}

	/**
	 * Process posted data if present.
	 *
	 * @throws Exception
	 *
	 * @param array $posted_data Posted data
	 *
	 * @return bool
	 */
	private function _process_posted_data( $posted_data, $step ) {
		if ( ! empty( $posted_data ) && ! check_admin_referer( 'create_event_ticket' ) ) {
			throw new Exception( __( 'Error - please try again', 'woocommerce-box-office' ) );
		}

		// No posted data to process.
		if ( empty( $posted_data['submit_create_ticket'] ) ) {
			return false;
		}

		// Validate posted data.
		$this->_validate( $posted_data );

		// Maybe create order and tickets.
		if ( 2 === $step ) {
			$order_item_id          = $this->_maybe_create_order_item();
			$this->_created_tickets = $this->_create_tickets( $order_item_id );

			$this->_maybe_send_emails( $posted_data );
		}

		return true;
	}

	/**
	 * Maybe create order item.
	 *
	 * @throws Exception
	 *
	 * @return int Order item ID
	 */
	private function _maybe_create_order_item() {
		$product = $this->_current_variation_id
			? wc_get_product( $this->_current_variation_id )
			: $this->_current_product;

		$total = $product->get_price() * absint( $this->_clean_data['quantity'] );

		if ( wc_prices_include_tax() ) {
			$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class() );
			$base_taxes     = WC_Tax::calc_tax( $total, $base_tax_rates, true );
			$total          = $total - array_sum( $base_taxes );

			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				$total = round( $total, absint( get_option( 'woocommerce_price_num_decimals' ) ) );
			}
		}

		switch ( $this->_clean_data['create_order_method'] ) {
			case 'new':
				$order_id = $this->_create_order( $this->_clean_data['customer_id'], $total );
				break;
			case 'existing':
				$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $this->_current_order->id : $this->_current_order->get_id();
				$this->_current_order->set_total( $this->_current_order->get_total() + $total );
				break;
			default:
				$order_id = 0;
		}

		$item_id = 0;
		if ( $order_id ) {
			$item_id = $this->_current_order->add_product(
				$product,
				$this->_clean_data['quantity'],
				array(
					'variation' => $this->_current_variation,
					'totals'    => array(
						'subtotal' => $total,
						'total'    => $total,
					),
				)
			);

			if ( ! $item_id ) {
				throw new Exception( __( 'Error: could not create order item', 'woocommerce-box-office' ) );
			}

			$this->_current_order->calculate_totals( wc_tax_enabled() );
		}

		return $item_id;
	}

	/**
	 * Create order from given customer and total.
	 *
	 * @since 1.0.0
	 * @version 1.1.7
	 *
	 * @param int    $customer_id Customer ID.
	 * @param string $total       Total for the order.
	 *
	 * @return int Order ID.
	 */
	private function _create_order( $customer_id, $total ) {
		$is_pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

		$order = wc_create_order( array(
			'customer_id' => $customer_id,
		) );

		$order->set_total( $total );
		if ( ! $is_pre_wc_30 ) {
			$order->save();
		}

		// Set order address.
		if ( $customer_id ) {
			$customer = new WC_Customer( $customer_id );

			$keys = array(
				'first_name',
				'last_name',
				'company',
				'address_1',
				'address_2',
				'city',
				'state',
				'postcode',
				'country',
			);
			foreach ( array( 'shipping', 'billing' ) as $type ) {
				$address = array();
				foreach ( $keys as $key ) {
					$address[ $key ] = $is_pre_wc_30
						? (string) get_user_meta( $customer_id, $type . '_' . $key, true )
						: ( is_callable( array( $customer, 'get_' . $type . '_' . $key ) ) ? $customer->{'get_' . $type . '_' . $key}() : '' );
				}
				$order->set_address( $address, $type );
			}
		}

		// Cache order.
		$this->_current_order = $order;

		return $is_pre_wc_30 ? $order->id : $order->get_id();
	}

	/**
	 * Create tickets.
	 *
	 * @param int $item_id Order item ID
	 *
	 * @return array List of created tickets
	 */
	private function _create_tickets( $item_id = 0 ) {
		$tickets = array();

		// Creates ticket(s) and inserts order item meta.
		foreach ( $this->_clean_data['ticket_fields'] as $index => $fields ) {
			$ticket_data = array(
				'product_id'    => $this->_current_product->get_id(),
				'variation_id'  => $this->_current_variation_id,
				'variations'    => $this->_current_variation,
				'fields'        => $fields,
				'order_item_id' => $item_id,
				'customer_id'   => $this->_clean_data['customer_id'],
			);

			$ticket = new WC_Box_Office_Ticket( $ticket_data );
			$ticket->create( 'publish' );

			// Create a barcode for the ticket, see issue #62
			$barcode_text = WCBO()->components->ticket_barcode->generate_barcode_text_for_ticket();
			update_post_meta( $ticket->id, '_barcode_text', $barcode_text );

			$tickets[] = $ticket;

			if ( $item_id ) {	
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

		return $tickets;
	}

	/**
	 * Maybe send emails to ticket holders if user opt-in to 'Send confirmation email to each ticket?'.
	 *
	 * @param array $posted_data Posted data
	 *
	 * @return void
	 */
	private function _maybe_send_emails( $posted_data ) {
		if ( ! isset( $posted_data['send_confirmation_email'] ) ) {
			return;
		}

		if ( empty( $this->_created_tickets ) ) {
			return;
		}

		$ticket_ids = array();
		foreach ( $this->_created_tickets as $ticket ) {
			$ticket_ids[] = $ticket->id;
		}

		WCBO()->components->cron->schedule_send_email_after_tickets_published( time(), $ticket_ids, true );
	}

	/**
	 * Validate posted data.
	 *
	 * @throws Exception
	 *
	 * @param array $data Posted data. Basically from $_POST
	 *
	 * @return void
	 */
	private function _validate( $data ) {
		$exception = null;
		foreach ( $this->_fields as $field => $prop ) {
			// Check if field is a required one.
			$is_required = $this->_is_field_required( $field, $prop, $data );

			// Sanitize.
			$this->_sanitize_field( $field, $prop, $data );

			// Skip validation if field is not required.
			if ( ! $is_required ) {
				continue;
			}

			// Validate.
			$this->_validate_field( $field, $prop, $data );
		}
	}

	/**
	 * Check if field is required. If required make sure it's available in posted
	 * data.
	 *
	 * @throws Exception
	 *
	 * @param string $field      Field's name
	 * @param array  $field_prop Field's properties
	 * @param array  $data       Posted data
	 *
	 * @return bool True if field is required
	 */
	private function _is_field_required( $field, $field_prop, $data ) {
		$is_required = isset( $field_prop['required'] ) ? $field_prop['required'] : false;
		if ( is_callable( $is_required ) ) {
			$is_required = call_user_func_array( $is_required, array( $data ) );
		}

		if ( $is_required && ! isset( $data[ $field ] ) ) {
			throw new Exception( __( 'Missing value for field %s', 'woocommerce-box-office' ) );
		}

		return $is_required;
	}

	/**
	 * Sanitize field's value and store sanitized value into clean_data.
	 *
	 * @param string $field      Field's name
	 * @param array  $field_prop Field's properties
	 * @param array  $data       Posted data
	 *
	 * @return void
	 */
	private function _sanitize_field( $field, $field_prop, $data ) {
		// Get sanitizer.
		$sanitizer = array( $this, '_return_no_change' );
		if ( ! empty( $field_prop['sanitize'] ) && is_callable( $field_prop['sanitize'] ) ) {
			$sanitizer = $field_prop['sanitize'];
		}

		// Sanitize.
		if ( isset( $data[ $field ] )  ) {
			$this->_clean_data[ $field ] = call_user_func_array( $sanitizer, array( $data[ $field ] ) );
		}
	}

	/**
	 * Sanitize field's value and store sanitized value into clean_data.
	 *
	 * @throws Exception
	 *
	 * @param string $field      Field's name
	 * @param array  $field_prop Field's properties
	 * @param array  $data       Posted data
	 *
	 * @return void
	 */
	private function _validate_field( $field, $field_prop, $data ) {
		// Get validator.
		$validator = null;
		if ( isset( $this->_clean_data[ $field ] ) && ! empty( $field_prop['validate'] ) && is_callable( $field_prop['validate'] ) ) {
			$validator = $field_prop['validate'];
		}

		if ( $validator && ! call_user_func_array( $validator, array( $this->_clean_data[ $field ], $data ) ) ) {
			$message = ! empty( $field_prop['error_message'] ) ? $field_prop['error_message'] : sprintf( __( 'Something wrong with field %s.', 'woocommerce-box-office' ), $field );
			throw new Exception( $message );
		}
	}

	/**
	 * Sanitize create order method.
	 *
	 * @param string $value Create order method
	 *
	 * @return string Create order method
	 */
	private function _sanitize_create_order_method( $value ) {
		if ( ! in_array( $value, array( 'existing', 'new', 'no_order' ) ) ) {
			$value = 'no_order';
		}

		return $value;
	}

	/**
	 * Default sanitizer. Return $value without any change.
	 *
	 * @param mixed $value Value
	 *
	 * @return mixed Value
	 */
	private function _return_no_change( $value ) {
		return $value;
	}

	/**
	 * Returns true if $value is NOT zero.
	 *
	 * @param int $value Value
	 *
	 * @return bool True if $value is NOT zero
	 */
	private function _validate_not_zero( $value ) {
		return $value !== 0;
	}

	/**
	 * Validate create order method.
	 *
	 * @param string $value Value
	 *
	 * @return bool True if $value is valid create order method
	 */
	private function _validate_create_order_method( $value ) {
		return in_array( $value, array( 'existing', 'new', 'no_order' ) );
	}

	/**
	 * Validate order ID.
	 *
	 * @param int $value Order ID
	 *
	 * @return bool True if $value is valid order ID
	 */
	private function _validate_existing_order( $value ) {
		if ( ! $value || 'shop_order' !== get_post_type( $value ) ) {
			return false;
		}

		// Cache order.
		$this->_current_order = new WC_Order( $value );

		return true;
	}

	/**
	 * Validate product ID.
	 *
	 * @throws Exception
	 *
	 * @param int   $value Product ID
	 * @param array $data  Posted data
	 *
	 * @return bool True if $value is valid product ID
	 */
	private function _validate_product( $value, $data ) {
		if ( ! $value || 'product' !== get_post_type( $value ) ) {
			return false;
		}

		// Shortcut.
		$product = wc_get_product( $value );
		$qty     = $this->_clean_data['quantity'];

		// Cache product. Need to cache early before any line which throws Exception.
		$this->_current_product = $product;

		// Cache current variation ID.
		$this->_current_variation_id = $this->_get_variation_id( $product, $data );

		// Cache variation data.
		$this->_current_variation = $this->_current_variation_id ? wc_get_product_variation_attributes( $this->_current_variation_id ) : array();

		// Used for checking.
		$product_check = $this->_current_variation_id ? wc_get_product( $this->_current_variation_id ) : $product;

		// When new order is created or existing order is used, check product properties.
		if ( 'no_order' !== $data['create_order_method'] ) {
			if ( ! $product_check->is_purchasable() ) {
				throw new Exception( __( 'The product cannot be purchased.', 'woocommerce-box-office' ) );
			}

			if ( $product_check->is_sold_individually() && $qty > 1 ) {
				throw new Exception( __( 'Product is set to sold individually. You can only add one ticket.', 'woocommerce-box-office' ) );
			}

			if ( ! $product_check->is_in_stock() ) {
				throw new Exception( sprintf( __( 'You cannot create ticket for &quot;%s&quot; because the product is out of stock.', 'woocommerce-box-office' ), $product_check->get_title() ) );
			}

			if ( ! $product_check->has_enough_stock( $qty ) ) {
				throw new Exception( sprintf( __( 'You cannot create that amount of ticket(s) because there is not enough stock (%s remaining).', 'woocommerce-box-office' ), $product_check->get_stock_quantity() ) );
			}
		}

		return true;
	}

	/**
	 * Get variation ID.
	 *
	 * @throws Exception
	 *
	 * @param WC_Product $product Product
	 * @param array      $data    Posted data
	 *
	 * @return int Variation ID
	 */
	private function _get_variation_id( $product, $data ) {
		$variation_id = 0;

		if ( ! $product->is_type( 'variable' ) || 2 !== $this->_get_step( $data ) ) {
			return $variation_id;
		}

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$variation_id = $product->get_matching_variation( $data );
		} else {
			$data_store = WC_Data_Store::load( 'product' );
			$variation_id = $data_store->find_matching_product_variation( $product, $data );
		}

		if ( ! $variation_id ) {
			throw new Exception( __( 'Please select the product options.', 'woocommerce-box-office' ) );
		}

		// Validate selected variation.
		$attributes = $product->get_variation_attributes();
		foreach ( $attributes as $name => $values ) {
			$key = 'attribute_' . sanitize_title( $name );
			if ( empty( $data[ $key ] ) ) {
				throw new Exception( sprintf( __( 'Please select option for %s.', 'woocommerce-box-office' ), $name ) );
			}

			$value = wc_clean( $data[ $key ] );
			if ( ! in_array( $value, $values ) ) {
				throw new Exception( sprintf( __( 'Invalid value for option %s.', 'woocommerce-box-office' ), $name ) );
			}

			$this->_clean_data[ $key ] = $value;
		}

		return $variation_id;
	}

	/**
	 * Validate ticket fields.
	 *
	 * @throws Exception
	 *
	 * @param array $value Ticket fields
	 * @param array $data  Posted data
	 *
	 * @return bool True if $value is valid ticket fields
	 */
	private function _validate_ticket_fields( $value, $data ) {
		// Get product to be passed to ticket form.
		if ( is_a( $this->_current_product, 'WC_Product' ) ) {
			$product = $this->_current_product;
		} else {
			// self::_validate_product might throws an exception and `$this->_current_product`
			// is not set. So need to get product from posted data.
			$product = wc_get_product( $data['product_id'] );
		}

		if ( $this->_clean_data['quantity'] !== sizeof( $data['ticket_fields'] ) ) {
			throw new Exception( __( 'Number of ticket fields does not match with provided ticket quantity.', 'woocommerce-box-office' ) );
		}

		$ticket_form = new WC_Box_Office_Ticket_Form( $product );
		$ticket_form->validate( $data );

		$this->_clean_data['ticket_fields'] = $ticket_form->get_clean_data();

		return true;
	}

	/**
	 * Only require ticket_order_id when ticket_order_type is 'existing'.
	 *
	 * @param array $posted_data Posted data
	 *
	 * @return bool True if ticket_order_type is 'existing'
	 */
	private function _maybe_require_order_id( $posted_data ) {
		return $posted_data['create_order_method'] === 'existing';
	}

	/**
	 * Only require field after step one
	 *
	 * @param array $posted_data Posted data
	 *
	 * @return bool True after posted from step #1
	 */
	private function _require_it_after_step_one( $posted_data ) {
		if ( empty( $posted_data['create_ticket_step'] ) ) {
			return false;
		}

		return absint( $posted_data['create_ticket_step'] ) === 2;
	}

	/**
	 * Maybe print errors.
	 *
	 * @return void
	 */
	public function maybe_print_errors() {
		foreach ( $this->_errors as $error ) {
			printf( '<div class="error"><p>%s</p></div>', esc_html( $error ) );
		}
	}
}
