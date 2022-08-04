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

?>
<div class="ywpi-document-data">
	<div class="template_customer_details">
		<?php
		do_action( 'yith_ywpi_template_customer_details', $document );
		?>
	</div>
</div>
