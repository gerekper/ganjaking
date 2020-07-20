=== YITH Point of Sale for WooCommerce ===

== Changelog ==

= 1.0.5 - Released on 03 Jul 2020 =

* New: support for WooCommerce 4.3
* Update: plugin framework
* Update: language files
* Fix: scrolling issues in category view
* Fix: tax rounding issue
* Fix: issues when some payment methods have empty amount when paying through POS
* Fix: scan product by SKU when there are restrictions for categories in Register
* Tweak: prevent notices when retrieving orders through REST API
* Tweak: added login messages and errors
* Tweak: improved search speed
* Tweak: set the default value for 'tax status' field to 'Enabled' when creating a new product in POS
* Tweak: improved search by SKU when scanning a product
* Tweak: fixed style issue in placeholders of select fields in Registers
* Tweak: limit the 'Popular Tendered' suggestions to 6 to prevent style issues
* Dev: added yith_pos_order_processed_after_showing_details action
* Dev: added yith_pos_default_selected_payment_gateway filter
* Dev: added yith_pos_coupon_custom_discount_amount filter
* Dev: added yith_pos_coupon_custom_discounts_array filter
* Dev: added yith_pos_is_product_coupon filter
* Dev: added yith_pos_is_cart_coupon filter
* Dev: added yith_pos_coupon_is_valid_for_product filter
* Dev: added yith_pos_coupon_is_valid_for_cart filter
* Dev: added yith_pos_cart_item_product_name filter
* Dev: added yith_pos_show_stock_badge_in_search_results filter
* Dev: added yith_pos_receipt_order_item_name_quantity filter
* Dev: added yith_pos_header_menu_items filter
* Dev: added yith_pos_receipt_order_item_price filter
* Dev: added yith_pos_product_list_query_args filter
* Dev: added yith_pos_product_section_tabs filter
* Dev: added yith_pos_search_include_variations filter
* Dev: added yith_pos_search_include_searching_by_sku filter
* Dev: added yith_pos_scan_product_tab_active_default filter
* Dev: added yith_pos_new_product_default_data filter
* Dev: added yith_pos_customer_to_update filter
* Dev: added yith_pos_customer_use_email_as_username filter
* Dev: added yith_pos_customer_to_create filter
* Dev: added yith_pos_cart_item_product_price filter in react

= 1.0.4 - Released on 14 May 2020 =

* New: support for WooCommerce 4.2
* New: restock items automatically after refunds
* Update: plugin framework
* Update: language files
* Fix: issue when adding cash-in-end and closing the register
* Fix: issue when editing customer
* Dev: added yith_pos_product_get_meta filter in React
* Dev: added yith_pos_show_price_including_tax_in_receipt filter
* Dev: added yith_pos_show_tax_row_in_receipt filter

= 1.0.3 - Released on 22 April 2020 =

* New: support for WooCommerce 4.1
* New: French translation (thanks to Josselyn Jayant)
* New: Greek translation
* Fix: show dates in correct language
* Fix: empty search field after scanning a product
* Fix: issue when changing order status for orders including custom products
* Fix: issue when reducing the stock of products without multi-stock options set
* Fix: search results width and height in small screens
* Fix: RTL style
* Fix: undefined variable error in store wizard summary
* Fix: issue when activating the plugin in the network
* Tweak: improved popular tendered behavior
* Tweak: prevent register-closing call failure by waiting for closing before redirect
* Dev: added yith_pos_show_itemized_tax_in_receipt filter

= 1.0.2 - Released on 3 March 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: show prices including/excluding taxes based on WooCommerce settings
* New: italian translation
* New: spanish translation
* New: dutch translation
* Update: plugin framework
* Fix: language option in combination with WPML
* Fix: multi-stock option in Product Edit page
* Fix: show correct currency in 'cash in hand' window
* Fix: show iOS body class for iOS devices only
* Fix: multi-stock issue with variable products
* Tweak: improved search
* Tweak: remove 'Cashier' and 'Manager' roles automatically whenever users are removed from Cashiers or Managers from the store settings.

= 1.0.1 - Released on 13 February 2020 =

* New: order status set to 'Processing' if the order includes shipping lines, otherwise it'll be set to 'Completed'
* Fix: password issue when creating a customer
* Fix: issue with admin capabilities
* Tweak: improved category exclusion in registers
* Tweak: improved barcode behaviour after scanning the product
* Tweak: filter by YITH POS or online shown as select
* Tweak: added a default receipt when installing the plugin for the first time
* Tweak: prevent errors if using an outdated version of WooCommerce Admin
* Tweak: added control to check if the browser is supported
* Tweak: improved style
* Tweak: removed mandatory option for pos gateway payments on WooCommerce Settings Payment
* Tweak: play sound when changing product quantity
* Dev: added yith_pos_order_status filter

= 1.0.0 - Released on 05 February 2020 =

* Initial release
