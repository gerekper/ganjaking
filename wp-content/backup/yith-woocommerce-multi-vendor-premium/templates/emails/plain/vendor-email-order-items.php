<?php
/**
 * Email Order Items (plain)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$vendor_products = $vendor->get_products( array( 'fields' => 'ids' ) );

foreach ( $items as $item_id => $item ) :
    $products_from_item = null;

    if( YITH_Vendors()->is_wc_2_7_or_greather && is_callable( array( $item, 'get_product' ) ) ){
        $products_from_item = $item->get_product();
    }

    else {
        $products_from_item = $order->get_product_from_item( $item );
    }

    /** @var $_product WC_Product */

	$_product = apply_filters( 'woocommerce_order_item_product', $products_from_item, $item );

     if( ! in_array( yit_get_base_product_id( $_product ), $vendor_products ) ) {
        continue;
    }

    $item_meta = YITH_Vendors()->is_wc_2_7_or_greather ? new WC_Order_Item_Product( $item ) : new WC_Order_Item_Meta( $items['item_meta'], $_product );

    /** @var $commission YITH_Commission */
    $commission = ! empty( $item['commission_id'] ) ? YITH_Commission( $item['commission_id'] ) : false;

	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {

		// Title
		echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item, $_product->is_visible() );

		// SKU
		if ( $show_sku && $_product->get_sku() ) {
			echo ' (#' . $_product->get_sku() . ')';
		}

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

		// Variation
		if ( ! empty( $item_meta ) && ! empty( $item_meta->meta ) ) {
			echo ( $item_meta_content = $item_meta->display( true, true ) ) ? "\n" . $item_meta_content : '';
		}

        // Commission id
        if ( ! empty( $item['commission_id'] ) && $commission ) {
            echo "\n" . _x( 'Commission id:', 'New Order Email', 'yith-woocommerce-product-vendors' ) . ' ' . $commission->id . ' (' . $commission->get_view_url( 'admin' ) . ')';
            echo "\n" . _x( 'Commission rate:', 'New Order Email', 'yith-woocommerce-product-vendors' ) . ' ' . $commission->rate;
	        echo "\n" . _x( 'Tax:', 'New Order Email', 'yith-woocommerce-product-vendors' ) . ' ' . $order->get_item_tax( $item );
	        echo "\n" . _x( 'Earnings:', 'New Order Email', 'yith-woocommerce-product-vendors' ) . ' ' . $commission->amount;
        }

		// Quantity
		echo "\n" . sprintf( __( 'Quantity: %s', 'yith-woocommerce-product-vendors' ), $item['qty'] );

		// Cost
		echo "\n" . sprintf( __( 'Cost: %s', 'yith-woocommerce-product-vendors' ), $order->get_formatted_line_subtotal( $item ) );

		// Download URLs
		if ( $show_download_links && $_product->exists() && $_product->is_downloadable() ) {
			$download_files = $order->get_item_downloads( $item );
			$i              = 0;

			foreach ( $download_files as $download_id => $file ) {
				$i++;

				if ( count( $download_files ) > 1 ) {
					$prefix = sprintf( __( 'Download %d', 'yith-woocommerce-product-vendors' ), $i );
				} elseif ( $i == 1 ) {
					$prefix = __( 'Download', 'yith-woocommerce-product-vendors' );
				}

				echo "\n" . $prefix . '(' . esc_html( $file['name'] ) . '): ' . esc_url( $file['download_url'] );
			}
		}

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );

	}

	// Note
	if ( $show_purchase_note && ( $purchase_note =  $_product->get_purchase_note() ) ) {
		echo "\n" . do_shortcode( wp_kses_post( $purchase_note ) );
	}

	echo "\n\n";

endforeach;
