<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style>

   body {
      font-family: [[PDFFONTFAMILY]];
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

    .shop_table {

    }

    .orderdetails {
      border-collapse:collapse;
    } 

    .ordercontent {
      border-collapse:collapse;
    }

    .pdf_table_row {

    }

    .pdf_table_cell {

    }

    .pdf_table_row_title {

    }

    .pdf_table_cell_title {

    }

    .pdf_table_row_heading {
      color: #FFF;
      background-color:#000;
    }

    .pdf_table_cell_heading {

    }

    .pdf_table_row_odd {

    }

    .pdf_table_row_even {
      background: #CACACA;
    }

    .pdf_table_cell_odd {

    }

    .pdf_table_cell_even {

    }
    
    .pdf_table_row_even {
        background: #CACACA;
    }

    [[PDFCURRENCYSYMBOLFONT]]
  </style> 
</head>
  <body[[PDFRTL]]> 
  <div id="header"> 
  <table width="100%">
	  <tr>
    	<td valign="top" width="60%" id="logo">[[PDFLOGO]]</td>
      <td valign="top" width="40%">
        <table width="100%">
          <tr>
            <td colspan="2" id="invoicetitle"><h2>[[PDFDOCTITLE]]</h2></td>
          </tr>
          <tr>
            <td width="50%" id="invoicenumberheading">[[PDFINVOICENUMHEADING]]</td>
            <td width="50%" id="invoicenumber">[[PDFINVOICENUM]]</td>
          </tr>
          <tr>
            <td id="invoicedateheading">[[PDFINVOICEDATEHEADING]]</td>
            <td id="invoicedate">[[PDFINVOICEDATE]]</td>
          </tr>
          <tr>
            <td id="ordernumberheading">[[PDFORDERENUMHEADING]]</td>
            <td id="ordernumber">[[PDFORDERENUM]]</td>
          </tr>
          <tr>
            <td id="orderdateheading">[[PDFORDERDATEHEADING]]</td>
            <td id="orderdate">[[PDFORDERDATE]]</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <table width="100%">
    <tr>
      <td valign="top" width="33%" id="company-info">
      <h3>[[PDFINVOICE_SUPPLYDETAILS_HEADING]]</h3>
          [[PDFCOMPANYNAME]]<br />
          [[PDFCOMPANYDETAILS]]
      </td> 
    	<td valign="top" width="33%" id="billingdetails">
    	<h3>[[PDFINVOICE_BILLINGDETAILS_HEADING]]</h3>
  		    [[PDFBILLINGADDRESS]]<br />
          [[PDFBILLINGTEL]]<br />
          [[PDFBILLINGEMAIL]]
          [[PDFBILLINGVATNUMBER]]
      <h3>[[PDFINVOICE_PAYMETHOD_HEADING]]</h3>
          [[PDFINVOICEPAYMENTMETHOD]]
    	</td>
    	<td valign="top" width="33%" id="shippingdetails">
    	<h3>[[PDFINVOICE_SHIPPINGDETAILS_HEADING]]</h3>
		      [[PDFSHIPPINGADDRESS]]
      <h3>[[PDFINVOICE_SHIPMETHOD_HEADING]]</h3>
          [[PDFSHIPPINGMETHOD]]
    	</td>
    </tr>

    [[PDFSHIPMENTTRACKING]]
    
  </table>
  </div>

  <div id="footer"> 

    <div class="copyright">[[PDFREGISTEREDNAME_SECTION]] [[PDFREGISTEREDADDRESS_SECTION]]</div>
    <div class="copyright">[[PDFCOMPANYNUMBER_SECTION]] [[PDFTAXNUMBER_SECTION]]</div>

  </div> 
  <div id="content">
	  [[ORDERINFOHEADER]]
    [[ORDERINFO]]
    [[PDFBARCODES]]
    
	<table table width="100%">
    	<tr>
        	<td width="60%" valign="top" id="ordernotes">
            [[PDFORDERNOTES]]
        	</td>
        	<td width="40%" valign="top" align="right">
            
            	<table width="100%" id="ordertotals">
                [[PDFORDERTOTALS]]
            	</table>
            
        	</td>
		</tr>
	</table>

  </div> 
</body> 
</html> 