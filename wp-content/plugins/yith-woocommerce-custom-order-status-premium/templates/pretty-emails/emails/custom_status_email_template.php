<?php
/**
 * Customer Status email template for pretty email plugin
 *
 * @author        Yithemes
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php include( MBWPE_TPL_PATH . '/settings.php' ); ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

    <p><?php echo $custom_message; ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

    <h2 <?php echo $orderref; ?>><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></h2>

    <table cellspacing="0" cellpadding="6" style="border-collapse:collapse; width: 100%; border: 1px solid <?php echo $bordercolor; ?>;" border="1" bordercolor="<?php echo $bordercolor; ?>">
        <thead>
        <tr>
            <th scope="col" width="50%" style="<?php echo $missingstyle; ?>text-align:center; border: 1px solid <?php echo $bordercolor; ?>;"><?php _e( 'Product', 'woocommerce' ); ?></th>
            <th scope="col" width="25%" style="<?php echo $missingstyle; ?>text-align:center; border: 1px solid <?php echo $bordercolor; ?>;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
            <th scope="col" width="25%" style="<?php echo $missingstyle; ?>text-align:center; border: 1px solid <?php echo $bordercolor; ?>;"><?php _e( 'Price', 'woocommerce' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php include( MBWPE_TPL_PATH . '/tbody.php' ); ?>
        </tbody>
        <?php include( MBWPE_TPL_PATH . '/tfoot.php' ); ?>
    </table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text ); ?>

<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) : ?>

    <h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>

    <?php if ( $billing_email = yit_get_prop( $order, 'billing_email' ) ) : ?>
        <p><strong><?php _e( 'Email:', 'woocommerce' ); ?></strong> <?php echo $billing_email; ?></p>
    <?php endif; ?>
    <?php if ( $billing_phone = yit_get_prop( $order, 'billing_phone' ) ) : ?>
        <p><strong><?php _e( 'Tel:', 'woocommerce' ); ?></strong> <?php echo $billing_phone; ?></p>
    <?php endif; ?>

    <?php wc_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>

<?php else : ?>

    <?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>

<?php include( MBWPE_TPL_PATH . '/treatments.php' ); ?>