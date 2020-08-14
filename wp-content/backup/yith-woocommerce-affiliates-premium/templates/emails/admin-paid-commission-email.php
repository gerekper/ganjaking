<?php
/**
 * New pending payment email template
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

<h2><?php _e( 'New payments issued to the gateway', 'yith-woocommerce-affiliates' ) ?></h2>

<p><?php printf( '%s <strong>%s</strong> %s', _n( 'Payment', 'Payments', count( $payments ), 'yith-wcaf' ), implode( ' | #', array_keys( $payments ) ), _n( 'was issued to gateway and is awaiting IPN response', 'were issued to gateway and are awaiting IPN response', count( $payments ), 'yith-wcaf' ) ); ?></p>

<?php if( ! empty( $payments ) ): ?>
<?php foreach( $payments as $payment ): ?>
	<p>
		<strong><?php _e( 'Payment:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="ID">#<?php echo $payment['ID'] ?></span><br />
		<strong><?php _e( 'Affiliate:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="affiliate"><a href="mailto:<?php echo $payment['user_email'] ?>"><?php echo $payment['user_login'] ?></a> (<?php _e( 'Affiliate token:', 'yith-woocommerce-affiliates' ) ?> <?php echo $payment['affiliate_token']?>)</span><br />
		<strong><?php _e( 'Receiver:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="receiver"><?php echo $payment['payment_email']?></span><br />
		<strong><?php _e( 'Amount:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="amount"><?php echo wc_price( $payment['amount'], array( 'currency' => $currency ) ) ?></span><br />
	</p>
<?php endforeach; ?>
<?php endif; ?>

<?php do_action( 'woocommerce_email_footer',$email ); ?>
