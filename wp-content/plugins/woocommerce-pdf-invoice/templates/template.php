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

    [[PDFPAIDINFULLOVERLAY]]

    [[PDFCURRENCYSYMBOLFONT]]

    [[PDFINVOICEADDITIONALCSS]]
  </style> 
</head>
  <body[[PDFRTL]]>
      <div id="header"> 
        <table table width="100%">

          <tr>
              <td valign="top" width="50%" id="logo">[[PDFLOGO]]</td>
              <td valign="top" width="50%" id="company-info">[[PDFCOMPANYNAME]]<br />[[PDFCOMPANYDETAILS]]<br /></td>
          </tr>

        </table>

        <table table width="100%">
          <tr>
            <td width="20%" valign="top" id="invoicenumberheading">[[PDFINVOICENUMHEADING]]</td>
            <td width="30%" valign="top" id="invoicenumber">[[PDFINVOICENUM]]</td>
            <td width="20%" valign="top" id="ordernumberheading">[[PDFORDERENUMHEADING]]</td>
            <td width="30%" valign="top" id="ordernumber">[[PDFORDERENUM]]</td>
          </tr>

          <tr>
            <td valign="top" id="invoicedateheading">[[PDFINVOICEDATEHEADING]]</td>
            <td valign="top" id="invoicedate">[[PDFINVOICEDATE]]</td>
            <td valign="top" id="orderdateheading">[[PDFORDERDATEHEADING]]</td>
            <td valign="top" id="orderdate">[[PDFORDERDATE]]</td>
          </tr>
    
          <tr>
            <td valign="top" id="paymethodheading">[[PDFINVOICE_PAYMETHOD_HEADING]]</td>
            <td valign="top" id="paymethod">[[PDFINVOICEPAYMENTMETHOD]]</td>
            <td valign="top" id="shipmethodheading">[[PDFINVOICE_SHIPMETHOD_HEADING]]</td>
            <td valign="top" id="shipmethod">[[PDFSHIPPINGMETHOD]]</td>
          </tr>

          [[PDFSHIPMENTTRACKING]]
    
          <tr>   
          <td valign="top" colspan="2" id="billingdetails">
          <h3>[[PDFINVOICE_BILLINGDETAILS_HEADING]]</h3>
          [[PDFBILLINGADDRESS]]<br />
          [[PDFBILLINGTEL]]<br />
          [[PDFBILLINGEMAIL]]
          [[PDFBILLINGVATNUMBER]]
          </td>
          <td valign="top" colspan="2" id="shippingdetails">
          <h3>[[PDFINVOICE_SHIPPINGDETAILS_HEADING]]</h3>
          [[PDFSHIPPINGADDRESS]]
          </td>
          </tr>
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

        <table width="100%">
          <tr>
            <td width="60%" valign="top" id="ordernotes">[[PDFORDERNOTES]]</td>
            <td width="40%" valign="top" align="right">
              <table width="100%" id="ordertotals">[[PDFORDERTOTALS]]</table>
            </td>
          </tr>
        </table>

      </div>

</body> 
</html> 