<?php
/**
 * Order content template.
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
<div class="invoice-content">
	<?php
	/**
	 * The yith_ywpi_invoice_template_products_list hook
	 *
	 * @hooked show_invoice_products_list_template - 10 (Show products list)
	 */
	do_action( 'yith_ywpi_invoice_template_products_list', $document );
	?>

	<?php
	/**
	 * The yith_ywpi_invoice_template_products_list hook
	 *
	 * @hooked show_invoice_products_list_template - 10 (Show products list)
	 */
	do_action( 'yith_ywpi_invoice_template_totals', $document );
	?>
</div>
