# DEVELOPER.md

## Useful URLs
* Help Scout home: https://www.helpscout.net/
* Dashboard: https://secure.helpscout.net/dashboard/
* Docs: https://docs.woothemes.com/document/woocommerce-help-scout/

## What it Does
* There are two flows - one where the customer initiates a "conversation" and the other where the store owner does

## Help Scout Account
* The FREE account will NOT work.  It does not support Help Scout API requests.
* After setting up a STANDARD plan account ( https://secure.helpscout.net/members/plan/ ) be sure to add WooCommerce to your apps ( https://secure.helpscout.net/apps/ )
* There is a link to Activation Instructions near the top of the form on the https://secure.helpscout.net/apps/woocommerce/ page

## Mailboxes
* You'll need to set up a REAL email address for testing, e.g. support-test@mydomain.com on a host (one that you can set up an auto-forwarding rule for)
* Don't use a personal Gmail account - everything will be forwarded to Help Scout and that will mess other things up
* You'll need to set up an auto-forwarding rule for that email address to Help Scout
* You can get the correct support...@helpscout.net email address from your connection-settings page for the mailbox
* See https://secure.helpscout.net/settings/mailbox/61396/ for more information for more information on how to set up your email forwarding

## Customer Initiated Conversation
* The customer clicks on the "Get Help" button in the My Account or My Order page and submits a form

## Store Owner Initiated Conversation
* The store owner fills in the "Report an Issue" meta box on the Order.
* A new ticket is created in Help Scout (using AJAX - no screen refresh)

## Help Scout Conversation Sidebar Order Data
* This data is populated by Help Scout by accessing the store owner's site using the WooCommerce API
* A consume key and consumer secret for Help Scout must be 1) generated in wp-admin/admin.php?page=wc-settings&tab=api&section=keys and 2) provided to Help Scout at https://secure.helpscout.net/apps/woocommerce/
* Note: Only Read access needs to be granted to Help Scout

## Building and Linting
* This extension has a Gruntfile that minifies CSS and JS and lints JS. It should be run after cloning and before committing changes.  To do so:
* Install grunt-cli globally if you haven't already done so using `npm install -g grunt-cli`
* Change to the directory of where you cloned the extension
* Install project dependencies with `npm install`
* Run Grunt with `grunt`
* Scrutinize the output of grunt for warnings and errors

## Testing Customer Initiated Conversation and Help Scout Conversation Sidebar Order Data
* Use incognito mode to visit the store
* Complete a purchase, creating an account on the store using a disposable test email address (e.g. yourgmailaccount+randomnumber@gmail.com )
* Go to My Account and click Get Help for that order
* Enter a subject and description and hit Send
* Verify you get a "Thank you for your contact, we will respond as soon as possible." message
* If you get an error message (0) instead, make sure the API Key in /wp-admin/admin.php?page=wc-settings&tab=integration matches your API key in https://secure.helpscout.net/users/api/{your help scout user id}/
* Go to your mailbox on Help Scout
* Verify you get a new Conversation with the subject you used
* Open the Conversation
* Verify you see the description and order details (number, date, item purchased, quantity, shipping method)
* Verify you see WooCommerce recent order information in the sidebar, including customer since (date) and lifetime value (amount) and a list of recent orders

## Testing Store Owner Initiated Conversation
* Open Edit Order page for an order
* Look for the "Report and Issue" meta box on the right (you may have to scroll down a bit)
* Enter a subject and description and hit Start Conversation
* Verify you get a "Conversation created successfully"
* If you get an error message (0) instead, make sure the API Key in /wp-admin/admin.php?page=wc-settings&tab=integration matches your API key in https://secure.helpscout.net/users/api/{your help scout user id}/
* Go to your mailbox on Help Scout
* Verify you get a new Conversation with the subject you used
* Open the Conversation
* Verify you see the description and order details (number, date, item purchased, quantity, shipping method)
* Verify you see WooCommerce recent order information in the sidebar, including customer since (date) and lifetime value (amount) and a list of recent orders
