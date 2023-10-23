=== YITH WooCommerce Badge Management Premium ===

== Changelog ==

= 2.21.0 - Released on 14 September 2023 =

* New: support for WooCommerce 8.1
* Update: YITH plugin framework
* Fix: prevent fatal error on function "yith_wcbm_get_badge_object"
* Fix: issue on rule for backorder products
* Fix: integration with YITH Dynamic v4.0
* Dev: added check to did_filter function

= 2.20.0 - Released on 18 August 2023 =

* New: support for WordPress 6.3
* New: support for WooCommerce 8.0
* New: support to WooCommerce blockified templates
* Update: YITH plugin framework

= 2.19.0 - Released on 11 July 2023 =

* New: support for WooCommerce 7.9
* New: support declared for WooCommerce HPOS feature
* Update: YITH plugin framework
* Update: language files

= 2.18.0 - Released on 9 June 2023 =

* New: support for WooCommerce 7.8
* Update: YITH plugin framework
* Update: language files
* Fix: badge rules option saving with the membership integration
* Fix: force integer value for quantity in rule
* Tweak: added transient management to improve bestsellers rules performance
* Tweak: best sellers badge rule performance improvements

= 2.17.0 - Released on 15 May 2023 =

* New: support for WooCommerce 7.7
* New: support for WooCommerce HPOS feature
* Update: YITH plugin framework
* Update: language files

= 2.16.0 - Released on 18 April 2023 =

* New: support for WooCommerce 7.6
* New: support for PHP 8.1
* Update: YITH plugin framework
* Update: language files

= 2.15.0 - Released on 20 March 2023 =

* New: support for WordPress 6.2
* New: support for WooCommerce 7.5
* Update: YITH plugin framework
* Update: language files
* Fix: added selectWoo as dependency for badges rules page scripts

= 2.14.0 - Released on 16 February 2023 =

* New: support for WooCommerce 7.4
* New: compatibility with Fana theme
* Update: YITH plugin framework
* Update: language files
* Tweak: take product_id if exists 

= 2.13.0 - Released on 16 January 2023 =

* New: support for WooCommerce 7.3
* New: badge products rule condition for bestsellers
* New: badge placeholders and placeholders list in modal
* Update: YITH plugin framework
* Update: language files

= 2.12.0 - Released on 15 December 2022 =

* New: support for WooCommerce 7.2
* Update: YITH plugin framework
* Update: language files

= 2.11.0 - Released on 21 November 2022 =

* New: support for WordPress 6.1
* New: support for WooCommerce 7.1
* Update: YITH plugin framework
* Update: language files 

= 2.10.0 - Released on 17 October 2022 =

* New: support for WooCommerce 7.0
* New: badge placeholders for css and text types
* Update: YITH plugin framework
* Update: language files

= 2.9.0 - Released on 20 September 2022 =

* New: support for WooCommerce 6.9
* Update: YITH plugin framework
* Update: language files
* Dev: new filter 'yith_wcbm_product_transient_expiration'

= 2.8.0 - Released on 16 August 2022 =

* New: support for WooCommerce 6.8
* Update: YITH plugin framework
* Update: language files
* Fix: badge style not applying in the last badge created
* Fix: not show badge from rules in variations if the parent product is in the exclusion list

= 2.7.0 - Released on 18 July 2022 =

* New: support for WooCommerce 6.7
* Update: YITH plugin framework
* Update: language files
* Tweak: improved plugin performances using transients

= 2.6.0 - Released on 22 June 2022 =

* New: support for WooCommerce 6.6
* Update: YITH plugin framework
* Update: language files

= 2.5.0 - Released on 17 May 2022 =

* New: support for WordPress 6.0
* New: support for WooCommerce 6.5
* Update: YITH plugin framework
* Update: language files
* Fix: badges hidden on my account page
* Fix: Fatal error "Call to a member function is_type() on string"
* Dev: new yith_wcbm_allow_badges_in_my_account_page filter to allow badge showing on my account page

= 2.4.0 - Released on 11 April 2022 =

* New: support for WooCommerce 6.4
* Update: YITH plugin framework
* Update: language files
* Fix: issue with badge rule when scheduling it if the language was different from English

= 2.3.0 - Released on 9 March 2022 =

* New: support for WooCommerce 6.3
* Update: YITH plugin framework
* Update: language files
* Fix: empty CSS properties in inline CSS code
* Fix: issue with creating badge rules associations table when switching from free to premium version
* Fix: issue with variation bulk editing of variable products
* Tweak: added help tab
* Tweak: product quick edit display and allow to remove the product badges
* Tweak: show badge on "recent products" according to product publishing date
* Dev: added 'yith_wcbm_product_badge_metabox' filter to customize Badge Options product metabox

= 2.2.0 - Released on 31 January 2022 =

* New: support for WordPress 5.9
* New: support for WooCommerce 6.2
* Update: YITH plugin framework
* Update: language files
* Fix: avoiding issues when wp_kses is called too early
* Fix: badges over the WooCommerce cart widget
* Tweak: update badge transient even when products are updated by external plugins
* Dev: added yith_wcbm_get_product_badges filter to manipulate the badges retrieved for the products
* Dev: added yith_wcbm_is_allowed_adding_badge_tags_in_wp_kses filter to choose when adding badge tags to wp_kses allowed HTML

= 2.1.0 - Released on 17 January 2022 =

* New: support for WooCommerce 6.1
* Update: YITH plugin framework
* Update: language files
* Fix: issue with showing and sanitizing badges with some elementor widgets
* Tweak: added a small style snippet to improve the compatibility with the blocksy theme
* Tweak: changed the fields position to exclude product in Badge Rules to improve the UX
* Dev: new yith_wcbm_add_badge_tags_in_wp_kses_allowed_html filter to avoid tags sanitization using wp_kses
* Dev: new yith_wcbm_get_badge_allowed_html filter to edit the allowed html in badges array
* Dev: new yith_wcbm_badge_rule_is_valid_for_product filter to handle the rule validation in a custom way

= 2.0.4 - Released on 29 December 2021 =

* Fix: checks on badge rules with recent and low-stock product conditions

= 2.0.3 - Released on 28 December 2021 =

* Fix: improved checks on badge rules for "recent products"
* Tweak: edit badge HTML content in CSS and text badge types using the text Editor tab

= 2.0.2 - Released on 24 December 2021 =

* Fix: print variation badges only when needed
* Fix: issue with max key length for badge rule associations table
* Fix: update product badge options before using them
* Tweak: performance improvements using transient while retrieving product badges
* Dev: new yith_wcbm_is_allowed_variation_badge_showing filter to handle the visibility of variation badges

= 2.0.1 - Released on 23 December 2021 =

* Fix: check unique titles just for badge and badge rules
* Fix: class property accessibility issue

= 2.0.0 - Released on 22 December 2021 =

* New: Badge editing panel style, with new UI/UX
* New: badge text-editor to enter text in badge text and css type
* New: badge margin option
* New: badge positioning fields and behaviours
* New: badge selector for image, css and advanced type
* New: ability to import badges from a YITH server
* New: badge rules to add in a massive way the badges to the products by product features, categories, tags and shipping-classes
* New: YITH Dynamic Pricing and Discount integration
* New: YITH Auction integration
* New: badge options on variation of variable products
* New: support for WooCommerce 6.0
* Tweak: badge settings in product editing page style improved
* Tweak: General Settings style and UX improved
* Update: YITH plugin framework
* Update: language files
* Dev: new object classes to handle badge and badge premium
* Dev: new object classes to handle badge rules (one class for each type of badge rule)
* Dev: new yith_wcbm_show_product_variation_badges_in_badge_column filter to hide/show badges assigned to the variation of a variable product in product list badge column
* Dev: new yith_wcbm_badge_text_editor_font_size_min filter to choose the minimum font size used in the badge editing text editor
* Dev: new yith_wcbm_badge_text_editor_font_size_max filter to choose the maximum font size used in the badge editing text editor
* Dev: new yith_wcbm_translate_badge_strings filter to choose if translating the strings inside the badges
* Dev: new yith_wcbm_badge_classes to edit badge classes array

= 1.7.0 - Released on 07 November 2021 =

* New: support for WooCommerce 5.9
* Update: YITH plugin framework
* Fix: warning on ngettext function "A non-numeric value encountered in /wp-includes/pomo/plural-forms.php on line 280"

= 1.6.0 - Released on 07 October 2021 =

* New: support for WooCommerce 5.8
* Update: YITH plugin framework
* Update: language files
* Fix: get Dynamic Pricing Rule name when it comes to old rules
* Dev: new filter yith_wcbm_valid_with_other_variations to check if even the variations have a Dynamic Rules applied
* Dev: new filter yith_wcbm_template_path_advanced_sale_badges to change the path where retrieving advanced sale badges
* Dev: new filter yith_wcbm_discount_text_advanced_badge to manipulate the "Discount" text in advanced badges

= 1.5.1 - Released on 27 September 2021 =

* Update: YITH plugin framework
* Update: language files
* Fix: debug info feature removed for all logged in users

= 1.5.0 - Released on 13 September 2021 =

* New: support for WooCommerce 5.7
* Update: YITH plugin framework
* Update: language files

= 1.4.15 - Released on 6 August 2021 =

* New: support for WooCommerce 5.6
* Update: YITH plugin framework
* Update: language files

= 1.4.14 - Released on 29 June 2021 =

* New: support for WordPress 5.8
* New: support for WooCommerce 5.5
* Update: YITH plugin framework
* Update: language files
* Fix: prevent wrong checking for checkboxes on quick edit function
* Dev: added yith_wcbm_category_name filter for the category names in Category Badges option page

= 1.4.13 - Released on 31 May 2021 =

* New: support for WooCommerce 5.4
* New: support for YITH Easy Order Page for WooCommerce
* Update: YITH plugin framework
* Update: language files
* Dev: added yith_wcbm_sanitize_badge_text filter
* Dev: added yith_wcbm_sanitize_badge_css_text filter

= 1.4.12 - Released on 5 May 2021 =

* New: support for WooCommerce 5.3
* Update: YITH plugin framework
* Update: language files

= 1.4.11 - Released on 7 April 2021 =

* New: support for WooCommerce 5.2
* Update: YITH plugin framework
* Update: language files
* Fix: support for YITH WooCommerce Dynamic Pricing and Discounts Premium
* Tweak: prevent 'non-numeric value encountered' notice in 'category badges' panel tab

= 1.4.10 - Released on 1 March 2021 =

* New: support for WordPress 5.7
* New: support for WooCommerce 5.1
* New: German translation
* New: German (Formal) translation
* Update: YITH plugin framework
* Update: language files

= 1.4.9 - Released on 27 January 2021 =

* New: support for WooCommerce 5.0
* Update: YITH plugin framework
* Update: language files
* Fix: badge settings layout on mobile
* Dev: added yith_wcbm_advanced_badge_product_price filter
* Dev: added yith_wcbm_advanced_badge_variation_price filter
* Dev: added yith_wcbm_advanced_badge_product_regular_price filter
* Dev: added yith_wcbm_advanced_badge_variation_regular_price filter
* Dev: added yith_wcbm_image_badge_alt_text filter

= 1.4.8 - Released on 28 Dec 2020 =

* New: support for WooCommerce 4.9
* Update: plugin framework
* Update: language files
* Fix: support for YITH WooCommerce Dynamic Pricing and Discounts

= 1.4.7 - Released on 02 Dec 2020 =

* New: support for WordPress 5.6
* New: support for WooCommerce 4.8
* Update: plugin framework
* Update: language files
* Fix: support for YITH WooCommerce Dynamic Pricing and Discounts
* Fix: badge positioning issue in combination with Proteo theme

= 1.4.6 - Released on 04 Nov 2020 =

* New: support for WooCommerce 4.7
* Fix: support for YITH WooCommerce Dynamic Pricing and Discounts
* Update: plugin framework
* Update: language files

= 1.4.5 - Released on 05 Oct 2020 =

* New: support for WooCommerce 4.6
* Update: plugin framework
* Update: language files
* Dev: added yith_wcbm_product_thumbnail_allowed_html filter

= 1.4.4 - Released on 17 Sep 2020 =

* New: support for WooCommerce 4.5
* Fix: support for YITH WooCommerce Dynamic Pricing and Discounts
* Update: plugin framework
* Update: language files

= 1.4.3 - Released on 03 Jul 2020 =

* New: support for WooCommerce 4.3
* Update: plugin framework
* Update: language files

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
* Dev: added yith_wcbm_wpml_autosync_product_badge_translations filter and function

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
* Fix: issues if the yith_wcbm_get_badges_premium is used with the third deprecated param
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
* Dev: added yith_wcbm_is_wpml_parent_based_on_default_language function
* Dev: added yith_wcbm_is_wpml_parent_based_on_default_language filter

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
