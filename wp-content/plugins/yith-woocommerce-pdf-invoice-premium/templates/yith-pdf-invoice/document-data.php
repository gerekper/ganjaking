<?php
/**
 * Document data for default document template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/document-data.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$data_section_color = wp_kses_post( get_option( 'ywpi_data_section_color' ) );

?>
<div class="ywpi-document-data">
	<?php
	/**
	 * DO_ACTION: yith_ywpi_template_customer_details
	 *
	 * Show data of customer on invoice template
	 *
	 * @param object $document the document object
	 */
	do_action( 'yith_ywpi_template_customer_details', $document );
	?>

	<div class="invoice-values" style="background-color: <?php echo $data_section_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> " >
		<?php
		/**
		 * DO_ACTION: yith_ywpi_template_document_details
		 *
		 * Show the document details section in the document
		 *
		 * @param object $document the document object
		 */
		do_action( 'yith_ywpi_template_document_details', $document );
		?>
	</div>
</div>
