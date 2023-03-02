<?php
/**
 * Meta Box: Coupon Usage
 *
 * Displays an order list in which the coupon has been used.
 *
 * @package WC_Store_Credit/Admin/Meta_Boxes
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Admin_Send_Credit_Page class.
 */
class WC_Store_Credit_Meta_Box_Coupon_Usage {

	/**
	 * Output the meta box.
	 *
	 * @since 3.3.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function output( $post ) {
		$coupon = wc_store_credit_get_coupon( $post->ID );
		$orders = self::get_orders( $coupon );

		include 'views/html-coupon-store-credit-usage.php';
	}

	/**
	 * Gets the orders in which the coupon has been used.
	 *
	 * @since 3.3.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 * @return array
	 */
	public static function get_orders( $coupon ) {
		/**
		 * Filters the arguments to query the orders in which the coupon has been used.
		 *
		 * @since 3.3.0
		 *
		 * @param array     $args   The query arguments.
		 * @param WC_Coupon $coupon Coupon object.
		 */
		$args = apply_filters(
			'wc_store_credit_coupon_usage_query_args',
			array(
				'status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-failed', 'wc-partial-payment' ),
				'return' => 'object',
			),
			$coupon
		);

		return wc_store_credit_get_coupon_orders( $coupon, $args );
	}

	/**
	 * Gets the table columns.
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public static function get_columns() {
		/**
		 * Filters the columns displayed in the Store Credit usage meta box.
		 *
		 * @since 3.3.0
		 *
		 * @param array $columns The table columns.
		 */
		return apply_filters(
			'wc_store_credit_coupon_usage_columns',
			array(
				'number' => __( 'Order Number', 'woocommerce-store-credit' ),
				'date'   => __( 'Date', 'woocommerce-store-credit' ),
				'status' => __( 'Status', 'woocommerce-store-credit' ),
				'credit' => __( 'Credit used', 'woocommerce-store-credit' ),
			)
		);
	}

	/**
	 * Gets the column value of the coupon related order.
	 *
	 * @since 3.3.0
	 *
	 * @param string    $column The column key.
	 * @param WC_Coupon $coupon Coupon object.
	 * @param WC_Order  $order  Order object.
	 * @return mixed
	 */
	public static function get_column_value( $column, $coupon, $order ) {
		$value = '&ndash;';

		switch ( $column ) {
			case 'number':
				$value = sprintf(
					'<a href="%1$s">#%2$s</a>',
					esc_url( $order->get_edit_order_url() ),
					esc_html( $order->get_order_number() )
				);
				break;
			case 'date':
				$value = wc_store_credit_get_datetime_html( $order->get_date_created() );
				break;
			case 'status':
				$value = sprintf(
					'<mark class="order-status %1$s"><span>%2$s</span></mark>',
					esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ),
					esc_html( wc_get_order_status_name( $order->get_status() ) )
				);
				break;
			case 'credit':
				$credit_used = wc_get_coupon_store_credit_used_for_order( $order, $coupon );
				$value       = wc_price( $credit_used );
				break;
		}

		/**
		 * Filters the column value of the coupon related order.
		 *
		 * @since 3.3.0
		 *
		 * @param mixed     $value  The column value.
		 * @param WC_Coupon $coupon Coupon object.
		 * @param string    $column The column key.
		 * @param WC_Order  $order  Order object.
		 */
		return apply_filters( 'wc_store_credit_coupon_usage_column_value', $value, $column, $coupon, $order );
	}
}
