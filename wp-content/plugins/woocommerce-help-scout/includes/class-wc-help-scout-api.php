<?php
/**
 * Help Scout API.
 *
 * @package  WC_Help_Scout_API
 * @category Integration
 * @author   WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Help_Scout_API extends WC_API_Resource {

	/**
	 * The route base.
	 *
	 * @var string
	 */
	protected $base = '/wc-help-scout';

	/**
	 * Register the routes for this class
	 *
	 * GET /wc-help-scout
	 *
	 * @param  array $routes
	 *
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /wc-help-scout
		$routes[ $this->base ] = array(
			array( array( $this, 'get_app_data' ), WC_API_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get Customer data for Help Scout APP.
	 *
	 * @param  int    $customer_id    Help Scout customer ID.
	 * @param  string $customer_email Customer email.
	 * @param  int    $orders         Total of last orders.
	 * @param  int    $products       Total of purchased products.
	 *
	 * @return array                  Customer data for the APP.
	 */
	public function get_app_data( $customer_id, $customer_email = '', $orders = 5, $products = 0 ) {
		$customer_id = $this->validate_request( $customer_id, 'customer', 'read' );

		if ( is_wp_error( $customer_id ) ) {
			return $customer_id;
		}

		// Get customer data.
		$customer_data = $this->get_customer_data( $customer_id, $customer_email, $orders, $products );

		if ( is_wp_error( $customer_data ) ) {
			return $customer_data;
		}

		return array( 'customer' => apply_filters( 'woocommerce_help_scout_api_response', $customer_data, $customer_id, $customer_email, $orders, $this->server ) );
	}

	/**
	 * Get customer data by Help Scout ID or email.
	 *
	 * @param  int    $id       Help Scout customer ID.
	 * @param  string $email    Customer email.
	 * @param  int    $orders   Total of last orders.
	 * @param  int    $products Total of purchased products.
	 *
	 * @return array            Customer data.
	 */
	protected function get_customer_data( $id, $email, $orders, $products ) {
		global $wpdb;

		$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_help_scout_customer_id' AND meta_value = %d", $id ) );

		if ( $customer_id ) {
			$customer = new WP_User( $customer_id );

			return $this->get_registered_user_data( $customer, $orders, $products );
		} elseif ( ! empty( $email ) && is_email( $email ) ) {
			$customer = get_user_by( 'email', $email );

			if ( $customer ) {
				// Add Help Scout customer id.
				update_user_meta( $customer->ID, '_help_scout_customer_id', absint( $id ) );

				return $this->get_registered_user_data( $customer, $orders, $products );
			} else {
				// Try to get data from a non-registered user.
				$customer = $this->get_non_registered_user_data( $email, $orders, $products );

				if ( $customer ) {
					return $customer;
				}
			}
		}

		return new WP_Error( 'wc_help_scout_api_invalid_customer', __( 'Invalid customer', 'woocommerce-help-scout' ), array( 'status' => 404 ) );
	}

	/**
	 * Get data from a registered user.
	 *
	 * @param  WC_Order $customer Customer.
	 * @param  int      $orders   Total of last orders.
	 * @param  int      $products Total of purchased products.
	 *
	 * @return array              Customer data.
	 */
	protected function get_registered_user_data( $customer, $orders, $products ) {
		$customer_data = $this->get_customer_details( $customer );
		$customer_data['last_orders'] = $this->get_last_orders( $customer, $orders );
		$customer_data['purchased_products'] = $this->get_purchased_products( $customer, $products );

		return $customer_data;
	}

	/**
	 * Get data from a non-registered user.
	 *
	 * @param  string $email    Customer.
	 * @param  int    $orders   Total of last orders.
	 * @param  int    $products Total of purchased products.
	 *
	 * @return array            Customer data.
	 */
	protected function get_non_registered_user_data( $email, $orders, $products ) {
		global $wpdb;

		$orders_limit  = ( 0 < $orders ) ? ' LIMIT ' . absint( $orders ) : '';
		$customer_data = array();
		$last_order    = null;

		// Get the customer orders.
		$order_ids = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_billing_email' AND meta_value = %s$orders_limit", $email ) );

		if ( ! $order_ids ) {
			return array();
		}

		$orders_count = 0;
		$last_orders  = array();
		foreach ( $order_ids as $item ) {
			$order = wc_get_order( $item->post_id );

			if ( empty( $order ) ) {
				continue;
			}

			if ( 0 === $orders_count ) {
				$last_order = $order;
			}

			$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );

			$last_orders[] = array(
				'id'     => $order->get_order_number(),
				'url'    => add_query_arg( array( 'post' => $item->post_id, 'action' => 'edit' ), admin_url( 'post.php' ) ),
				'date'   => $this->server->format_datetime( $order_date ),
				'total'  => $order->get_total(),
				'status' => $order->get_status()
			);

			$orders_count++;
		}

		if ( ! $last_order ) {
			return array();
		}

		// Custom general data.
		$customer_data['id'] = 0;
		$customer_data['total_spent'] = '';
		$customer_data['sign_up'] = array(
			'date' => '',
			'diff' => ''
		);
		$customer_data['currency'] = array(
			'code'   => get_woocommerce_currency(),
			'symbol' => get_woocommerce_currency_symbol( get_woocommerce_currency() )
		);
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$customer_data['billing_address'] = array(
				'first_name' => $last_order->billing_first_name,
				'last_name'  => $last_order->billing_last_name,
				'company'    => $last_order->billing_company,
				'address_1'  => $last_order->billing_address_1,
				'address_2'  => $last_order->billing_address_2,
				'city'       => $last_order->billing_city,
				'state'      => $last_order->billing_state,
				'postcode'   => $last_order->billing_postcode,
				'country'    => $last_order->billing_country,
				'email'      => $last_order->billing_email,
				'phone'      => $last_order->billing_phone
			);
			$customer_data['shipping_address'] = array(
				'first_name' => $last_order->shipping_first_name,
				'last_name'  => $last_order->shipping_last_name,
				'company'    => $last_order->shipping_company,
				'address_1'  => $last_order->shipping_address_1,
				'address_2'  => $last_order->shipping_address_2,
				'city'       => $last_order->shipping_city,
				'state'      => $last_order->shipping_state,
				'postcode'   => $last_order->shipping_postcode,
				'country'    => $last_order->shipping_country,
				'phone'      => $last_order->billing_phone
			);
		} else {
			$customer_data['billing_address'] = array(
				'first_name' => $last_order->get_billing_first_name(),
				'last_name'  => $last_order->get_billing_last_name(),
				'company'    => $last_order->get_billing_company(),
				'address_1'  => $last_order->get_billing_address_1(),
				'address_2'  => $last_order->get_billing_address_2(),
				'city'       => $last_order->get_billing_city(),
				'state'      => $last_order->get_billing_state(),
				'postcode'   => $last_order->get_billing_postcode(),
				'country'    => $last_order->get_billing_country(),
				'email'      => $last_order->get_billing_email(),
				'phone'      => $last_order->get_billing_phone()
			);
			$customer_data['shipping_address'] = array(
				'first_name' => $last_order->get_shipping_first_name(),
				'last_name'  => $last_order->get_shipping_last_name(),
				'company'    => $last_order->get_shipping_company(),
				'address_1'  => $last_order->get_shipping_address_1(),
				'address_2'  => $last_order->get_shipping_address_2(),
				'city'       => $last_order->get_shipping_city(),
				'state'      => $last_order->get_shipping_state(),
				'postcode'   => $last_order->get_shipping_postcode(),
				'country'    => $last_order->get_shipping_country(),
				'phone'      => $last_order->get_billing_phone()
			);
		}

		$customer_data['name'] = $customer_data['billing_address']['first_name'] . ' ' . $customer_data['billing_address']['last_name'];
		$customer_data['email'] = $customer_data['billing_address']['email'];
		$customer_data['avatar_url'] = $this->get_avatar_url( $customer_data['billing_address']['email'] );

		$customer_data['profile_url'] = '';

		// Set the last orders.
		$customer_data['last_orders'] = $last_orders;

		// Get the purchased products.
		$purchased_products = array();
		$products_limit     = ( 0 < $products ) ? 'LIMIT ' . absint( $products ) : '';
		$products_query     = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT DISTINCT order_items.order_item_name
				FROM   $wpdb->postmeta AS postmeta
					LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items
					ON order_items.order_id = postmeta.post_id
					AND order_items.order_item_type = 'line_item'
				WHERE  postmeta.meta_key = '_billing_email'
				AND    postmeta.meta_value = %s
				$products_limit
			 ", $email )
		);

		foreach ( $products_query as $item ) {
			$purchased_products[] = $item->order_item_name;
		}

		$customer_data['purchased_products'] = $purchased_products;

		return apply_filters( 'woocommerce_help_scout_customer_data', $customer_data );
	}

	/**
	 * Get customer details.
	 *
	 * @param  WC_User $customer Customer data.
	 *
	 * @return array             Customer details.
	 */
	protected function get_customer_details( $customer ) {
		$sign_up_date   = $this->server->format_datetime( $customer->data->user_registered );
		$lifetime_value = get_user_meta( $customer->ID, '_money_spent', true );
		$currency_code  = get_woocommerce_currency();

		$data = array(
			'id'              => $customer->ID,
			'name'            => $customer->first_name . ' ' . $customer->last_name,
			'email'           => $customer->user_email,
			'total_spent'     => $lifetime_value,
			'sign_up'         => array(
				'date' => $sign_up_date,
				'diff' => human_time_diff( date( 'U', strtotime( $sign_up_date ) ), current_time( 'timestamp' ) )
			),
			'currency'        => array(
				'code'   => $currency_code,
				'symbol' => get_woocommerce_currency_symbol( $currency_code )
			),
			'avatar_url'      => $this->get_avatar_url( $customer->user_email ),
			'billing_address' => array(
				'first_name' => $customer->billing_first_name,
				'last_name'  => $customer->billing_last_name,
				'company'    => $customer->billing_company,
				'address_1'  => $customer->billing_address_1,
				'address_2'  => $customer->billing_address_2,
				'city'       => $customer->billing_city,
				'state'      => $customer->billing_state,
				'postcode'   => $customer->billing_postcode,
				'country'    => $customer->billing_country,
				'email'      => $customer->billing_email,
				'phone'      => $customer->billing_phone,
			),
			'shipping_address' => array(
				'first_name' => $customer->shipping_first_name,
				'last_name'  => $customer->shipping_last_name,
				'company'    => $customer->shipping_company,
				'address_1'  => $customer->shipping_address_1,
				'address_2'  => $customer->shipping_address_2,
				'city'       => $customer->shipping_city,
				'state'      => $customer->shipping_state,
				'postcode'   => $customer->shipping_postcode,
				'country'    => $customer->shipping_country,
			),
			'profile_url'     => add_query_arg( array( 'user_id' => $customer->ID ), admin_url( 'user-edit.php' ) )
		);

		return $data;
	}

	/**
	 * Get customer last orders.
	 *
	 * @param  WC_User $customer Customer data.
	 * @param  int     $total    Total of orders to list.
	 *
	 * @return array             Last orders list.
	 */
	protected function get_last_orders( $customer, $total ) {
		$orders = array();

		$args = array(
			'posts_per_page'      => intval( $total ),
			'post_type'           => 'shop_order',
			'meta_key'            => '_customer_user',
			'meta_value'          => $customer->ID,
			'ignore_sticky_posts' => 1
		);

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			$args['post_status'] = array_keys( wc_get_order_statuses() );
		}

		$query = get_posts( $args );

		foreach ( $query as $item ) {
			$order = new WC_Order( $item->ID );
			$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );
			$orders[] = array(
				'id'     => $order->get_order_number(),
				'url'    => add_query_arg( array( 'post' => $item->ID, 'action' => 'edit' ), admin_url( 'post.php' ) ),
				'date'   => $this->server->format_datetime( $order_date ),
				'total'  => $order->get_total(),
				'status' => $order->get_status()
			);
		}

		return $orders;
	}

	/**
	 * Get the customer purchased products.
	 *
	 * @param  WC_User $customer Customer data.
	 * @param  int     $products Total of products to list.
	 *
	 * @return array             Purchased products list.
	 */
	protected function get_purchased_products( $customer, $products ) {
		global $wpdb;

		$purchased_products = array();
		$limit              = ( 0 < $products ) ? 'LIMIT ' . absint( $products ) : '';
		$query              = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT DISTINCT order_items.order_item_name
				FROM   $wpdb->postmeta AS postmeta
					LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items
					ON order_items.order_id = postmeta.post_id
					AND order_items.order_item_type = 'line_item'
				WHERE  postmeta.meta_key = '_customer_user'
				AND    postmeta.meta_value = %d
				$limit
			 ", $customer->ID )
		);

		foreach ( $query as $item ) {
			$purchased_products[] = $item->order_item_name;
		}

		return $purchased_products;
	}

	/**
	 * Wrapper for @see get_avatar() which doesn't simply return the URL so we need to pluck it from the HTML img tag.
	 *
	 * @param  string $email The customer's email.
	 * @return string        The URL to the customer's avatar.
	 */
	private function get_avatar_url( $email ) {
		$avatar_html = get_avatar( $email );

		// Get the URL of the avatar from the provided HTML
		preg_match( '/src=["|\'](.+)[\&|"|\']/U', $avatar_html, $matches );

		if ( isset( $matches[1] ) && ! empty( $matches[1] ) ) {
			return esc_url_raw( $matches[1] );
		}

		return null;
	}

	/**
	 * Validate the request by checking:
	 *
	 * 1) the ID is a valid integer
	 * 2) the current user has the proper permissions
	 *
	 * @see    WC_API_Resource::validate_request().
	 * @param  string|int   $id      The customer ID.
	 * @param  string       $type    The request type, unused because this method overrides the parent class.
	 * @param  string       $context The context of the request, either `read`, `edit` or `delete`.
	 *
	 * @return int|WP_Error          Valid user ID or WP_Error if any of the checks fails.
	 */
	protected function validate_request( $id, $type, $context ) {

		$id = absint( $id );

		// Validate ID.
		if ( empty( $id ) ) {
			return new WP_Error( 'wc_help_scout_api_invalid_customer_id', __( 'Invalid customer ID', 'woocommerce-help-scout' ), array( 'status' => 404 ) );
		}

		if ( 'read' != $context ) {
			return new WP_Error( 'wc_help_scout_api_invalid_context', __( 'You have only read permission', 'woocommerce-help-scout' ), array( 'status' => 401 ) );
		}

		if ( ! current_user_can( 'list_users' ) ) {
			return new WP_Error( 'wc_help_scout_api_user_cannot_read_customer', __( 'You do not have permission to read this customer', 'woocommerce-help-scout' ), array( 'status' => 401 ) );
		}

		return $id;
	}
}
