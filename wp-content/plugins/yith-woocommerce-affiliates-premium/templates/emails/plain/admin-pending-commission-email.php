<?php
/**
 * New confirmed commission email template plain
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

printf( __( "Commission #%s switched to pending status and is awaiting payment\n", 'yith-woocommerce-affiliates' ), $commission['ID'] );

echo "\n==========\n\n";

echo __( 'Commission:', 'yith-woocommerce-affiliates' );
echo ' #' . $commission['ID'];
echo "\n";

echo __( 'Affiliate:', 'yith-woocommerce-affiliates' );
echo ' ' . $commission['user_login'];
echo ' ' . '(' . __( 'Affiliate token:', 'yith-woocommerce-affiliates' ) . ' ' . $affiliate['token'] . ')';
echo "\n";

echo  __( 'Rate:', 'yith-woocommerce-affiliates' );
echo ' ' . number_format( $commission['rate'], 2 ) . '%';
echo "\n";

echo  __( 'Amount:', 'yith-woocommerce-affiliates' );
echo ' ' . printf( get_woocommerce_price_format(), get_woocommerce_currency_symbol( $currency ), $commission['amount'] );
echo "\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

