<?php
/**
 * Store Credit: Rest API manager
 *
 * @package WC_Store_Credit
 * @since   2.4.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_REST_API.
 */
class WC_Store_Credit_REST_API {

	/**
	 * Constructor.
	 *
	 * @since 2.4.4
	 */
	public function __construct() {
		add_filter( 'woocommerce_rest_prepare_shop_order', array( $this, 'order_response' ), 10, 3 ); // V1.
		add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'order_response' ), 10, 3 );
		add_filter( 'woocommerce_rest_shop_order_schema', array( $this, 'order_schema' ) );
	}

	/**
	 * Filters the order data returned in a REST API response.
	 *
	 * @since 2.4.4
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param mixed            $order    Order object or post object.
	 * @param WP_REST_Request  $request  Request object.
	 * @return WP_REST_Response
	 */
	public function order_response( $response, $order, $request ) {
		if ( $order instanceof WP_Post ) {
			$order = wc_store_credit_get_order( $order->ID );
		}

		$credit = wc_get_store_credit_used_for_order( $order );

		if ( 0 < $credit ) {
			$data = $this->process_order_data( $response->get_data(), $order, $request );

			$response->set_data( $data );
		}

		return $response;
	}

	/**
	 * Processes the order data.
	 *
	 * @since 2.4.4
	 *
	 * @param array    $data    The order data.
	 * @param WC_Order $order   Order object.
	 * @param mixed    $filters Request filters.
	 * @return array
	 */
	protected function process_order_data( $data, $order, $filters = array() ) {
		$credit = wc_get_store_credit_used_for_order( $order );

		if ( 0 < $credit ) {
			$dp = ( isset( $filters['dp'] ) ? intval( $filters['dp'] ) : 2 );

			// Include the store credit info.
			$data['store_credit'] = wc_format_decimal( $credit, $dp );

			if ( ! wc_store_credit_apply_before_tax( $order ) ) {
				$total_discount = $order->get_total_discount();
				$discount_total = max( 0, $total_discount - $credit );

				// Removes the store credit from the cart discount.
				$data['discount_total'] = wc_format_decimal( $discount_total, $dp );
			}
		}

		return $data;
	}

	/**
	 * Filters the order schema.
	 *
	 * @since 2.4.4
	 *
	 * @param array $schema The order schema.
	 * @return array
	 */
	public function order_schema( $schema ) {
		$schema['store_credit'] = array(
			'description' => __( 'Store credit discount for the order.', 'woocommerce-store-credit' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit' ),
			'readonly'    => true,
		);

		return $schema;
	}
}

return new WC_Store_Credit_REST_API();
