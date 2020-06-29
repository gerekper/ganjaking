=== YITH WooCommerce Dynamic Pricing and Discounts  ===

Contributors: yithemes
Tags: woocommerce bulk pricing, woocommerce discounts, woocommerce dynamic discounts, woocommerce dynamic pricing, woocommerce prices, woocommerce pricing, woocommerce wholesale pricing, woocommerce cart discount, pricing, dynamic pricing, cart discount, special offers, bulk price
Requires at least: 4.5
Tested up to: 5.4
Stable tag: 1.6.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

YITH WooCommerce Dynamic Pricing and Discounts offers a powerful tool to directly modify prices and discounts of your store

== Description ==

An easy way to give new prices and offers!
With a simple click you can create dynamic offers to the customers of your shop: apply a discount percentage to the cart when it contains a certain number of products, or implement a small sale for each product New!

== Installation ==
Important: First of all, you have to download and activate WooCommerce plugin, which is mandatory for YITH WooCommerce Dynamic Pricing and Discounts to be working.

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce Dynamic Pricing and Discounts` from Plugins page.


= Configuration =
YITH WooCommerce Dynamic Pricing and Discounts will add a new tab called "Dynamic Pricing" in "YIT Plugins" menu item.
There, you will find all YITH plugins with quick access to plugin setting page.


== Changelog ==
= 1.6.5 - Released on 21 May 2020 =
* New: Support for WooCommerce 4.2
* New: Elementor Support
* Update: Plugin Framework
* Fix: Fixed issue with minimum price for variable products
* Fix: Issue on quantity table

= 1.6.4 - Released on 11 May 2020 =
* New: Option to change product price when customer changes quantity
* New: Option to select the default quantity in the table
* Update: Plugin Framework
* Update: Language file
* Dev: Added yith_ywdpd_valid_num_of_orders_status filter

= 1.6.3 - Released on 04 May 2020 =
New: Support for WooCommerce 4.1
Tweak: Update price when the product quantity is changed
Tweak: Quantity table improved
Update: Plugin Framework
Fix: Fixed "Price format" default value
Dev: Added ywdpd_apply_to_is_valid filter

= 1.6.2 - Released on 23 March 2020 =
Fix: Fixed Assets Loading

= 1.6.1 - Released on 20 March 2020 =
Fix: Category Select on Pricing rule editor
Fix: Cart discount issue when an exclusion rule is set

= 1.6.0 - Released on 18 March 2020 =
Tweak: Update panel option
Update: Language files
Update: Plugin Framework

= 1.5.9 - Released on 26 February 2020 =
New: Support for WordPress 5.4
New: Support for WooCommerce 4.0
Update: Language files
Update: Plugin Framework
Tweak: Added the possibility to select the quantity of the products offered as gifts
Fix: Conflicts with WPML plugin
Fix: Quantity table issue when clicking the price


= 1.5.8 - Released on 23 December 2019 =
New: Support for WooCommerce 3.9
Update: Plugin Framework
Update: Italian language
Fix: Fixed untranslatable string
Fix: Load owl carousel script only if gift product exists
Fix: Fixed removing gift products issue

= 1.5.7 - Released on 14 November 2019 =
Update: Language files
Update: Plugin Framework
Fix: Warning message

= 1.5.6 - Released on 31 October 2019 =
Update: javascript and css scripts

= 1.5.5 - Released on 30 October 2019 =
New: Added new discount mode "Gift Products" to Price Rule
New: Support for WordPress 5.3
New: Support for WooCommerce 3.8
Update: Language files
Update: Plugin Framework
Fix: Fixed issue on product notes
Dev: Added new filter 'ywdpd_force_cart_sorting'

= 1.5.4 - Released on 01 August 2019 =
New: Support for WooCommerce 3.7
Update: Italian language
Fix: Fixed price calculation with YITH WooCommerce Recover Abandoned Cart
Fix: Fixed issue with YITH WooCommerce Composite Product
Dev: Added filter 'ywdpd_skip_cart_sorting'

= 1.5.3 - Released on 29 May 2019 =
Update: Plugin Framework
Fix: Fixed some issue for rule execution
Dev: Added yith_ywdpd_cart_rules_discount_value_row action

= 1.5.2 - Released on 9 April 2019 =
New: Support for WooCommerce 3.6.0 RC1
New: Feature to exclude some products, categories or tags from the discount calculation
Tweak: Added transient for dynamic discount rules
Update: Language files
Update: Plugin Framework
Fix: Fixed the taxonomy check for variation products

= 1.5.1 - Released on 12 March 2019 =
Update: Language files
Update: Plugin Framework
Fix: YITH WooCommerce Brands Add-on Premium Integration
Fix: Duplicate rule issue with key and ID
Fix: Table rules hidden for variations
Fix: Issue for WooCommerce Composite Products
Fix: Compatibility issue with PHP 7.3

= 1.5.0 - Released on 29 January 2019 =
New: Duplicate rule option
Tweak: Drag and drop of rule
Update: Plugin Framework
Update: Language files
Fix: Issue with YITH WooCommerce Added to Cart Popup
Fix: discount percentage on princing discounts when amount is "1"
Fix: Hidden table quantity wrapper if table is empty
Fix: Added fix for YITH WooCommerce Catalog Mode
Dev: Replaced get_discount actions with get_price_and_discount actions
Dev: Added filters "ywdpd_json_search_tags_args" and "ywdpd_json_search_categories_args" to modify the $args for get_terms() when searching for tags/categories.

= 1.4.9 - Released on 05 December 2018 =
New: Support for WordPress 5.0
Update: Plugin Framework
Update: Language files
Fix: discount percentage on Cart discounts when amount is "1"
Fix: check include taxes with wc_prices_include_tax() rather than WC()->cart->tax_display_cart
Fix: fixed loop when the coupon is not added via ajax
Fix: issue with exclusion list
Fix: guest coupon code
Dev: New filter 'ywdpd_apply_discount_current_difference' and 'ywdpd_validate_apply_to_discount'
Dev: New filter 'ywdpd_get_variable_prices' and 'ywdpd_include_shipping_on_totals'
Dev: New filter 'ywdpd_check_cart_coupon'

= 1.4.8 - Released on 23 October 2018 =
Update: Plugin Framework
Update: Language files
Fix: Fix on get_maximum price for variable product

= 1.4.7 - Released on 16 October 2018 =
New: Support for WooCommerce 3.5
Update: Plugin Framework
Fix: Timezone issue

= 1.4.6 - Released on 26 September 2018 =
Update: Plugin Framework
Update: Language files
Fix: Fixed schedule timezone issue
Fix: Fix some issue with PHP 7.2.x
Fix: Fix integration with YITH WooCommerce Added to cart Popup and Special Offers
Dev: New filter 'ywdpd_table_custom_hook'


= 1.4.5 - Released on 17 May 2018 =
New: Support for WordPress 4.9.6 RC2
New: Support for WooCommerce 3.4.0 RC1
New: Search rules on backend
New: Persian Language
New: Integration with YITH WooCommerce Added to Cart Popup Premium
Update: Plugin Framework
Update: Language files
Fix: Price table
Dev: New filter 'ywdpd_check_if_single_page'
Dev: New filter 'ywdpd_show_minimum_price_for_simple'

= 1.4.4 - Released on 29 January 2018 =
New: Support for WooCommerce 3.3 RC2
Update: Plugin Framework
Fix: Subtotal calculation after price disc rule applied
Fix: Integration with YITH WooCommerce Membership
Fix: Issue product on sale

= 1.4.3 - Released on 08 January 2018 =
Update: Plugin Framework
Fix: Issue when the discount starts from 1 with 100% off
Fix: For minimum price
Fix: Php notice in backend
Fix: On Off issue
Dev: Added action 'ywdpd_before_replace_cart_item_price'
Dev: Added condition for load scripts on plugin pages only
Dev: Added filter ywdpd_round_total_price

= 1.4.2 - Released on 15 December 2017 =
Fix: Search taxonomies error in rules
Fix: Metabox on-off on save options

= 1.4.1 - Released on 13 December 2017 =
Fix: Priority field in Cart Discount
Fix: Stylesheet backend
Update: Plugin Framework

= 1.4.0 - Released on 11 December 2017 =
New: Restyling Plugin Panel
Tweak: Better performances
Update: Plugin Framework
Fix: Table price issue when any variation is selected as default

= 1.3.0 - Released on 27 October 2017 =
New: Support for WooCommerce 3.2 RC2
Update: Plugin Framework
Fix: Issue with price table and cart item price for variable products
Fix: Issue with YITH WooCommerce Color and Label Variations

= 1.2.9 - Released on 27 September 2017 =
Fix: discount missed in single product page
Fix: variation display prices when a single the variation is on-sale

= 1.2.8 - Released on 20 September 2017 =
New: Cart Discount option 'Maximum number of orders required'
New: Cart Discount option 'Maximum past expense required'
New: German Translation
New: Dutch Translation
Update: Plugin Framework
Fix: Conflict with plugin WooCommerce Point of Sale
Fix: Issue between Dynamic and Points and Rewards on product variable
Fix: Issue between Dynamic and YITH WooCommerce Added to Cart Popup
Fix: Min variation regular price
Fix: Coupon issues
Dev: Added filter ywdpd_apply_discount
Dev: Added filter ywdpd_dynamic_category_list
Dev: Added filter ywdpd_dynamic_exclude_category_list
Dev: Added filter ywcdp_product_is_on_sale


= 1.2.7 - Released on 09 June 2017 =
New: Support for WooCommerce 3.0.8
New: Support for WordPress 4.8
Update: Plugin Framework
Fix: Cart Discount with other coupon

= 1.2.6 - Released on 26 May 2017 =
Fix: Coupons for Cart Discount
Fix: Notice for sale price

= 1.2.5 - Released on 18 May 2017 =
Fix: Fatal error with the plugin WooCommerce Points of Sale
Fix: Multiple coupons
Fix: Price table in a variable product with different quantity rules for variations
Dev: Moved filter ywdpd_dynamic_label_coupon position

= 1.2.4 - Released on 11 May 2017 =
New: html code can be added to notes
Update: Plugin Framework
Fix: Multiple special offers
Fix: Cart rule for Minimum and Maximum items in cart
Fix: Hidden coupon messages

= 1.2.3 - Released on 12 April 2017 =
Fix: Tax calculation in cart

= 1.2.2 - Released on  10 April 2017 =
New: WooCommerce 3.0.1
New: Option to extend the rules to the translated objects
Update: Plugin Framework
Fix: Integration with YITH WooCommerce Role Based Prices
Fix: Coupon for cart discount discount

= 1.2.1 - Released on  06 April 2017 =
New: WooCommerce 3.0 compatibility
Update: Plugin Framework
Fix: Filter get price
Fix: Coupon label
Fix: Coupon individual use

= 1.2.0 - Released on  04 April 2017 =
New: WooCommerce 3.0-RC2 compatibility
Update: Plugin Framework
Fix: Quantity table role for variation
Dev: Added filter 'yith_ywdpd_get_discount_price'
Dev: Added action 'ywdpd_before_cart_process_discounts'

= 1.1.9 - Released on  15 February 2017 =
Fix: Minimum price on quantity rules

= 1.1.8 - Released on  14 February 2017 =
Fix: Conflict with some WooThemes Plugins when the filter 'woocommerce_get_price' is used


= 1.1.7 - Released on  11 February 2017 =
New: Integration with YITH WooCommerce Brands Add-on - Premium v. 1.0.9
New: Compatibility with WooCommerce Mix and Match Product v. 1.1.8
New: Option to rename the rule
New: Quantity table now updates every time a variation is selected
New: Custom format for prices with  %discounted_price%, %original_price% and %percentual_discount%
New: Options to show starting price if a quantity-discount rule applies to the product
New: Option to clone a rule
New: Date and time picker in the rule editor
New: Discount type in Cart Discount rules
New: Option to choose whether apply the discount on subtotal inclusing or excluding tax
Dev: Added filter 'ywdpd_exclude_products_from_discount' to exclude product from discount rules
Tweak: Special offer calculation


= 1.1.6 - Released on 11 November 2016  =
Update: Plugin Framework
Fix: Add new rule in admin panel
Fix: Round precision on cart

= 1.1.5 - Released on 09 November 2016  =
New: Drag and drop to order the price and cart rules
Update: Plugin Framework
Fix: Html price where tax are included
Fix: Exclude a list of roles save option

= 1.1.4 - Released on 12 October 2016  =
New: Option 'Disable with other coupon' in price discount rules
New: Compatibily with YITH WooCommerce Product Bundles v1.1.2
Update: Plugin Framework
Fix: Cart Discount calculated before tax
Fix: Special offers issues


= 1.1.3 - Released on 26 August 2016  =
New: Spanish translation
New: Italian translation
New: Sorting cart by price
New: Compatibily with YITH WooCommerce Role Based Prices
Tweak: Variation html price
Update: Plugin Framework
Fix: Variation min regular price
Fix: Issue in save options
Fix: Special offers issues


= 1.1.2 -  Released on 10 June 2016 =
New: Support on WooCommerce 2.6 RC1
Update: Plugin Framework

= 1.1.1 -  Released on 01 June 2016 =
New: Guest on the list of roles
Update: Plugin Framework
Fix: Javascript errors in backend

= 1.1.0 - Released on 16 May 2016 =
New: Compatibility with YITH WooCommerce Membership Premium
New: Compatibility with WooCommerce 2.5.1
New: Variation products into the product list
New: Compatiblity with YITH WooCommerce Multi Vendor Premium
New: Textarea fields to show messages in single product page in the apply and adjustment products
New: Tags to select all products with same tags
New: Option in cart discount to enable the cart discount also if there's a coupon applied
New: Options to enable shop_manager to edit settings
New: Options to add notes for Quantity Discounts to show in Products "Apply To"
Tweak: Template price table
Tweak: Now the rules have single 'Save changes' button
Fix: Refresh calculation cart after added a product in cart
Fix: Price on excluded products
Fix: Special Offers quantity to check
Fix: Some error in validate_apply_to and is_in_exclusion_rule functions

= 1.0.3 - Released on 15 January 2016 =
New: filter ywdpd_show_price_on_table_pricing on pricing table

= 1.0.2 - Released on 14 January 2016 =
New: Compatibility with YITH WooCommerce Gift Cards Premium
New: Support for WooCommerce 2.5
Tweak: Table quantity for variations with min and max amount
Update: Plugin Framework

= 1.0.1 - Released on 12 August 2015 =
New: Support for WooCommerce 2.4.2
Update: Plugin Framework

= 1.0.0 - Released on 03 July 2015 =
Initial release

