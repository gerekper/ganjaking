# WooCommerce Amazon Fulfillment
An integration between WooCommerce (WC) and Amazon Multi-channel Fulfillment (MCF) / FBA by Never Settle.

## Description
This plugin integrates Amazon Multi-channel Fulfillment (MCF) with WooCommerce to provide a powerful automated shipping solution to store owners. 
It requires an active Amazon Pro Seller account and - as of version 4 - uses the new Amazon Selling Partner API (SP-API).
When updating to version 4 for the first time, it will attempt to automatically migrate existing MWS credentials to SP-API credentials.
Most existing Sellers should be automatically updated. However, if there are issues with the automatic migration, and for new Sellers,
There's now a 1-click Login with Amazon (LWA) process to authorize the extension to access your Seller Central and FBA data. 

## Features 
* Ultimate flexibility: Select which individual products you want to be handled by MCF / FBA and which ones you want to manually fulfill
* NEW - Import Products from your Amazon fulfillment account into to WooCommerce
* NEW - There's now a native WC Shipping method included for Amazon
* NEW - Enable or Disable any of the Amazon shipping speeds in the new Amazon Shipping method for WC
* NEW - Dynamic estimated arrival times are provided in the Cart and Checkout from Amazon's SP-API 
* NEW - Dynamic estimated fulfillment rates are retrieved from the Amazon API and presented during the shopping experience 
* NEW - Specify a fixed amount, or a % mark-up to dynamically add to Amazon's estimated fulfillment rates
* NEW - Shipment status, carrier, and tracking number are provided to customer on the view order screen and admin on the edit order screen in WP Admin 
* Automatically sends orders to FBA for fulfillment when payment is received
* Manually submit orders to FBA if necessary
* Easily track the current status of fulfillment orders on the normal Orders screen with support for both full and partial fulfillment (orders can be mixed)
* Fully integrated into standard WooCommerce conventions and processes like order status, order notes, etc.
* Supports THREE inventory sync modes: 
  * Update local product stock numbers from FBA inventory every time an order is placed
  * Update local stock numbers automatically on an hourly schedule
  * Manually trigger a full inventory sync from Amazon stock level numbers (also a good idea to do when setting up the integration)
* NEW - Supports international fulfillment through MCF using Amazon's international shipping features
* The customer receives a shipping notice from Amazon when the order actually ships (this can be disabled)
* Optionally receive shipping notifications to your WP admin email address or set a different address to receive notifications
* Optionally receive email notifications at the site admin email address when a fulfillment submission fails
* Smart Fulfillment decision engine with granular control of when NS FBA should or should not send fulfillment requests to Amazon at both the order and the item level
* Manual override settings to bypass other active conditions when an order is Manually sent to FBA
* Option to disable sending orders to FBA that match specific shipping methods
* Vacation Mode to enable sending to FBA regardless of product settings
* Perfection Mode to disable sending to FBA if ALL products in an order are not set to Fulfill with Amazon FBA
* Option to monitor for shipping status from Amazon and set the order status to Completed when shipping status has been detected
* And more!

## How it Works
* NS FBA adds a new option in the product settings to turn on "Fulfill with Amazon FBA" per product
* NS FBA adds 3 new order statuses to WooCommerce: sent-to-fba, part-to-fba, and fail-to-fba
* These new statuses will be set automatically based on the results of the order submission to FBA. These statuses will also show unique icons in the Orders view so that you can quickly see where things are at. 
* Once everything is configured correctly per the instructions in the Installation section, NS FBA works automatically behind the scenes to send all order items marked for fulfillment to FBA when WooCommerce detects that the order payment is complete.
* Important note: This event is only automatically triggered with electronic payment gateways like Stripe, and PayPal. If you are accepting manual forms of payment like checks, the process will not be completely automatic. You will need to manually send the order to FBA using the new custom Order Action "Send to Amazon FBA" when you have received payment.  
* NS FBA also includes minor style tweaks to the existing Order Status icons to make them larger and easier to see based on Danny Santoro's post here: http://danielsantoro.com/customize-woocommerce-order-status-icons/
* The fulfillment request sends the proper data to trigger a shipping notification email from Amazon when it is actually shipped. This email will go to the customer's email address and the site's WordPress admin email address.
* Whether the fulfillment request is successfully submitted to FBA or not, in both cases, it will add an order note to the order with details and a link to the full log.
* If there is an error with an order submission to FBA it will also email the site admin a notification. NOTE: this functionality depends on a properly configured environment for PHP error_log() with the email parameter set to work.
* Note: NS FBA currently only syncs stock levels between Amazon and WooCommerce depending on your settings. It cannot sync or import other product data from Amazon (although that is on our radar to build)

## Installation
1. Install like any other plugin by uploading the zip through the WordPress dashboard and activating it on your WooCommerce site
2. Configure your MWS settings and options on the new Amazon Fulfillment menu item under the main WooCommerce menu
3. Test your API credentials with the test button to make sure your connection is working
4. Make 100% sure that all your SKUs in WooCommerce that you want to fulfill through FBA match your Seller SKUs inside FBA
5. Go through every product in WooCommerce that you want to send to FBA, check the new "Fulfill with Amazon FBA" option in the product general tab, and save
6. NS FBA for WooCommerce works behind the scenes to send all order's shipping information to Amazon
7. For manual forms of payment like checks, or to manually re-submit an order to fulfillment, you can use the new custom order Action "Send to Amazon FBA" (but use that carefully)

## Full Documentation

https://woocommerce.com/document/amazon-fulfillment/

## Frequently Asked Questions

https://woocommerce.com/document/amazon-fulfillment/#section-17 

## Changelog

### 2024-02-05 - version 4.1.9.8
* Fix order sync tracking details.

### 2024-01-29 - version 4.1.9.7
* Retrieve tracking details during order sync.

### 2024-01-22 - version 4.1.9.6
* Better handling of digital products. Digital products will count to total fulfiled products to set the order status to sent-to-fba
* Authorize url is now marketplace specific.

### 2023-12-14 - version 4.1.9.5
* Improved option settings usage

### 2023-12-05 - version 4.1.9.4
* Fix issues with Kint debugger.
* Improve installation notice message.

### 2023-11-09 - version 4.1.9.3
* Fix issues with shipping cost calculations.
* Add logging for shipping calculation errors.

### 2023-10-22 - version 4.1.9.2
* Fix issues with fulfilment.
* Code improvements.

### 2023-10-16 - version 4.1.9.1
* Add plugin compatibility with WooCommerce Subscriptions recurring orders.
* Introduce `ns_fba_fulfilment_order_fulfilment_items` hook to manually add items that should be fulfiled but are skipped.
* Update code to have more reusable functions.
* Speed improvements. 

### 2023-05-29 - version 4.1.9
* Added WooCommerce HPOS Support

### 2023-04-12 - version 4.1.8
* Fix cron error when not fully loaded.

### 2023-04-12 - version 4.1.7
* Fix customer fulfilment order details

### 2023-04-12 - version 4.1.6
* Fix `ns_fba_skip_post_fulfillment_order` filter logging


### 2023-04-12 - version 4.1.5
* New Filter `ns_fba_skip_post_fulfillment_order` to allow skipping orders for fulfilment.
* Fixed background tasks not firing after the first time.
* Fixed issue of inventory log size growing too large.


### 2023-03-02 - version 4.1.4
* Added setting option to only sync products that fulfil with Amazon.
* Added API timeout default duration to allow for adequate API request time.


### 2023-01-20 - version 4.1.3
* Fix order sync update issue
* Add error handling when calculating shipping costs.

### 2023-01-02 - version 4.1.2
* Fix issue calculating shipping fees for variations.
* Fix order status check
* Implement better handling for long product SKU


### 2022-10-10 - version 4.1.1
* Fix issue where failed email is sent when an order is fulfiled successfully.


### 2022-09-29 - version 4.1.0
* Remove support for MWS
* Implement work-around for Amazon API not returning all SKUs to fix inventory sync
* Implement logging through WooCommerce logging
* Add URL to package tracking number for shipment status

### 2022-07-22 - version 4.0.9
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Fix issue in passing # symbol to Amazon with temporary work-around
* Fix issue with SKUs containing + symbol with temporary work-around
* Fix BLANK_BOX feature reference per the SP API model
* Fix some issues with inventory sync not going past the first page of results
* Fix php notices and error handling when API errors are returned

### 2022-06-30 - version 4.0.8
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Fix issue with variation stock level not syncing
* Fix issue with orders containing variations not auto sending to Amazon

### 2022-06-24 - version 4.0.7
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Revert order number sent to Amazon to WC order / post ID
* Fix issue preventing scheduled inventory sync
* Fix automatic order status syncing (depends on active inventory sync schedule)

### 2022-06-23 - version 4.0.6
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Restore support for PHP 7.3 and prior by removing typed class properties

### 2022-06-21 - version 4.0.5
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Fix issue with variation SKUs not being properly sent to Amazon

### 2022-06-17 - version 4.0.4
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Fix additional error Disabled Shipping Methods setting

### 2022-06-16 - version 4.0.3
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Fix error when Disabled Shipping Methods setting is used

### 2022-06-15 - version 4.0.2
* Patch to MAJOR UPDATE (see full details of version 4.0.0)
* Fix plugin activation error when Sync Shipping Status setting is ON

### 2022-06-06 - version 4.0.1
* Version Bump to fix issue with WooCommerce Marketplace changelog.txt parsing
* MAJOR UPDATE (see details below in version 4.0.0)

### 2022-06-06 - version 4.0.0
* MAJOR UPDATE
* Implemented Amazon's new Selling Partner API (SP-API)
* Implemented Login with Amazon (LWA) for authorizing access to Seller Central and fulfillment data
* Temporary support for both MWS and SP-API (you must update to SP-API before Jul 31, 2022)
* Added features to Import Products and map SKUs from Amazon fulfillment into to WooCommerce
* Added a native WC Shipping method for Amazon with the ability to enable/disable any of the Amazon shipping speeds
* Added dynamic estimated arrival times in the Cart and Checkout from Amazon's SP-API
* Added dynamic estimated fulfillment rates from Amazon and provided to customer during shopping experience
* Added features to set a fixed or % mark-up amount to add to Amazon's estimated fulfillment rates
* Added shipment status, carrier, and tracking number to the view order screen and admin edit order screen

### 2021-03-29 - version 3.3.8
* Fixed bug with Amazon settings display logic

### 2021-03-24 - version 3.3.7
* Added setting for log auto-deletion threshold
* Fixed bug with plugin not taking effect for network-activated WooCommerce
* Fixed bug with custom order statuses not being counted as paid in reporting

### 2021-02-16 - version 3.3.6
* Added bulk order post action for sending orders to Amazon
* Added manual-only mode for sending orders by admin action but not automatically
* Fixed PayPal IPN timing irregularity handling

### 2020-10-08 - version 3.3.5
* Fixed changelog formatting

### 2020-09-15 - version 3.3.4
* Added experimental retry failed orders setting
* Added better request parameter logging for troubleshooting
* Fixed WP 5.5 deprecated notice from PHPMailer
* Fixed signature error for addresses with trailing whitespace

### 2020-08-17 - version 3.3.3
* Added ability to check tracking info from admin order page
* Fixed bug with products not updating when syncing all inventory manually
* Fixed inventory update failure for SKUs with special characters
* Updated Kint library used for debug logs

### 2020-06-18 - version 3.3.2
* Add misc. improvements to settings UI

### 2020-04-02 - version 3.3.1
* Fix bug with missing displayableOrderComment parameter from blank setting value
* Convert actions using admin notices to AJAX handling (for compatibility with WooCommerce 4.0)

### 2020-03-25 - version 3.3.0
* Added setting and filter for customizing the displayable order comment
* Added order status filter for customizing status logic
* Added support for Singapore marketplace
* Added support for COD orders in Japan
* Added additional logging for failed order submissions
* Fixed bug with invalid queryStartDateTime when testing Amazon API connection

### 2019-08-20 - version 3.2.9
* Added Support for Far East Region including Australia and Japan
* Added composer to support current Kint lib and updates

### 2019-07-23 - version 3.2.8
* Updated Kint debugging library to latest version

### 2019-06-17 - version 3.2.7
* Fixed bug with refund report totals
* Fixed bug with setting of application version / user-agent

### 2019-05-17 - version 3.2.6
* Fixed bug with shipping method names that have special characters or extra spaces

### 2019-04-09 - version 3.2.5
* Fixed bug related to Amazon MWS library's inconsistent use of setMarketplace vs. setMarketplaceId 

### 2019-04-02 - version 3.2.4
* Added test for cURL version to ensure compatibilty with required TLS1.2
* Added better WP_ERROR trapping when plugin can't establish an SSL connection to API

### 2019-04-01 - version 3.2.3
* Version bump to make sure latest is deployed to Woo Market

### 2019-03-22 - version 3.2.2
* Fixed issue in FBA library with ambiguity between Marketplace and MarketplaceId parameters
* Fixed issue with some API requests not signing all the parameters and failing to submit to Amazon

### 2019-03-21 - version 3.2.1
* Completed EU support

### 2019-03-20 - version 3.2.0
* First overhaul and transition version supporting new authentication model for MWS Auth Token
* Added support for North America and Europe regions for MWS Auth Token request signing

### 2018-07-24 - version 3.1.9
* Added exclusion for Polylang which can trigger false positive detection on WPML

### 2018-07-02 - version 3.1.8
* Added feature to set FBA stock threshold, which - when reached - will zero out WC stock level for oversell protection
* Updated sequence order of address check to prevent an error when no order items are set to FBA 
* Updated behavior of amazon notification email - leaving the setting blank now will NOT send the admin address instead
* Updated translation file

### 2018-06-01 - version 3.1.7
* Added support for auto currency switching to GBP when UK marketplace ID is detected inside EU region
* Added option to exclude customer phone number from order data sent to FBA
* Updated automatic currency handling for non-EUR in EUR marketplace IDs other than UK
* Added woocommerce-order-details class section to wrap Order Tracking Info (thank you James for the suggestion!)

### 2018-05-11 - version 3.1.6
* Added support for multiple dynamic marketplace IDs in EU region. Auto detect and send ID based on shipping address. 

### 2018-04-26 - version 3.1.5
* Added condition to prevent virtual variations from getting sent to Amazon even if their parent product is set to send to FBA
* Updated some settings labels to match Amazon changes
* Fixed bug with option Update WC levels from FBA not disabling

### 2018-03-14 - version 3.1.4
* Added support for the new Australia Amazon Region
* Fixed wrapping with on/off toggle in custom admin column for products
* Added new fixed format for UA sent to Amazon with fulfillment order request

### 2018-01-23 - version 3.1.3
* Fixed reversed logic on the Encoding Convert BYPASS setting to ensure non-Latin-1 characters are converted with that setting OFF

### 2018-01-15 - version 3.1.2
* Added option to automatically delete logs older than 30 days

### 2017-10-16 - version 3.1.1
* Added support for syncing stock levels across WPML translated product IDs in addition to main product IDs

### 2017-10-16 - version 3.1.0
* Added support for WPML translated shipping methods in shipping methods disabled for FBA setting
* Fixed bug with checking for the shipping sync and order auto-complete settings 

### 2017-09-10 - version 3.0.9
* Updated behavior of international order failures to leave the order status alone
* Fixed false positive on condition for disabling shipping status on order view page 
* Only update stock level if the product exists and if it's set to fulfill with FBA

### 2017-08-31 - version 3.0.8
* Added support for new WooCommerce header upgrade and compatability notifications
* Added full version history in new format for this file

### 2017-08-22 - version 3.0.7
* Add - First release on WooCommerce.com!
* Removed legacy plugin license and updating mechanisms
* Added support for WooCommerce updates via Woo header
* Shifted Order-level rule checking to come after all individual order items have been checked for FBA fulfillment setting

### 2017-07-01 - version 3.0.6
* Added Multisite support for the licensing and updates components 

### 2017-06-24 - version 3.0.5
* Additional WC3 fixes

### 2017-06-02 - version 3.0.4
* Fixed issue with international shipping setting on/off not being honored

### 2017-05-24 - version 3.0.3
* Added ability for the new version to automatically backup and pull in settings from v2 format
* Updated several deprecated calls to their WC 3.0 equivalent
* Fixed timing sequence of manual inventory sync to ensure it happens after woocommerce_init since it calls wc_get_product

### 2017-05-14 - version 3.0.2
* Fixed issue introduced by WC 3.0 which breaks returning a WC_Product directly for inventory updates

### 2017-05-01 - version 3.0.1 
* Documentation updates and file name changes for Woo

### 2017-04-05 - version 3.0.0.1 
* Major overhaul for WooCommerce Marketplace
* Converted all settings to WC Integration implementation
* Hide other settings until the integration is properly configured
* Added FBA on/off toggle to the WooCommerce product list table

### 2017-02-27 - version 2.0.0.5 
* Added support for Amazon India Region

### 2017-02-04 - version 2.0.0.4 
* Put back old behavior of saving the settings and then running inventory test when test is clicked

### 2017-02-03 - version 2.0.0.3 
* Fix to amazon bug with marketplace ID not working on FulfillmentOrderRequest for US + CAD consolidated accounts

### 2017-01-25 - version 2.0.0.1 
* Fix for scenario that can lead to a fatal error with simple products getting submitted to FBA

### 2017-01-17 - version 2.0.0.0  
* Major version bump that should have happened instead of 1.1.0.0
* Obfuscated log file names
* Updated to latest marketplace ID distinction for inventory checks to fix US vs Canada inventory discrepency

### 2016-12-28 - version 1.1.0.3 
* Added feature to send parent SKU instead of variation SKU per product 

### 2016-11-30 - version 1.1.0.2 
* Added option to disable Amazon shipping notice email to customer email address
* Added logging for character encoding conversions
* Added option to override encoding character check and pass the order to FBA regardless
* Added new icon to highlight new features as they are released
* Added new option to sync fulfillment status from FBA and automatically update order status to Completed
* Added new option to bypass encoding conversion completely

### 2016-11-29 - version 1.1.0.1 
* Moved product setting Fulfill with Amazon FBA to the Product Shipping tab
* Massive code cleanup and refactor
* Added option to turn error email messages ON/OFF when an order fails to send to FBA
* All new smart fulfillment decision engine with granular control at the order and item level
* Added manual override settings to bypass other conditions when order is manually sent to FBA
* Added setting to disable sending to FBA for international orders
* Added setting to disable sending to FBA for specific shipping methods
* Added Vacation Mode to enable sending to FBA regardless of product settings
* Added Perfection Mode to disable sending to FBA if ALL products in an order are not set to Fulfill with Amazon FBA
* Added option and features to display shipping and tracking information to the customer order view page

### 2016-11-25 - version 1.1.0.0 
* Massive overhaul of settings and many improvements
* Reorganized order of settings to be more intuitive
* Converted all applicable settings to multi-select with pre-filled values
* Dynamically pulling all active shipping methods now for mapping to Amazon Shipping Speeds 

### 2016-05-24 - version 1.0.9.1 
* Added failsafe to catch orders that have un-convertable encodings and notify admin so they can edit the address before sending
* Updated to remove WC deprecated parameters in email_order_items_table()

### 2016-11-21 - version 1.0.9.0 
* Added full international translation support

### 2016-09-01 - version 1.0.8.7 
* Added support for the Sequential Order Numbers Pro extension. NS FBA now sends this value to Amazon instead of the internal ID. 

### 2016-08-18 - version 1.0.8.6 
* Fixed bug in Wordpress 4.6 when querying terms

### 2016-07-14 - version 1.0.8.5 
* Fixed bug in Amazon PHP library in later versions of PHP with duplicated parameters in function

### 2016-06-30 - version 1.0.8.4 
* Added conversion for non-Latin-1 characters to prevent Amazon from rejecting orders

### 2016-05-25 - version 1.0.8.3 
* Added new custom status "Partial to FBA" for tracking mixed orders
* Added conditions, handling, and new icon for Partial to FBA status

### 2016-05-25 - version 1.0.8.2 
* Added additional param for Kint check to deconflict with other plugins
* Added experimental param to allow manual order send to override product fulfillment setting

### 2016-03-11 - version 1.0.8.1 
* Updated to latest Amazon API PHP Library versions

### 2015-04-24 - version 1.0.8.0 
* Modified Address handling to dynamically set Name to Company name if specified and Line 1 to Person name

### 2015-03-29 - version 1.0.7.9 
* Modified Amazon PHP Library constant names to deconflict with other plugins that use the same library

### 2015-03-18 - version 1.0.7.8 
* Modified the behavior of the manual inventory sync button to pull in all items that had inventory levels change in last 365 days

### 2015-03-15 - version 1.0.7.7 
* Fixed custom statuses and call to woocommerce_reports_order_statuses filter that WooCommerce broke in 2.2.10

### 2015-02-04 - version 1.0.7.6 
* Added custom currency override for stores selling in a different currency than their default Amazon Marketplace currency
* Improved stock level sync support for stores with large inventories (many skus) 

### 2015-01-10 - version 1.0.7.5 
* Added backwards compatibility with PHP 5.2 and earlier which does not support anonymous functions
* Added new DEBUG mode with additional checking and logging to help troubleshoot problematic edge cases
* Added new order note in scenarios where no products in the order are set to fulfill with FBA

### 2014-12-04 - version 1.0.7.4 
* Added setting to specify a different email address to include in Amazon's notify list for order events. Default is still to use the WP admin email address for this. 

### 2014-11-23 - version 1.0.7.3 
* Added button to manually initiate full inventory sync

### 2014-11-12 - version 1.0.7.2 
* Fixed bug introduced by 1.0.7.1 because Amazon was returning an error about perUnitPrice only being for Cash on Delivery orders  

### 2014-11-11 - version 1.0.7.1 
* Added support for international orders by adding Amazon's required declared value properties in the fulfillment API requests  

### 2014-11-10 - version 1.0.7 
* Added automated hourly inventory sync functionality and option 
* Added logging for inventory sync updates 

### 2014-09-27 - version 1.0.6.1 
* Fixed bug with inventory sync on variations

### 2014-10-02 - version 1.0.6 
* Added 1-way inventory sync feature from Amazon > WooCommerce
* Fixed bug with WooCommerce reporting leaving out custom FBA status orders

### 2014-09-25 - version 1.0.5.1 
* Added variation support

### 2014-09-24 - version 1.0.5 
* Added per-order shipping speed settings to map to Flat Rate methods

### 2014-09-21 - version 1.0.4 
* Updated for WooCommerce 2.2 to work with new custom statuses structure

### 2014-08-27 - version 1.0.3  
* Added side-bar

### 2014-08-22 - version 1.0.2 
* First public release
