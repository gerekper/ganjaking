== Installation ==

 * Purchase the extension and download the zip file from WooCommerce.com
 * Login to your WordPress dashboard. Click on Plugins | Add New from the left hand menu
 * Click on the "Upload" option, then click "Browse" to select the zip file from your computer.
 * After the zip file has been selected press the "Install Now" button.
 * On the Plugins page, find the Xero for "WooCommerce Xero Integration" plugin and press "Activate"
 

== Configuring A Connection to Xero == 

Xero’s API uses 2 legged OAuth for validating all connections.  There are two steps to setting up the connection between your WooCommerce shopping cart and your Xero account.  First, you will need to generate a Self-signed Certificate (X509) for use with this module.  Second, you will need to define your WooCommerce site as a Public Application and allow it to connect to your Xero account.  Instructions for these steps are below.


Step 1. Generating a Private/Public Key pair

(These instructions are referenced from the Xero Blog)
 
-- Windows users --

You can download OpenSSL for Windows here.

http://www.slproweb.com/products/Win32OpenSSL.html

To run the commands below, go tot he OpenSSL32 directory on your PS, and then change to the /bin directory.

Note:  You may need to open the command prompt with elevated status (Run as administrator)  If the OpenSSL just recently installed, you might need to restart the computer

-- Mac users --

OpenSSL comes shipped with Mac OS X.

See http://developer.apple.com/mac/library/documentation/Darwin/Reference/ManPages/man1/openssl.1ssl.html for more info.

-- Using OpenSSL --

Use a command line prompt and the following commands to generate a private and public key pair.

1.  The following command will generate a private key file named "privatekey.pem" in the current directory

openssl genrsa -out privatekey.pem 1024

2.  This command uses the previously created private key file to create a public certificate to be used when setting up your private application in the next step.  You will be asked to provide 7 pieces of information about your company that will be included in the certificate file: Country Name (2 letter code), State or Province Name (Full name), Locality (eg city), Organization Name (eg, company), Organizational Unit Name (eg, section), Common Name (eg, Your name), Email Address.
openssl req -newkey rsa:1024 -x509 -key privatekey.pem -out publickey.cer -days 365
3.  To verify the files were created correctly, verify the first line of each file.

The private key will begin with the following line:

—–BEGIN RSA PRIVATE KEY—–

The public certificate will begin with the following line:

—–BEGIN CERTIFICATE—–

Step 2.  Setup Up A Private Application in Xero

  * Login to your Xero account at http://login.xero.com
  * Once logged in, go to the API area at http://api.xero.com
  * You will be at a page titled "Xero Developer Centre"
  * Verify your name is in the top right corner of the page
  * Click on the "My Applications" tab
  * Click the "Add Application" button
  * Fill out the form with the following options:
		  * What type of application are you developing?  Select "Private"
		  * Application Name: Enter the name of your WooCommerce site.
		  * Please select which organisation your application can access:  Select which Xero company to access. The extension can only access one company at a time.
		  * X509 Public Key Certificate: Paste the certificate file you created in Step 1. above.  Note: Certificate files begin with the text  "—–BEGIN CERTIFICATE—–"
  * Press Save and you will be taken to the Edit Application page with the note "Application Added"
  * The Edit Application page will have a box titled "OAuth Credentials" showing the "Consumer Key" and the "Consumer Secret".   These will be used in the next step – Configuring Xero for WooCommerce  
  
  
== Configuring Xero for WooCommerce ==

-- Setup OAuth Credentials --
  * Login to your WordPress dashboard and go to WooCommerce > Xero to fill out the required configuration settings.
  * Fill "Consumer Key" and "Consumer Secret" settings fields with the OAuth Credentials retrieved when registering your private application with Xero in the previous step.

-- Setup Certificate Files --
  * The Public/Private key pair created in Step 1. above need to be placed on your hosting account
  * Use an FTP/SFTP program to create a directory named "xero-certs" at the same level as your public_html directory
  * Place the two files into this directory  
  * Fill the "Private Key" and "Public Key" settings fields with the paths to these files.  You may need to contact your web host to find the path for these files.  
  
-- Setup Default Account Numbers --

The invoices and payments sent to Xero need to be associated with accounts in your company’s Chart of Accounts.  Use the Account fields in the admin dashboard to specify the account number for each type of account.  Note: The Tax Rate associated with the Xero account needs to match the tax rate setup in WooCommerce.

  * Sales Account – This account will collect all sales of items in your store
  * Sales Tax Account – This account will collect tax associated with purchase
  * Discount Account – This account will collect all discounts given through coupons
  * Shipping Account – This account will collect all shipping charges
  * Payment Account – This account will collect all payments made.  This account either needs to be Account Type "Bank" or have "Enable Payments to this account" checked in the Edit Account Details popup.  


== Miscellaneous Settings ==

-- Orders with zero total --
  * Check the box for Orders with zero total to enable export of invoices for orders that have a grand total of zero

-- Debug --
  * Check the box for the Debug option to enable logging for this extension
  * The log file is located at: /wp-logs/
  
  
  
  
  
  
  
== Processing ==
Orders made in your WooCommerce store will be copied to your Xero account as an approved invoice.  When payment is completed (normally immediately) then a payment is added to the invoice making the invoice paid in full.

A note is added to each order in WooCommerce for the invoice including the Xero invoice reference number (Invoice ID).


== FAQ ==

1. When are orders sent to Xero?

Completed orders are sent to Xero during the checkout process when the customer completes checkout successfully.  This is tied to the action "woocommerce_checkout_order_processed".

2. When are payments sent to Xero?

Payments are sent to Xero when the payment is complete.  This is tied to the action "woocommerce_payment_complete".

3. How do I know if an invoice has been sent to Xero?

Entries are added to the Order Notes area of the order page.  There will be one message for invoice and one for payment.

4. What happens if the tax rate is not setup correctly?

If tax is charged in WooCommerce, and the Xero default payment account does not have tax associated with it then the invoice will still be created, but  there will be an adjustment added to the Invoice in the amount of the tax.

5. What happens if an invoice fails?

If creating the Xero invoice fails for any reason there will be a note added to Order Notes section with text “XERO Error” and the error message.

6. How do I see debug information?

To view debug information make sure the Debug checkbox option is checked in the Xero settings page.  The log file is located at /wp-content/plugins/woocommerce/logs/xero.txt

7. Why aren’t payments being exported?

If invoices are being created, but payments are not being created, make sure that the Xero account that is used for “Payment Account”  has “Enable payments to this account” checked in the Edit Account Details popup.