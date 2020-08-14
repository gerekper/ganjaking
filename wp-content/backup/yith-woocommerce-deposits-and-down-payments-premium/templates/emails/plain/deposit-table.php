<?php
/**
 * Deposit table (plain)
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit; // Exit if accessed directly
}

$items = $parent_order->get_items( 'line_item' );
$total_paid = 0;
$total_to_pay = 0;

if( ! empty( $items ) ):
	foreach( $items as $item_id => $item ):
		if( ! isset( $item['deposit'] ) || ! $item['deposit'] ) {
			continue;
		}

		$product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : $parent_order->get_product_from_item( $item );
		$suborder = wc_get_order( $item['full_payment_id'] );

		if( ! $product || ! $suborder || in_array( $suborder->get_status(), array( 'completed', 'processing', 'cancelled' ) ) ){
			continue;
		}

		$paid = $parent_order->get_item_total( $item, true );
		$paid += in_array( $suborder->get_status(), array( 'processing', 'completed' ) ) ? $suborder->get_total() : 0;
		$to_pay = in_array( $suborder->get_status(), array( 'processing', 'completed' ) ) ? 0 : $suborder->get_total();

		$total_paid += $paid;
		$total_to_pay += $to_pay;

		echo sprintf( '%s #%d', __( 'Order', 'yith-woocommerce-deposits-and-down-payments' ), $suborder->get_order_number() );
		echo "\n" . $item['name'];
		echo "\n" . wc_get_order_status_name( $suborder->get_status() );
		echo "\n" . sprintf( '%s (of %s)', sprintf( get_woocommerce_price_format(), '', $paid ), sprintf( get_woocommerce_price_format(), '', $paid + $to_pay ) );
		echo "\n\n";
	endforeach;
endif;

echo "==========\n\n";

echo sprintf( '%s: %s', __( 'Total paid', 'yith-woocommerce-deposits-and-down-payments' ), sprintf( get_woocommerce_price_format(), '', $total_paid ) ) . "\n";
echo sprintf( '%s: %s', __( 'Total to pay', 'yith-woocommerce-deposits-and-down-payments' ), sprintf( get_woocommerce_price_format(), '', $total_to_pay ) ) . "\n";