<?php
/**
 * The Template for invoice
 *
 * Override this template by copying it to [your theme folder]/woocommerce/yith-pdf-invoice/invoice-template.php
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<?php
	/**
	 * yith_ywpi_template_head hook
	 *
	 * @hooked add_style_files - 10 (add css file based on type of current document
	 */
	do_action( 'yith_ywpi_template_head', $document );
	?>
</head>

<body>
<div class="invoice-document <?php echo $main_class; ?>">
	<?php
	/**
	 * Show the header of the document
	 */
	do_action( 'yith_ywpi_template_document_header', $document ); ?>

	<?php
	/**
	 * Show the template that contains the company data
	 */
	do_action( 'yith_ywpi_template_company_data', $document ); ?>

	<?php
	/**
	 * Show the template for the customer and invoice data
	 */
	do_action( 'yith_ywpi_template_document_data', $document ); ?>

	<?php
	/**
	 * Show the template for the product details
	 */
	do_action( 'yith_ywpi_template_order_content', $document ); ?>

	<?php
	/**
	 * Show the template for the order notes
	 */
	do_action( 'yith_ywpi_template_notes', $document ); ?>
</div>
</body>
</html>