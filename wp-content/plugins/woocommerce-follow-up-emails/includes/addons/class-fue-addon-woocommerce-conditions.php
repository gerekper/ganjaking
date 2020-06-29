<?php

/**
 * Class FUE_Addon_Woocommerce_Conditions
 */
class FUE_Addon_Woocommerce_Conditions {

	/**
	 * @var FUE_Addon_Woocommerce
	 */
	private $fue_wc;

	/**
	 * Class constructor
	 *
	 * @param FUE_Addon_Woocommerce $wc
	 */
	public function __construct( $wc ) {
		$this->fue_wc = $wc;

		add_action( 'fue_email_form_conditions_meta', array( $this, 'email_form_conditions'), 10, 2  );

		add_filter( 'fue_api_email_response', array( $this, 'normalize_conditions_output' ), 10, 2 );
		add_filter( 'fue_api_edit_email_data', array( $this, 'normalize_conditions_input' ) );
	}

	/**
	 * Inject additional form fields into the conditions section
	 *
	 * @param FUE_Email $email
	 * @param int $idx
	 */
	public function email_form_conditions( $email, $idx ) {
		$conditions = $email->conditions;
		$categories = $categories = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );
		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/conditions.php';
	}

	/**
	 * Get WC-related conditions
	 * @return array
	 */
	public function get_store_conditions() {
		$conditions = array();

		$conditions['bought_products']      = __('customer bought these products', 'follow_up_emails');
		$conditions['bought_categories']    = __('customer bought from these categories', 'follow_up_emails');

		$conditions['first_purchase']       = __('on first purchase', 'follow_up_emails');
		$conditions['order_total_above']    = __('order total above:', 'follow_up_emails');
		$conditions['order_total_below']    = __('order total below:', 'follow_up_emails');

		$conditions['total_orders_above']   = __('total orders by customer above:', 'follow_up_emails');
		$conditions['total_orders_below']   = __('total orders by customer below:', 'follow_up_emails');
		$conditions['total_purchases_above']= __('total purchase amount by customer above:', 'follow_up_emails');
		$conditions['total_purchases_below']= __('total purchase amount by customer below:', 'follow_up_emails');

		$conditions['payment_method']       = __('payment method used:', 'follow_up_emails');
		$conditions['shipping_method']      = __('shipping method used:', 'follow_up_emails');

		return $conditions;
	}

	/**
	 * Get signup conditions
	 * @return array
	 */
	public function get_signup_conditions() {
		$conditions = array();

		$conditions['has_purchased']     = __('has made a purchase', 'follow_up_emails');
		$conditions['has_not_purchased'] = __('has not made a purchase', 'follow_up_emails');

		return $conditions;
	}

	/**
	 * Test if $item passes the requirements in $condition
	 * @param array $condition
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error
	 */
	public function test_store_condition( $condition, $item ) {

		switch ( $condition['condition'] ) {

			case 'first_purchase':
				$result = $this->test_first_purchase_condition( $item );
				break;

			case 'bought_products':
				$result = $this->test_bought_products_condition( $item, $condition );
				break;

			case 'bought_categories':
				$result = $this->test_bought_categories_condition( $item, $condition );
				break;

			case 'order_total_above':
			case 'order_total_below':
				$result = $this->test_order_total_condition( $item, $condition );
				break;

			case 'total_orders_above':
			case 'total_orders_below':
				$result = $this->test_total_orders_count_condition( $item, $condition );
				break;

			case 'total_purchases_above':
			case 'total_purchases_below':
				$result = $this->test_total_purchases_condition( $item, $condition );
				break;

			case 'payment_method':
				$result = $this->test_payment_method( $item, $condition );
				break;

			case 'shipping_method':
				$result = $this->test_shipping_method( $item, $condition );
				break;

			default:
				return new WP_Error( 'fue_email_conditions', sprintf( __('Unknown condition: %s', 'follow_up_emails'), $condition['condition'] ) );
				break;

		}

		return $result;

	}

	/**
	 * Test if $item passes the requirements in $condition
	 * @param array $condition
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error
	 */
	public function test_signup_condition( $condition, $item ) {

		switch ( $condition['condition'] ) {

			case 'has_purchased':
				$result = $this->test_has_purchased_condition( $item, $condition );
				break;

			case 'has_not_purchased':
				$result = $this->test_has_not_purchased_condition( $item, $condition );
				break;

			default:
				return new WP_Error( 'fue_email_conditions', sprintf( __('Unknown condition: %s', 'follow_up_emails'), $condition['condition'] ) );
				break;

		}

		return $result;

	}

	/**
	 * Test will pass if the customer is buying from the store (all or a specific product) for the first time.
	 *
	 * If a `product_id` property is present, this test will pass if that product is being
	 * purchased for the first time. It will fail otherwise.
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error
	 */
	public function test_first_purchase_condition( $item ) {
		$customer = fue_get_customer_from_order( $item->order_id );

		if ( !$customer ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf( __('Customer data could not be found (Order #%d)', 'follow_up_emails'), $item->order_id )
			);
		}

		if ( empty( $item->product_id ) ) {
			$count = $this->fue_wc->count_customer_purchases( $customer->id, $item->product_id );
		} else {
			$count = $this->fue_wc->count_customer_purchases( $customer->id );
		}

		if ( $count != 1 ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf( __('first_purchase condition failed for queue #%d (purchases: %d)', 'follow_up_emails'), $item->id, $count )
			);
		}

		return true;

	}

	/**
	 * Test will pass if the customer has bought all of the products
	 * specified in the condition
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_bought_products_condition( $item, $condition ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;
		$products   = array_filter( array_map( 'absint', explode(',', $condition['products'] ) ) );
		$customer   = fue_get_customer_from_order( $item->order_id );
		$result     = true;

		if ( !$customer ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf( __('Customer data could not be found (Order #%d)', 'follow_up_emails'), $item->order_id )
			);
		}

		if ( !empty( $products ) ) {

			foreach ( $products as $product ) {

				if ( !$this->fue_wc->customer_purchased_product( $customer, $product ) ) {
					// no purchases found for this product
					$wc_product = WC_FUE_Compatibility::wc_get_product( $product );

					return new WP_Error(
						'fue_email_conditions',
						sprintf(
							__('Customer has not purchased a required product (#%d - %s)', 'follow_up_emails'),
							$product,
							$wc_product->get_formatted_name()
						)
					);
				}

			}

		}

		return true;
	}

	/**
	 * Test will pass if the customer has bought from all of the categories specified
	 *
	 * @param $item
	 * @param $condition
	 * @return bool|WP_Error
	 */
	public function test_bought_categories_condition( $item, $condition ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;
		$categories = array_filter( array_map( 'absint', $condition['categories'] ) );
		$customer   = fue_get_customer_from_order( $item->order_id );
		$result     = true;

		if ( !$customer ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf( __('Customer data could not be found (Order #%d)', 'follow_up_emails'), $item->order_id )
			);
		}

		if ( !empty( $categories ) ) {

			foreach ( $categories as $category ) {

				if ( !$this->fue_wc->customer_purchased_from_category( $customer, $category ) ) {
					// no purchases found for this product
					$wc_category = get_term( $category, 'product_cat' );

					return new WP_Error(
						'fue_email_conditions',
						sprintf(
							__('Customer has not purchased from a required category (%s)', 'follow_up_emails'),
							$wc_category->name
						)
					);
				}

			}

		}

		return true;
	}

	/**
	 * Test will pass if the order's total amount is above/below the specified value
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_order_total_condition( $item, $condition ) {
		$email  = new FUE_Email( $item->email_id );

		if ( $email->trigger == 'cart' ) {
			$total  = WC()->cart->subtotal;
		} else {
			$order  = WC_FUE_Compatibility::wc_get_order( $item->order_id );
			$total  = $order->get_total();
		}

		$value  = floatval( $condition['value'] );

		if ( $condition['condition'] == 'order_total_above' ) {
			$result = $total > $value;
		} else {
			$result = $total < $value;
		}

		if ( !$result ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Order total: %s / Condition value: %s', 'follow_up_emails'),
					$condition['condition'],
					$total,
					$value
				)
			);
		}

		return $result;
	}

	/**
	 * Test will pass if the customer's number of orders is above/below the specified value
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_total_orders_count_condition( $item, $condition ) {
		$customer = fue_get_customer_from_order( $item->order_id );

		if ( !$customer ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf( __('Customer data could not be found (Order #%d)', 'follow_up_emails'), $item->order_id )
			);
		}

		$total  = $customer->total_orders;
		$value  = floatval( $condition['value'] );

		if ( $condition['condition'] == 'total_orders_above' ) {
			$result = $total > $value;
		} else {
			$result = $total < $value;
		}

		if ( !$result ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Orders: %s / Condition value: %s', 'follow_up_emails'),
					$condition['condition'],
					$total,
					$value
				)
			);
		}

		return $result;
	}

	/**
	 * Test will pass if the total purchase amount of a customer is above/below the specified value
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_total_purchases_condition( $item, $condition ) {
		$customer = fue_get_customer_from_order( $item->order_id );

		if ( !$customer ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf( __('Customer data could not be found (Order #%d)', 'follow_up_emails'), $item->order_id )
			);
		}

		$total  = $customer->total_purchase_price;
		$value  = floatval( $condition['value'] );

		if ( $condition['condition'] == 'total_purchases_above' ) {
			$result = $total > $value;
		} else {
			$result = $total < $value;
		}

		if ( !$result ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Total Purchases: %s / Condition value: %s', 'follow_up_emails'),
					$condition['condition'],
					$total,
					$value
				)
			);
		}

		return $result;
	}

	/**
	 * Test will pass if the user linked to $item has already made a purchase
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_has_purchased_condition( $item, $condition ) {
		if ( empty( $item->user_id ) ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Queue item has no User ID', 'follow_up_emails'),
					$condition['condition']
				)
			);
		}

		$user = get_user_by( 'id', $item->user_id );
		$orders = $this->count_customer_orders( $user->user_email );

		if ( $orders == 0 ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Customer has 0 orders', 'follow_up_emails'),
					$condition['condition']
				)
			);
		}

		return true;
	}

	/**
	 * Test will pass if the user linked to $item has not made a purchase
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_has_not_purchased_condition( $item, $condition ) {
		if ( empty( $item->user_id ) ) {
			return true;
		}

		$user = get_user_by( 'id', $item->user_id );
		$orders = $this->count_customer_orders( $user->user_email );

		if ( $orders > 0 ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Customer has %d orders', 'follow_up_emails'),
					$condition['condition'],
					$orders
				)
			);
		}

		return true;
	}

	/**
	 * Test will pass if the order's payment method matches the selected value
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_payment_method( $item, $condition ) {
		$order      = WC_FUE_Compatibility::wc_get_order( $item->order_id );
		$method     = WC_FUE_Compatibility::get_order_prop( $order, 'payment_method' );
		$value      = $condition['payment_method'];

		$result = ($value == $method);

		if ( !$result ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Payment method: %s / Condition value: %s', 'follow_up_emails'),
					$condition['condition'],
					$method,
					$value
				)
			);
		}

		return $result;
	}

	/**
	 * Test will pass if any of the order's shipping method matches the selected value
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_shipping_method( $item, $condition ) {
		$order      = WC_FUE_Compatibility::wc_get_order( $item->order_id );
		$methods    = $order->get_shipping_methods();
		$value      = $condition['shipping_method'];
		$result     = false;

		$method_names = array();

		foreach ( $methods as $method ) {
			$method_id      = trim( current( explode( ':', (string) $method['method_id'] ) ) );
			$method_names[] = $method_id;

			if ( $method_id === $value ) {
				$result = true;
				break;
			}
		}

		if ( !$result ) {
			return new WP_Error(
				'fue_email_conditions',
				sprintf(
					__('Condition "%s" failed. Shipping method mismatch: %s / Condition value: %s', 'follow_up_emails'),
					$condition['condition'],
					implode( ', ', $method_names ),
					$value
				)
			);
		}

		return $result;
	}

	/**
	 * Normalize the conditions array
	 *
	 * @param array $email_data
	 * @return array
	 */
	public function normalize_conditions_input( $email_data ) {
		if ( !isset( $email_data['requirements'] ) ) {
			return $email_data;
		}

		$normalized = array();

		foreach ( $email_data['requirements'] as $req ) {

			if ( $req['condition'] == 'bought_products' ) {
				$req['products'] = '';
				if ( is_array( $req['value'] ) && !empty( $req['value'] ) ) {
					$req['products'] = implode( ',', $req['value'] );
					$req['value']    = '';
				}
			} elseif ( $req['condition'] == 'bought_categories' ) {
				if ( !is_array( $req['value'] ) ) {
					$req['value'] = array( $req['value'] );
				}

				$req['categories']  = $req['value'];
				$req['value']       = '';
			}

			$normalized[] = $req;

		}

		$email_data['conditions'] = $normalized;
		unset( $email_data['requirements'] );

		return $email_data;
	}

	/**
	 * Normalize the conditions array
	 *
	 * @param array $email_data
	 * @param FUE_Email $email
	 * @return array
	 */
	public function normalize_conditions_output( $email_data, $email ) {
		if ( empty( $email_data['requirements'] ) ) {
			return $email_data;
		}

		$normalized = array();

		foreach ( $email_data['requirements'] as $req ) {

			if ( $req['condition'] == 'bought_products' ) {
				$req['value'] = explode( ',', $req['products'] );
				unset( $req['products'] );
			} elseif ( $req['condition'] == 'bought_categories' ) {
				$req['value'] = $req['categories'];
				unset( $req['categories'] );
			}

			$normalized[] = $req;

		}

		$email_data['requirements'] = $normalized;

		return $email_data;
	}

	/**
	 * Checks if an order matches the given email's conditions in terms of status change.
	 *
	 * It will not exactly match the order's status history, instead it will check if an
	 * order's status is different from the email trigger and match the order status to
	 * a status condition
	 *
	 * @param WC_Order  $order
	 * @param FUE_Email $email
	 * @return bool
	 */
	public static function order_matches_status_condition( $order, $email ) {
		$status = WC_FUE_Compatibility::get_order_status( $order );

		if ( $status == $email->trigger ) {
			return false;
		}

		$conditions = $email->conditions;

		foreach ( $conditions as $condition ) {
			if ( $condition['condition'] == $status ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if there is a match in the order's status and the email's trigger
	 *
	 * @param int|WC_Order  $order
	 * @param int|FUE_Email $email
	 * @return bool
	 */
	private function order_status_matches_email_trigger( $order, $email ) {
		if ( is_numeric( $order ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( $order );
		}

		if ( is_numeric( $email ) ) {
			$email = new FUE_Email( $email );
		}

		return WC_FUE_Compatibility::get_order_status( $order ) == $email->trigger;
	}

	/**
	 * Get the number of purchases the given customer has made
	 *
	 * @param string $email
	 * @return int
	 */
	private function count_customer_orders( $email ) {
		$count      = 0;
		$customer   = fue_get_customer( 0, $email );

		if ( $customer ) {
			$count = $customer->total_orders;
		}

		return apply_filters( 'fue_count_customer_orders', $count, $email );
	}

}
