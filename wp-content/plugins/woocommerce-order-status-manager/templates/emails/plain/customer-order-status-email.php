<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Default customer order status email template.
 *
 * @type string $email_heading Email heading.
 * @type string $email_body_text Email body.
 * @type \WC_Order $order Order object.
 * @type bool $sent_to_admin If the email is sent to an admin.
 * @type bool $plain_text Whether email is plain text.
 * @type bool $show_download_links Whether to show download links.
 * @type bool $show_purchase_note Whether to show purchase note.
 * @type \WC_Email $email The email object.
 *
 * @since 1.0.0
 * @version 1.10.0
 */

echo $email_heading . "\n\n";

if ( $email_body_text ) {
	echo "\n\n" . $email_body_text . "\n\n";
}

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );

/* translators: Placeholders: %s - order number */
echo sprintf( __( 'Order number: %s', 'woocommerce-order-status-manager' ), $order->get_order_number() ) . "\n";

if ( $date_created = $order->get_date_created() ) {

	/* translators: Placeholders: %s - order date */
	echo sprintf( __( 'Order date: %s', 'woocommerce-order-status-manager' ), date_i18n( wc_date_format(), $date_created->getTimestamp() ) ) . "\n";
}

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

echo "\n";

$email_order_items = array(
	'show_download_links' => $show_download_links,
	'show_sku'            => false,
	'show_purchase_note'  => $show_purchase_note,
	'plain_text'          => true
);

echo wc_get_email_order_items( $order, $email_order_items );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );

echo esc_html__( 'Your details', 'woocommerce-order-status-manager' ) . "\n\n";

if ( $billing_email = $order->get_billing_email() ) {
	echo esc_html__( 'Email:', 'woocommerce-order-status-manager' ); echo $billing_email . "\n";
}

if ( $billing_phone = $order->get_billing_phone() ) {
	echo esc_html__( 'Tel:', 'woocommerce-order-status-manager' ); ?> <?php echo $billing_phone . "\n";
}

wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
