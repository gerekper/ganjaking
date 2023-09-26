<?php
/**
 * New order email (plain)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

printf( esc_html__( 'Hi %s,', 'woocommerce-advanced-notifications' ), esc_html( $recipient_name ) );

echo "\n\n";

/* translators: 1: first name 2: last name */
$order_first_name = $order->get_billing_first_name();
$order_last_name  = $order->get_billing_last_name();
printf( esc_html__( 'You have received an order from %1$s %2$s:', 'woocommerce-advanced-notifications' ), esc_html( $order_first_name ), esc_html( $order_last_name ) );

echo "\n\n";

echo "============================================================\n";

printf( esc_html__( 'Order: %s', 'woocommerce-advanced-notifications' ), esc_html( $order->get_order_number() ) );

echo "\n";

$datetime_formatted = wc_format_datetime( $order->get_date_created() );
printf( '%s', esc_html( $datetime_formatted ) );

echo "\n";

echo "============================================================";

$displayed_total = 0;

foreach ( $order->get_items() as $item_id => $item ) {

	if ( is_callable( array( $item, 'get_product' ) ) ) {
		$_product = $item->get_product();
	} else {
		$_product = $order->get_product_from_item( $item );
	}

	$display = false;

	$product_id = $_product->is_type( 'variation' ) ? $_product->get_parent_id() : $_product->get_id();

	if ( $triggers['all'] || in_array( $product_id, $triggers['product_ids'] ) || in_array( $_product->get_shipping_class_id(), $triggers['shipping_classes'] ) )
		$display = true;

	if ( ! $display ) {

		$cats = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );

		if ( sizeof( array_intersect( $cats, $triggers['product_cats'] ) ) > 0 ) {
			$display = true;
		}

	}

	if ( ! $display && ! empty( $show_all_items ) ) {
		$display = true;
	}

	if ( ! $display ) {
		continue;
	}

	$displayed_total += $order->get_line_total( $item, true );

	$item_meta = new WC_Order_Item_Product( $item_id );

	// Product name.
	$item_product_name = apply_filters( 'woocommerce_order_product_title', $item['name'], $_product );
	echo "\n" . esc_html( $item_product_name );

	// SKU.
	echo $_product->get_sku() ? ' (#' . esc_html( $_product->get_sku() ) . ')' : '';

	if ( $show_prices ) {
		echo ' (' . esc_html( $order->get_line_subtotal( $item ) ) . ')';
	}

	echo ' X ' . esc_html( $item['qty'] );

	// Allow other plugins to add additional product information here.
	do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

	// Variation.
	$item_string = wp_strip_all_tags(
		wc_display_item_meta(
			$item,
			array(
				'before'    => "\n- ",
				'separator' => "\n- ",
				'after'     => "",
				'echo'      => false,
				'autop'     => false,
			)
		)
	);

	echo esc_html( $item_string );

	// File URLs.
	if ( $show_download_links ) {
		wc_display_item_downloads( $item );
	}

	// Allow other plugins to add additional product information here.
	do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );

	echo "\n";

}

echo "============================================================\n";

if ( $show_totals ) {

	$totals = $order->get_order_item_totals();
	if ( $totals ) {
		foreach ( $totals as $total ) {
			echo esc_html( $total['label'] ) . ' ';
			echo esc_html( preg_replace( "/&#?[a-z0-9]{2,8};/i", "", $total['value'] ) );
			echo "\n";
		}
	} else {
		// Only show the total for displayed items.
		echo esc_html__( 'Total', 'woocommerce-advanced-notifications' ) . ': ';
		echo esc_html( $displayed_total );
		echo "\n";
	}

}

if ( $order->get_customer_note() ) {
	echo esc_html__( 'Note', 'woocommerce-advanced-notifications' ) . ': ';
	echo wp_kses_post( wptexturize( $order->get_customer_note() ) );
	echo "\n";
}

echo "\n\n";

/**
* @hooked WC_Emails::customer_details() Shows customer details
* @hooked WC_Emails::email_address() Shows email address
*/
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n\n";

echo "Regards,\n" . esc_html( $blogname );
