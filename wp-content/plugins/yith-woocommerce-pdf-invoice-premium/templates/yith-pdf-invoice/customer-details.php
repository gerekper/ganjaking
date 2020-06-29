<?php
/**
 * The Template for invoice
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/customer-details.php
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<div class="ywpi-customer-details">
	<div class="ywpi-customer-content">
		<?php echo $content; ?>
		<?php do_action ( 'yith_pdf_invoice_after_customer_content', $document, $order_id ); ?>
	</div>
</div>