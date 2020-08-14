<?php
/**
 * New confirmed commission email template
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

<h2><?php _e( 'New commission confirmed', 'yith-woocommerce-affiliates' ) ?></h2>

<p><?php printf( '%s <strong>#%s</strong> %s', __( 'Your commission', 'yith-woocommerce-affiliates' ), $commission['ID'], __( 'switched to pending is now ready to be paid by an admin', 'yith-woocommerce-affiliates' ) ); ?></p>

<p>
	<strong><?php _e( 'Commission:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="ID">#<?php echo $commission['ID'] ?></span><br />
	<strong><?php _e( 'Rate:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="rate"><?php echo number_format( $commission['rate'], 2 )?>%</span><br />
	<strong><?php _e( 'Amount:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="amount"><?php echo wc_price( $commission['amount'], array( 'currency' => $currency ) ) ?></span><br />
</p>

<?php do_action( 'woocommerce_email_footer',$email ); ?>
