=== YITH WooCommerce Bulk Product Editing ===

== Changelog ==

= 1.2.24 - Released on 03 Jul 2020 =

* New: support for WooCommerce 4.3
* Update: plugin framework
* Update: language files

= 1.2.23 - Released on 18 May 2020 =

* New: support for WooCommerce 4.2
* New: possibility to edit 'Low stock threshold' field
* Update: plugin framework
* Update: language files

= 1.2.22 - Released on 27 April 2020 =

* New: support for WooCommerce 4.1
* Update: language files
* Update: plugin framework

= 1.2.21 - Released on 28 February 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: option to store hidden columns per user
* Update: language files
* Tweak: improved style

= 1.2.20 - Released on 20 December 2019 =

* New: support for WooCommerce 3.9
* Update: language files
* Update: plugin framework
* Fix: issue when loading downloadable files in plugin table
* Tweak: improved style

= 1.2.19 - Released on 6 November 2019 =

* Update: plugin framework

= 1.2.18 - Released on 30 October 2019 =

* Update: plugin framework

= 1.2.17 - Released on 29 October 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: panel style
* New: added 'Variation' in Product Type filter, so it's possible to directly filter product variations
* New: possibility to enable/disable variations by setting their status to publish/private
* Update: plugin framework
* Update: language files
* Tweak: improved performances
* Tweak: by default only the first 3 attributes will be enabled in Enabled Columns
* Fix: issue when setting 'Sale price to' option
* Dev: added yith_wcbep_hide_empty_categories filter


= 1.2.16 - Released on 5 August 2019 =

* New: sort products by SKU
* New: filter by 'Allow Backorders'
* New: support to WooCommerce 3.7
* Update: plugin framework
* Update: language files
* Fix: label for 'Stock quantity' field in Filters
* Fix: bulk edit tabs freeze on hover
* Tweak: prevent issue when product is not set
* Tweak: fixed issue when selecting products on filters
* Dev: added yith_wcbep_allow_editing_custom_fields_in_variations filter
* Dev: added yith_wcbep_save_custom_field_{} filter


= 1.2.15 - Released on 29 May 2019 =

* Fix: product type editing
* Fix: issue when adding categories
* Update: plugin framework


= 1.2.14 - Released on 9 April 2019 =

* New: support to WooCommerce 3.6
* New: filter for catalog visibility
* Update: language files
* Update: plugin framework
* Fix: allow negative numbers for menu_order when bulk editing
* Tweak: exporting product by using the WooCommerce Exporter in CSV
* Removed - importing behavior since you can import products by using the WooCommerce Import feature


= 1.2.13 - Released on 6 February 2019 =

* New: support to WooCommerce 3.5.4
* New: 'Use light query' option for stores with large amount of products
* Fix: editing description and short description
* Fix: issue when editing Stock Qty and Stock Status at the same time
* Fix: notice for accessing product properties directly
* Update: plugin framework
* Update: language files


= 1.2.12 - Released on 30 November 2018 =

* New: support to WordPress 5 RC1
* New: filter Title, Description and SKU through Regular Expressions
* Update: plugin framework
* Update: language files


= 1.2.11 - Released on 23 October 2018 =

* Update: Plugin Framework


= 1.2.10 - Released on 10 October 2018 =

* New: support to WooCommerce 3.5.x
* New: filter by description
* New: possibility to enable/disable REGEX on search for texts
* New: possibility to delete 'description', 'short description' and 'purchase note' fields
* New: possibility to edit description on variations
* Fix: support to YITH WooCommerce Badge Management 1.3.14
* Fix: issues when selecting on filters
* Fix: filtering by price for variable products
* Fix: filtering by price
* Fix: method POST for AJAX call
* Fix: Custom Taxonomy filtering issue
* Fix: method POST for AJAX call
* Fix: catalog visibility editing
* Fix: stock statuses for WooCommerce 3.x
* Tweak: improved performances on editing
* Tweak: improved style
* Update: Plugin Framework
* Dev: added yith_wcbep_before_bulk_edit_product action
* Dev: added yith_wcbep_after_bulk_edit_product action
* Dev: added yith_wcbep_show_main_product filter
* Dev: added yith_wcbep_attributes_to_hide_in_filters filter


= 1.2.9 - Released on 29 May 2018 =

* New: support to WooCommerce 3.4
* New: support to WordPress 4.9.6
* New: sorting by menu order
* New: possibility to round prices on bulk editing
* New: filter by Shipping class
* Update: Spanish language
* Update: plugin framework
* Fix: variation names
* Fix: translation issues
* Fix: clone issue with PHP 7
* Fix: save_post action params
* Tweak: fixed doc url

= 1.2.8 - Released on 31 January 2018 =

* New: support to WooCommerce 3.3
* Update: Plugin Framework
* Fix: price filter issue with decimals

= 1.2.7 - Released on 9 January 2018 =

* New: filter by Status
* Update: Plugin Framework 3
* Fix: negative stock quantity issue in variation products
* Fix: WPML language issue
* Fix: prevent issue when clicking on Bulk Editor buttons
* Fix: issue when filtering by price
* Fix: issue when editing menu order with WooCommerce 2.6

= 1.2.6 - Released on 11 October 2017 =

* New: support to Support to WooCommerce 3.2.0 RC2
* Fix: sale price scheduling with WooCommerce 3.x
* Fix: table pagination issue
* Fix: attribute field issue when the field is empty
* Tweak: prevent issue due by wrong html code in description, short description and purchase note

= 1.2.5 - Released on 13 September 2017 =

* New: filter by Stock Status
* New: filter by Stock Quantity
* Fix: issue when saving changes in sorted table
* Fix: issue when saving 'is visible' and 'used for variations' attribute options
* Fix: issue when removing image
* Fix: bulk editor z-index to prevent issue with WordPress Media Library

= 1.2.4 - Released on 28 August 2017 =

* New: bulk edit 'is visible' attribute option
* New: bulk edit 'used for variations' attribute option
* New: sort by stock quantity
* New: bulk edit 'Menu Order'
* New: added links to display products in the table
* New: Dutch language
* Tweak: use REGEX to search for title in DB
* Fix: WPML integration
* Fix: datepicker z-index
* Fix: category issue

= 1.2.3 - Released on 5 June 2017 =

* New: product type filter
* Fix: Product Type saving
* Fix: Image Gallery saving
* Fix: up-sells and cross-sells delete
* Fix: bulk editor z-index issue
* Fix: hidden Vendor fields in the bulk editor for vendor users

= 1.2.2 - Released on 14 April 2017 =

* New: support to WooCommerce 3.0.3
* Fix: select2 width
* Fix: attribute issue with WooCommerce 3.0.x
* Fix: "Sale Price From" and "Sale Price To" date issues

= 1.2.1 - Released on 10 April 2017 =

* New: support to WooCommerce 3.0.1
* New: edit custom taxonomies
* New: possibility to hide/show filter section
* Fix: visibility issue when creating new products
* Fix: image editor open issue
* Tweak: improved Bulk Editor style
* Tweak: added X button to close bulk editor
* Tweak: improved filter box style

= 1.2.0 - Released on 28 February 2017 =

* New: support to WooCommerce 2.7.0-beta-4
* Update: replaced chosen with select2
* Fix: style issue
* Fix: bulk editor button issues

= 1.1.26 - Released on 5 January 2017 =

* Fix: downloadable file issue
* Fix: chosen issue in bulk edit filters
* Fix: style issue in Firefox
* Fix:  issue when decrease price (and other numbers) with bulk editor

= 1.1.25 =

* Fix: issue in combination with YITH WooCommerce Brands Add-on

= 1.1.24 =

* Fix: integration with YITH WooCommerce Brands Add-on

= 1.1.23 =

* New: integration with YITH WooCommerce Multi Vendor

= 1.1.22 =

* Fix: issue with variation attributes

= 1.1.21 =

* New: Spanish language

= 1.1.20 =

* New: bulk edit "Stock status" field

= 1.1.19 =

* New: filter products by brand (in combination with YITH WooCommerce Brands Add-on Premium)

= 1.1.18 =

* New: bulk edit product images

= 1.1.17 =

* New: bulk edit "Date" field
* Fix: sorting and pagination issues when applying attribute and category filters

= 1.1.16 =

* New: possibility to bulk edit "Sale Price From" and "Sale Price To" dates
* Fix: saving tags issue when "Hierarchical Management for Products Tags" of YITH WooCommerce Ajax Product Filters is enabled
* Fix: search bug with many attributes
* Fix: documentation link

= 1.1.15 =

* New: custom field bulk editing
* New: compatibility to YITH WooCommerce Deposits and Down Payments Premium
* Fix: compatibility issue on YITH WooCommerce Badge Management Premium (bulk removing badges)

= 1.1.14 =

* Fix: bug during exporting
* Tweak: fixed minor bugs

= 1.1.13 =

* New: Shop Manager can now use Bulk Product Editing panel
* Fix: Bulk Product Editing table bugs
* Tweak: fixed minor bugs

= 1.1.12 =

* Fix: js script bug on Bulk Product Editing page

= 1.1.11 =

* Fix: edit date bug
* Fix: new product bug

= 1.1.10 =

* Tweak: fixed edit attribute

= 1.1.9 =

* Tweak: fixed edit attribute
* Tweak: fixed minor bugs

= 1.1.8 =

* New: possibility to enable/disable table columns to improve performance
* Tweak: fixed minor bug

= 1.1.7 =

* New: possibility to increase sale price by value or percentage from regular price

= 1.1.6 =

* New: sorting by weight, width, height and length
* Tweak: fixed minor bugs

= 1.1.5 =

* New: possibility to filter products by weight

= 1.1.4 =

* New: compatibility with WooCommerce 2.5
* Improved: functionality to show variations in Bulk Product Editing table
* Tweak: fixed minor bug

= 1.1.3 =

* New: compatibility with WooCommerce 2.5 RC2
* New: support to YITH WooCommerce Brands Add-on Premium
* Tweak: fixed minor bug

= 1.1.2 =

* New: compatibility with WooCommerce 2.5 BETA 3
* New: support to YITH WooCommerce Badge Management Premium
* Tweak: fixed minor bug

= 1.1.1 =

* New: compatibility with WordPress 4.4
* New: compatibility with WooCommerce 2.4.12
* New: WPML compatibility
* Tweak: fixed bug in table pagination

= 1.1.0 =

* Tweak: improved performance
* Tweak: improved search
* Tweak: improved table sorting
* Tweak: decreased response time for table creation

= 1.0.10 =

* Tweak: improved preformance

= 1.0.9 =

* Fix: variation display filtering by attributes

= 1.0.8 =

* Fix: variation visibility by category filter
* Fix: price bug after product saving

= 1.0.7 =

* Fix: product image saving

= 1.0.6 =

* Fix: minor bug

= 1.0.5 =

* Fix: stock quantity and manage stock save
* Fix: saving options for stock quantity and manage stock

= 1.0.4 =

* Tweak: performance improved for bulk product editing

= 1.0.3 =

* Fix: minor bug

= 1.0.2 =

* Initial release

== Suggestions ==

If you have suggestions about how to improve YITH WooCommerce Bulk Product Editing, you can [write us](mailto:plugins@yithemes.com "Your Inspiration Themes") so we can enhance our YITH WooCommerce Bulk Product Editing plugin.

== Translators ==

= Available Languages =
* English (Default)

If you have created your own language pack, or have an update for an existing one, you can send [gettext PO and MO file](http://codex.wordpress.org/Translating_WordPress "Translating WordPress")
[use](http://yithemes.com/contact "Your Inspiration Themes") so we can bundle it into YITH WooCommerce Bulk Product Editing

== Upgrade notice ==

= 1.0.0 =

Initial release