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
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Default customer order status email template.
 *
 * Note: the .td class used in table is from WooCommerce core (see email-styles.php).
 *
 * @type string $email_heading The email heading.
 * @type string $email_body_text The email body.
 * @type \WC_Order $order The order object.
 * @type bool $sent_to_admin Whether email is sent to admin.
 * @type bool $plain_text Whether email is plain text.
 * @type bool $show_download_links Whether to show download links.
 * @type bool $show_purchase_note Whether to show purchase note.
 * @type \WC_Email $email The email object.
 *
 * @since 1.0.0
 * @version 1.10.0
 */
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php if ( $email_body_text ) : ?>
<div id="body_text"><?php echo $email_body_text; ?></div>
<?php endif; ?>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2><?php echo esc_html__( 'Order:', 'woocommerce-order-status-manager' ) . ' ' . $order->get_order_number(); ?></h2>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align: left;"><?php esc_html_e( 'Product', 'woocommerce-order-status-manager' ); ?></th>
			<th class="td" scope="col" style="text-align: left;"><?php esc_html_e( 'Quantity', 'woocommerce-order-status-manager' ); ?></th>
			<th class="td" scope="col" style="text-align: left;"><?php esc_html_e( 'Price', 'woocommerce-order-status-manager' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php

		$email_order_items = array(
			'show_purchase_note'  => $show_purchase_note,
			'show_download_links' => $show_download_links,
			'show_sku'            => false,
		);

		echo wc_get_email_order_items( $order, $email_order_items );

		?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++; ?>
					<tr>
						<th class="td" scope="row" colspan="2" style="text-align: left; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
						<td class="td" style="text-align: left; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2><?php esc_html_e( 'Customer details', 'woocommerce-order-status-manager' ); ?></h2>

<?php if ( $billing_email = $order->get_billing_email() ) : ?>
	<p><strong><?php esc_html_e( 'Email:', 'woocommerce-order-status-manager' ); ?></strong> <?php echo $billing_email; ?></p>
<?php endif; ?>
<?php if ( $billing_phone = $order->get_billing_phone() ) : ?>
	<p><strong><?php esc_html_e( 'Tel:', 'woocommerce-order-status-manager' ); ?></strong> <?php echo $billing_phone; ?></p>
<?php endif; ?>

<?php wc_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
