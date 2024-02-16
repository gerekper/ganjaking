<?php
/**
 * Thankyou receipe.
 *
 * @package WooCommerce Redsys Gateway
 * @since 10.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Show receipe in thank you page.
 *
 * @param string $text Text.
 * @param object $order Order.
 */
function redsys_show_recipe_auth( $text, $order ) {

	if ( ! empty( $order ) ) {
		$order_id = $order->get_id();
		if ( WCRed()->is_paid( $order_id ) && WCRed()->is_redsys_order( $order_id ) ) {
			$numero_autorizacion = WCRed()->get_order_auth( $order_id );
			$website             = get_site_url();
			$date                = WCRed()->get_order_date( $order_id );
			$hour                = WCRed()->get_order_hour( $order_id );
			$fuc                 = WCRed()->get_redsys_option( 'customer', 'redsys' );
			$commerce_name       = WCRed()->get_redsys_option( 'commercename', 'redsys' );
			$textthabks          = __( 'Thanks for your purchase, the details of your transaction are: ', 'woocommerce-redsys' ) . '<br />';
			$textthabks         .= __( 'Website: ', 'woocommerce-redsys' ) . $website . '<br />';
			$textthabks         .= __( 'FUC: ', 'woocommerce-redsys' ) . $fuc . '<br />';
			$textthabks         .= __( 'Authorization Number: ', 'woocommerce-redsys' ) . $numero_autorizacion . '<br />';
			$textthabks         .= __( 'Commmerce Name: ', 'woocommerce-redsys' ) . $commerce_name . '<br />';
			$textthabks         .= __( 'Date: ', 'woocommerce-redsys' ) . $date . '<br />';
			$textthabks         .= __( 'Hour: ', 'woocommerce-redsys' ) . $hour . '<br />';
			return $text . '<br />' . $textthabks;
		} else {
			return $text;
		}
	} else {
		return $text;
	}
}
add_filter( 'woocommerce_thankyou_order_received_text', 'redsys_show_recipe_auth', 20, 2 );
