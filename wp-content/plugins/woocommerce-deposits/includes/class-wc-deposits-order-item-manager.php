<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Order_Item_Manager class.
 */
class WC_Deposits_Order_Item_Manager {

	/**
	 * See if an item is a deposit.
	 *
	 * @param  array  $item
	 * @return boolean
	 */
	public static function is_deposit( $item ) {
		return 'line_item' === $item['type'] && ! empty( $item['is_deposit'] );
	}

	/**
	 * Get payment plan if used.
	 *
	 * @return bool or object
	 */
	public static function get_payment_plan( $item ) {
		$payment_plan = ! empty( $item['payment_plan'] ) ? absint( $item['payment_plan'] ) : 0;
		return $payment_plan ? WC_Deposits_Plans_Manager::get_plan( $payment_plan ) : false;
	}

	/**
	 * See if an item has been fully paid.
	 *
	 * @param  array  $item
	 * @param  WC_Order $parent_order
	 * @return boolean
	 */
	public static function is_fully_paid( $item, $parent_order = NULL ) {
		$remaining_balance_order_id = ! empty( $item['remaining_balance_order_id'] ) ? absint( $item['remaining_balance_order_id'] ) : 0;
		$remaining_balance_paid     = ! empty( $item['remaining_balance_paid'] );
		$payment_plan               = ! empty( $item['payment_plan'] ) ? absint( $item['payment_plan'] ) : 0;

		if ( $remaining_balance_order_id ) {
			$remaining_balance_order = wc_get_order( $remaining_balance_order_id );
			return $remaining_balance_order->has_status( array( 'processing', 'completed' ) );
		} elseif ( $payment_plan && $parent_order ) {
			return WC_Deposits_Plans_Manager::is_order_plan_fully_paid( $parent_order );
		} else {
			return $remaining_balance_paid;
		}
	}
}
