=== YITH WooCommerce Product Bundles ===

== Changelog ==

= 1.3.9 - Released on 03 Jul 2020 =

* New: support for WooCommerce 4.3
* Update: plugin framework
* Update: language files
* Fix: added bundle product line prices in shipping packages to prevent issues with plugins handling shipping methods
* Dev: added yith_wcpb_add_label filter
* Dev: added yith_wcpb_after_bundled_item_title action
* Dev: added yith_wcpb_bundled_item_quantity_input_step filter
* Dev: added yith_wcpb_bundled_item_quantity_input_min filter
* Dev: added yith_wcpb_bundled_item_quantity_input_max filter
* Dev: added yith_wcpb_allowed_product_types filter

= 1.3.8 - Released on 21 May 2020 =

* New: support for WooCommerce 4.2
* New: support for Aelia Currency Switcher
* Update: plugin framework
* Update: language files
* Fix: rounding price issue in Cart and Checkout
* Dev: added yith_wcpb_round_bundled_item_price filter
* Dev: added yith_wcpb_round_bundled_item_price_rounded filter

= 1.3.7 - Released on 23 April 2020 =

* New: support for WooCommerce 4.1
* New: support for Flatsome quick-view feature
* Update: language files
* Update: plugin framework
* Fix: 'non well formed numeric value' issues

= 1.3.6 - Released on 28 February 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: support for YITH Proteo theme
* Update: language files
* Update: plugin framework
* Fix: add-to-cart style in combination with Elementor plugin
* Fix: bundle subtotal in orders
* Tweak: prevent 'non-numeric value' error

= 1.3.5 - Released on 20 December 2019 =

* New: possibility to search by sku when adding products to the bundle
* Fix: issue with non-numeric values
* Update: language files
* Update: plugin framework

= 1.3.4 - Released on 6 November 2019 =

* Fix: integration with YITH WooCommerce Request a Quote
* Update: plugin framework

= 1.3.3 - Released on 30 October 2019 =

* Update: Spanish language
* Update: plugin framework

= 1.3.2 - Released on 28 October 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: panel style
* Tweak: added alert when clicking on disabled add-to-cart button if some variations are not selected or unavailable
* Update: plugin framework
* Fix: fatal error 'Call to a member function is_type() on bool'
* Fix: price issues with taxes
* Fix: enable/disable checkbox when clicking on label for optional items
* Fix: issue with product object in quick view
* Fix: YITH WooCommerce Role Based Prices integration
* Dev: added yith_wcpb_minimum_characters_ajax_search filter

= 1.3.1 - Released on 5 August 2019 =

* New: support to WooCommerce 3.7
* New: support for YITH WooCommerce Waiting List also with 'Out of stock sync' enabled
* Update: plugin framework
* Update: language files
* Fix: added missing parameter in woocommerce_add_cart_item_data filter
* Fix: sync price of 'per item pricing' bundles for 'wc_product_meta_lookup' data
* Fix: sync stock status in frontend only
* Dev: added yith_wcpb_get_bundle_products_by_item function

= 1.3.0 - Released on 3 July 2019 =

* New: possibility to set minimum quantity to zero for bundled items
* New: support to YITH WooCommerce Deposits and Down Payments
* Update: plugin framework
* Update: language files
* Fix: integration with YITH WooCommerce Request a Quote
* Fix: enable/disable add-to-quote button in combination with YITH WooCommerce Request a Quote
* Fix: shipping issue in combination with YITH WooCommerce Multi Vendor
* Fix: attributes not shown when adding to the cart bundles with variable products
* Fix: prevent adding bundle products in orders through 'Add products' box
* Tweak: fixed text-domain for some strings
* Dev: added yith_wcpb_wc_dropdown_variation_attribute_options function
* Dev: added yith_wcpb_use_wc_dropdown_variation_attribute_options filter
* Dev: added params to yith_wcpb_before_bundle_woocommerce_add_to_cart action
* Dev: added params to yith_wcpb_after_bundle_woocommerce_add_to_cart params
* Dev: added yith_wcpb_ajax_get_bundle_total_price_response filter
* Dev: added params to JS trigger yith_wcpb_ajax_update_price_request

= 1.2.20 - Released on 23 May 2019 =

* New: integration with YITH WooCommerce Name Your Price 1.1.5
* Update: language files
* Update: plugin framework
* Fix: fatal error 'Call to undefined method YITH_WC_Bundled_Item::get_raw_title()'
* Fix: show variation name inside request a quote list table
* Tweak: prevent notice when adding to cart bundle

= 1.2.19 - Released on 30 April 2019 =
* Fix: support to WPML
* Tweak: removed deprecate woocommerce_stock_html filter by using wc_get_stock_html function
* Dev: added yith_wcpb_default_bundle_item_description filter

= 1.2.18 - Released on 10 April 2019 =
* New: support to WooCommerce 3.6
* New: option to show the price of bundled items in Cart and Checkout when the option 'per item pricing' is enabled
* Fix: integration with YITH WooCommerce Multi Vendor
* Fix: support to YITH WooCommerce Dynamic Pricing and Discounts
* Fix: non-closed <a> tag in bundle widget
* Fix: stock status issue in combination with third-party plugins
* Fix: removed 'qty' css class in quantity buttons of bundled items to prevent issue with themes that customize the quantity buttons
* Update: plugin framework
* Update: language files
* Tweak: check if bundle is in cart, otherwise remove bundled items, if they are there
* Tweak: prevent notices with PHP 7
* Dev: added yith_wcpb_select_product_box_args filter
* Dev: added yith_wcpb_widget_before_product_title action

= 1.2.17 - Released on 5 February 2019 =
* New: set minimum/maximum quantity of different items allowed in the bundle
* Fix: stock status CSS class in single product page
* Fix: issue when overriding default selection for variable attributes on hidden bundled items
* Update: language files
* Update: plugin framework
* Tweak: removed '1x' label in title if the bundled item quantity is set to one

= 1.2.16 - Released on 6 December 2018 =
* New:  support to WordPress 5.0
* Fix: YITH WooCommerce Role Based Prices integration
* Fix: failed add to cart operation when maximum limit value of bundle is set to zero
* Fix: style issues
* Update: plugin framework
* Update: language files
* Tweak: issue on displaying stock availability
* Dev: added yith_wcpb_customization_bundled_item_stock_html filter

= 1.2.15 - Released on 23 October 2018 =

* Fix: bundled items issue when 'non-bundled shipping' option is enabled
* Fix: integration with YITH WooCommerce Request a Quote
* Tweak: prevent issues if srcset is false in bundled item thumbnails
* Update: Plugin Framework
* Dev: added yith_wcpb_woocommerce_get_cart_item_from_session filter

= 1.2.14 - Released on 11 October 2018 =

* New: support to WooCommerce 3.5.x
* New: option to open bundled images in gallery through PhotoSwipe
* Fix: issue with quantity when adding to cart bundle from shop page
* Fix: support to YITH WooCommerce Request a Quote
* Fix: integration with YITH WooCommerce Quick View in combination with Storefront theme
* Update: Plugin Framework

= 1.2.13 - Released on 28 September 2018 =

* New: support to WooCommerce 3.5.0-beta.1
* New: popup for adding items in bundle
* New: set how to display price of 'per item pricing' bundles in Shop
* New: possibility to open bundle item links through YITH WooCommerce Quick View
* Fix: issue in combination with coupons with specified products
* Fix: shipping issue with virtual product
* Fix: issue when adding to cart a non purchasable variation
* Fix: min-max quantity issue with variable products
* Fix: max price for variable products if some variations are selected in bundled item
* Tweak: prevent issues on cart
* Tweak: prevent issues with empty prices on backend
* Update: Plugin Framework
* Update: Spanish language
* Update: Italian language
* Dev: added yith_wcpb_before_product_bundle_options_tab action
* Dev: added yith_wcpb_after_product_bundle_options_tab action

= 1.2.12 - Released on 31 May 2018 =

* New: support to WooCommerce 3.4.x
* New: support to WordPress 4.9.6
* Fix: deprecated notice
* Update: plugin framework
* Update: Italian language
* Update: Dutch language

= 1.2.11 - Released on 23 April 2018 =

* New: possibility to manually force price sync of bundle with 'per item pricing' option enabled
* New: Spanish translation
* Fix: order item meta saving issue
* Fix: integration with YITH WooCommerce Catalog Mode
* Fix: order again when order contains bundle products
* Fix: issue with shipping tab in virtual simple products
* Tweak: fixed doc url

= 1.2.10 - Released on 31 January 2018 =

* New: support to WooCommerce 3.3
* Update: Plugin Framework
* Fix: replaced woocommerce_add_order_item_meta hook with woocommerce_new_order_item
* Dev: added yith_wcpb_bundled_item_thumbnail_size filter

= 1.2.9 - Released on 10 January 2018 =

* New: added bundle_add_to_cart shortcode
* Update: Plugin Framework 3

= 1.2.8 - Released on 5 December 2017 =

* New: Dutch language
* New: Italian language
* Fix: issue in combination with YITH WooCommerce Catalog Mode
* Fix: removed bottom borders in variation select
* Fix: hidden table if all bundled items are hidden
* Fix: default variation issue when the item is optional
* Update: language files
* Tweak: removed price suffix in Price Html to prevent price issues
* Tweak: show default price for variable product if no-variation is selected
* Dev: added yith_wcpb_bundled_item_show_default_price_for_variables filter
* Dev: added yith_wcpb_bundled_item_displayed_price filter
* Dev: added yith_wcpb_bundled_item_is_hidden filter
* Dev: added yith_wcpb_bundled_item_is_optional filter
* Dev: added product_id param to yith_wcpb_bundled_item_calculated_discount filter
* Dev: added woocommerce_after_add_to_cart_quantity action

= 1.2.7 - Released on 11 October 2017 =

* New: support to Support to WooCommerce 3.2.0 RC2
* Fix: YITH WooCommerce Request a Quote integration
* Tweak: replaced 'Clear selection' text with 'Clear' to reset variations

= 1.2.6 - Released on 11 September 2017 =

* Fix: per item pricing bundle sorting
* Fix: issue when click on Add Product and no product is selected
* Fix: purchasable issue with variable products
* Fix: wpml integration issues
* Tweak: added indicator for not-purchasable bundled items in backend
* Tweak: added check to prevent errors
* Dev: added yith_wcpb_bundled_item_calculated_discount filter
* Dev: added yith_wcpb_cart_error_notice_minimum_not_reached filter
* Dev: added yith_wcpb_cart_error_notice_maximum_exceeded filter
* Dev: added yith_wcpb_after_bundled_item_quantity_input action

= 1.2.5 - Released on 30 June 2017 =

* Fix: prevent issue allowing simple and variable product only as bundled items
* Fix: exclude bundled product from discount in combination with YITH WooCommerce Dynamic Pricing and Discounts
* Fix: quantities in cart in YITH WooCommerce Dynamic Pricing integration
* Fix: WPML integration issue with hidden variable items
* Tweak: improved report performances

= 1.2.4 - Released on 27 June 2017 =

* Fix: YITH WooCommerce Role Based Prices integration
* Fix: compatibility issue
* Fix: help-tip
* Dev: added yith_wcpb_help_tip function
* Tweak: prevent open item detail when click on product edit link

= 1.2.3 - Released on 6 June 2017 =

* Fix: integration with YITH WooCommerce Role Based Prices
* Fix: fatal error if the bundle item is hided
* Tweak: prevent fatal error in metabox
* Tweak: refactoring

= 1.2.2 - Released on 11 May 2017 =

* New: possibility to add shortcodes to bundled item descriptions
* Fix: slashes in bundled item titles and descriptions
* Fix: email issue in combination with YITH WooCommerce Request A Quote

= 1.2.1 - Released on 24 April 2017 =

* New: support to WooCommerce 3.0.4
* Fix: bundle product saving
* Fix: js error in frontend_add_to_cart.js
* Fix: 'Read more' text localization

= 1.2.0 - Released on 9 March 2017 =

* New: support to WooCommerce 2.7.0-RC1
* New: WPML Multi-currency support
* Fix: WPML integration issues
* Fix: hidden variable bundled item issue

= 1.1.7 - Released on 18 January 2017 =

* Fix: price sorting issue
* Fix: variable price issues
* Dev: added yith_wcpb_add_cart_item_data_check filter

= 1.1.6 - Released on 11 January 2017 =

* Fix: js price issue
* Fix: missing hook attribute

= 1.1.5 - Released on 10 January 2017 =

* New: out of stock synchronization
* New: choose how to view order pricing for "per item pricing" bundle products
* New: decimal discount percentage for bundled items
* Fix: hidden items in order table
* Fix: variable price issues
* Fix: js bundle form issues
* Fix: responsive cart table
* Tweak: updated language file

= 1.1.4 - Released on 15 December 2016 =

* New: integration with YITH WooCommerce Catalog Mode 1.4.8
* New: support to YITH WooCommerce PDF Invoice and Shipping List
* New: show the download links in the bundle if the bundled items are hidden in the order details
* Fix: issues with variable custom attributes
* Fix: issues with hidden bundled items
* Fix: show the variation prices if a variation is selected by default
* Fix: issues with YITH WooCommerce Role Based Prices
* Fix: hide bundled items in Cart and Checkout
* Dev: added jQuery trigger yith_wcpb_ajax_update_price_request
* Dev: added filter yith_wcpb_bundle_pip_bundled_items_subtotal
* Dev: added filter yith_wcpb_show_bundled_items_prices
* Dev: added filter yith_wcpb_ajax_update_price_enabled

= 1.1.3 - Released on 17 October 2016 =

* Fix: displayed price in cart in combination with YITH WooCommerce Dynamic Prices and Discounts

= 1.1.2 - Released on 12 October 2016 =

* Fix: integration to YITH WooCommerce Role Based Prices 1.0.9
* Fix: compatibility with YITH WooCommerce Dynamic Prices and Discounts 1.1.4
* Fix: issues with orders including virtual and downloadable items only, which did not automatically switch to completed

= 1.1.1 - Released on 30 September 2016 =

* New: integration with YITH WooCommerce Role Based Prices 1.0.9
* Fix: frontend issue in combination with themes that customize select fields
* Fix: issue during add to cart validation for stock quantity of bundled items

= 1.1.0 - Released on 28 September 2016 =

* New: integration with YITH WooCommerce Request a Quote 1.5.7
* New: possibility to show only bundles including the currently viewed product in widget
* Fix: issue during checkout
* Fix: display quantity input for optional variable bundled items
* Fix: issue in combination with YITH WooCommerce Role Based Prices
* Fix: display variation prices
* Tweak: improved frontend style

= 1.0.27 - Released on 29 August 2016 =

* New: compatibility with YITH WooCommerce Quick View 1.1.2

= Version 1.0.26 - Released: Aug 26, 2016=

* New: hidden link when a bundled item is an "hidden" product

= Version 1.0.25 - Released: Aug 09, 2016=

* Fix: "Add to cart" issue in combination with WooCommerce Multilingual

= Version 1.0.24 - Released: Aug 02, 2016=

* New: possibility to set the maximum and the minimum for the sum of the bundled item quantity in a bundle
* New: show price for variable products
* New: show price for optional bundled items
* New: change thumbnails when variation is selected
* Fix: displayed price when no variation is selected in variable products
* Tweak: improved frontend style
* Tweak: tab label "Bundle Options" changed in "Bundled Items"
* Tweak: created new tab "Bundle Options" for setting minimum and maximum quantity for bundled item quantity

= 1.0.23 - Released on 7 July 2016 =

* New: support to WooCommerce 2.6.2
* Fix: display price for bundle with optional and variable products
* Fix: general tab display issue
* Fix: issue depending on bundle item sorting

= 1.0.22 - Released on 23 June 2016 =

* Fix: product percentage discount coupon for bundles
* Fix: update price as soon as a bundle single page is loading

= 1.0.21 - Released on 3 June 2016 =

* Fix: WPML compatibility issue
* Fix: issue appearing in combination with YITH Woocommerce Role Based Price Premium

= 1.0.20 - Released on 10 May 2016 =

* Fix: price calculation
* Fix: issue appearing in combination with YITH Woocommerce Role Based Price Premium

= 1.0.19 - Released on 19 April 2016 =

* New: edit button for bundled items
* Fix: frontend add to cart JS (disable/enable add to cart button)
* Fix: memory error caused by ajax add to cart support for bundle products
* Fix: display price for bundled items when the option "show price without tax" is selected in WooCommerce settings page
* Fix: enqueue-style-and-script protocol

= 1.0.18 - Released on 30 March 2016 =

* Tweak: added ajax-add-to-cart support for bundle products

= 1.0.17 - Released on 17 March 2016 =

* Tweak: added possibility to override templates
* Tweak: fixed product search bug
* Tweak: fixed minor bugs

= 1.0.16 - Released on 11 March 2016 =

* New: possibility to hide bundled items in cart, mini-cart and checkout page

= 1.0.15 - Released on 4 March 2016 =

* Fix: hidden stock info label in bundled items that don't have stock management

= 1.0.14 - Released on 17 February 2016 =

* Tweak: fixed widget bug

= 1.0.13 - Released on 19 January 2016 =

* New: support to WooCommerce 2.5
* Tweak: fixed html price from-to

= 1.0.12 - Released on 15 January 2016 =

* New: automatic set virtual for bundle if it contains only virtual products
* New: support to WooCommerce 2.5 RC2
* Tweak: fixed cart item count

= 1.0.11 - Released on 30 December 2015 =

* New: WPML compatibility

= 1.0.10 - Released on 18 December 2015 =

* Tweak: fixed tax calculation for bundles with per-price-items options enabled
* Tweak: fixed items count in cart

= 1.0.9 - Released on 15 December 2015 =

* New: compatibility with WordPress 4.4
* New: compatibility with WooCommerce 2.4.12
* Tweak: added icon for bundle products in admin product list
* Tweak: fixed SKU bug in bundles with variable items

= 1.0.8 - Released on 10 December 2015 =

* New: shortcode to bundled items and add to cart button
* Tweak: fixed price calculation for bundle product with variables and discount

= 1.0.7 - Released on 1 December 2015 =

* Fix: minor bugs

= 1.0.6 - Released on 29 October 2015 =

* Fix: minor bugs

= 1.0.5 - Released on 25 August 2015 =

* Fix: minor bugs

= 1.0.4 - Released on 20 August 2015 =

* New: Support to WordPress 4.3
* Fix: minor bugs

= 1.0.3 - Released on 19 August 2015 =

* Fix: minor bug

= 1.0.2 - Released on 18 August 2015 =

* New: Support to WordPress 4.2.4
* New: Support to WooCommerce 2.4.4

= 1.0.1 - Released on 24 July 2015 =

* New: autoupdate price on Bundle single product page
* New: setting to hide/show bundled items in WC Reports

= 1.0.0 - Released on 21 July 2015 =

* Initial release