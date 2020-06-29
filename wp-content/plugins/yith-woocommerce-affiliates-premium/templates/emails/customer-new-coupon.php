<?php
/**
 * Affiliate ban
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

<h2><?php _e( 'Check out your brand new coupon', 'yith-woocommerce-affiliates' ) ?></h2>

<p><?php printf( __( 'Hi %s,', 'yith-woocommerce-affiliates' ), $display_name ) ?></p>

<p>
    {content_html}
</p>

<?php do_action( 'woocommerce_email_footer',$email ); ?>
