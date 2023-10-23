<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Policy content.
 *
 * @package YITH\PDFInvoice
 */

?>

<p><?php echo wp_kses_post( _x( 'While you visit our site, we collect information about you during the checkout process on our store. We\'ll track:', 'Privacy Policy Content', 'yith-woocommerce-pdf-invoice' ) ); ?></p>
<ul>
	<li><?php echo wp_kses_post( _x( 'SSN and VAT of an order: we\'ll add this information to the PDF created', 'Privacy Policy Content', 'yith-woocommerce-pdf-invoice' ) ); ?></li>
</ul>
