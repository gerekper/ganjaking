=== CHANGELOG ===

=== Version 1.3.13 === Released on 26 May 2020

* New: Support for WooCommerce 4.2
* Update: Plugin framework
* Update: Language files
* Fix: Missing class attribute for required fields

=== Version 1.3.12 === Released on 27 April 2020

* New: Support for WooCommerce 4.1
* Update: Plugin framework
* Fix: Prevent fatal error "Call to undefined method WC_Product::get_category_ids()"

=== Version 1.3.11 === Released on 17 April 2020

* Update: Plugin framework
* Update: qtip2 script to version 3.0.3
* Tweak: Improved responsive style

=== Version 1.3.10 === Released on 11 March 2020

* Fix: issue with required field not working properly

=== Version 1.3.9 === Released on 09 March 2020

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* Update: Italian translation
* Fix: required fields in according to selected country
* Fix: issue on removing a condition on a rule

=== Version 1.3.8 === Released on 14 February 2020

* Update: Plugin Core
* Fix: Checkout required fields don't match plugin settings
* Tweak: Allow adding more values to fields check conditions

=== Version 1.3.7 === Released on 04 February 2020

* New: Support to WooCommerce 3.9.1
* Update: Plugin Core
* Fix: Wrong state field type when country field is not a select dropdown
* Fix: Additional fields didn't work properly with conditional rules
* Fix: Wrong value of "ship to different address" checkbox when checkout has custom fields

=== Version 1.3.6 === Released on 08 January 2020

* Update: spanish language
* Fix: phone and email fields not shown in My Account billing section

=== Version 1.3.5 === Released on 23 December 2019

* New: Support to WooCommerce 3.9
* New: Support to WordPress 5.3.2
* Update: Plugin framework
* Update: Dutch language

=== Version 1.3.4 === Released on 07 December 2019

* Fix: issue with rule validation on radio and checkout input fields

=== Version 1.3.3 === Released on 29 November 2019

* Update: Italian language
* Update: Notice handler
* Update: Plugin framework
* Fix: JavaScript set required condition for fields by checking products in cart

=== Version 1.3.2 === Released on 06 November 2019

* Fix: undefined variable $condition_is_valid

=== Version 1.3.1 === Released on 06 November 2019

* Fix: undefined function

=== Version 1.3.0 === Released on 06 November 2019

* New: Show fields with logic conditions
* Update: Plugin Core

=== Version 1.2.14 === Released on 25 October 2019

* New: Support for WooCommerce 3.8
* New: Support for WordPress 5.3
* Update: Plugin Core

=== Version 1.2.13 === Released on 26 September 2019

* New: Added field type "number"
* Update: Plugin Core
* Fix: Prevent warning "array_merge(): Argument #1 is not an array"
* Dev: Set min date for datepicker through new filter 'ywccp_datepicker_min_date'

=== Version 1.2.12 === Released on 06 August 2019

* New: Support to WooCommerce 3.7.0 RC2
* Update: Italian language
* Update: Plugin Core
* Fix: Decode option keys in order to prevent issue with not latin charset

=== Version 1.2.11 === Released on 11 June 2019

* New: Support to WooCommerce 3.6.4
* New: Support to WordPress 5.2.1
* Update: Plugin Core
* Fix: Warning explode() expects parameter 2 to be string, array given
* Fix: Warning: Invalid argument supplied for foreach()
* Dev: New filter 'ywccp_datepicker_change_year'
* Dev: New filter 'ywccp_datepicker_change_month'
* Dev: New filter 'ywccp_datepicker_year_range'
* Dev: New filter 'ywccp_custom_shipping_fields'
* Dev: New filter 'ywccp_custom_billing_fields'

=== Version 1.2.10 === Released on 03 May 2019

* New: Support to WooCommerce 3.6.2
* Update: Plugin Core
* Fix: Missing classes for heading field type

=== Version 1.2.9 === Released on 09 April 2019

* New: Support to WooCommerce 3.6.0 RC1
* Update: Plugin Core
* Fix: Validation process for VAT with whitespaces

=== Version 1.2.8 === Released on 26 March 2019

* New: Support to WooCommerce 3.5.7
* Update: Plugin Core
* Fix: WPML translation not used for shipping fields showed in popup of Multiple Shipping Address plugin
* Fix: Made plain_text param optional for ywccp_email_additional_fields_list function
* Dev: Filtered get_address method of WC_Order class

=== Version 1.2.7 === Released on 28 January 2019

* New: Support to WooCommerce 3.5.4
* Update: Plugin Core
* Update: Dutch translation
* Fix: Missing fields in user edit profile admin section

=== Version 1.2.6 === Released on 02 January 2019

* Update: Plugin Core
* Update: Spanish translation
* Fix: Missing translations in order table using WPML or Polylang

=== Version 1.2.5 === Released on 05 December 2018

* New: Support to WooCommerce 3.5.2
* New: Support to WordPress 5.0.0
* Tweak: Drag and drop icon and new cursor for the checkout fields edit table
* Update: Plugin Core

=== Version 1.2.4 === Released on 08 November 2018

* Update: Plugin Core
* Fix: Checkout fields showed in a wrong order

=== Version 1.2.3 === Released on 26 October 2018

* New: Support to WooCommerce 3.5.0
* Update: Plugin Core
* Update: Languages files

=== Version 1.2.2 === Released on 28 September 2018

* New: Support to WooCommerce 3.4.5
* New: Support to WordPress 4.9.8
* New: Support to Polylang 2.3.10
* Update: Italian language
* Update: Dutch language
* Update: Plugin Core
* Dev: New hook "ywccp_scripts_registered" after scripts register

=== Version 1.2.1 === Released on 30 May 2018

* New: Support to WooCommerce 3.4.1
* New: Support to WordPress 4.9.6
* New: YITH WooCommerce EU Vat compatibility
* Update: Plugin core

=== Version 1.2.0 === Released on 15 May 2018

* New: Support to WooCommerce 3.4 RC1
* New: General Data Protection Regulation (GDPR) compliance
* Update: Plugin Core
* Update: Italian language files
* Update: Dutch language files
* Fix: Customer order note string fields not translatable
* Fix: New fields name sanitize and clean-up

=== Version 1.1.0 === Released on 31 January 2018

* New: Support to WooCommerce 3.3.0
* New: Support to WordPress 4.9.2
* New: Dutch translation
* New: Option show/hide field label for the formatted addresses
* Update: Plugin Core
* Update: Language files
* Fix: Get customer address functions now includes also custom fields
* Fix: Custom fields visibility in my account pages

=== Version 1.0.11 === Released on 29 November 2017

* New: Support to WooCommerce 3.2.5
* New: Support to WordPress 4.9.0
* Fix: Filter customer details for AJAX action in order edit pages
* Fix: Plugin scripts minimization

=== Version 1.0.10 === Released on 24 October 2017

* New: Support to WooCommerce 3.2.1
* Update: Plugin Core
* Fix: WPML String Translation for "locale" and "locale default" arrays

=== Version 1.0.9 === Released on 10 October 2017

* New: Support to WooCommerce 3.2.0 RC2
* New: Support to WordPress 4.8.2
* Update: Plugin Core

=== Version 1.0.8 === Released on 20 September 2017

* New: German Translation ( thanks to Alexander Cekic )
* Fix: Prevent infinite loops if default options are not already saved

=== Version 1.0.7 === Released on 30 August 2017

* Fix: RTL issue for checkout fields order
* Fix: Prevent infinite loops on some cases when getting the default WC checkout fields
* Fix: Undefined index notices

=== Version 1.0.6 === Released on 24 August 2017

* New: Support to WooCommerce 3.1.2
* New: Support to WordPress 4.8.1
* Update: Plugin Framework
* Update: Language files
* Fix: Removed unnecessary empty option for fields type select
* Fix: Translate also select options using WPML
* Fix: Stripslashes for label and placeholder options
* Fix: Removed unnecessary "clear row" option

=== Version 1.0.5 === Released on 03 March 2017

* New: Option for choose date format for date fields
* New: Support to WooCommerce 2.7 RC 1
* New: Allow not latin charset for options values
* New: Option to overwrite formatted addresses ordering
* Update: Plugin Framework
* Update: Language files
* Fix: Compatibility issues with WPML
* Fix: Required for heading fields
* Fix: Skip validate process for empty fields

=== Version 1.0.4 === Released on 05 August 2016

* Update: Plugin Framework
* Fix: CSS issue with datepicker

=== Version 1.0.3 === Released on 22 July 2016

* New: Type Tel for checkout fields
* New: Spanish Translation
* New: Compatibility with WooCommerce Customer Order Csv Export
* Update: Language files
* Update: Core plugin
* Fix: Placeholder for type select doesn't show
* Fix: Plugin documentation link
* Fix: Checkbox required in checkout process now is checked correctly

=== Version 1.0.2 === Released on 07 June 2016

* New: Support to WooCommerce 2.6 Beta
* New: Italian Translation
* Update: Language files
* Update: Core plugin

=== Version 1.0.1 === Released on 03 May 2016

* New: Now you can specify the key when you add an option for a select or radio type ( key::value )
* Fix: show/hide correctly State field on Country change
* Fix: Javascript error with select2 script

=== Version 1.0.0 === Released on 22 April 2016

 * Initial Release
