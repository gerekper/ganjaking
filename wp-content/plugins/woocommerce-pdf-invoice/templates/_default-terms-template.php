<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head> 
  <style> 
  body {
      font-family: "DejaVu Sans", "DejaVu Sans Mono", "DejaVu", sans-serif, monospace;
  }
    @page { 
		margin: 20px 50px 100px 50px; 
	} 
 
    #footer { 
		position: fixed; 
		left: 0px; 
		bottom: -150px; 
		right: 0px; 
		height: 100px; 
		font-size:11px; 
		text-align: center;
	} 
	
	#content { 
		font-size:11px; 
	}
  </style> 
  <body> 
 
  <div id="footer"> 

    <div class="copyright"><?php echo apply_filters( 'pdf_template_registered_name_text', __( 'Registered Name : ', PDFLANGUAGE ) ); ?>[[PDFREGISTEREDNAME]] <?php echo apply_filters( 'pdf_template_registered_office_text', __( 'Registered Office : ', PDFLANGUAGE ) ); ?>[[PDFREGISTEREDADDRESS]]</div>
    <div class="copyright"><?php echo apply_filters( 'pdf_template_company_number_text', __( 'Company Number : ', PDFLANGUAGE ) ); ?>[[PDFCOMPANYNUMBER]] <?php echo apply_filters( 'pdf_template_vat_number_text', __( 'VAT Number : ', PDFLANGUAGE ) ); ?>[[PDFTAXNUMBER]]</div>
    
  </div> 
  <div id="content">
  	<h2>[[TERMSTITLE]]</h2>
	[[TERMS]]
  </div> 
</body> 
</html> 