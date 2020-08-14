<?php
/**
 * New affiliate email template
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

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<h2><?php _e( 'New Affiliate registered', 'yith-woocommerce-affiliates' ) ?></h2>

<p><?php printf( '%s <strong>%s</strong>', __( 'A new affiliate has been registered for user', 'yith-woocommerce-affiliates' ), $affiliate['user_login'] ); ?></p>

<p>
	<strong><?php _e( 'Username:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="username"><?php echo $affiliate['user_login'] ?></span><br />
	<strong><?php _e( 'User email:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="email"><a href="mailto:<?php echo $affiliate['user_email'] ?>"><?php echo $affiliate['user_email'] ?></a></span><br />
	<strong><?php _e( 'Affiliate token:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="token"><?php echo $affiliate['token'] ?> (<?php echo $affiliate_referral_url ?>)</span><br />
	<strong><?php _e( 'Payment email:', 'yith-woocommerce-affiliates' ) ?></strong> <span class="token"><?php echo isset( $affiliate['payment_email'] ) ? $affiliate['payment_email'] : __( 'N/A', 'yith-woocommerce-affiliates' ) ?></span><br />
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
