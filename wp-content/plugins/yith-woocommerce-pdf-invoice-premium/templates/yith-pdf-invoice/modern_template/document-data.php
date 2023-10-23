<?php
/**
 * Document data for black white document template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/modern_template/document-data.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="ywpi-document-data">
	<div class="template_customer_details">
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
	</div>
</div>
