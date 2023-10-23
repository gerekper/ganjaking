<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Custom order status email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/custom_status_email_template.php.
 *
 * @var string           $email_heading      Email heading.
 * @var YITH_WCCOS_Email $email              The Email.
 * @var string           $custom_message     The custom message.
 * @var bool             $display_order_info True if the order info needs to be displayed.
 * @var WC_Order         $order              The order.
 * @var bool             $sent_to_admin      True if this is sent to admin.
 * @var bool             $plain_text         True if this is a plain text.
 *
 * @see        http://docs.woothemes.com/document/template-structure/
 * @author     YITH <plugins@yithemes.com>
 * @package    YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php echo wp_kses_post( $custom_message ); ?></p>

<?php

if ( $display_order_info ) {
	do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

	do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

	do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
}

do_action( 'woocommerce_email_footer', $email );

?>
