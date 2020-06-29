<?php
/**
 * Renew needs action email template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Stripe
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

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p>
    <?php echo sprintf( __( 'Hi %s,', 'yith-woocommerce-stripe' ), $username ) ?>
</p>

{opening_text}

<p style="text-align: center;">
    <a class="button alt" href="<?php echo $pay_renew_url ?>" style="color: <?php echo $pay_renew_fg ?> !important; font-weight: normal; text-decoration: none !important; display: inline-block; background: <?php echo $pay_renew_bg ?>; border-radius: 5px; padding: 10px 20px; white-space: nowrap; margin-top: 20px; margin-bottom: 30px;"><?php _e( 'Confirm Payment', 'yith-woocommerce-stripe' ) ?></a>
</p>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

?>

{closing_text}

<?php do_action( 'woocommerce_email_footer' ); ?>
