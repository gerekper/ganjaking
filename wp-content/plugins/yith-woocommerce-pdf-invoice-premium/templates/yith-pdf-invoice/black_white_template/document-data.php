<?php
/**
 * Document data for black white document template.
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

$data_section_color = wp_kses_post( get_option( 'ywpi_data_section_color_black_white' ) );

?>
<div class="ywpi-document-data" style="background-color: <?php echo $data_section_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> " >
	<div class="template_document_details">
		<?php
		/**
		 * The yith_ywpi_template_document_details hook.
		 *
		 * @hooked show_invoice_template_customer_details - 10 (Show data of customer on invoice template)
		 */
		do_action( 'yith_ywpi_template_document_details', $document );
		?>
	</div>
	<div class="template_customer_details">
		<?php
		do_action( 'yith_ywpi_template_customer_details', $document );
		?>
	</div>
</div>
