<?php
/**
 * Order content template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/order-content.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="invoice-content">
	<?php
	/**
	 * DO_ACTION: yith_ywpi_invoice_template_products_list
	 *
	 * Action to show the products list.
	 *
	 * @param object $document the document object
	 */
	do_action( 'yith_ywpi_invoice_template_products_list', $document );
	?>

	<?php
	/**
	 * DO_ACTION: yith_ywpi_invoice_template_totals
	 *
	 * Action to show the template totals.
	 *
	 * @param object $document the document object
	 */
	do_action( 'yith_ywpi_invoice_template_totals', $document );
	?>
</div>
