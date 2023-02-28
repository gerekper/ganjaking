=== Polylang for WooCommerce ===
Contributors: Chouby, manooweb, sebastienserre, greglone
Tags: multilingual, woocommerce
Requires at least: 5.7
Tested up to: 6.1
Requires PHP: 7.0
Stable tag: 1.7.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

This plugin must be used in combination with WooCommerce and Polylang. It allows you to translate the WooCommerce pages, simple, variable and grouped products, categories, tags, attributes directly in the WooCommerce interface. Common data such as the stock and prices are automatically synchronized across products translations. And of course, emails are sent to customers in their language.

Some popular extensions such as WooCommerce Subscriptions, Dynamic Pricing, Table Rate shipping, WooCommerce Bookings have been integrated too. More will come in the future.

This extension can be used either with the free version of Polylang or in combination with Polylang Pro.

== Changelog ==

= 1.7.2 (2023-02-21) =

* Fix a fatal error when sending emails and the order has no language.
* Fix a warning with WC Brands.
* Fix image saved in wrong language in WC Brands.

= 1.7.1 (2023-01-10) =

* Fix import of terms with shared slugs broken by WP 6.0.
* Fix block filter by product attributes broken by WC 6.4.
* Fix fatal error when duplicating variable products and some variations have been deleted with Polylang deactivated.

= 1.7 (2022-08-23) =

* Requires WordPress 5.7 or higher, WC 5.1 or higher, Polylang 3.0 or higher.
* Allow to translate cart when using multiple domains in recent browsers. Requires WC 6.7.
* Filter the product search block results by language.
* Add compatibility with Mix & Match Products 2.0. Props @helgatheviking
* Fix multiple emails sent to emails added through the filter 'woocommerce_email_recipient_new_order'
* Fix a deprecated notice with PHP 8.1
* Fix WooCommerce pages translations not created when using the WooCommerce wizard.
* Fix query for reserved stock
* Fix REST API for products with Polylang Pro 3.2+
* Fix deprecated notice in WooCommerce Subscriptions.
* Fix a fatal error with WooCommerce Variation Swatches and Photos.
* Fix WC Brands compatibility not loaded on frontend.
* Fix a conflict with Germanized for WooCommerce

= 1.6.3 (2022-01-31) =

* Fix error when manually sending the invoice email from backend.
* Fix a conflict with WooCommerce Germanized sending the new order email twice.

= 1.6.2 (2021-12-15) =

* Fix a conflict which could prevent emails from 3rd party plugins to be sent

= 1.6.1 (2021-11-23) =

* Fix a PHP notice with variations when objet cache is active

= 1.6 (2021-11-02) =

* Requires PHP 7.0 or higher, WordPress 5.4 or higher, WC 4.3 or higher, Polylang 2.8 or higher
* Improve robustness and documentation of code
* Remove jQuery usage when resetting the cart
* Improve performance when switching the language on front
* Add compatibility with WooCommerce Brands
* Send emails to admin in the user's language when available
* Allow to translate emails from name and address
* Add filters "pllwc_user_email_actions" and "pllwc_order_email_actions" to ease the translation of custom emails
* Add the filter "pllwc_enable_cart_translation" to allow to disable the cart translation
* Fix add to cart page id when the language is set from the content
* Fix the translation of several blocks modified in WC 5.6
* Fix a conflict with the ACF Google map field

= 1.5.7 (2021-09-14) =

* Fix wizard may be launched when it has already been skipped
* Fix a PHP notice with WooCommerce Ajax Products filter by BeRocket
* Fix Javascript error when the ACF Google map filed is added

= 1.5.6 (2021-07-06) =

* WordPress 5.8: fix a fatal error with the legacy widget preview in the new widget block editor

= 1.5.5 (2021-05-31) =

* Use a text area for email additionnal content translations
* Fix filter "In stock" for bulk translated products
* WC Bookings: fix product translations not added to existing bookings
* WC Bookings: fix relationship between product and booking duplicated in database when saving a product

= 1.5.4 (2021-04-20) =

* Fix javascript error: wpAjax is not defined
* Fix strings translations not loaded in emails sent by admin when the language is not switched

= 1.5.3 (2021-03-03) =

* Add compatibility with Polylang 3.0
* Fix attributes not selected after the product language has been changed
* Fix remaining deprecated jQuery functions
* Fix PHP notice when importing products without language in the CSV file

= 1.5.2 (2021-01-26) =

* WooCommerce Bookings: fix persons not copied
* WooCommerce Composite products: fix fatal error in cart with unstranslated product

= 1.5.1 (2020-11-30) =

* Add compatibility with Polylang 2.9
* Fix order received url with some payment gateways
* Fix compatibility with WooCommerce Checkout Field Editor

= 1.5 (2020-09-07) =

* Use composer for autoload
* Add debug information to Site Health
* Add compatibility with stock handling method to prevent race conditions when orders come in at the same time (requires WC 4.5).
* WooCommerce Product Bundles: fix cart translation

= 1.4.4 (2020-07-23) =

* Fix payment link in customer invoice email

= 1.4.3 (2020-06-29) =

* Add compatibility with Polylang Pro 2.7.4 (REST API for product categories and product tags)
* Fix compatibility with WooCommerce Checkout Field Editor 1.3.6+
* Fix a fatal error when WooCommerce Subscriptions deactivates itself
* Fix a potential warning with WooCommerce Bookings

= 1.4.2 (2020-05-25) =

* Fix variable product removed from the cart when a second browser tab displays the backend
* Fix variable product removed from the cart when a REST request occurs on front
* Fix shop pages set as child page
* Fix PHP warning when the option `woocommerce_bacs_accounts` serialization is corrupted
* Fix PHP notice when a user resetting his password has a user locale which doesn't exist in Polylang

= 1.4.1 (2020-04-21) =

* Fix fatal error on order-received page when no order id is provided.
* Fix the translations status report visible when no languages are defined.
* Fix PHP notice in wizard.
* Subscriptions: fix translated slugs of endpoints not working.
* Subscriptions: fix a conflict with WooCommerce Memberships causing a PHP notice.

= 1.4 (2020-04-06 ) =

* Requires WooCommerce 3.4 or higher, Polylang 2.7 or higher
* Don't allow users without manage_product_terms permissions to create categories using the product importer
* Add a WooCommerce step to the wizard
* Add compatibility with WooCommerce Free Gift Coupons
* Fix new account email sent in wrong language when the language is set from the content
* Fix variation name in "Pay for order" form

= 1.3.3 (2020-03-05) =

* Fix wrong subject & heading language for emails sent in bulk
* Fix wrong translation url when the shop is not translated
* WC Subscriptions: Fix translated variations not deleted when deleting a subscription variation.
* WC Composite Products: Fix fatal error when creating a new translation when the plugin WC Product Search is activated
* WC Composite Products: Fix PHP warnings

= 1.3.2 (2020-02-13) =

* Fix paged shop pages redirected to main shop page (introduced in 1.3.1)

= 1.3.1 (2020-02-11) =

* Fix fatal error when the product_cat taxonomy is unregistered or in case of DB error
* Fix language switcher not working for order-pay endpoint
* Fix canonical redirect for the shop page
* Fix variations copy when several product variation are using the same attribute

= 1.3 (2020-01-20) =

* Requires PHP 5.6 or higher, WordPress 4.9 or higher, WooCommerce 3.2 or higher, Polylang 2.6 or higher
* Change text domain from 'pllwc' to 'polylang-wc'
* Add compatibility with WooCommerce 3.9
* Add compatibility with WooCommerce Composite Products
* Add compatibility with Checkout Field Editor for WooCommerce
* Add compatibility with "All Reviews" block introduced in WC 3.8. Requires Polylang Pro 2.6.9+
* Add compatibility with "All Products" block introduced in WC 3.9
* Duplicate media if they don't exist when translating a product gallery
* Fix jQuery undefined if a user decides to load jQuery in the footer
* Fix duplicated sku error when creating a product translation with the REST API
* Fix "On sale products" block. Requires Polylang Pro 2.6.9+

= 1.2.5 (2019-12-26) =

* WC Bookings: Translate pending confirmation email
* WC Bookings: Fix notice when adding a bookable product to the cart
* Fix order emails not sent in the order language when processed from the order preview.

= 1.2.4 (2019-11-14) =

* Add compatibility with Polylang 2.6.7

= 1.2.3 (2019-11-05) =

* Add compatibility with WC 3.8, with the exception of 5 blocks: Best Selling Products, Newest products, On sale products, Top rated products, All rewiews. See https://polylang.pro/blocks-in-woocommerce-3-8/
* Translate customer payment page link in admin edit order screen
* Fix strings in wrong language when the language is set from the content
* Fix PHP notice in REST requests
* Fix product categories displayed in wrong language in product catalog with object cache
* Fix Stock status is wrongly copied when variations are set to "out of stock"

= 1.2.2 (2019-08-07) =

* Add compatibility with WooCommerce 3.7
* Fix user locale not honored in the WooCommerce status page
* Fix error 404 for single product pages (for language set from the content + shop base with category)
* Dynamic pricing: fix pricing rules messed when synchronizing more than one

= 1.2.1 (2019-05-06) =

* Fix hierarchy of product categories in WC 3.6
* Fix WC pages created with shared slug when they must not
* Fix fatal error when false is passed to the action `woocommerce_product_object_updated_props`

= 1.2 (2019-04-17) =

* Add compatibility with WooCommerce 3.6 and Polylang 2.6
* Improve stock synchronization performance (requires WC 3.6)
* Allow to translate the account name and bank name in BACS payment gateway
* Fix tax rate label not translated
* Fix individually added variations not correctly synchronized
* Fix variations not correctly updated when changing an attribute term slug
* Fix update of the products language and translations via the WP REST API
* Subscriptions: fix limit to one of any status not working

= 1.1.3 (2019-03-20) =

* Improve performance when synchronizing product variations
* Fix fatal error when reading a corrupted product variation
* Fix fatal error with WC Bookings 1.13+

= 1.1.2 (2019-02-21) =

* Fix product (ACF) custom fields not synchronized

= 1.1.1 (2019-01-07) =

* Fix stock and stock status not correctly copied when creating a new product translation

= 1.1 ( 2018-12-17) =

* Add compatibility with WooCommerce Product Bundles
* Add compatibility with Mix and Match Products
* Add compatibility with WooCommerce Variation Swatches and Photos
* Add compatibility with WooCommerce Min/Max Quantities
* Allow to use the WC REST API to add languages to products and orders instead of the WP REST API
* Fix incorrect urls in language switcher for order-received endpoint
* Fix: don't synchronize term order as it doesn't work with ajax term ordering
* Fix an edge case where no variations appear when translating a variable product

= 1.0.4 (2018-10-15) =

* Add compatibility with WooCommmerce 3.5 and Polylang 2.4
* Fix PHP notice introduced by WooCommerce Subscriptions 2.3
* Fix translation of endpoint in WooCommerce Subscriptions 2.3+
* Fix admin order actions buttons sending emails in the wrong language
* Fix variations not copied when creating a synchronized product
* Fix wrong payment link in the customer invoice email
* Fix language not set in reset password email when the language is set from the content

= 1.0.3 (2018-08-22) =

* Fix wpml-config.xml not honored when synchronizing variations
* Fix wrong theme locale and language attributes when sending emails from admin
* Fix report sales by category
* Fix hierarchy lost when importing product cats sharing the same name
* Fix a fatal error with PDF Invoices Packing Slips

= 1.0.2 (2018-07-02) =

* WC Bookings: Fix bookings associated to new bookable products
* WC Bookings: Fix email not sent in the right language
* WC Bookings: Fix booking details in the wrong language after saving it from admin

= 1.0.1 (2018-06-11) =

* WC 3.4: Fix fatal error with variable products not translated in the default language

= 1.0 (2018-05-24)=

* Minimum versions are WP 4.7, WooCommerce 3.0 and Polylang 2.3
* Major refactor for products and orders management (language, translations, synchronization)
* Refactor email management
* Add compatibility with WooCommerce 3.4
* Translate product and payment method in account when the order has been placed in a different language
* Defer the translation of gateways strings ( for compatibility with WooCommerce Invoice Gateway )
* Allow to use the Account page as static front page
* Fix WooCommerce translations loaded sooner than usual
* Fix language of term attributes created in the product metabox
* Fix linking 2 existing variable products messing the variations
* Fix product search not filtered by language in admin (introduced by WC 3.1)
* Fix product ordering not synchronized when the 'Page order' synchronization is activated
* Fix cancelled email possibly not sent in the order language (since WC 3.1)
* Fix default product category in some edge cases
* Fix coupon restrictions with untranslated products
* Fix filter price widget when using multiple domains
* Subscriptions: fix deprecation notice (props David De Boer)
* Follow-Up emails: Translate categories in addtional rule when copying or synchronizing an email

= 0.9.5 (2018-04-12) =

* Fix default product category not created when adding a language
* Fix subpages of shop page being 404 when using the shop base in products permalinks
* WC Bookings: fix reminder email sent in the wrong language
* WC Bookings: fix some bookable product metas not synchronized

= 0.9.4 (2018-01-29) =

* Fix: Ajax request loop when multiple tabs in different languages are open

= 0.9.3 (2018-01-17) =

* WC 3.3: add support for the default product category
* WC 3.3: fix urls in widget layered nav
* WC 3.3: fix enpoints in nav menu metabox
* PLL 2.3: don't use deprecated methods
* PLL 2.3: fix wrong taxonomy term assigned when duplicating products
* Fix: don't accept special characters in edit-address translated slugs
* WC Bookings: allow to translate products with existing bookings
* Stripe gateway: fix description not translated

= 0.9.2 (2017-12-07) =

* Fix WooCommerce pages translations added to the menu in the default language (when using auto add pages to menu)
* Fix subscriptions limit for translated subscription product
* Fix coupon not working with the free version of Polylang

= 0.9.1 (2017-10-23) =

* WC 3.2: Fix minicart not translated correctly.

= 0.9 (2017-10-10) =

* Add support for the REST API V2 (needs Polylang Pro 2.2.1 or later)
* Add compatibility with WooCommerce Follow-Up Emails (version tested: 4.5.0)
* Add compatibility with Yith WooCommerce Ajax Search (version tested: 1.5.3)
* Add compatibility with WooCommerce Bulk Stock Management (version tested: 2.2.9)
* Fix compatibility with WooCommerce Stock Manager (version tested: 1.1.6)
* Hide gateways properties in strings translations if not modified in WC settings
* Add post state to translations of the shop page, cart page, etc... as introduced by WooCommerce 3.2
* Remove synchronization of 'total_sales' product meta
* Fix import by SKU (needs WooCommerce 3.2)
* Fix import of grouped products
* Fix additional attribute term created when importing new attributes
* WC 3.2: Fix coupons not correctly validated when set from admin
* Fix translated taxonomies list table always filtered by the default language
* Fix variations loosing their attributes when a translation is trashed
* Fix variations removed when deleting a translated variable product from trash
* Fix attributes not assigned to variations when translating a variable product
* Fix incompatibility with WP 4.8.2 (placeholder %1$s in prepare)
* Fix wrong link in reset password email when there is no locale user meta

= 0.8.1 ( 2017-08-10 ) =

* Fix variations attributes not being saved when the custom fields synchronization is activated
* Fix variations featured image that cannot be modified when the featured image synchronization is activated

= 0.8 ( 2017-06-28 ) =

* Add support for CSV export and import introduced in WooCommerce 3.1
* Stop synchronizing external product urls (unless products are synchronized in Polylang Pro).
* Fix language not switching in persistent cart in WC 3.1
* Fix emails language when bulk sending mails (needs WooCommerce 3.1)
* Fix PHP warning when no language have been created yet
* Fix possible fatal error when duplicating a product
* Fix slug not translated in endpoints urls in admin menu metabox
* Fix language set from the content incorrect for products
* Fix enabled variations not synchronized

= 0.7.5 ( 2017-06-08 ) =

* Allow a different currency position per language
* Set PLL()->curlang when the language is switched in emails
* Fix endpoints metabox in WC 3.0+

= 0.7.4 ( 2017-05-10 ) =

* Fix: Impossible to order product categories
* Fix shortcodes cache introduced in WC 3.0 (needs WC 3.0.2+)
* Fix fatal error when duplicating products (introduced by WC 3.0.4)
* WC Bookings: Fix partially booked products get fully booked in other languages
* WC Bookings: Fix possible fatal error when switching the language
* WC Bookings: Fix booking removed from cart when switching the language
* WC Bookings: Fix translation of bookings endpoint

= 0.7.3 ( 2017-04-20 ) =

* Fix variable product stock status wrongly set to 'outofstock' (since WC 3.0)
* WC Dynamic pricing: Fix deleted pricing group not synchronized
* WC Bookings: Fix wrong booking language if the user switches the language after he added the product to cart
* WC Bookings: fix possibly untranslated _booking_persons meta

= 0.7.2 ( 2017-03-30 ) =

* Translate Apple Pay Button in WooCommerce Stripe Gateway
* Fix coupons when excluding an untranslated product
* WC Bookings: Fix conflict with Yoast SEO

= 0.7.1 ( 2017-03-22 ) =

* Fix page no found if the product slug or an endpoint slug is wrongly translated to an empty string
* Fix language metabox for orders without language (orders created before the plugin activation)
* Fix product category widget when shown as dropdown and the languages are set from subdomains or multiple domains
* Fix recently viewed products reset when switching the language (multiple domains)
* Fix: Honor wpml-config.xml custom fields section for variations
* Fix variations synchronization when an attribute includes an accent in German
* Fix incorrect default text domain in emails
* WC Bookings: Fix availability not updated when booking a product without resource

= 0.7 (2017-03-07) =

* WC2.7: Synchronize the new 'product_visibility' taxonomy
* WC2.7: Fix title of translated variations
* WC2.7: Fix SKU of translated variations
* WC2.7: Fix fatal error when duplicating a product (WooCommerce function removed)
* WC2.7: Fix synchronization of grouped products
* WC2.7: Fix quick edit for variable products
* WC2.7: Fix various deprecation notices
* Synchronize term metas based on a whitelist controlled by the filter 'pllwc_copy_term_metas'
* Fix endpoints slugs not translated in emails

= 0.6 (2017-02-16) =

* Add Basic support for WooCommerce shipment tracking (emails translations)
* Add support for WooCommerce Bookings
* Suppress 'woocommerce_add_cart_item_data' filter when translating the cart (conflicts with WooCommerce Bookings)
* Fix: use user locale instead of main site locale as default language filter in WP 4.7
* Fix product category filter when editing coupons in WP 4.7
* Fix empty attribute values in variations not correctly synchronized
* Fix terms sharing slugs across languages not duplicated (for WC < 2.7, fixed by Woocommerce in WC 2.7+)
* Fix variations default form values not synchronized in Ajax
* Fix fatal error with plain permalinks and WooCommerce deactivated

= 0.5 (2017-01-03) =

* Add support for WooCommerce Table Rate Shipping (Labels translation)
* Add support for WooCommerce Dynamic Pricing
* Improve compatibility with WooCommerce Price Based on Country (props Maikel van der Zande)
* Variations now correctly synchronized from the product data metabox (no need to click on Publish/Update button).
* Copy purchase note and variation description when duplicate content is active (with Polylang Pro)
* Remove constant PLLWC_URL
* Fix rewrite rules order modified when translating endpoints slugs
* Fix page for posts showing shop when the language is set from the content and the shop is on front
* Fix the widget layered nav when the shop is on front
* Fix widget layered nav returning empty results when choosing an attribute having a shared slug
* Fix widget price filter when using subdomains or multiple domains
* Fix translated endpoint url when using plain permalinks
* Fix product grouping not synchronized
* Fix site title and date format not translated in emails sent from admin
* Fix Subscriptions customer completed order not translated
* Fix home urls when using plain permalinks and the shop is on front
* Fix delete link not working in mini cart when the language is set from the content and the language has been switched

= 0.4.6 (2016-12-19) =

* Fix emails sent from admin not correctly translated in WP 4.7
* Fix attribute label not translated in emails sent from admin
* Fix translation of WooCommerce subscriptions emails sent from admin
* Fix the widget layered nav when the language is set from the content

= 0.4.5 (2016-11-29) =

* Fix sale price schedule not synchronized across translations for variations

= 0.4.4 (2016-11-23) =

* Adapt user language to WP 4.7
* Add translation for emails added in WooCommerce Subscriptions 2.1
* Fix customer renewal invoice email sent in default language
* Fix email not translated when order status changes from on hold to processing
* Fix account and checkout links not translated in emails
* Fix variations attribute not populated when creating a variable product translation in WP 4.7
* Fix PHP notices when the product language is changed

= 0.4.3 (2016-11-08) =

* Add nocache constants support to translated WooCommerce pages
* Fix stock notifications sent one time per language

= 0.4.2 (2016-10-25) =

* Include translated custom fields in filter 'pllwc_exclude_copy_post_metas'
* Fix: Avoid deleting translated variations when deleting a variable product
* Fix: Hide Shipping classes taxonomy from Polylang settings (to avoid a conflict if checked).

= 0.4.1 (2016-10-17) =

* Disable the notice about untranslated shipping classes for WC < 2.6 to avoid an ugly fatal error
* Fix string not sanitized

= 0.4 (2016-10-10) =

* Remove languages and translations for the shipping classes
* Add support for WooCommerce subscriptions
* Fix attributes list not updated when changing the product language in languages metabox
* Fix shipping class not copied / synchronized in variations

= 0.3.6 (2016-09-27) =

* Fix "Shop" link in breadcrumb (needs Polylang 2.0.5+)
* Fix product base not translated when including %product_cat%
* Fix coupons not applicable to translated products

= 0.3.5 (2016-09-19) =

* Fix: wrong in stock status when variations stocks are managed at parent product level
* Fix: adding a translated product to cart doesn't update the quantity but creates a new product line in cart (thanks to Stanislas Khromov for the initial fix)
* Fix cart and various messages not translated when the language is set from the content
* Fix duplication of variable products creating the double of variations

= 0.3.4 (2016-09-05) =

* Add compatibility with .mo files loading management modified in Polylang 2.0.4
* Fix variation attributes not translated in cart after the language has been switched

= 0.3.3 (2016-08-15) =

* Fix issue with Flatsome theme which bypasses the filter 'woocommerce_get_myaccount_page_id'

= 0.3.2 (2016-08-09) =

* Add compatibility with WooCommerce Stock Manager
* Fix shipping not translated when switching the language on the cart page in WC 2.6
* Fix error 404 on pages when the shop is displayed on front (introduced in 0.3.1)

= 0.3.1 (2016-08-01) =

* Fix error 404 when using orderby on a shop displayed on front
* Fix fatal error with PHP < 5.4

= 0.3 (2016-07-25) =

* Synchronize the cart on subdomains and multiple domains (need Polylang Pro 2.0)
* Fix database error that would be introduced by Polylang Pro 2.0 beta 2
* Fix shipping method translation in WC 2.6
* Fix default global attributes for variations not being copied / synchronized
* Fix orders in other languages not displayed in My account > My orders in WC 2.6

= 0.2.1 (2016-07-20) =

* Fix variations being duplicated when saved
* Fix attributes and variations not being copied / synchronized (introduced in 0.2)
* Fix nav menu not correctly displayed on front page when the front page displays the shop page

= 0.2 (2016-07-14) =

* Synchronization of custom fields are now based on a white list
* Remove filter 'pllwc_exclude_copy_post_metas' now useless and add filter 'pllwc_copy_post_metas'
* The media list when adding an image to a product category is now filtered by language
* Make endpoints menu items compatible with new endpoints added in WC 2.6
* Get back the old shipping classes UI to display languages and translations in WC 2.6 (the new UI doesn't allow this yet).
* Fix shipping methods titles not appearing in Strings translations in WC 2.6
* Fix database error in WC 2.6
* Fix error in case a 3rd party plugin uses a closure for a filter/action

= 0.1.2 (2016-06-15) =

* Synchronize custom attributes (they were just copied in previous versions)
* Synchronize variations based on custom attributes
* Fix variations not being copied if already associated to an unsaved product

= 0.1.1 (2016-06-06) =

* Min Polylang version is 1.9.2
* Fix issues with product variations

= 0.1 (2016-05-25) =

* Initial release
