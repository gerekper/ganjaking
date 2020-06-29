<?php
/**
 * HTML Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  YITH
 */

do_action( 'yith_ywraq_email_before_raq_table', $order );


echo "****************************************************\n\n";

echo "\n";

$items = $order->get_items();
if ( ! empty( $items ) ) :
	foreach ( $items as $item ) :
		$product = wc_get_product( $item['product_id'] );

		$subtotal = wc_price( $item['line_total'] );

		$meta  = yith_ywraq_get_product_meta_from_order_item( $item['item_meta'], false );
		$title = $product->get_title();

		if ( $product->get_sku() !== '' && get_option( 'ywraq_show_sku' ) === 'yes' ) {
			$sku    = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
			$title .= apply_filters( 'ywraq_sku_label_html', $sku, $_product );
		}

		echo $title . ' ' . yith_ywraq_get_product_meta( $item, false ) . ' | ';
		echo $item['qty'];
		echo ' ' . apply_filters( 'ywraq_quote_subtotal_item_plain', $subtotal, $item['line_total'], $product );
		echo "\n";
	endforeach;


	foreach ( $order->get_order_item_totals() as $key => $total ) {
		echo $total['label'] . ': ' . $total['value'];
		echo "\n";
	}

	echo "\n";
endif;

echo "\n****************************************************\n\n";

do_action( 'yith_ywraq_email_after_raq_table', $order );
