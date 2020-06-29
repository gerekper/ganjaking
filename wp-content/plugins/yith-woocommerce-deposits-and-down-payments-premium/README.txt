=== YITH WooCommerce Deposits and Down Payments ===

Contributors: yithemes
Tags:  deposits, deposits and down payments, down payments, down payment, deposit, woocommerce deposits, woocommerce down payments, rate, amount, full payment, balance, backorder, sales, woocommerce, wp e-commerce
Requires at least: 4.0.0
Tested up to: 5.4
Stable tag: 1.3.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://yithemes.com/docs-plugins/yith-woocommerce-deposits-and-down-payments

== Changelog ==

= 1.3.7 - Released on 11 May 2020 =

* New: support for WooCommerce 4.1
* Update: plugin framework
* Tweak: added specific classes to order action buttons
* Tweak: wrapped product note inside div
* Tweak: added classes and attributes to make my deposits table responsive
* Fix: issue with calculate shipping form on variable products
* Fix: added check on "deposit expiring" email, to avoid possible fatal error when suborder no longer exists
* Fix: issue wit fee handling when printing out balance subtotal
* Fix: bundle price issue when deposit is not enabled on product in combination with YITH WooCommerce Product Bundle
* Dev: added trigger yith_wcdp_updated_deposit_form

= 1.3.6 - Released on 10 March 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: added shortcode yith_wcdp_desposit_value
* Tweak: improved order filtering
* Tweak: adds balance lines to order review
* Fix: PHP Fatal error: Uncaught Error: Call to a member function is_purchasable() on bool
* Dev: added yith_wcdp_before_suborders_create and yith_wcdp_after_suborders_create actions

= 1.3.5 – Released on 24 December 2019 =

* New: support for WooCommerce 3.9
* Update: plugin framework
* Update: Italian language
* Update: Spanish language
* Update: Dutch language
* Tweak: prevent plugin from removing custom orders views
* Fix: issue with tax displayed on order details/email
* Fix: prevent notice when filtering order items

= 1.3.4 - Released on 06 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* Tweak: method that prints additional notes now checks for deposit status with dedicated method
* Update: Plugin framework
* Update: Italian language
* Update: Spanish language
* Fix: prevent notice during cron execution
* Dev: added new action yith_wcdp_deposits_created_notification
* Dev: added new filter yith_wcdp_customer_deposit_created_email_enabled

= 1.3.3 - Released: Aug, 09 - 2019 =

* New: WooCommerce 3.7.0 RC2 support
* Tweak: cast deposit value to double to avoid "non numeric value" error
* Tweak: add fees on grand total on cart and checkout page
* Tweak: change balance order status to pending payment when deposit order status changed from failed status
* Tweak: add paragraph in deposit and balance payment for show inline block in all webmail clients
* Update: Italian language
* Update: internal plugin framework
* Fix: removed useless yith_wcpb_ajax_update_price_request trigger that used to break js execution on variable products
* Fix: get correct parent product id when a variation is selected
* Fix: expiration days are now added to today's date
* Fix: notice on thank you page
* Dev: added new filter yith_wcdp_show_total_html
* Dev: added new filter yith_wcdp_deposit_print_product_note
* Dev: added new filters for sending deposit expiration email

= 1.3.2 - Released: Jun, 18 - 2019 =

* New: added Product Bundle compatibility
* Fix: Multi Vendor plugin hide balance orders without commissions (vendor with commission rate 0%)
* Fix: product purchasable state can now be filtered by generic option too
* Tweak: removed balances from sales count on WC reports
* Tweak: preventing problems when suborder id doesn't exists
* Tweak: check if product exits
* Tweak: added currency on wc_price for deposit table and deposit list
* Updated: Dutch language

= 1.3.1 - Released: May, 20 - 2019 =

* New: WordPress 5.2 support
* Update: internal plugin framework
* Fix: notice undefined variable on is_deposit_enabled_for_product
* Fix: prevent notice Undefined variable $expiration_date
* Fix: avoid adding extra label to cart total when no deposit is in cart
* Fix: disable shipping on deposit order when option is checked
* Tweak: change isset for empty in order to prevent no print suborders on the email
* Tweak: preventing warning when checking deposits over a gift card product
* Tweak: improved compatibility with Gift Card
* Tweak: improved compatibility with Multi Vendor
* Dev: added yith_wcdp_has_action_after_shop_loop_item filter
* Dev: added yith_wcdp_single_deposit_price filter

= 1.3.0 - Released: Apr, 10 - 2019 =

* New: WooCommerce 3.6 support
* New: added options to make deposits expire on a specific date
* New: added option to choose whether deposit or balance should be virtual
* Tweak: improved frontend price handling - now js uses accounting library to format price
* Tweak: always retrieve a fresh product on is_deposit_enabled_on_product method, to avoid unexpected behaviour
* Tweak: improved deposit-list template for emails
* Update: internal plugin framework
* Update: Italian language
* Update: Spanish language
* Dev: added yith_wcdp_my_account_print_quick_deposit_action filter
* Dev: added second parameter to yith_wcdp_virtual_on_deposit filter (order)
* Dev: Added a new condition to modify the stock status

= 1.2.4 - Released: Feb, 04 - 2019 =

* New: WooCommerce 3.5.4 support
* Tweak: deposit can be 0 now
* Tweak: reviewed option panel
* Update: internal plugin framework
* Update: Dutch language
* Fix: double shipping being added to order total when balance is virtual
* Fix: Fatal Error on cart totals when Composite products are added to cart

= 1.2.3 - Released: Dec, 18 - 2018 =

* New: WordPress 5.0 support
* New: WooCommerce 3.5.2 support
* Update: plugin framework to latest revision
* Update: Dutch language
* Tweak: improved "Balances" column appearance on backend
* Tweak: added new parameter "$email" on "woocommerce_email_header" and "woocommerce_email_footer" actions
* Tweak: replaced help-tips images with appropriate function
* Fix: disabled stock check during support cart handling
* Fix: exclude held balance items from stock check
* Dev: added yith_wcdp_enqueue_frontend_script_template_js filter

= 1.2.2 - Released: Oct, 24 - 2018 =

* New: updated plugin framework
* Fix: plugin do not create suborders when only shipping is added to balance

= 1.2.1 - Released: Oct, 15 - 2018 =

* New: support to WooCommerce 3.5.x
* New: support to WordPress 4.9.8
* New: added suborder status synch when main order is cancelled
* New: deposit template is now loaded via ajax, if dedicated option is enabled
* Tweak: improved YITH WooCommerce Product Add-ons compatibility
* Tweak: improved totals, tax and coupon calculation, when deposit is applied to cart
* Update. Italian language
* Update: Spanish language
* Fix: payment complete status for orders that contains only deposits
* Fix: avoid WooCommerce increasing sales counter twice for a product purchased with deposit
* Dev: new filter yith_wcdp_deposist_value
* Dev: new filter yith_wcdp_product_price_for_deposit_operation
* Dev: new action yith_wcdp_after_add_deposit_to_cart
* Dev: new filter yith_wcdp_show_cart_total to filter cart total displayed value
* Dev: new filter yith_wcdp_show_cart_total_html to filter cart total html
* Dev: new filter yith_wcdp_is_deposit_mandatory  to let third party dedvelopers set deposit as mandatory on product level via code

= 1.2.0 - Released: Jan, 31 - 2018 =

* New: WooCommerce 3.3.0 support
* New: Updated plugin-fw
* New: added nl_NL translation
* New: integration with YITH WooCommerce Composite Products
* New: added label with order total including balance in the cart
* Tweak: do not hide "Add deposit to Cart" form in variable product when variation handling is not available
* Tweak: moved Balances email sending to order completed action
* Tweak: filter order status on My Account page only
* Tweak: fixed Sold Individually behaviour when Deposit gets added to cart
* Fix: preventing Fatal Error: Called method get_order_id on a non-object for WC < 3.0
* Fix: notice when sending deposit email
* Fix: issue with Added to Cart message not appearing
* Fix: added an additional check to avoid js errors on single product page
* Dev: added yith_wcdp_disable_deposit_variation_option filter in order to disable per-variation handling when not required, and drastically improve performance for variable products

= 1.1.2 - Released: Nov, 06 - 2017 =

* New: added WC 3.2.1 compatibility
* Tweak: recalculate totals after restoring original cart (avoid checkout skipping the payment)
* Tweak: added procedure to disable deposit when removing category rule
* Tweak: plugin now shows prices including taxes when required
* Tweak: added checks over product before adding it to temporary cart
* Fix: error when retrieving products to enable for category deposit rule
* Fix: customer can now pay balance orders even if products are out of stock (stock handling is processed during deposit)
* Dev: added yith_wcdp_is_deposit_enabled_on_product filter, to let third party plugin filter is_deposit_enabled_on_product() return value
* Dev: added yith_wcdp_skip_support_cart filter, to let third party plugin avoid support cart processing
* Dev: added yith_wcdp_suborder_add_cart_item_data filter, to let third party plugin add cart item data during cart processing for suborders creation

= 1.1.1 - Released: Apr, 21 - 2017 =

* Tweak: update plugin-fw
* Tweak: optimized meta saving
* Tweak: avoid double "Deposit" or "Full Payment" label before order item name
* Fix: problem with duplicated meta
* Fix: variation rate when category rate is set
* Fix: problem with product's select on Deposit tab
* Dev: added yith_wcdp_disable_email_notification filter, to let disable balance email notifications

= 1.1.0 - Released: Apr, 03 - 2017 =

* New: WordPress 4.7.3 compatibility
* New: WooCommerce 3.0-RC2 compatibility
* New: option to change Deposit label on the frontend
* New: compatibility with YITH Dynamic Pricing and Discounts
* New: compatibility with YITH Event Tickets for WooCommerce
* New: Compatibility with YITH WooCommerce Product Addon
* New: Compatibility with YITH Pre Order for WooCommerce
* New: "Reset Data" handling for variation form on single product page
* New: deposit ID on "New Order" email
* New: improved wpml config to let admin correctly localize plugin labels
* Tweak: new text-domain
* Tweak: fixed downloads not appearing for "partially-paid" orders
* Tweak: fixed plugin when product has more then 30 variations
* Tweak: added check for product on deposit table, to avoid possible fatal errors when removing products from the store
* Tweak: added check over product when filtering get_product_from_item
* Tweak: added balance total to "Suborder" column in order page
* Fix: js error that was repeating #yith-wcdp-add-deposit-to-cart at each found_variation
* Fix: preventing warning on setting panel, when no shipping method is set
* Fix: possible notice due to undefined global $post
* Fix: possible notice when global $post is not an object
* Fix: WooCommerce decreasing stock both on Deposit and Balance orders
* Fix: problem with get_cart_from_session when using YITH Stripe and YITH Subscription
* Fix: js handling for "Shipping Calculator" on variable products
* Fix: Wrong deposits amount in admin email
* Fix: heading string for "My Deposits" section
* Dev: added yith_wcdp_not_downloadable_on_deposit filter to make deposit downloadable, when needed
* Dev: fixed yith_wcdp_deposit_value and yith_wcdp_deposit_balance filters (now they send variation_id and product_id as additional parameters to filter)

= 1.0.4 - Released: Oct, 10 - 2016 =

* Added: compatibility with variable products
* Added: filter yith_wcdp_skip_cart_item_processing to let dev skip add deposit to cart programmatically
* Added: YITH_WCDP_PROCESS_SUBORDERS constant to avoid suborder with deposit when deposit is forced
* Added: compatibility for shipping zones
* Added: compatibility with YITH WooCommerce PDF Invoice premium
* Added: compatibility with YITH WooCommerce Booking
* Added: option to choose whether deposit should be checked as default or not
* Added: get_deposit method, to get deposit value for a specific product/variation/user/price
* Tweak: changed plugin text domain to yith-woocommerce-deposits-and-down-payments
* Tweak: made plugin work with [product_page] woocommerce shortcode

= 1.0.3 - Released: Jun, 13 - 2016 =

* Added: WooCommerce 2.6-RC1 compatibility
* Added: yith_wcdp_deposit_label filter to change deposit label
* Added: yith_wcdp_full_payment_label filter to change full amount label
* Added: yith_wcdp_process_deposit to let third party plugin to prevent plugin from processing deposits for some products
* Added: yith_wcdp_propagate_coupons to let coupons be applied to suborders
* Added: yith_wcdp_virtual_on_deposit to let third party plugin make deposits product not virtual
* Added: function yith_wcdp_get_order_subtotal

= 1.0.2 - Released: May, 02 - 2016 =

* Added: support for WordPress 4.5.1
* Added: support for WooCommerce 2.5.5
* Added: capability for the user to regenerate shipping methods basing on shipping address in single product page
* Added: compatibility with YITH WooCommerce Bulk Product Editing premium
* Added: Quick / Bulk deposit options edit for products
* Added: handling for custom product type
* Added: global option for "Create Suborders"
* Tweak: Passed product variable to templates, avoiding global variable usage
* Tweak: added qty calculation on "Full Amount" / "Down payment"
* Fixed: email templates for WooCommerce 2.5
* Fixed: plugin changing internal pointer of item array in backend order page
* Fixed: YITH Plugins view id (preventing assets to load on admin plugin settings page)

= 1.0.1 - Released: Dec, 01 - 2015 =

* Initial release