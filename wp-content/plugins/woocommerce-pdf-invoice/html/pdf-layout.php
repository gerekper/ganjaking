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

		// Template CSS
		// woocommerce_pdf_invoice_layout[template_css]

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

	    <?php $pdf_generator_array = array( 'DOMPDF' => 'DOMPDF' , 'MPDF' => 'MPDF' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_generator]"><?php _e('Choose which PDF Generator to use.', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('By default the PDF Invoice extension will DOMPDF. DOMPDF does not support RTL invoices. If you are using RTL in your invoice then switch to MPDF. See docs for more information.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_generator]" id="woocommerce_pdf_invoice_settings[pdf_generator]" style="width: 350px;">
	            <?php foreach ( $pdf_generator_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_generator'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[attach_multiple]"><?php _e('Choose which additional emails to attach the PDF to.', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('By default the PDF Invoice extension will only atach to the Completed Order Email, if you want to attach it to other emails sent by WooCommerce then make selections here(ctrl-click or cmd-click for multiple selections)', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select multiple name="woocommerce_pdf_invoice_settings[attach_multiple][]" id="woocommerce_pdf_invoice_settings[attach_multiple]" style="width: 350px;" class="chosen_select">
	            <?php foreach ( $attach_multiple as $key => $value ) { 
	            
	            // Backwards compatibility
	            if ( $woocommerce_pdf_invoice_options['attach_neworder'] == 'true' && $key == 'new_order' && null === $woocommerce_pdf_invoice_options['attach_multiple'] ) { ?>
	                <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
	            
	            <?php } elseif ( null !== $woocommerce_pdf_invoice_options['attach_multiple'] && in_array( $key, $woocommerce_pdf_invoice_options['attach_multiple'] ) ) {?>
	                <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
	                
	            <?php } else { ?>
	                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
	            
	            <?php } ?>
	            
	            <?php } ?>
	        </select>
	        </td>
	    </tr>
	    
	    <!-- Create Invoice number etc if order is processing -->
	    <?php $create_array = array( 'completed' => 'Completed' , 'processing' => 'Processing' , 'pending' => 'Pending', 'on-hold' => 'On Hold', 'manual' => 'Manually create invoices' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[create_invoice]"><?php _e('When to create the invoice', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Set the point during the transaction when the invoice should be created.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[create_invoice]" id="woocommerce_pdf_invoice_settings[create_invoice]" style="width: 350px;">
	            <?php foreach ( $create_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['create_invoice'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>
	     
	    <!-- Show invoice link on Thank You page -->
	    <?php $thanks_array = array( 'false' => 'No' , 'true' => 'Yes' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[link_thanks]"><?php _e('Show "Download Invoice" link on Thank You page?', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Add a link to download the invoice to the Thank You for your order page', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[link_thanks]" id="woocommerce_pdf_invoice_settings[link_thanks]" style="width: 350px;">
	            <?php foreach ( $thanks_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['link_thanks'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>                     

	    <!-- Send invoice, send invoice and link to download or just link to download -->
	    <?php 
	    $attachment_method_array = array( '0' => 'Attach PDF to email', '1' => 'Attach PDF to email and include link to download PDF', '2' => 'Only include link to download PDF', '3' => 'Create an invoice but do not add link or attach a PDF' ); 
	    if( !isset( $woocommerce_pdf_invoice_options['attachment_method'] ) ) {
	        $woocommerce_pdf_invoice_options['attachment_method'] = '0';
	    }
	    ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[attachment_method]"><?php _e('Attach a PDF Invoice to the email or include a download link?', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Choose how you want to send the invoice to your customer, PDF attachment, PDF attachment and download link or just download link', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[attachment_method]" id="woocommerce_pdf_invoice_settings[attachment_method]" style="width: 350px;">
	            <?php foreach ( $attachment_method_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['attachment_method'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <!-- Invoice Download Link Format -->
	    <?php 
	    $invoice_download_url_placeholder = __('Download your PDF Invoice [[PDFINVOICEDOWNLOADURL]]', 'woocommerce-pdf-invoice' );
	    if ( isset( $woocommerce_pdf_invoice_options['invoice_download_url'] ) && $woocommerce_pdf_invoice_options['invoice_download_url'] != '' ) {
	        $invoice_download_url = $woocommerce_pdf_invoice_options['invoice_download_url'];
	    } else { 
	        $invoice_download_url = $invoice_download_url_placeholder;
	    } 
	    ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[invoice_download_url]"><?php _e('Invoice download URL format', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('This text is used in emails and the "thank you" page to display a link to download the invoice.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>

	        <td class="forminp forminp-number">
	            <textarea id="woocommerce_pdf_invoice_settings[invoice_download_url]" 
	            name="woocommerce_pdf_invoice_settings[invoice_download_url]"
	            placeholder="<?php echo $invoice_download_url_placeholder ?>" style="width: 350px;"><?php echo $invoice_download_url; ?>
	            </textarea>                     
	        </td>
	        <p>
	            <?php __('Current URL Format :', 'woocommerce-pdf-invoice' ) . $invoice_download_url; ?><br />
	            <?php __('Default URL Format :', 'woocommerce-pdf-invoice' ) . $invoice_download_url_placeholder; ?>
	        </p>

	    </tr>

	    <!-- Paper size -->
	    <?php $paper_array = array( 'a4' => 'A4', 'letter' => 'Letter' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[paper_size]"><?php _e('Paper Size', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Set the paper size of your PDF invoice, this is only really used if your customer prints it out.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[paper_size]" id="woocommerce_pdf_invoice_settings[paper_size]" style="width: 350px;">
	            <?php foreach ( $paper_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['paper_size'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>
	    
	    <!-- Page Orientation -->
	    <?php $orientation_array = array( 'portrait' => 'Portrait', 'landscape' => 'Landscape' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[paper_orientation]"><?php _e('Paper Orientation', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Set the paper orientation of your PDF invoice, this is only really used if your customer prints it out.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[paper_orientation]" id="woocommerce_pdf_invoice_settings[paper_orientation]" style="width: 350px;">
	            <?php foreach ( $orientation_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['paper_orientation'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>
	    
	    <!-- PDF Logo -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[logo_file]"><?php _e('PDF Logo', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php echo sprintf( __("Add a logo to your PDF, otherwise it will just use your WordPress title %s. PNG, JPEG or GIF", 'woocommerce-pdf-invoice' ), get_bloginfo( 'name' ) ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">

	            <input id="woocommerce_pdf_invoice_settings_logo_file" 
	            name="woocommerce_pdf_invoice_settings[logo_file]" 
	            type="text" 
	            value="<?php echo ( isset($woocommerce_pdf_invoice_options['logo_file']) ? $woocommerce_pdf_invoice_options['logo_file'] : '' ); ?>"
	            placeholder="<?php _e('Copy the URL to your logo into here or upload using the button', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>

	            <button id="woocommerce_pdf_invoice_settings_logo_file_button" class="button new upload_pdf_logo" name="woocommerce_pdf_invoice_settings[logo_file]_button" type="text" /><?php _e('Upload Your Logo', 'woocommerce-pdf-invoice' ); ?></button>

	        <?php echo ( null !== $woocommerce_pdf_invoice_options['logo_file'] ? '<br /><p><img src="'.$woocommerce_pdf_invoice_options['logo_file'].'" /></p>' : '' ); ?>
	        </td>
	    </tr>

	    <!-- $enable_remote = $dompdf->get_option("enable_remote"); -->
	    <?php $remote_array = array( 'false' => 'No (recommended)' , 'true' => 'Yes' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[enable_remote]"><?php _e('Remote Logo', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php echo sprintf( __("Allow remote images, if your logo is not hosted on your site then set this to 'YES'.", 'woocommerce-pdf-invoice' ), get_bloginfo( 'name' ) ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">

	            <select name="woocommerce_pdf_invoice_settings[enable_remote]" id="woocommerce_pdf_invoice_settings[enable_remote]" style="width: 350px;">
	            <?php foreach ( $remote_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['enable_remote'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	            </select>
	            <p><?php _e('If this is set to Yes, PDF Invoice will access remote sites for images and CSS files as required.
	                <br /><strong>==== IMPORTANT ====</strong><br />
	                Setting this to yes may allow malicious php code in remote html pages to be executed by your server with your account privileges. This setting may increase the risk of system exploit. 
	                <br /><strong>Do not change this settings without understanding the consequences.</strong></p>', 'woocommerce-pdf-invoice' ); ?>
	        </td>
	    </tr>

	    <!-- $enable_subsetting = $dompdf->get_option("enable_subsetting"); -->
	    <?php $subsetting_array = array( 'true' => 'Yes (recommended)' , 'false' => 'No' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[enable_subsetting]"><?php _e('Font Subsetting', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php echo sprintf( __("Enable font subsetting, which embeds only used characters into the file, set this to 'YES' and test to confirm the invoice looks correct.", 'woocommerce-pdf-invoice' ), get_bloginfo( 'name' ) ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">

	            <select name="woocommerce_pdf_invoice_settings[enable_subsetting]" id="woocommerce_pdf_invoice_settings[enable_subsetting]" style="width: 350px;">
	            <?php foreach ( $subsetting_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['enable_subsetting'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	            </select>
	            <p><?php _e('Setting this option to yes will significantly reduce the PDF file size. You should check the invoice to confirm that all characters are displayed correctly.', 'woocommerce-pdf-invoice' ); ?></p>
	        </td>
	    </tr>
	    
	    <!-- Company Name -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_company_name]"><?php _e('Company Name', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('The name of your company, this shows at the top of the invoice', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_company_name]" 
	            name="woocommerce_pdf_invoice_settings[pdf_company_name]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['pdf_company_name'] ? $woocommerce_pdf_invoice_options['pdf_company_name'] : '' ); ?>"
	            placeholder="<?php _e('Your company name', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>                     
	        </td>
	    </tr>
	    
	    <!-- Company Details -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_company_details]"><?php _e('Company Information', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="This is the address that your business operates from." src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <textarea id="woocommerce_pdf_invoice_settings[pdf_company_details]" 
	            name="woocommerce_pdf_invoice_settings[pdf_company_details]"
	            placeholder="<?php _e('Your company contact info', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"><?php echo ( isset($woocommerce_pdf_invoice_options['pdf_company_details']) ? $woocommerce_pdf_invoice_options['pdf_company_details'] : '' ); ?></textarea>                     
	        </td>
	    </tr>
	    
	    <!-- Registered Name -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_registered_name]"><?php _e('Registered Name', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="This sets the legal name of your company." src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_registered_name]" 
	            name="woocommerce_pdf_invoice_settings[pdf_registered_name]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['pdf_registered_name'] ? $woocommerce_pdf_invoice_options['pdf_registered_name'] : '' ); ?>"
	            placeholder="<?php _e('The legal name of your company', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>                     
	        </td>
	    </tr>
	    
	    <!-- Registered Address -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_registered_address]"><?php _e('Registered Office', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="This is the legal registered address of your company, it may be different to the address that your business operates from." src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <textarea id="woocommerce_pdf_invoice_settings[pdf_registered_address]" 
	            name="woocommerce_pdf_invoice_settings[pdf_registered_address]"
	            placeholder="<?php _e('The legal registered address of your company', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"><?php echo ( isset($woocommerce_pdf_invoice_options['pdf_registered_address']) ? $woocommerce_pdf_invoice_options['pdf_registered_address'] : '' ); ?></textarea>                     
	        </td>
	    </tr>
	    
	    <!-- Company Number -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_company_number]"><?php _e('Company Number', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="This is the government issued number for your business (in the UK it would be the number from Companies House)." src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_company_number]" 
	            name="woocommerce_pdf_invoice_settings[pdf_company_number]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['pdf_company_number'] ? $woocommerce_pdf_invoice_options['pdf_company_number'] : '' ); ?>"
	            placeholder="<?php _e('Government issued company ID', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>                     
	        </td>
	    </tr>
	    
	    <!-- Tax Number -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_tax_number]"><?php _e('Tax Number', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('If your buisness is registered for tax purposes your tax office may have issued you with a number (in the UK this would be your VAT number)', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_tax_number]" 
	            name="woocommerce_pdf_invoice_settings[pdf_tax_number]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['pdf_tax_number'] ? $woocommerce_pdf_invoice_options['pdf_tax_number'] : '' ); ?>"
	            placeholder="<?php _e('Govenment issued tax number if you have one', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>                     
	        </td>
	    </tr>

	    <!-- Invoice Sequential -->
	    <?php $sequential_array = array( 'true' => 'Yes', 'false' => 'No' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[sequential]"><?php _e('Use Sequential Invoice Numbering', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('By default WooCommerce uses the post->ID as the order number so there will be gaps in the order number sequence. By setting this to Yes invoice numbers will be sequential.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[sequential]" id="woocommerce_pdf_invoice_settings[sequential]" style="width: 350px;">
	            <?php foreach ( $sequential_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['sequential'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <!-- Invoice Number Reset -->
	    <?php $reset_array = array( 'FALSE' => 'No', 'TRUE' => 'Yes' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[annual_restart]"><?php _e('Reset Invoice Numbering to 1 at the start of each year', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Will reset the invoice number to 1 for the first order of the year.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[annual_restart]" id="woocommerce_pdf_invoice_settings[annual_restart]" style="width: 350px;">
	            <?php foreach ( $reset_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['annual_restart'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        <br /><?php _e('Use this option with caution, check with your local tax office if you are not sure if you need to use this.<br /><strong>You should include -{{year}} in the "Invoice number suffix" setting</strong>', 'woocommerce-pdf-invoice' ); ?>
	        </td>
	    </tr>
	    
	    <!-- Invoice Number Start -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[start_number]"><?php _e('Number of first invoice if not 1', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('What number would you like on the first invoice? Once you have issued an invoice changing this will make no difference', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[start_number]" 
	            name="woocommerce_pdf_invoice_settings[start_number]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['start_number'] ? $woocommerce_pdf_invoice_options['start_number'] : '' ); ?>"
	            placeholder="<?php _e('What number would you like on the first invoice?', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>
	        </td>
	    </tr>

	    <!-- Invoice Number Padding -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[padding]"><?php _e('Enter a pattern for the invoice number, EG 000000', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Set the length of the number part of your invoice number. EG 000000 would give an invoice number like ABC-000492', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[padding]" 
	            name="woocommerce_pdf_invoice_settings[padding]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['padding'] ? $woocommerce_pdf_invoice_options['padding'] : '' ); ?>"
	            placeholder="<?php _e('', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>
	        </td>
	    </tr>

	    <!-- Invoice Number Prefix -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_prefix]"><?php _e('Invoice number prefix', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Use this field to add a prefix to your invoice numbers. If you want your invoice number to look like ABC-123 then add ABC- to this field', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_prefix]" 
	            name="woocommerce_pdf_invoice_settings[pdf_prefix]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['pdf_prefix'] ? $woocommerce_pdf_invoice_options['pdf_prefix'] : '' ); ?>"
	            placeholder="<?php _e('Add an invoice number prefix', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>                     
	        </td>
	    </tr>
	    
	    <!-- Invoice Number Sufix -->
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_sufix]"><?php _e('Invoice number suffix', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Use this field to add a prefix to your invoice numbers. If you want your invoice number to look like 123-ABC then add -ABC to this field', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_sufix]" 
	            name="woocommerce_pdf_invoice_settings[pdf_sufix]" 
	            type="text" 
	            value="<?php echo ( null !== $woocommerce_pdf_invoice_options['pdf_sufix'] ? $woocommerce_pdf_invoice_options['pdf_sufix'] : '' ); ?>"
	            placeholder="<?php _e('Add an invoice number prefix suffix', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>                     
	        </td>
	    </tr>

	    <!--Next Invoice Number -->
	<?php
	    $next_invoice = WC_pdf_functions::get_next_invoice_number();
	?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_next_number]"><?php _e('The next invoice number will be', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Use this field to change the next Invoice Number. You MUST increase this number, you can not save a smaller number than the current number', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_next_number]" 
	            name="woocommerce_pdf_invoice_settings[pdf_next_number]" 
	            type="text" 
	            value="<?php echo $next_invoice ?>"
	            placeholder="<?php _e('Next Invoice Number', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>
	            <p><?php _e('Take care changing this number, you may be breaking your local tax law by having gaps in your invoice numbers.<br />You can only increase the number, decreasing it could lead to duplicate invoice numbers. ', 'woocommerce-pdf-invoice' ); ?></p>
	            <p><strong><?php _e("This option has no affect if you have the 'Reset Invoice Numbering to 1 at the start of each year' set to YES. ", "woocommerce-pdf-invoice" ); ?></strong><p>
	        </td>
	    </tr>
	                     
	    <!-- Invoice File Name Format -->
	    <?php if ( null !== ($woocommerce_pdf_invoice_options['pdf_filename']) && $woocommerce_pdf_invoice_options['pdf_filename'] != '' ) {
	            $invoice_filename = $woocommerce_pdf_invoice_options['pdf_filename'];
	          } else { 
	            $invoice_filename = '{{company}}-{{invoicenumber}}';
	          } ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_filename]"><?php _e('Invoice file name format', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Set the file name format for your PDF files. Bear in mind that your customer should be able to identify your invoice easily. Please review the documentation for accepted variables. Default is {{company}}-{{invoicenumber}}', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	            <input id="woocommerce_pdf_invoice_settings[pdf_filename]" 
	            name="woocommerce_pdf_invoice_settings[pdf_filename]" 
	            type="text" 
	            value="<?php echo $invoice_filename; ?>"
	            placeholder="<?php _e('Invoice filename layout', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>                     
	        </td>
	    </tr>
	    
	    <!-- Invoice Date -->
	    <?php $date_array = array( 'order' => 'Order Date', 'completed' => 'Completed Date' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_date]"><?php _e('Which date should the invoice use', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Do you want the invoice date to be the date of order or the date the order is completed.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_date]" id="woocommerce_pdf_invoice_settings[pdf_date]" style="width: 350px;">
	            <?php foreach ( $date_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_date'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>
	    
	    <!-- Invoice Date Format -->
	    <?php if ( null !== $woocommerce_pdf_invoice_options['pdf_date_format'] && $woocommerce_pdf_invoice_options['pdf_date_format'] != '' ) {
	            $invoice_date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];
	          } else { 
	            $invoice_date_format = 'j F, Y';
	          } ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_date_format]"><?php _e('Invoice date format', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Set the invoice date format, see the docs for further information and examples.', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <input id="woocommerce_pdf_invoice_settings[pdf_date_format]" 
	            name="woocommerce_pdf_invoice_settings[pdf_date_format]" 
	            type="text" 
	            value="<?php echo $invoice_date_format; ?>"
	            placeholder="<?php _e('j F, Y', 'woocommerce-pdf-invoice' ); ?>" style="width: 350px;"/>
	            <p>Current Date Format : <?php 
	            echo date_i18n( $invoice_date_format, strtotime( "now" ) ) ; ?></p>
	        </td>
	    </tr>
	    
	    <!-- Add PDF Terms Page -->
	    <?php $pages = get_pages(); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_termsid]"><?php _e('PDF Terms Page', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e('Set a terms page for your PDF invoices, if you set a terms page then an additional page will be added to the PDF. This terms pages uses a seperate template file so you can style the terms seperately', 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_termsid]" id="woocommerce_pdf_invoice_settings[pdf_termsid]" style="width: 350px;">
	            <option value="0" <?php selected( $woocommerce_pdf_invoice_options['pdf_termsid'], 0 ); ?>><?php _e('Select PDF terms page if required', 'woocommerce-pdf-invoice' ) ?></option>
	            <?php foreach ( $pages as $page ) { ?>
	                <option value="<?php echo $page->ID; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_termsid'], $page->ID ); ?>><?php echo $page->post_title; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>
	    
	    <!-- Invoice creation method -->
	    <?php $create_array = array( 'file' => 'File only', 'standard' => 'Standard' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_creation]"><?php _e('PDF Creation Method', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e("If you have problems with PDFs not creating change this option to 'File only'", 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_creation]" id="woocommerce_pdf_invoice_settings[pdf_creation]" style="width: 350px;">
	            <?php foreach ( $create_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_creation'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <!-- PDF Invoice Caching -->
	    <?php $cache_array = array( 'false' => 'No' , 'true' => 'Yes' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_cache]"><?php _e('Disable invoice number caching', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e("Set this to 'No' if you are using a caching plugin and you see any duplicated invoice numbers", 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_cache]" id="woocommerce_pdf_invoice_settings[pdf_cache]" style="width: 350px;">
	            <?php foreach ( $cache_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_cache'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <!-- PDF Invoice Debugging -->
	    <?php $debug_array = array( 'false' => 'No' , 'true' => 'Yes' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_debug]"><?php _e('PDF Debugging', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e("If you have problems with PDFs turn on debugging. DO NOT leave this option on, the log will get very large very quickly.", 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_debug]" id="woocommerce_pdf_invoice_settings[pdf_debug]" style="width: 350px;">
	            <?php foreach ( $debug_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_debug'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <!-- PDF currency symbol font -->
	    <?php $currency_font_array = array( 'false' => 'Use main body font (default)', 'Kelvinch' => 'Kelvinch',  'Symbola' => 'Symbola', 'Custom' => 'Use a custom font (see docs)' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_currency_font]"><?php _e('PDF Currency Font', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e("If your currency symbol is not showing in the PDF you can chose a differnt font just for the symbol.", 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_currency_font]" id="woocommerce_pdf_invoice_settings[pdf_currency_font]" style="width: 350px;">
	            <?php foreach ( $currency_font_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_currency_font'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <!-- PDF Invoice RTL -->
	    <?php $rtl_array = array( 'false' => 'No' , 'true' => 'Yes' ); ?>
	    <tr valign="top">
	        <th scope="row" class="titledesc">
	            <label for="woocommerce_pdf_invoice_settings[pdf_rtl]"><?php _e('Use RTL layout?', 'woocommerce-pdf-invoice' ); ?></label>
	            <img class="help_tip woocommerce-help-tip" data-tip="<?php _e("Set this to 'Yes' of you want the PDF in RTL layout", 'woocommerce-pdf-invoice' ); ?>" src="<?php echo plugins_url( 'woocommerce/assets/images/help.png' );?>" height="16" width="16" />                 
	        </th>
	        <td class="forminp forminp-number">
	        <select name="woocommerce_pdf_invoice_settings[pdf_rtl]" id="woocommerce_pdf_invoice_settings[pdf_rtl]" style="width: 350px;">
	            <?php foreach ( $rtl_array as $key => $value ) { ?>
	                <option value="<?php echo $key; ?>" <?php selected( $woocommerce_pdf_invoice_options['pdf_rtl'], $key ); ?>><?php echo $value; ?></option>
	            <?php } ?>
	        </select>
	        </td>
	    </tr>

	    <?php do_action( 'woocommerce_pdf_invoice_additional_fields_admin' ); ?>
	    
	</table>


	<p class="submit">
	<?php //backwards compatibility ?>
	<input type="hidden" name="woocommerce_pdf_invoice_settings[attach_neworder]" value="false" />
	<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'woocommerce-pdf-invoice' ); ?>" />
	</p>

	</form>

	</div>     

	<?php echo ob_get_clean(); 
            
