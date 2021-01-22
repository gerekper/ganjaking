<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	// Multisite
	$blog_id = NULL;
	if( function_exists( 'get_current_blog_id' ) ) {
		$blog_id = get_current_blog_id();
	}

	$admin_url = get_admin_url( $blog_id );
?>
<h3>Setting up PDF Invoices and customising the invoice template.</h3>

<p><a href="<?php echo $admin_url; ?>edit.php?post_type=pdfi_templates" title="<?php _e('Create and modify your invoice templates', 'woocommerce-pdf-invoice' ); ?>"><?php _e('Create and modify your invoice templates', 'woocommerce-pdf-invoice' ); ?></a></p>

<ol class="documentation-toc">
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-1" target="_blank"><?php _e("Installation and Updating", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-2" target="_blank"><?php _e("Setup and Configuration", 'woocommerce-pdf-invoice' ); ?></a>
		<ol>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-3" target="_blank"><?php _e("Date Format", 'woocommerce-pdf-invoice' ); ?></a></li>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-4" target="_blank"><?php _e("Testing Invoice Layouts", 'woocommerce-pdf-invoice' ); ?></a></li>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-5" target="_blank"><?php _e("PDF File Name Parameters", 'woocommerce-pdf-invoice' ); ?></a></li>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-6" target="_blank"><?php _e("PDF Invoice Number Suffix", 'woocommerce-pdf-invoice' ); ?></a></li>
		</ol>
	</li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-7" target="_blank"><?php _e("Create Invoices for Existing Orders", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-8" target="_blank"><?php _e("Order Screen", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-9" target="_blank"><?php _e("Usage", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-10" target="_blank"><?php _e("Customization", 'woocommerce-pdf-invoice' ); ?></a>
		<ol>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-11" target="_blank"><?php _e("Copy the template to your theme", 'woocommerce-pdf-invoice' ); ?></a></li>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-12" target="_blank"><?php _e("Add new placeholders", 'woocommerce-pdf-invoice' ); ?></a></li>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-13" target="_blank"><?php _e("Using the included fonts", 'woocommerce-pdf-invoice' ); ?></a></li>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-14" target="_blank"><?php _e("Using Google Fonts", 'woocommerce-pdf-invoice' ); ?></a></li>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-15" target="_blank"><?php _e("Using a True Type font", 'woocommerce-pdf-invoice' ); ?></a></li>
		</ol>
	</li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-16" target="_blank"><?php _e("Using Hooks and Filters", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-17" target="_blank"><?php _e("Translating PDF Invoice", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-18" target="_blank"><?php _e("FAQ", 'woocommerce-pdf-invoice' ); ?></a>
		<ol>
			<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-19" target="_blank"><?php _e("Are PDFs stored in a secure way?", 'woocommerce-pdf-invoice' ); ?></a></li>
		</ol>
	</li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-20" target="_blank"><?php _e("Bulk Exporting PDFs", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-21" target="_blank"><?php _e("Feedback and feature requests", 'woocommerce-pdf-invoice' ); ?></a></li>
	<li><a href="https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-22" target="_blank"><?php _e("Questions &amp; Support", 'woocommerce-pdf-invoice' ); ?></a></li>
</ol>