<?php
/**
 * Company logo template.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

$data_section_color = wp_kses_post( get_option( 'ywpi_data_section_color_modern' ) );

?>
<div class="ywpi-document-header">
	<div class="company-logo-section">
		<?php if ( $company_logo_path ) : ?>
			<img class="ywpi-company-logo" src="<?php echo wp_kses_post( apply_filters( 'yith_ywpi_company_image_path', $company_logo_path ) ); ?>">
		<?php endif; ?>
	</div>
	<div class="invoice-values" style="background-color: <?php echo $data_section_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> " >
		<?php
		/**
		 * The yith_ywpi_template_document_details hook.
		 *
		 * @hooked show_invoice_template_customer_details - 10 (Show data of customer on invoice template)
		 */
		do_action( 'yith_ywpi_template_document_details', $document );
		?>
	</div>
</div>
