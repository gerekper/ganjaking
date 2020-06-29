<?php
/**
 * The Template for invoice
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-template.php
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$data_section_color = get_option('ywpi_data_section_color');

?>
<div class="ywpi-document-data">
	<?php
	do_action ( 'yith_ywpi_template_customer_details', $document );
	?>

	<div class="invoice-values" style="background-color: <?php echo $data_section_color;?>" >
		<?php
		/**
		 * yith_ywpi_template_document_details hook
		 *
		 * @hooked show_invoice_template_customer_details - 10 (Show data of customer on invoice template)
		 */
		do_action ( 'yith_ywpi_template_document_details', $document );
		?>
	</div>
</div>
