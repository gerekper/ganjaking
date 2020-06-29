<?php
/**
 * New paid commission email template
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

<?php do_action( 'woocommerce_email_header', $email_heading,$email ); ?>

<h2><?php _e( 'New payments have been sent to your account', 'yith-woocommerce-affiliates' ) ?></h2>

<p><?php printf( '%s <strong>%s</strong> %s %s', __( 'Payment', 'yith-woocommerce-affiliates' ), $payment['ID'], __( 'was sent to your account, at', 'yith-wcaf' ), $payment['payment_email'] ); ?></p>

<strong><?php _e( 'Amount:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="amount"><?php echo wc_price( $payment['amount'], array( 'currency' => $currency ) ) ?></span><br />

<?php do_action( 'woocommerce_email_footer',$email ); ?>

