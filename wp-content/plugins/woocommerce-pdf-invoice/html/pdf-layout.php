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

	<p><?php _e( 'Configure the WooCommerce PDF settings here, refer to the <a href="'.PDFDOCSURL.'" target="_blank">WooCommerce PDF Invoice docs</a> for more information' , 'woocommerce-pdf-invoice' ) ?></p>

	<form method="post" action="options.php">

	<?php settings_fields('woocommerce_pdf_invoice_layout_group'); ?>

	<table class="form-table">

	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_layout_settings[template_css]"><?php _e('Set the CSS for PDF template.', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <textarea name="woocommerce_pdf_invoice_layout_settings[template_css]" id="woocommerce_pdf_invoice_layout_settings[template_css]" style="width: 350px; height:200px;"><?php echo $woocommerce_pdf_invoice_layout_options['template_css']; ?></textarea>
	        </td>
	    </tr>
<?php
		// Product rows

		// Totals section

		// get_pdf_template_invoice_number_text - Invoice No.
		// woocommerce_pdf_invoice_layout[invoice_number_text]

		// get_pdf_template_order_number_text - Order No.
		// woocommerce_pdf_invoice_layout[order_number_text]

		// get_pdf_template_invoice_date_text - Invoice Date
		// woocommerce_pdf_invoice_layout[invoice_date_text]

		// get_pdf_template_order_date_text - Order Date
		// woocommerce_pdf_invoice_layout[order_date_text]

		// get_template_payment_method_text - Payment Method
		// woocommerce_pdf_invoice_layout[payment_method_text]

		// get_pdf_template_shipping_method_text - Shipping Method
		// woocommerce_pdf_invoice_layout[shipping_method_text]

		// get_pdf_template_registered_name_text - Registered Name
		// woocommerce_pdf_invoice_layout[registered_name_text]

		// get_pdf_template_registered_office_text - Registered Office
		// woocommerce_pdf_invoice_layout[registered_office_text]

		// get_pdf_template_company_number_text - Company Number
		// woocommerce_pdf_invoice_layout[company_number_text]

		// get_pdf_template_vat_number_text - VAT Number 
		// woocommerce_pdf_invoice_layout[vat_number_text]
?>
	    <?php do_action( 'woocommerce_pdf_invoice_additional_layout_fields_admin' ); ?>
	    
	</table>


	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e( 'Save Layout Options', 'woocommerce-pdf-invoice' ); ?>" />
	</p>

	</form>

	</div>     

	<?php echo ob_get_clean(); 
            
