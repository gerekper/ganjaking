<?php
/**
 * Customer details template.
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
?>
<div class="ywpi-customer-details">
	<div class="ywpi-customer-content">
		<?php echo wp_kses_post( $content ); ?>
		<?php do_action( 'yith_pdf_invoice_after_customer_content', $document, $order_id ); ?>
	</div>
</div>
