== Changelog ==

= 1.7.2 = Released on 21 May 2020

* New: Support for WooCommerce 4.2
* Update: Plugin framework
* Update: Language files
* Fix: Wrong background color for subscription button
* Dev: New filter 'yith_wcwtl_email_address_label'

= 1.7.1 = Released on 24 April 2020

* New: Support for WooCommerce 4.1
* New: Support for YITH Proteo
* New: Support for YITH Event Tickets for WooCommerce
* Update: Plugin framework
* Fix: Error message after creating a new product

= 1.7.0 = Released on 10 March 2020

* New: Support for WooCommerce 4.0
* New: Support for WordPress 5.4
* New: Added recipient option in admin email
* Update: Plugin framework
* Update: Language files
* Fix: CSS layout issue on admin panel

= 1.6.9 = Released on 27 December 2019

* New: Support for WooCommerce 3.9
* Update: Plugin framework
* Update: Italian language
* Update: Spanish language
* Update: Dutch language

= 1.6.8 = Released on 29 November 2019

* Update: Notice handler
* Update: Plugin framework
* Update: WPML config file
* Fix: Empty notice showed to customers that subscribe to the waiting list with an email already registered
* Dev: New argument for filter 'yith_waitlist_link_label_instock_email'

= 1.6.7 = Released on 12 November 2019

* Fix: Empty notice showed to customers that subscribe to the waiting list with an email already registered

= 1.6.6 = Released on 5 November 2019

* New: Message to show when a guest user subscribes to a waiting list with an email already registered
* Update: Plugin framework

= 1.6.5 = Released on 30 October 2019

* Update: Plugin framework

= 1.6.4 = Released on 28 October 2019

* New: Support for WooCommerce 3.8
* New: Support for WordPress 5.3
* Tweak: Possibility to add the column "SKU" to the waiting list table
* Update: Plugin framework
* Update: Spanish language
* Fix: Call to undefined method WC_Order::get_permalink()
* Fix: Fatal error ( Call to undefined method WC_Order::get_image_id() )
* Dev: New filter 'yith_wcqv_taxonomy_quick_view_navigation'
* Dev: New filter 'yith-wcqv-enable-images-slider-pagination'

== 1.6.3 == Released: On 31 Jul 2019

* New: Support to WooCommerce 3.7.0 RC1
* Update: Plugin Core
* Update: Italian language
* Fix: Prevent multiple product save on delete waiting list process
* Fix: Check that variable implements countable before pass it to 'count' function

== 1.6.2 == Released: on 29 May 2019

* New: support to WooCommerce 3.6.4
* New: support to WordPress 5.2
* Update: Plugin Core

== 1.6.1 == Released: on 30 April 2019

* New: Support to WooCommerce 3.6.2
* New: Compatibility with YITH WooCommerce Product Bundles Premium
* Dev: New filter 'yith_wcwtl_recipient_admin_email'

== 1.6.0 == Released: on 11 April 2019

* New: Support to WooCommerce 3.6.0 RC2
* New: An email is sent to the store admin whenever a user subscribes to a waiting list
* New: Import user emails to a waiting list from a CSV file
* New: Option to enable/disable double opt-in for logged in customers
* Update: Plugin Core
* Update: Spanish translation
* Update: Italian translation
* Dev: Removed compatibility with WooCommerce versions older than 3.0

== 1.5.8 == Released: on 12 March 2019

* Fix: email not sent when product is out of stock

== 1.5.7 == Released: on 07 February 2019

* New: Support to WooCommerce 3.5.4
* Update: Plugin Core
* Fix: Multiple email addresses in unsubscribe email link
* Tweak: Improve plugin queries for better performance

== 1.5.6 == Released: on 05 December 2018

* New: Support to WooCommerce 3.5.2
* New: Support to WordPress 5.0.0
* Update: Plugin Core
* Update: Italian language
* Update: Dutch language
* Fix: Email address encoding to prevent issues with special characters

== 1.5.5 == Released on 26 October 2018

* New: Support to WooCommerce 3.5.0
* Update: Plugin Core
* Update: Languages files
* Fix: WPML issue with in stock email
* Fix: Missing opt-in email strings in WPML xml
* Tweak: Usage of js selectors to avoid conflicts using shortcode

== 1.5.4 == Released on 26 September 2018

* New: Support to WooCommerce 3.4.5
* Update: Plugin Core
* Update: Dutch translation

== 1.5.3 == Released on 22 August 2018

* New: Export waiting list users in CSV
* Update: Plugin Core
* Fix: Wrong success message showed when double op-tin is enabled

== 1.5.2 == Released on 27 July 2018

* New: Show alternative success message when double opt-in is enabled
* Updated: Dutch language file
* Updated: Spanish language file
* Fix: Unable to save stock

== 1.5.1 == Released on 30 May 2018

* New: Support to WooCommerce 3.4.1
* New: Privacy Policy DPA

== 1.5.0 == Released on 16 May 2018

* New: Support to WooCommerce 3.4 RC1
* New: General Data Protection Regulation (GDPR) compliance
* New: Option to add a Privacy Policy checkbox to the waiting list form
* New: Option to enable Double Opt-In subscription method
* New: Filter by product stock status for Waiting Lists table
* New: Search by product name for Waiting Lists table
* New: Search by email address for Waiting List Users table
* Update: Language files
* Update: Plugin Core
* Fix: Bulk action for Waiting Lists table

== 1.4.1 == Released on 30 March 2018

* New: Support to WooCommerce 3.3.4
* New: Support to WordPress 4.9.4
* Update: Plugin Core
* Fix: Add endpoint method
* Fix: Add product param to email templates filter
* Fix: Wrong name for custom attributes in waiting list table
* Dev: New filter 'yith_wcwtl_recipient_mail_subscribe'

== 1.4.0 == Released on 02 February 2018

* New: Support to WooCommerce 3.3.0
* New: Support to WordPress 4.9.2
* New: Dutch translation
* New: Submit frontend form using AJAX
* Update: Plugin Core
* Update: Language files
* Fix: Product name including variations on plugin's emails

== 1.3.1 == Released on 28 November 2017

* New: Support to WooCommerce 3.2.5
* New: Support to WordPress 4.9.0
* New: Norwegian translate (thanks to Bernhard Brynildsen)
* New: Add option to invert exclusions list logic
* Fix: Add form on get variations AJAX action
* Fix: Flush rewrite rules to prevent 404 error on my account plugin section

== 1.3.0 == Released on 09 November 2017

* New: Support to WooCommerce 3.2.3
* New: Support to WordPress 4.8.3
* New: New methods to send an email to all the users in the waiting list when a product is set back as 'In-stock'
* Update: Plugin Core
* Fix: Remove product from exclusions list

== 1.2.3 == Released on 26 October 2017

* New: Support to WooCommerce 3.2.1
* Update: Languages files
* Fix: Wrong argument for shortcode [ywcwtl_form]

== 1.2.2 == Released on 12 October 2017

* New: Support to WooCommerce 3.2.0
* New: Support to WordPress 4.8.2
* Update: Plugin Core

== 1.2.1 == Released on 05 September 2017

* New: Support to WooCommerce 3.1.2
* New: Support to WordPress 4.8.1
* Update: Plugin Core
* Update: Language files
* Fix: Correct image size for product thumb on email
* Fix: Uncaught error, [] operator not supported for strings
* Fix: Notice for string to array conversion for older php version, less then 5.6
* Fix: Compatibility issue with YITH WooCommerce Multi Vendor Premium
* Tweak: Auto delete plugin meta for products that don't exists anymore

== 1.2.0 == Released on 28 March 2017

* New: Support to WooCommerce 3.0.0 RC2
* New: Support to WordPress 4.7.3
* Update: Plugin Core
* Update: Language files

== 1.1.5 == Released on 26 January 2017

* New: Compatibility with Iconic WooCommerce Quick View
* New: Compatibility with YITH WooCommerce Quick View
* New: Support to WooCommerce 2.6.13
* New: Support to WordPress 4.7.2
* Fix: Double email subscription for guest customer
* Dev: Add product object $product as second parameter to filter yith_wcwtl_can_product_have_waitlist

== 1.1.4 == Released on 28 October 2016

* Fix: Waiting list for variable products

== 1.1.3 == Released on 27 October 2016

* New: Support to WooCommerce 2.6.7
* New: Support to WordPress 4.6.1
* New: Italian translation
* New: Compatibility with YITH WooCommerce Email Templates
* New: Shortcode for print waiting list form [ywcwtl_form product_id=""]
* Update: Plugin Core
* Update: Language files
* Fix: YITH WooCommerce Multi Vendor compatbility issue with variable products

== 1.1.2 == Released on 10 June 2016

* New: Support to WooCommerce 2.6 RC1
* New: Spanish translation
* Update: Plugin Core
* Update: Language file .pot

== 1.1.1 == Released on 15 April 2016

* New: Compatibility with Wordpress 4.5
* New: Product thumbnail in "My Waiting List" table
* New: Shortcode [ywcwtl_waitlist_table] using to print the waiting list table for customer
* New: Added minimized js files. Plugin loads full files version if the constant "SCRIPT_DEBUG" is defined and is true
* Update: Plugin Core
* Update: Language file .pot

== 1.1.0 == Released on 11 March 2016

* Update: WPML config XML
* Update: Plugin Core
* Update: Language file .pot
* Fix: Error on my-account page when a product on waitlist doesn't exists anymore
* Fix: Backorder products compatibility

== 1.0.9 == Released on 04 January 2016

* New: Compatibility with WooCommerce 2.5 BETA
* Update: Plugin Core
* Update: Language file .pot

== 1.0.8 == Released on 14 December 2015

* Update: Changed text domain from yith-wcwtl to yith-woocommerce-waiting-list
* Update: Language file .pot
* Fix: Compatibility issue with YITH WooCommerce Multi Vendor

== 1.0.7 == Released on 11 December 2015

* New: Compatibility with Wordpress 4.4
* Update: Plugin Core
* Fix: Double menu entry for YITH Plugins

== 1.0.6 == Released on 24 November 2015

* New: Compatibility with YITH WooCommerce Multi Vendor
* Update: Plugin Core
* Update: Language file

== 1.0.5 == Released on 12 October 2015

* New: Mandrill Integration
* New: Advanced text editor for content email options
* Update: Plugin Core

== 1.0.4 == Released on 09 September 2015

* Fix: Automatic email for variable product
* Fix: Redirect to correct page after sending mail

== 1.0.3 == Released on 21 August 2015

* New: Compatibility with Wordpress 4.3
* Fix: Exclusion List and Waiting List Tables error
* Fix: WPML config xml

== 1.0.2 == Released on 13 August 2015

* New: Compatibility with WooCommerce 2.4
* Update: Plugin Core
* Update: Language file

== 1.0.1 == Released on 21 April 2015

* Initial release