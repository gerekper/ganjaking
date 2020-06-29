=== WooCommerce Store Credit ===
Contributors: woocommerce, themesquad
Tags: woocommerce, credit, coupons
Requires at least: 4.7
Tested up to: 5.4
Stable tag: 3.2.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 3.4
WC tested up to: 4.2
Woo: 18609:c4bf3ecec4146cb69081e5b28b6cdac4

Create 'Store Credit' coupons for customers which are redeemable at checkout.

== Installation ==

1. Unzip and upload the plugin’s folder to your /wp-content/plugins/ directory.
2. Activate the extension through the ‘Plugins’ menu in WordPress.
3. Go to WooCommerce > Settings > General to configure the plugin.

== Documentation & support ==

Visit our [Product page](http://docs.woocommerce.com/document/woocommerce-store-credit/) to read the documentation and get support.

== Changelog ==

= 3.2.1 May 24, 2020 =
* Tweak - Added compatibility with WC 4.2.

= 3.2.0 May 12, 2020 =
* Feature - Sell Store credit coupons.
* Feature - Purchase Store Credit coupons and gift them to someone.

= 3.1.3 April 28, 2020 =
* Tweak - Added compatibility with WC 4.1.

= 3.1.2 March 24, 2020 =
* Tweak - Added compatibility with WC 4.0.
* Tweak - Tested compatibility with WP 5.4.
* Fix - Refresh the coupon list on the 'My Account' page when coupons are updated.
* Fix - Fixed wrong value for the parameter `$file` when calling the register activation hook.
* Fix - Fixed error 404 when visiting the 'Store Credit' endpoint on the 'My Account' page without flushing the rewrite rules.
* Dev - Added filter to tweak whether it's allowed to create coupons with tax included.

= 3.1.1 February 24, 2020 =
* Fix - Fixed fatal error when loading the customizer.

= 3.1.0 February 18, 2020 =
* Feature - Add a note to the customer when sending store credit.
* Feature - Filter coupons by customer or email in the coupon list.
* Tweak - Recovered the setting 'Delete after use'.
* Tweak - Keep the fields' values in the 'Send Store Credit' form on failure.
* Tweak - Added admin notice to enable the coupons.
* Tweak - Use the order's currency to display the Store Credit discount in the edit-order screen.

= 3.0.5 January 16, 2020 =
* Tweak - Added compatibility with WC 3.9.

= 3.0.4 November 5, 2019 =
* Tweak - Clear the shipping discounts before calculating them again.
* Tweak - Calculate the cart total using the partial cart totals.
* Fix - Fixed wrong discounts in the shipping costs when working with the extension 'WooCommerce AvaTax'.

= 3.0.3 October 31, 2019 =
* Tweak - Tested compatibility with WP 5.3.
* Tweak - Tested compatibility with WC 3.8.
* Fix - Fixed issue when applying a discount to a non-taxable shipping method.

= 3.0.2 October 10, 2019 =
* Tweak - Check that the coupon has been stored in the database before sending it to the customer.
* Tweak - Initialize coupon objects with the coupon code for adding compatibility with other extensions.
* Fix - The success message on the 'Send Credit' page was not translatable.

= 3.0.1 October 8, 2019 =
* Feature - Send credit to guest customers.
* Tweak - Updated the priority used to load the settings page.

= 3.0.0 September 24, 2019 =
* Feature - Create coupons which apply discounts to specific products or product categories.
* Feature - Define if the coupon amounts include tax or not.
* Feature - Define if the coupons also apply a discount to the shipping costs.
* Feature - Each coupon can be configured individually.
* Feature - Customize the coupon code format.
* Tweak - Removed customer email from the coupon code.
* Tweak - Always send to trash the exhausted coupons.
* Tweak - Updated notice message when applying an exhausted coupon in the cart.
* Tweak - Improved the email templates used to send credit to a customer.
* Tweak - Added endpoint to the 'My Account' page for displaying the customer's coupons.
* Tweak - Improved personal data exporter and eraser.
* Tweak - Removed unnecessary settings 'Coupon retention' and 'Delete after usage'.
* Tweak - Check the minimum requirements before initializing the plugin.
* Tweak - Added link to settings in the plugin action links.
* Tweak - Added link to the documentation on the plugins page.
* Tweak - Added compatibility with WC 3.7.
* Tweak - Tested compatibility with WP 5.2.
* Fix - Fixed the order balance when applying a coupon with tax included.
* Fix - Fixed report metrics for orders whose coupons include tax.
* Fix - Update the order balance after recovering an order from a 'cancelled', 'failed' or 'refunded' status.
* Fix - Fixed 'usage' counter after restoring a coupon.
* Fix - Fixed invalid decimal precision when storing the credit used for an order.
* Fix - Fixed issue when fetching a meta data for a `WC_Order_Refund` object.
* Fix - Fixed wrong discounts when applying a coupon with tax included in combination with the 'WooCommerce AvaTax' extension.
* Dev - Set the minimum requirements to WP 4.7 and WC 3.4.
* Dev - Removed deprecated code.

= 2.4.6 April 26, 2019 =
* Tweak - Display the tax label when necessary in the order item totals.
* Tweak - Moved the store credit row after the order subtotal in the order details when applying coupons before taxes.
* Tweak - Display the applied coupon code in the cart totals during checkout.
* Fix - Properly display the store credit value with or without taxes in the order item totals.
* Fix - Fixed invalid discount during checkout when applying a coupon before taxes and the items price includes taxes. Only for WC 3.4+.
* Fix - Fixed duplicate entry of the `_store_credit_used` meta when adding a coupon manually in the edit order screen.

= 2.4.5 April 15, 2019 =
* Fix - Fixed invalid discount when applying a coupon before taxes and the items price includes taxes.

= 2.4.4 April 4, 2019 =
* Tweak - Include the `store_credit` parameter in the orders data returned by the API requests.
* Tweak - Automatically delete store credit coupons with zero discount after recalculate order totals.
* Tweak - Added compatibility with WC 3.6.
* Fix - Fixed invalid PayPal request when applying an after-tax discount higher than the order subtotal.
* Dev - The method `WC_Abstract_Order->get_total_discount` now includes the store credit discount.

= 2.4.3 March 19, 2019 =
* Tweak - Synchronize the credit used by the orders in batches of 50 orders during the update process.

= 2.4.2 March 18, 2019 =
* Tweak - Remove older update notices on plugin activation.

= 2.4.1 March 15, 2019 =
* Fix - Fixed wrong discount when applying a 'Store Credit' coupon after taxes to a cart which contains subscription products.

= 2.4.0 March 11, 2019 =
* Feature - Apply 'Store Credit' coupons to an order in the admin screens.
* Feature - Apply multiple 'Store Credit' coupons to the same order.
* Feature - Set the payment method to 'Store Credit' in orders paid with a store credit coupon.
* Tweak - Only delete an exhausted coupon when all the orders where it was used are completed.
* Tweak - Restore the credit when the order is cancelled, refunded or fails.
* Tweak - Re-calculate the coupon discounts after updating the order items.
* Tweak - Restore the coupons' credit when necessary on updating an order.
* Fix - Fixed 'invalid coupon' error when cancelling the payment with PayPal.
* Fix - Fixed wrong discounts for coupons applied before taxes in WC versions between 3.2.2 and 3.3.5.

= 2.3.0 December 19, 2018 =
* Feature - Include the 'Store credit' used in the order totals.
* Feature - Display the 'Store credit' used in the invoices.
* Tweak - Exclude the 'Store credit' used from the 'Discount' order total.

= 2.2.0 October 30, 2018 =
* Feature - Rewritten the way the 'Store Credit' coupons are applied.
* Tweak - Save the used store credit on each purchase.
* Tweak - Define the constants if not already set.
* Fix - Fixed incorrect 'Store Credit' discounts when applied in combination with other coupons.
* Fix - PHP notice for undefined index.
* Fix - Remaining credit amount not correct when using taxes.
* Fix - Removed the use of the third parameter in the 'array_filter' function (Require PHP 5.6+).
* Dev - Added constant 'WC_STORE_CREDIT_VERSION'.

== Upgrade Notice ==

= 3.0 =
3.0 is a major update. It is important that you make backups and ensure you have installed WC 3.4+ before upgrading.
