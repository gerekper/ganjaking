<?php
/**
 * Order details email (plain text).
 *
 * @version 2.1.52
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_before_order_table', $order, true, false, $email );

echo "\n";

$pass_shipping = false;

foreach ( $order->get_items() as $item_id => $item ) :
	$_product   = $item->get_product();
	$product_id = ( 'product_variation' === $_product->post_type ) ? $_product->get_parent_id() : $_product->get_id();

	$pass_shipping |= 'yes' === get_post_meta( $product_id, '_wcpv_product_pass_shipping', true );
	$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product_id );

	// remove the order items that are not from this vendor
	if ( $this_vendor !== $vendor_id ) {
		continue;
	}

	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {

		// Title
		echo esc_html( wp_strip_all_tags( apply_filters( 'woocommerce_order_item_name', $item['name'], $item, false ) ) );

		// SKU
		if ( $_product->get_sku() ) {
			echo ' (#' . esc_html( wp_strip_all_tags( $_product->get_sku() ) ) . ')';
		}

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

		// Variation
		echo wp_kses_post( strip_tags( wc_display_item_meta( $item, array(
			'before'    => "\n- ",
			'separator' => "\n- ",
			'after'     => "",
			'echo'      => false,
			'autop'     => false,
		) ) ) );

		// Quantity
		echo "\n" . sprintf( esc_html__( 'Quantity: %s', 'woocommerce-product-vendors' ), esc_html( apply_filters( 'woocommerce_email_order_item_quantity', $item['qty'], $item ) ) );

		// Cost
		echo "\n" . sprintf( esc_html__( 'Cost: %s', 'woocommerce-product-vendors' ), esc_html( wp_strip_all_tags( $order->get_formatted_line_subtotal( $item ) ) ) );

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
	}

	echo "\n\n";

endforeach;

echo "==========\n\n";

$shipping_method = $order->get_shipping_method();
$customer_note   = $order->get_customer_note();

if ( $pass_shipping && ! empty( $shipping_method ) ) {
	echo esc_html__( 'Shipping method:', 'woocommerce-product-vendors' ) . "\t " . esc_html( $shipping_method ) . "\n";
}

/**
 * Determine if we should show the customer added note.
 *
 * @since 2.1.52
 * @param boolean  $show_note Whether to show cusotmer notes. Default true.
 * @param WC_Order $order     Order object.
 */
if ( $customer_note && apply_filters( 'wcpv_email_to_vendor_show_notes', true, $order ) ) {
	echo esc_html__( 'Customer note:', 'woocommerce-product-vendors' ) . "\t " . wp_kses_post( wptexturize( $customer_note ) ) . "\n";
}

do_action( 'woocommerce_email_after_order_table', $order, true, false, $email );
