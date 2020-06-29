<?php
/**
 * Plain Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @version 2.2.7
 * @since   1.6.0
 * @author  YITH
 *
 * @var $email_heading array
 * @var $reason array
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo esc_html( $email_heading . "\n\n" );

$order_id = $order->get_id();
$order_id = apply_filters( 'ywraq_quote_number', $order_id );

if ( 'accepted' === $status ) :
	// translators: number of quote .
	printf( esc_html__( 'The Proposal #%d has been accepted', 'yith-woocommerce-request-a-quote' ), esc_html( $order_id ) );
else :
	// translators: number of quote and reason .
	printf( esc_html__( 'The Proposal #%1$d has been rejected. %2$s', 'yith-woocommerce-request-a-quote' ), esc_html( $order_id ), esc_html( $reason ) );

endif;
echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
