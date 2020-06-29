<?php
/**
 * New paid commissions email template plain
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

echo "= " . $email_heading . " =\n\n";
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

printf( _n( "Payment %s was issued to gateway and is awaiting IPN response\n", "Payments %s were issued to gateway and are awaiting IPN response\n", count( $payments ), 'yith-wcaf' ), implode( ' | #', array_keys( $payments ) ) );

echo "\n==========\n\n";

$first = true;
if( ! empty( $payments ) ):
	foreach( $payments as $payment ):

		if( ! $first ):
			echo "\n==========\n\n";
		endif;

		echo __( 'Payment:', 'yith-woocommerce-affiliates' );
		echo ' #' . $payment['ID'];
		echo "\n";

		echo __( 'Affiliate:', 'yith-woocommerce-affiliates' );
		echo ' ' . $payment['user_login'];
		echo ' ' . '(' . __( 'Affiliate token:', 'yith-woocommerce-affiliates' ) . ' ' . $payment['affiliate_token'] . ')';
		echo "\n";

		echo  __( 'Receiver:', 'yith-woocommerce-affiliates' );
		echo ' ' . $payment['payment_email'];
		echo "\n";

		echo  __( 'Amount:', 'yith-woocommerce-affiliates' );
		echo ' ' . printf( get_woocommerce_price_format(), get_woocommerce_currency_symbol( $currency ), $payment['amount'] );
		echo "\n";

		$first = false;
	endforeach;
endif;

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

