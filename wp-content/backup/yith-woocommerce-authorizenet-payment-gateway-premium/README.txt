=== YITH WooCommerce Authorize.net Payment Gateway ===

Contributors: yithemes
Tags: authorize.net, woocommerce, products, themes, yit, e-commerce, shop, payment gateway, yith, woocommerce authorize.net payment gateway, woocommerce 2.6 ready, credit card, authorize
Requires at least: 4.0.0
Tested up to: 5.4
Stable tag: 1.1.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Changelog ==

= 1.1.15 - Released on 08 May 2020 =

* New: support for WooCommerce 4.1
* Update: plugin framework

= 1.1.14 - Released on 14 March 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: Void transaction instead of refund, when processing full amount refunds
* Update: plugin framework
* Tweak: code reformat
* Fix: item name is now edited using mb_substr, to avoid problems with UTF-16 strings
* Fix: notice when paying with saved card

= 1.1.13 - Released on 24 December 2019 =

* New: support for WooCommerce 3.9
* Tweak: Added customer IP to TransactionRequest
* Tweak: use get_order_number() to retrieve order id, instead of property
* Update: plugin framework
* Dev: added yith_wcauthnet_invoice_number filter

= 1.1.12 - Released on 05 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* Update: Plugin Framework
* Fix: now the plugin correctly handles code 4 status (held for review); it is managed as a successful payment
* Dev: added filter yith_wcauthnet_order_description

= 1.1.11 - Released on 09 August 2019 =

* New: WooCommerce 3.7.0 RC2 support
* New: added methods to handle delete card / set card default operations on authorize side
* Tweak: added check to avoid duplicated tokens
* Update: internal plugin framework
* Update: Italian language
* Fix: payment profile creation when user already has profile registered
* Dev: added filter yith_wcauthnet_add_line_item_id
* Dev: added filter yith_wcauthnet_response_message

= 1.1.10 - Released on 20 May 2019 =

* New: Support to WordPress 5.2
* Update: Plugin Framework

= 1.1.9 - Released on 04 April 2019 =

* New: WooCommerce 3.6.0 RC1 support
* Update: internal plugin framework

= 1.1.8 - Released on 30 January 2019 =

* New: WooCommerce 3.5.4 support
* Tweak: removed check over deprecated MD5 Hash
* Update: internal plugin framework
* Update: Spanish translation
* Update: Dutch translation
* Fix: itemized transaction product name too long
* Fix: card not being created on first customer purchase

= 1.1.7 - Released on 12 December 2018 =

* New: WordPress 5.0 support
* New: WooCommerce 3.5.2 support
* Tweak: updated plugin framework

= 1.1.6 - Released on 24 October 2018 =

* New: WooCommerce 3.5 support
* Tweak: updated plugin framework
* Update: Dutch language

= 1.1.5 - Released on 16 October 2018 =

* New: WordPress 4.9.8 support
* New: WooCommerce 3.5-rc support
* Tweak: updated plugin framework
* Fix: check if debug is enabled before trying to access logger object
* Update: Spanish language
* Update: Italian language

= 1.1.4 - Released on 28 May 2018 =

* New: WooCommerce 3.4 compatibility
* New: WordPress 4.9.6 compatibility
* New: updated plugin framework to latest version
* New: GDPR compliance
* New: Italian language
* New: Added shipping to itemized data for redirect mode

= 1.1.3 - Released on 01 February 2018 =

* New: added WooCommerce 3.3.0 support
* New: added WordPress 4.9.2 support
* New: updated plugin-fw to latest revision
* New: added Dutch translation
* Tweak: set correct number of decimal digit for taxes

= 1.1.2 - Released on 11 May 2017 =

* Fix: preventing possible fatal error when processing orders
* Fix: possible notice with WC 3.0 when printing log messages

= 1.1.1 - Released on 11 April 2017 =

* Fix: possible Fatal Error for WC 2.6.x users, when CIM enabled

= 1.1.0 - Released on 04 April 2017 =

* New: WordPress 4.7.3 compatibility
* New: WooCommerce 3.0.0 RC2 compatibility
* Tweak: Avoid using a single instance of API class,as this may lead to overwritten properties
* Tweak: Changed Transaction Key from text field to password field

= 1.0.10 - Released on 01 August 2016 =

* Tweak: Added again payment method description to payment method form
* Tweak: updated plugin-fw
* Fixed: form requesting card details on Authorize.net payment method (redirect transaction type)

= 1.0.9 - Released on 17 June 2016 =

* Added WooCommerce 2.6 compatibility
* Added: WooCommerce 2.6 tokenization support
* Tweak: Switched authorize.net serve url to https://secure2.authorize.net/gateway/transact.dll (Akamai SureRoute production)
* Tweak: rmeove old Saved Cards template to use WooCommerce my-account endpoint

= 1.0.8 - Released on 05 May 2016 =

* Added: Support to WordPress 4.5.1
* Added: Support to WooCommerce 2.5.5
* Added: js code to keep user data through update_checkout process
* Tweak: Removed deprecated WC functions/methods
* Fixed: Phone field problem with CIM library
* Fixed: eCheck not passing transactionMode in XML request, causing transaction error
* Fixed: system always resetting default card when deleting one

= 1.0.7 - Released on 13 January 2016 =

* Added: yith essential kit 1 compatibility
* Added: option to customize "Pay button"
* Added: WC 2.5-RC Compatibility
* Added: WP 4.4 Compatibility
* Tweak: Performance improved with new plugin core 2.0

= 1.0.6 - Released on 13 August 2015 =

* Added: Compatibility with WP 4.2.4
* Added: Compatibility with WC 2.4.2
* Tweak: Updated internal plugin-fw

= 1.0.5 - Released on 03 July 2015 =

* Tweak: formatted order amount with number_format() function
* Tweak: formatted relay url with user_trailingslashit() function
* Fixed: Fingerprint calculation for SIM

= 1.0.4 - Released on 19 June 2015 =

* Added: WooCommerce 2.3.11
* Fixed: Fingerprint calculation for prices without decimals

= 1.0.3 - Released on 04 May 2015 =

* Fixed: "Plugin Documentation" link appearing on all plugins
* Fixed: minor bugs

= 1.0.2 - Released on 29 April 2015 =

* Added: handling for "Authorize only" transactions
* Fixed: escaped add_query_arg and remove_query_arg

= 1.0.1 - Released on 09 March 2015 =

* Fixed: minor fixes

= 1.0.0 - Released on 20 February 2015 =

* Initial release