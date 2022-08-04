<?php
/**
 * Document footer template.
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$current_order = $document->order;
$order_id      = $current_order->get_id();

if ( YITH_PDF_Invoice()->is_visible_document_footer( $document ) ) : ?>
	<htmlpagefooter name="footer">
		<div id="document-footer" style="background-color:white; margin-top: -9px; padding-bottom: 10px">
			<hr class="footer-separator">
			<div id="footer">
				<table>
					<tr>
						<td class="footer-message"><?php echo wp_kses_post( YITH_PDF_Invoice()->get_footer_details( $order_id, $document ) ); ?></td>
					</tr>
				</table>
			</div>
		</div>
	</htmlpagefooter>
	<sethtmlpagefooter name="footer" value="on" />
<?php endif; ?>
