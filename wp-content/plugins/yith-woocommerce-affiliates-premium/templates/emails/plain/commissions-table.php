<?php
/**
 * Commission table template part plain
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
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

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php
if( ! empty( $commissions ) ) {

	echo strtoupper( __( 'Commissions', 'yith-woocommerce-affiliates' ) ) . "\n\n";

	$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $token );
	$user_id   = $affiliate['user_id'];

	$user_data = get_userdata( $user_id );

	if ( ! $user_data ) {
		return '';
	}

	$user_email = $user_data->user_email;

	$username = '';
	if ( $user_data->first_name || $user_data->last_name ) {
		$username .= esc_html( ucfirst( $user_data->first_name ) . ' ' . ucfirst( $user_data->last_name ) );
	} else {
		$username .= esc_html( ucfirst( $user_data->display_name ) );
	}

	echo sprintf( __( 'Affiliate: %s', 'yith-woocommerce-affiliates' ), $username ) . "\n\n";

	foreach ( $commissions as $item ) {
		if ( apply_filters( 'yith_wcaf_commission_visible', true, $item ) ) {

			echo sprintf( '#%d', $item['ID'] );

			echo ' - ';

			$product_id = $item['product_id'];
			$order_id   = $item['order_id'];
			$order      = wc_get_order( $order_id );

			if ( $order ) {
				$line_items = $order->get_items();
				$line_item  = isset( $line_items[ $item['line_item_id'] ] ) ? $line_items[ $item['line_item_id'] ] : false;

				if ( $line_item ) {
					/**
					 * @var $line_item \WC_Order_Item_Product
					 */
					$product = is_object( $line_item ) ? $line_item->get_product() : $order->get_product_from_item( $line_item );
				}
			} else {
				$product = wc_get_product( $product_id );
			}

			if ( isset( $product ) && $product ) {
				echo $product->get_title();
			}

			echo ' | ';

			$exclude_tax = YITH_WCAF_Commission_Handler()->get_option( 'exclude_tax' );

			$line_items = $order->get_items( 'line_item' );

			if ( ! empty( $line_items ) ) {
				$line_item = isset( $line_items[ $item['line_item_id'] ] ) ? $line_items[ $item['line_item_id'] ] : '';

				if ( ! empty( $line_item ) ) {
					$column = '';
					$column .= wc_price( $order->get_item_subtotal( $line_item, 'yes' != $exclude_tax ) * $line_item['qty'] );

					echo $column;
				}
			}

			echo ' | ';

			$column = '';
			$column .= sprintf( '%.2f%%', number_format( round( $item['rate'], 2 ), 2 ) );

			echo $column;

			echo ' | ';

			$column = '';
			$column .= wc_price( $item['amount'] );

			echo $column;

			echo "\n";

		}
	}
	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
}
