<?php
/**
 * Customer details template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/customer-details.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="ywpi-customer-details">
	<div class="ywpi-customer-content">
		<?php echo wp_kses_post( $content ); ?>
		<?php
		/**
		 * DO_ACTION: yith_pdf_invoice_after_customer_content
		 *
		 * Section after the customer content in the invoice.
		 *
		 * @param object $document the document object
		 * @param int $order_id the order ID
		 */
		do_action( 'yith_pdf_invoice_after_customer_content', $document, $order_id );
		?>
	</div>
</div>
