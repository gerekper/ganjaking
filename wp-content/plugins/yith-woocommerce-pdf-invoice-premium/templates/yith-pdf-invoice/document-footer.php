<?php
/**
 * Override this template by copying it to [your theme folder]/woocommerce/yith-pdf-invoice
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$current_order   = $document->order;
$order_id = $current_order->get_id();

if ( YITH_PDF_Invoice()->is_visible_document_footer( $document ) ): ?>
	<htmlpagefooter name="footer">
		<hr />
		<div id="footer">
			<table>
				<tr>
					<td><?php echo YITH_PDF_Invoice()->get_footer_details( $order_id, $document ); ?></td>
				</tr>
			</table>
		</div>
	</htmlpagefooter>
	<sethtmlpagefooter name="footer" value="on" />
<?php endif; ?>
