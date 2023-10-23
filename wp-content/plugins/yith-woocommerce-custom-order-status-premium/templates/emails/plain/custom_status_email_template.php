<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Custom order status email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/custom_status_email_template.php.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( wp_strip_all_tags( wptexturize( $custom_message ) ) ) . "\n\n";

if ( $display_order_info ) {

	echo "----------------------------------------\n\n";

	do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

	echo "\n----------------------------------------\n\n";

	do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

	do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

}
echo "\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
