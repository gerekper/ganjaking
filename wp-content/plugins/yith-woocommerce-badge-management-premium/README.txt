=== YITH WooCommerce Badge Management Premium ===

== Changelog ==

= 1.4.2 - Released on 20 May 2020 =

* New: support for WooCommerce 4.2
* Update: plugin framework
* Update: language files
* Dev: added yith_wcbm_valid_product_to_apply_with_other_variations filter

= 1.4.1 - Released on 24 April 2020 =

* New: support to WooCommerce 4.1
* Update: plugin framework
* Update: language files
* Fix: integration with Flatsome
* Tweak: prevent issues when hiding sale badges

= 1.4.0 - Released on 24 March 2020 =

* Fix: admin language issue in product categories in combination with WPML
* Fix: category badge issues in combination with WPML
* Update: plugin framework
* Update: language files
* Tweak: prevent 'non well formed numeric value' errors

= 1.3.27 - Released on 27 February 2020 =

* New: support to WordPress 5.4
* New: support to WooCommerce 4.0
* New: support for YITH Proteo theme
* Fix: admin language issue in combination with WPML
* Update: plugin framework
* Update: language files
* Dev: added yith_wcbm_get_badge_filters filter

= 1.3.26 - Released on 20 December 2019 =

* New: support for WooCommerce 3.9
* New: added 'yith-wcbm-product-has-badges' CSS class to product with badges
* Update: plugin framework
* Update: language files
* Tweak: fixed issue if date column doesn't exist

= 1.3.25 - Released on 5 November 2019 =

* Update: plugin framework

= 1.3.24 - Released on 30 October 2019 =

* Update: plugin framework

= 1.3.23 - Released on 28 October 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: support for Twenty Twenty theme
* New: panel style
* Update: plugin framework
* Fix: force positioning badges when clicking on product tabs
* Dev: added yith_wcbm_number_cat_to_show filter
* Dev: added yith_wcbm_category_pagination_totals filter
* Dev: added yith_wcmb_wpml_autosync_product_badge_translations filter and function

= 1.3.22 - Released on 1 August 2019 =

* New: support to WooCommerce 3.7
* New: edit badges through Quick Edit
* Fix: hidden badges on YITH Frontend Manager products section
* Update: plugin framework
* Update: language files

= 1.3.21 - Released on 23 May 2019 =

* New: support to WordPress 5.2
* Update: plugin framework
* Tweak: added new CSS class for anchor point position

= 1.3.20 - Released on 9 April 2019 =

* New: support to WooCommerce 3.6
* New: support to YITH Booking Theme
* Update: language files
* Update: plugin framework

= 1.3.19 - Released on 31 January 2019 =

* New: Support to WooCommerce 3.5.4
* Fix: textdomain for some strings
* Fix: timezone issue with starting and ending date
* Fix: support to YITH WooCommerce Auctions
* Fix: support to YITH WooCommerce Dynamic Pricing and Discounts
* Update: plugin framework
* Update: language files
* Tweak: added CSS class to badges including the product ID
* Tweak: pagination for categories in settings
* Dev: added yith_wcbm_text_badge_text filter
* Dev: added yith_wcbm_css_badge_text filter

= 1.3.18 - Released on 5 December 2018 =

* New:  support to WordPress 5.0
* Update: plugin framework
* Update: language files
* Tweak: prevent issues with some themes that prints each variation directly in Shop page

= 1.3.17 - Released on 23 October 2018 =

* Fix: rotation for text badges
* Fix: hidden badges in mini-cart of Avada theme
* Update: plugin framework

= 1.3.16 - Released on 10 October 2018 =

* Fix: style issue with Advanced Badges

= 1.3.15 - Released on 9 October 2018 =

* New: support to WooCommerce 3.5.x
* New: possibility to scale badge in mobile
* New: 'Single Product Image' option in Force Positioning settings
* Fix: issue when hiding badges on Single Product pages
* Fix: hidden badges in YITH WooCommerce Added to Cart Popup
* Fix: round saved price
* Fix: hidden badges in Orders on AJAX call
* Tweak: prevent duplicate badges
* Tweak: prevent issues with some themes
* Update: Dutch language
* Dev: added yith_wcbm_print_container_image_and_badge filter

= 1.3.14 - Released on 26 June 2018 =

* Fix: support to YITH WooCommerce Dynamic Pricing and Discounts
* Fix: support to YITH WooCommerce Auctions
* Fix: issues if the yith_wcmb_get_badges_premium is used with the third deprecated param
* Tweak: prevent issues on iOS devices

= 1.3.13 - Released on 24 June 2018 =

* New: possibility to set multiple badge specific for each product
* New: option to force badge positioning through JS to prevent some theme issues
* Fix: style issue in combination with Flatsome theme

= 1.3.12 - Released on 31 May 2018 =

* New: support to WooCommerce 3.4.x
* New: support to WordPress 4.9.6
* Update: Italian language
* Update: Spanish language
* Update: plugin framework
* Fix: print only published badges
* Fix: badge column style in Product list
* Tweak: added version in styles

= 1.3.11 - Released on 28 February 2018 =

* Fix: issue in WooCommerce gallery
* Fix: on-sale badge issue with themes
* Dev: added yith_wcmb_is_wpml_parent_based_on_default_language function
* Dev: added yith_wcmb_is_wpml_parent_based_on_default_language filter

= 1.3.10 - Released on 1 February 2018 =

* Fix: issues in combination with Yoast SEO plugin
* Fix: enqueue styles and scripts only where needed
* Tweak: prevent warning in advanced badges

= 1.3.9 - Released on 31 January 2018 =

* New: support to WooCommerce 3.3
* New: Low stock badge
* New: enable shop manager to edit options
* New: Dutch language
* Update: Plugin Framework
* Fix: product metabox style
* Fix: rotation issue with advanced badges
* Dev: added yith_wcbm_product_is_on_sale_based_on_woocommerce filter

= 1.3.8 - Released on 30 December 2017 =

* Fix: removed yith-wcbm-clearfix from badge container by default
* Dev: added yith_wcbm_container_image_and_badge_extra_classes filter

= 1.3.7 - Released on 29 December 2017 =

* New: support to eStore theme
* New: support to Total theme
* New: support to Shopkeeper theme
* New: French translation (thanks to Pierre-Yves QUEMENER)
* Tweak: added yith-wcbm-clearfix class to container-image-and-badge
* Update: Plugin Framework 3
* Fix: WPML integration
* Fix: YITH WooCommerce Dynamic Pricing and Discounts integration
* Fix: enqueue styles and scripts in admin Badge pages only
* Fix: prevent deleting badge issue on WP bulk editing products
* Fix: theme integrations
* Fix: rotation issues
* Fix: removed http and https protocols in image url to prevent issues
* Dev: theme compatibility refactoring
* Dev: added yith_wcbm_clearfix_class filter

= 1.3.6 - Released on 11 October 2017 =

* New: support to Support to WooCommerce 3.2.0 RC2
* New: support to Electro theme
* New: possibility to set width and height as 'auto' in text badges
* Fix: badge dragging issue
* Fix: width, height and line height issues in text badges
* Fix: WPML integration

= 1.3.5 - Released on 10 August 2017 =

* Fix: flip text issue in Safari
* Fix: flip text issue in css badges
* Fix: changed hook priority to prevent issues

= 1.3.4 - Released on 29 June 2017 =

* New: 3D rotation
* New: Flip badge text horizontally and vertically
* Fix: discount calculation issue
* Fix: use woocommerce_single_product_image_thumbnail_html filter
* Fix: on-sale badge issue with variable products
* Fix: badge style issue
* Fix: badge preview class
* Dev: added yith_wcbm_image_badge_url filter
* Tweak: fixed plugin documentation link

= 1.3.3 - Released on 9 May 2017 =

* Fix: badge saving
* Fix: hidden badges in feeds
* Tweak: removed badges in wishlist table of YITH WooCommerce Wishlist
* Dev: added yith_wcbm_get_badge_premium filter

= 1.3.2 - Released on 11 April 2017 =

* New: support to WooCommerce 3.0.1
* Fix: post thumbnail issue

= 1.3.1 - Released on 5 April 2017 =

* New: support to WooCommerce 3.0.0
* Fix: badge saving
* Fix: issue in combination with YITH WooCommerce Catalog Mode
* Fix: hidden badges in YITH WooCommerce Request a Quote widget
* Fix: Basel theme compatibility issue
* Fix: percentage discount issue
* Fix: YITH WooCommerce Dynamic Pricing and Discounts integration issue

= 1.3.0 - Released on 6 March 2017 =

* New: support to WooCommerce 2.7.0-RC1
* New: shortcode yith_badge_container
* New: Basel theme support
* Tweak: improved theme compatibility
* Dev: added new class to easily add support to themes

= 1.2.30 - Released on 19 January 2017 =

* New: choose whether showing the discount amount or percentage in advanced badges
* Fix: integration with YITH WooCommerce Dynamic Pricing and Discounts
* Fix: issues in combination with YITH WooCommerce Quick View

= 1.2.29 - Released on 13 January 2017 =

* New: badge preview in admin badge list
* Tweak: prevent issue with themes that don't use the "woocommerce_single_product_image_html" and "post_thumbnail_html" filters without the second parameter

= 1.2.28 - Released on 2 January 2017 =

* Fix: advanced badge issues

= 1.2.27 - Released on 23 December 2016 =

* New: duplicate badges
* New: Flatsome support
* Fix: removed badge permalink
* Fix: old PHP version issues
* Fix: advanced badge styles

= 1.2.26 - Released on 7 November 2016 =

* New: support to YITH WooCommerce Role Based Prices
* Fix: localization of 2 advanced badges

= 1.2.25 - Released on 24 October 2016 =

* Fix: advanced badge issue
* Fix: automatic on-sale badge issue
* Dev: added filter yith_wcbm_show_featured_badge_on_product
* Dev: added filter yith_wcbm_show_on_sale_badge_on_product
* Dev: added filter yith_wcbm_show_out_of_stock_badge_on_product

= 1.2.24 - Released on 18 October 2016 =

* Fix: issue in combination with YITH Auctions for WooCommerce and YITH WooCommerce Dynamic Pricing and Discounts

= 1.2.23 - Released on 17 October 2016 =

* New: integration with YITH Auctions for WooCommerce 1.0.10
* New: support to YITH WooCommerce Dynamic Pricing and Discounts
* Fix: issue in combination with Storefront Sticky Add to Cart
* Fix: css badge class
* Dev: added yith_wcbm_advanced_badge_info filter

= 1.2.22 =

* New: show badges based on currently selected language using WPML
* New: Spanish language

= 1.2.21 =

* New: choose how to show advanced badges in variable products
* Fix: prevent issues if shop manager (or administrator) role doesn't exist
* Fix: advanced badge bug

= 1.2.20 =

* Fix: compatibility issue with YITH WooCommerce Dynamic Pricing and Discount Premium

= 1.2.19 =

* New: Italian language
* Fix: badge text and style

= 1.2.18 =

* Fix: badges in YITH WooCommerce Recently Viewed Products emails hidden because causing graphical issues
* Fix: badges in YITH WooCommerce Questions and Answers emails hidden because causing graphical issues

= 1.2.17 =

* New: support to WordPress 4.5.1
* Fix: badges in YITH WooCommerce Waiting List emails hidden because causing graphical issues

= 1.2.16 =

* Tweak: hidden badges in ajax search box
* Tweak: prevent badges from showing in WooCommerce emails
* Fix: badge css styles

= 1.2.15 =

* Tweak: localized "Discount" text
* Fix: advanced badge display price including/excluding tax

= 1.2.14 =

* New: possibility to set shipping class badges
* Fix: advanced badge styles

= 1.2.13 =

* Fix: advanced badge styles

= 1.2.12 =

* Tweak: fixed bug with badges on variable products

= 1.2.11 =

* Tweak: hidden wpml translated badge in select fields of Badge Management settings
* Tweak: fixed minor bug

= 1.2.10 =

* Tweak: hidden badges in YITH WooCommerce Frequently Bought Together form
* Tweak: hidden badges in YITH WooCommerce Save for Later section

= 1.2.9 =

* Tweak: fixed minor bugs

= 1.2.8 =

* New: possibility to set badges for price rules of YITH WooCommerce Dynamic Pricing and Discount Premium 1.0.3
* Tweak: fixed hidden badges in Single Product Page
* Tweak: fixed badge style W3C

= 1.2.7 =

* New: support to WordPress 4.4.1
* New: support to WooCommerce 2.5
* Tweak: hided badges in WooCommerce mini-cart
* Tweak: fixed minor bug

= 1.2.6 =

* New: support to WooCommerce 2.5 BETA 3
* Tweak: fixed css bug with YITH WooCommerce Frequently Bought Together
* Tweak: fixed minor bug

= 1.2.5 =

* New: support to WordPress 4.4
* New: support to WooCommerce 2.4.12
* New: possibility to show/hide badges in sidebars
* New: possibility to show/hide WooCommerce default "On Sale" badge when the product has another badge.
* New: advanced badge management for variable product (that have same discount percentage)

= 1.2.4 =

* New: Bulk Actions to add badge to products
* New: Badge column in product list
* Tweak: fixed option to hide badges in single product page

= 1.2.3 =

* New: automatic Out of Stock badges
* New: compatibility with YITH WooCommerce Multi Vendor version 1.7.4 or greater
* Tweak: fix minor bug about showing badges on products

= 1.2.2 =

* Tweak: now you can schedule badge showing in product edit page

= 1.2.1 =

* Fix: minor bugs

= 1.2.0 =

* New: WPML Compatibility to localize all elements of the badges

= 1.1.10 =

* Fix: minor bugs

= 1.1.9 =

* Fix: removed badges in cart and checkout
* Fix: minor bugs

= 1.1.8 =

* New: Support to WordPress 4.3
* Fix: WPML css badge text bug

= 1.1.7 =

* New: Support to WooCommerce 2.4.4

= 1.1.6 =

* New: Support to WordPress 4.2.4
* New: Support to WooCommerce 2.4.2
* Fix: WPML deprecated functions

= 1.1.5 =

* New: WPML Compatibility
* New: Compatibility with more themes
* Fix: Bug with widget sidebar on header

= 1.1.4 =

* New: Support to WooCommerce shortcodes
* Fix: Compatibility with more themes

= 1.1.3 =

* Fix: badge display in widget top related product

= 1.1.2 =

* Fix: Wrong admin css handle

= 1.1.1 =

* New: Support to WooCommce 2.3.9
* Update: Default language file
* Update: Plugin framework
* Fix: Can't make a badge issue
* Fix: Hide on sale option not working

= 1.1.0 =

* Initial release
