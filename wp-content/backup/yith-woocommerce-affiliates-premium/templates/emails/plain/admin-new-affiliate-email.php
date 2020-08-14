<?php
/**
 * New affiliate email template plain
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

echo __( 'A new affiliate has been registered for user', 'yith-woocommerce-affiliates' );
echo ' ' . $affiliate['user_login'];
echo "\n";

echo "\n==========\n\n";

echo __( 'Username:', 'yith-woocommerce-affiliates' );
echo ' ' . $affiliate['user_login'];
echo "\n";

echo __( 'User email:', 'yith-woocommerce-affiliates' );
echo ' ' . $affiliate['user_email'];
echo "\n";

echo  __( 'Affiliate token:', 'yith-woocommerce-affiliates' );
echo ' ' . $affiliate['token'];
echo '(' . $affiliate_referral_url. ')';
echo "\n";

echo __( 'Payment email:', 'yith-woocommerce-affiliates' );
echo isset( $affiliate['payment_email'] ) ? ' ' . $affiliate['payment_email'] : ' ' . __( 'N/A', 'yith-woocommerce-affiliates' );
echo "\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

