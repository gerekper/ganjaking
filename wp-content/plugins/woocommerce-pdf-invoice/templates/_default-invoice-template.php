<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style>
			body {
				font-family: [[PDFFONTFAMILY]];
				font-size:11px;
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
		</style> 
	</head>
	<body> 
		<div id="header">

			<table table width="100%">

				<tr>
					<td valign="top" width="50%" id="logo">[[PDFLOGO]]</td>
					<td valign="top" width="50%" id="company-info">[[PDFCOMPANYNAME]]<br />[[PDFCOMPANYDETAILS]]<br /></td>
				</tr>

			</table>

			<table table width="100%" class="address">

				<tr>
					<td width="20%" valign="top" class="heading">[[PDFINVOICENUMHEADING]] :</td>
					<td width="30%" valign="top">[[PDFINVOICENUM]]</td>
					<td width="20%" valign="top" class="heading">[[PDFORDERENUMHEADING]] :</td>
					<td width="30%" valign="top">[[PDFORDERENUM]]</td>
				</tr>

				<tr>
					<td valign="top" class="heading">[[PDFINVOICEDATEHEADING]] :</td>
					<td valign="top">[[PDFINVOICEDATE]]</td>
					<td valign="top" class="heading">[[PDFORDERDATEHEADING]] :</td>
					<td valign="top">[[PDFORDERDATE]]</td>
				</tr>

				<tr>
					<td valign="top" class="heading">[[PDFINVOICE_PAYMETHOD_HEADING]] :</td>
					<td valign="top">[[PDFINVOICEPAYMENTMETHOD]]</td>
					<td valign="top" class="heading">[[PDFINVOICE_SHIPMETHOD_HEADING]] :</td>
					<td valign="top">[[PDFSHIPPINGMETHOD]]</td>
				</tr>

			</table>

			<table table width="100%" class="billing_shipping_address">

				<tr>  
					<td valign="top" width="50%" class="heading">
						<h3>[[PDFINVOICE_BILLINGDETAILS_HEADING]]</h3>
					</td>

					<td valign="top" width="50%" class="heading">
						<h3>[[PDFINVOICE_SHIPPINGDETAILS_HEADING]]</h3>
					</td>
				</tr>

				<tr>  
					<td valign="top" width="50%" class="billing_shipping_address">
						[[PDFBILLINGADDRESS]]<br />
						[[PDFBILLINGTEL]]<br />
						[[PDFBILLINGEMAIL]]
						[[PDFBILLINGVATNUMBER]]
					</td>

					<td valign="top" width="50%" class="billing_shipping_address">
						[[PDFSHIPPINGADDRESS]]
					</td>
				</tr>

			</table>

		</div>

		<table id="footer-content" width="100%">
			<tr>
				<td>
					<div id="footer"> 

						<div class="copyright">
							[[PDFINVOICE_REGISTEREDNAME_HEADING]] : [[PDFREGISTEREDNAME]]
							[[PDFINVOICE_REGISTEREDOFFICE_HEADING]]: [[PDFREGISTEREDADDRESS]]
						</div>

						<div class="copyright">
							[[PDFINVOICE_COMPANYNUMBER_HEADING]] : [[PDFCOMPANYNUMBER]] 
							[[PDFINVOICE_VATNUMBER_HEADING]] : [[PDFTAXNUMBER]]
						</div>

					</div>
				</td>
			</tr>
		</table>

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
            <!-- field:width:title-->
            ORDERDETAILS 
	            quantity:5:Qty:, 
	            product:50:Description:, 
	            priceex:9:Price Ex:, 
	            totalex:9:Total Ex:, 
	            tax:7:Tax:, 
	            priceinc:10:Price Inc:, 
	            totalinc:10:Total Inc: 
            ENDORDERDETAILS
             </table>

            [[PDFBARCODES]]

            <table table width="100%">
                <tr>
                    <td width="60%" valign="top">[[PDFORDERNOTES]]</td>
                    <td width="40%" valign="top" align="right">
                        <table width="100%" class="pdfordertotals" >[[PDFORDERTOTALS]]</table>
                    </td>
                </tr>
            </table>

        </div>
    </body>
</html>