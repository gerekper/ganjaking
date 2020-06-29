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
?>
<div class="invoice-content">
	<?php
	/**
	 * yith_ywpi_invoice_template_products_list hook
	 *
	 * @hooked show_invoice_products_list_template - 10 (Show products list)
	 */
	do_action( 'yith_ywpi_invoice_template_products_list', $document ); ?>

	<?php
	/**
	 * yith_ywpi_invoice_template_products_list hook
	 *
	 * @hooked show_invoice_products_list_template - 10 (Show products list)
	 */
	do_action( 'yith_ywpi_invoice_template_totals', $document ); ?>
</div>