<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var YITH_Commission $commission
 * @var YITH_Vendor $vendor
 * @var WC_Product $product
 * @var WC_Order $order
 * @var array $item
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$commissions_total = $shipping_fee_total = 0;
$wc_price_args     = apply_filters( 'yith_wcmv_commissions_bulk_email_wc_price_args', array() );
$rate = sprintf( "%s%s || ", '%', _x( 'Rate', '[Email]: meanse commissions rate', 'yith-woocommerce-product-vendors' ) );
add_filter( 'yith_wcmv_commission_have_been_calculated_text', '__return_empty_string' );

echo __( 'Commission ID', 'yith-woocommerce-product-vendors' ) . ' || ' . "\t";
echo __( 'Order ID', 'yith-woocommerce-product-vendors' ) . ' || ' . "\t";
echo __( 'SKU', 'yith-woocommerce-product-vendors' ) . ' || ' . "\t";
echo __( 'Amount', 'yith-woocommerce-product-vendors' ) . ' || ' . "\t";
echo $rate . "\t";
echo __( 'New Status', 'yith-woocommerce-product-vendors' ) . "\t";

if ( $show_note ) {
	echo ' || ' . __( 'Note', 'yith-woocommerce-product-vendors' ) . ' || ';
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";


foreach ( $commissions as $commission ) :
	if ( 'shipping' == $commission->type ) {
		$shipping_fee_total = $shipping_fee_total + $commission->get_amount();
	} else {
		$commissions_total = $commissions_total + $commission->get_amount();
	}

	echo "#" . $commission->id;
	echo ' || ' . "\t";

	$order_id = $commission->get_order() instanceof WC_Order ? $commission->get_order()->get_id() : '';
	$currency = $commission->get_order() instanceof WC_Order ? $commission->get_order()->get_currency() : '';

	if ( ! empty( $order_id ) ) :
		echo "#" . $order_id;
	else:
		echo ' - ';

	endif;
	echo ' || ' . "\t";

	if ( 'shipping' == $commission->type ) {
		$info = _x( 'Shipping', '[admin] part of shipping fee details', 'yith-woocommerce-product-vendors' );
	} else {
		$info = '-';
		$item = $commission->get_item();
		if ( $item instanceof WC_Order_Item ) {
			//array( 'currency' => $item->get_currency() )
			$product = $commission->get_product();

			if ( $product ) {
				$sku = $product->get_sku( 'view' );

				if ( ! empty( $sku ) ) {
					$info = $sku;
				}
			}
		}
	}

	echo $info;
	echo ' || ' . "\t";
	echo $commission->get_amount( 'display', array( 'currency' => $currency ) );
	echo ' || ' . "\t";
	echo $commission->get_rate( 'display' );
	echo ' || ' . "\t";
	echo $new_commission_status;

	if ( $show_note ) :
		echo ' || ' . "\t";
		$msg = '-';

		if ( $item instanceof WC_Order_Item_Product ) {
			/**
			 * Check if the commissions included tax
			 */
			$commission_included_tax = wc_get_order_item_meta( $item->get_id(), '_commission_included_tax', true );
			/**
			 * Check if the commissions included coupon
			 */
			$commission_included_coupon = wc_get_order_item_meta( $item->get_id(), '_commission_included_coupon', true );

			$msg = YITH_Commissions::get_tax_and_coupon_management_message( $commission_included_tax, $commission_included_coupon );
		}

		echo $msg;
	endif;
	echo "\n";
endforeach;

if ( ! empty( $commissions_total ) ) :
	_ex( 'Total product commissions', '[Email commissions report]: Total commissions amount', 'yith-woocommerce-product-vendors' );
	echo ' : ';
	echo wc_price( $commissions_total, $wc_price_args );
	echo "\n\n";
endif;

if ( ! empty( $shipping_fee_total ) ) :
	_ex( 'Total Shipping Fee', '[Email commissions report]: Total commissions amount', 'yith-woocommerce-product-vendors' );
	echo ' : ';
	echo wc_price( $shipping_fee_total, $wc_price_args );
	echo "\n\n";
endif;

remove_filter( 'yith_wcmv_commission_have_been_calculated_text', '__return_empty_string' );

