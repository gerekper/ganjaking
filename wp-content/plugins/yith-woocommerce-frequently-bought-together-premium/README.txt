== Changelog ==

== 1.6.1 == Released on 23 April 2020

* New: Support for WooCommerce 4.1.0
* New: Support for YITH Proteo theme
* Update: Plugin framework
* Update: Italian language
* Fix: Disable add to cart for not purchasable variations
* Dev: Added total_discount variable to yith-wfbt-form.php template

== 1.6.0 == Released on 09 March 2020

* New: Support for WooCommerce 4.0.0
* New: Support for WordPress 5.4
* Update: Plugin framework
* Update: Language files

== 1.5.7 == Released on 12 February 2020

* Fix: "This product" label not shown inside the form

== 1.5.6 == Released on 04 February 2020

* New: Support for WooCommerce 3.9.1
* New: Option to set cross-sells or up-sells as frequently bought together products
* Update: Spanish language
* Update: Dutch language
* Update: Plugin framework
* Dev: New filter 'yith_wfbt_total_discount'
* Dev: New filter 'yith_wcfbt_add_coupon'
* Dev: New filter 'yith_wfbt_this_product_label'

== 1.5.5 == Released on 21 December 2019

* New: Support for WooCommerce 3.9
* New: Support for WordPress 5.3.2
* Update: Plugin framework

== 1.5.4 == Released on 18 December 2019

* Fix: form not showed when only one product is selected

== 1.5.3 == Released on 09 December 2019

* Update: Plugin framework
* Fix: Check stock status for variable products
* Fix: Frequently Bought Together form doesn't appear on variable products under certain conditions

== 1.5.2 == Released on 29 November 2019

* Update: Notice handler
* Update: Plugin framework

== 1.5.1 == Released on 28 November 2019

* Update: Main language file
* Update: Italian language

== 1.5.0 == Released on 28 November 2019

* New: Support for variations, customers can choose the variation they prefer directly in Frequently Bought Together form
* Update: Plugin framework

== 1.4.3 == Released on 05 November 2019

* Update: Plugin framework

== 1.4.2 == Released on 30 October 2019

* Update: Plugin framework
* Update: Spanish language

== 1.4.1 == Released on 29 October 2019

* New: Support for WooCommerce 3.8
* New: Support for WordPress 5.3
* Update: Plugin framework
* New: Option to show the form above product meta
* Update: Italian language
* Update: Dutch language
* Fix: Prevent fatal error "Call to a member function get_code() on null"
* Fix: Prevent error on array_merge in shortcode template


== 1.4.0 == Released on 09 August 2019

* New: Support to WooCommerce 3.7
* New: Added plugin data to WooCommerce import/export products process
* Update: Italian language
* Update: Plugin Core
* Tweak: Removed useless coupon messages when a discount for a group is set
* Tweak: Removed "remove link" from group discount totals in cart and checkout page
* Tweak: Added an alert message if WooCommerce coupons are disabled
* Dev: New filter 'yith_wfbt_price_to_display'

== 1.3.11 == Released on 29 May 2019

* New: support to WooCommerce 3.6.4
* New: support to WordPress 5.2
* Update: Plugin Core

== 1.3.10 == Released on 09 April 2019

* New: Support to WooCommerce 3.6.0 RC1
* Update: Spanish language
* Update: Plugin Core
* Dev: New hook 'yith_wfbt_end_item'
* Dev: New filter 'yith-wfbt-coupon-individual-use'
* Tweak: Form additional text wrapped inside a tag 'p'

== 1.3.9 == Released on 30 January 2019

* New: Support to WooCommerce 3.5.4
* Update: Plugin Core
* Fix: Percentage discount validation process
* Fix: Integration issue with YITH WooCommerce Multi Vendor

== 1.3.8 == Released on 06 December 2018

* New: Support to WooCommerce 3.5.2
* New: Support to WordPress 5.0.0
* New: Support to Gutenberg
* Update: Plugin Core
* Update: Languages files
* Update: Dutch translation

== 1.3.7 == Released on 26 October 2018

* Update: Plugin Core
* Update: Languages files
* Fix: JavaScript error "element is not focusable" in edit product pages

== 1.3.6 == Released on 23 October 2018

* New: Support to WooCommerce 3.5.0
* Update: Plugin Core
* Fix: Error discount coupon doesn't exist
* Fix: Error on missing function get_default_language (WPML)
* Tweak: Validate input in product tab before update

== 1.3.5 == Released on 01 October 2018

* New: Support to WooCommerce 3.5.0 RC1
* Update: Plugin Core
* Update: Dutch language
* Fix: Exclude empty discount code
* Fix: Discount calculation for price including taxes
* Tweak: Set no-cache header for "add to cart" action

== 1.3.4 == Released on 11 September 2018

* Update: Plugin Core
* Fix: Discount code validation

== 1.3.3 == Released on 06 September 2018

* Fix: Discount percentage calculation

== 1.3.2 == Released on 06 September 2018

* New: Show additional text before products list

== 1.3.1 == Released on 05 September 2018

* Fix: Update meta error with PHP 7.2

== 1.3.0 == Released on 04 September 2018

* New: Support to WooCommerce 3.4.5
* New: Support to WordPress 4.9.8
* New: Now is possible to set a discount to the frequently bought together group
* New: New option to set related products as frequently bought products
* New: Option to redirect to checkout page after to "add to cart" action
* New: Handled frontend actions using AJAX to better performance
* New: Manage plugin's product meta as unique meta for better performance
* New: Option to set products in form unchecked by default
* Update: Plugin Core
* Update: Translation files
* Update: Main plugin frontend template
* Dev: New action yith_wfbt_group_added_to_cart triggered after add to cart action
* Dev: New filter yith_wfbt_filter_group_products_front to filter frontend products
* Fix: Responsive style

== 1.2.1 == Released on 31 May 2018

* New: Support to WooCommerce 3.4.1
* New: Support to WordPress 4.9.6
* Update: Dutch translation
* Update: Plugin Core
* Update: Italian translation
* Fix: Documentation url
* Fix: Error "Call to a member function get_permalink() on boolean"

== 1.2.0 == Released on 30 January 2018

* New: Support to WordPress 4.9.2
* New: Support to WooCommerce 3.3.0 RC2
* New: Compatibility with YITH WooCommerce Multi Vendor Premium
* New: Dutch translation
* Update: Plugin core
* Update: Language files

== 1.1.3 == Released on 16 October 2017

* New: Support to WordPress 4.8.2
* New: Support to WooCommerce 3.2.1
* Update: Plugin core

== 1.1.2 == Released on 22 July 2017

* New: Support to WordPress 4.8
* New: Support to WooCommerce 3.1.1
* New: Add decimal and thousand separator from WooCommerce settings during frontend JS price calculation
* Update: Plugin core
* Fix: Shortcode return value missing
* Fix: Exclude deleted products automatically from frequently bought products
* Fix: Force correct image size on ajax loading

== 1.1.1 == Released on 24 April 2017

* New: Support to WordPress 4.7.4
* New: Support to WooCommerce 3.0.4
* Update: Plugin core

== 1.1.0 == Released on 28 March 2017

* New: Support to WordPress 4.7.3
* New: Support to WooCommerce 3.0.0
* Update: Plugin core
* Update: Language files
* Fix: Responsive slider carousel on Wishlist page

== 1.0.7 == Released on 25 October 2016

* New: Support to WooCommerce 2.6.6
* New: Italian translation
* New: Spanish translation
* Update: Plugin core
* Fix: WPML integration issue

== 1.0.6 == Released on 10 June 2016

* New: Support to WooCommerce 2.6 RC1
* Update: Plugin core

== 1.0.5 == Released on 27 April 2016

* New: WordPress 4.5 compatibility
* New: WPML compatibility
* New: Shortcode [ywfbt_form] to print "bought together" products
* Update: Template yith-wfbt-form.php to version 1.0.5
* Update: Changed plugin textdomain from ywbt to yith-woocommerce-frequently-bought-together
* Update: Language file
* Update: Plugin core
* Fix: "Out of stock" products are no longer included in "bought together" group

== 1.0.4 == Released on 12 January 2016

* New: Compatibility with WooCommerce 2.5
* New: Option to set a background to "Frequently Bought" form
* Update: Plugin Core

== 1.0.3 == Released on 28 August 2015

* Fix: Thumbnail image size option

== 1.0.2 == Released on 21 August 2015

* New: Compatibility with WooCommerce 2.4.x
* New: ITA Translation
* New: Compatibility with Wordpress 4.3
* Update: Plugin Core

== 1.0.1 == Released on 24 July 2015

* Fix: Minor Bugs
* Update: Plugin Core

== 1.0.0 == Released on 22 May 2015

* Initial release