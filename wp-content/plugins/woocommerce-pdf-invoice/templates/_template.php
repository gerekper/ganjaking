<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style>

   body {
      font-family: "DejaVu Sans", "DejaVu Sans Mono", "DejaVu", sans-serif, monospace;
      font-size: 11px;
    }
    @page { 
  		margin: 480px 50px 100px 50px;
  	} 
    #header { 
  		position: fixed; 
  		left: 0px; 
  		top: -460px; 
  		right: 0px; 
  		height: 480px; 
  		text-align: center;
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
  		font-size:10px;
  	}

    #logo img {
      max-width:340px;
    }

    .barcode {
      text-align:center;
      width: 50%;
    }

    .page_break { 
      page-break-before: always; 
    }

    table.pdforderdetails {
      border-collapse:collapse;
    }

    thead.pdforderdetails_headings {
      background-color:black;
      color:white;
      margin:0;
    }

    .pdforderdetails_headings tr {
      margin:0;
    }

    .pdforderdetails_headings th {
      margin:0;
      padding:5px 0 5px 5px !important;
    }

    .pdforderdetails_row_even {
      background-color:#cacaca;
    }

    [[PDFCURRENCYSYMBOLFONT]]
  </style> 
</head>
  <body> 
  <div id="header"> 
  <table table width="100%">
	<tr>
    	<td valign="top" width="100%" id="logo">[[PDFLOGO]]</td>
	</tr>
  </table>

  <table table width="100%">
	<tr>
	   	<td valign="top"><?php echo apply_filters( 'pdf_template_invoice_number_text', __( 'Invoice No. :', 'woocommerce-pdf-invoice' ) ); ?>: [[PDFINVOICENUM]]</td>
	</tr>
	<tr>
	   	<td valign="top"><?php echo apply_filters( 'pdf_template_invoice_date_text', __( 'Invoice Date :', 'woocommerce-pdf-invoice' ) ); ?>: [[PDFINVOICEDATE]]</td>
  </tr>
  <tr>
	   	<td valign="top"><?php echo apply_filters( 'pdf_template_payment_method_text', __( 'Payment Method :', 'woocommerce-pdf-invoice' ) ); ?>: [[PDFINVOICEPAYMENTMETHOD]]</td>
  </tr>
  </table>

  <table table width="100%">    
  <tr>   
    	<td valign="top" colspan="2">
        <strong>Furnizor</strong>:[[PDFCOMPANYNAME]]<br />
        <strong>Nr. Reg. Com.</strong>: 
        <strong>CIF</strong>: 
        <strong>Capital social</strong>: 
        <strong>Cont</strong>: 
        <strong>Banca</strong>: 
        [[PDFCOMPANYDETAILS]]
    	</td>
    	<td valign="top" colspan="2">
        [[PDFBILLINGADDRESS]]<br />
        [[PDFBILLINGTEL]]<br />
        [[PDFBILLINGEMAIL]]
        [[PDFBILLINGVATNUMBER]]
    	</td>
    </tr>
  </table>
  </div>

  <div id="footer"> 

    <div class="copyright">[[PDFREGISTEREDNAME_SECTION]] [[PDFREGISTEREDADDRESS_SECTION]]</div>
    <div class="copyright">[[PDFCOMPANYNUMBER_SECTION]] [[PDFTAXNUMBER_SECTION]]</div>

  </div> 
  <div id="content">
  <table class="pdforderdetails" width="100%">
                <thead>
                    <tr>
                        <th align="left" width="100%">
                        <h2>[[PDFINVOICE_ORDERDETAILS_HEADING]]</h2>
                        </th>
                    </tr>
                </thead>
            </table>
            <table class="pdforderdetails" width="100%">
              ORDERDETAILSproduct:45:Description:,sku:20:Code:,quantity:5:Qty:,priceex:10:Price Ex:,totalex:10:Total Ex:,tax:10:Tax:ENDORDERDETAILS
            </table>
    [[PDFBARCODES]]
    
	<table table width="100%">
    	<tr>
        	<td width="60%" valign="top">
            [[PDFORDERNOTES]]
        	</td>
        	<td width="40%" valign="top" align="right">
            
            	<table width="100%">
                [[PDFORDERTOTALS]]
            	</table>
            
        	</td>
		</tr>
	</table>

  </div> 
</body> 
</html> 