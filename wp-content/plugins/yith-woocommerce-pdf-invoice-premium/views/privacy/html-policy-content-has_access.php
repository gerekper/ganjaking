<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Policy content.
 *
 * @package YITH\PDFInvoice
 */

?>

<p><?php echo wp_kses_post( _x( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'Privacy Policy Content', 'yith-woocommerce-pdf-invoice' ) ); ?></p>
<ul>
	<li><?php echo wp_kses_post( _x( 'SSN and VAT of an order', 'Privacy Policy Content', 'yith-woocommerce-pdf-invoice' ) ); ?></li>
</ul>
