<?php
/**
 * Invoice template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/invoice-template.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<?php
	/**
	 * DO_ACTION: yith_ywpi_invoice_template_products_list
	 *
	 * Add CSS file based on type of current document
	 *
	 * @param object $document the document object
	 */
	do_action( 'yith_ywpi_template_head', $document );
	?>
</head>

<body>
	<div class="invoice-document <?php echo wp_kses_post( $main_class ); ?>">
		<?php
		/**
		 * DO_ACTION: yith_ywpi_template_document_header
		 *
		 * Show the header of the document.
		 *
		 * @param object $document the document object
		 */
		do_action( 'yith_ywpi_template_document_header', $document );
		?>

		<?php
		/**
		 * DO_ACTION: yith_ywpi_template_company_data
		 *
		 * Show the template that contains the company data.
		 *
		 * @param object $document the document object
		 */
		do_action( 'yith_ywpi_template_company_data', $document );
		?>

		<?php
		/**
		 * DO_ACTION: yith_ywpi_template_document_data
		 *
		 * Show the template for the customer and invoice data.
		 *
		 * @param object $document the document object
		 */
		do_action( 'yith_ywpi_template_document_data', $document );
		?>

		<?php
		/**
		 * DO_ACTION: yith_ywpi_template_order_content
		 *
		 * Show the template for the product details.
		 *
		 * @param object $document the document object
		 */
		do_action( 'yith_ywpi_template_order_content', $document );
		?>

		<?php
		/**
		 * DO_ACTION: yith_ywpi_template_notes
		 *
		 * Show the template for the order notes.
		 *
		 * @param object $document the document object
		 */
		do_action( 'yith_ywpi_template_notes', $document );
		?>
	</div>
</body>
</html>
