<?php
/**
 * Plain Template Email for Request A Quote List table
 *
 * @package YITH Woocommerce Request A Quote
 * @version 1.4.4
 * @since   1.0.0
 * @author  YITH
 */

do_action( 'yith_ywraq_email_before_raq_table', $raq_data );
$show_total_column = ! ( get_option( 'ywraq_hide_total_column', 'yes' ) === 'yes' );

echo "****************************************************\n\n";

echo "\n";

if ( ! empty( $raq_data['raq_content'] ) ) :
	foreach ( $raq_data['raq_content'] as $item ) :

		if ( isset( $item['variation_id'] ) ) {
			$product = wc_get_product( $item['variation_id'] );
		} else {
			$product = wc_get_product( $item['product_id'] );
		}

		$title = $product->get_title();

		if ( $product->get_sku() !== '' && get_option( 'ywraq_show_sku' ) === 'yes' ) {
			$sku    = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $product->get_sku();
			$title .= ' ' . esc_html( apply_filters( 'ywraq_sku_label_html', $sku, $product ) );
		}

		echo esc_html( $title . ' ' . yith_ywraq_get_product_meta( $item, false ) ) . ' | ';
		echo $item['quantity'];
		if ( $show_total_column ) {
			echo ' ' . apply_filters( 'yith_ywraq_hide_price_template', WC()->cart->get_product_subtotal( $product, $item['quantity'] ), $product->get_id(), $item );
		}

		echo "\n";
	endforeach;
endif;

echo "\n****************************************************\n\n";

do_action( 'yith_ywraq_email_after_raq_table', $raq_data );
