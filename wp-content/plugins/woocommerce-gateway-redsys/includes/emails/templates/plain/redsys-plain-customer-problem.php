<?php
/**
 * Customer cancelled order email
 *
 * @package WooCommerce Redsys Gateway
 * @since 1.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
echo '= ' . esc_attr( $email_heading ) . " =\n\n"; // phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
echo sprintf( esc_html__( 'The order #%d has been cancelled. The order details:', 'woocommerce' ), esc_html( $order->id ) ) . "\n\n"; // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
