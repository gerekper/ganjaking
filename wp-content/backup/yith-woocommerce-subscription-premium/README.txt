=== YITH WooCommerce Subscription  ===

Tags: checkout page, recurring billing, subscription billing, subscription box, Subscription Management, subscriptions, paypal subscriptions
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 1.7.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==
It allows you to manage recurring payments for product subscription that grant you constant periodical income


== Installation ==
Important: First of all, you have to download and activate WooCommerce plugin, which is mandatory for YITH WooCommerce Subscription to be working.

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce Subscription` from Plugins page.


= Configuration =
YITH WooCommerce Subscription will add a new tab called "Subscription" in "YIT Plugins" menu item.
There, you will find all YITH plugins with quick access to plugin setting page.

== Changelog ==

= 1.7.8 - Released on 26 May 2020 =
* New: Support for WooCommerce 4.2
* New: Added French translation
* Update: Plugin framework
* Fix: Fixed update shipping address issue

= 1.7.7 - Released on 4 May 2020 =
* New: Support for WooCommerce 4.1
* Tweak: Added payment method title to renewal orders
* Update: Plugin framework
* Fix: Flush rewrite rule fix
* Dev: added new filter ywsbs_calculate_order_totals_condition to fix a problem in a customer site with EU VAT


= 1.7.6 - Released on 9 March, 2020 =
* New: Support for WordPress 5.4
* New: Support for WooCommerce 4.0
* New: Added "Enable Shop Manager" option
* Update: Plugin framework
* Fix: Prevent fatal error if the subscription order is null
* Dev: Added new action 'ywsbs_before_subscription_deleted'
* Dev: Added 'ywsbs_skipped_price_html_filter' filter

= 1.7.5 - Released on 19 February, 2020 =

* Update: Spanish language
* Fix: Fix undefined index tnx_type
* Fix: Add full log for renew order processing
* Fix: Address fields fix
* Fix: Process_payment function for WC Stripe compatibility
* Fix: Check on cart_item['data']
* Dev: Added filter ywsbs_free_trial_label to change the trial text

= 1.7.4 - Released on 24 December, 2019 =
* New: Support for WooCommerce 3.9
* Update: Plugin framework
* Fix: Fixed responsive subscription table on my account
* Dev: Added new filters 'ywsbs_force_multiple_subscriptions' and 'ywsbs_subscription_subtotal_html'

= 1.7.3 - Released on 26 November, 2019 =
* Update: Plugin framework

= 1.7.2 - Released on 30 October, 2019 =
* Update: Plugin framework

= 1.7.1 - Released on 23 October, 2019 =
* New: Support for WordPress 5.3
* New: Support for WooCommerce 3.8
* Update: Italian language
* Update: Plugin framework
* Fix: Load payment gateways before call pay renew order action
* Fix: Capability issues

= 1.7.0 - Released on 03 October, 2019 =
* Tweak: Improved the debug logging
* Tweak: Added method to make renews payable when customer visit pay page from gateway confirmation email
* Tweak: Added order item meta in subscription email and metabox
* Tweak: Added warning on backend, when the admin try to change manually the status of a renew order
* Update: Language Files
* Fix: Fixed PayPal enabled check
* Dev: Added the filter 'ywsbs_load_assets'

= 1.6.0 - Released on 01 August, 2019 =
* New: Support for WooCommerce 3.7
* Update: Plugin framework
* Update: Italian language
* Fix: Fixed issue with the renew remind email
* Dev: Added filter 'ywsbs_check_order_before_pay_renew_order'
* Dev: Added the action 'ywsbs_before_pay_renew_order'
* Dev: Add action yith_suborder_renew_created to synchronize suborder with the parent

= 1.5.9 - Released on 29 May, 2019 =
* Update: Plugin framework
* Update: Language files
* Fix: Fixed with trial subscription and PayPal
* Fix: Fixed option name in subscriptions-related template
* Fix: Check renew order before payment
* Dev: Added meta is_a_renew on Multi Vendor suborder
* Dev: Save subscr_id meta when a ipn is received
* Dev: Cancel the subscription if parent_order is null

= 1.5.8 - Released on 16 April, 2019 =
* Tweak: Added a delay before cancel the subscriptions don't renewed
* Fix: Warning errors at backend under specific conditions

= 1.5.7 - Released on 09 April, 2019 =
* New: Support for WooCommerce 3.6
* Update: Plugin framework
* Update: Language files
* Update: Translation for the string 'Cancel subscription now'
* Fix: Set order_args in WooCommerce session, to fix Stripe issue
* Fix: Coupon amount
* Fix: Numeric issues
* Dev: Added filter 'ywsbs_use_date_format'

= 1.5.6 - Released on 26 Feb, 2019 =
* Update: Plugin framework
* Update: Language files
* Fix: Subscription status translation
* Fix: Fixed typo
* Fix: Subscription field show if check virtual or downloadable checkbox at first time
* Fix: Check if subscription has renews in the payment done email
* Fix: Recurring payment total in renew order email

= 1.5.5 - Released on 05 Feb, 2019 =
* Update: Plugin framework
* Update: Language files
* Tweak: added check if order contains subscriptions to not execute ywcsb_after_calculate_totals
* Fix: Fixed possible error under specific conditions
* Fix: Fixed add to cart validation
* Fix: $max_lenght array with localized index,
* Fix: Fixed a non numeric value on max_lenght field
* Fix: Fixed recurrent coupon on variation
* Dev: Added the filter 'ywsbs_register_panel_position'

= 1.5.4 - Released on 06 December, 2018 =
* New: Support for WordPress 5.0
* Update: Plugin framework
* Fix: Fixed wrong total subscription amount if price per is greater then one
* Fix: Moved transaction check to valid IPN handling

= 1.5.3 - Released on 23 October, 2018 =
* New: Integration with YITH WooCommerce Account Funds from version 1.1.2
* New: Integration with WooCommerce Stripe Gateway 4.1.12
* Update: Language files
* Update: Plugin framework

= 1.5.2 - Released on 23 October, 2018 =
* Update: Language files
* Update: Plugin framework
* Fix: Possible fatal error with YITH WooCommerce Event Tickets

= 1.5.1 - Released on 12 October, 2018 =
* New: Support for WooCommerce 3.5
* Fix: Deleting meta data not save the changes in the order
* Fix: Delete subscription error
* Dev: Added actions 'ywsbs_before_add_to_cart_subscription' and 'ywsbs_after_add_to_cart_subscription'

= 1.5.0 - Released on 05 October, 2018 =
* Fix: Possible fatal error under particular conditions.

= 1.4.9 - Released on 26 Sep, 2018 =
* Update: Plugin framework
* Fix: Some code notices

= 1.4.8 - Released on 10 Sep, 2018 =
* Fix: Fixed standard PayPal transaction id registration on renew order.
* Fix: Issues with PHP 7.2.
* Fix: Issues with Renew Reminder email.
* Fix: Fixed pause and cancellation issue.

= 1.4.7 - Released on 28 August, 2018 =
* Update: Language files
* Update: Plugin framework
* Fix: Multiple subscription products on cart
* Fix: Renew Reminder cron
* Fix: Email templates
* Fix: Show subscription total on the checkout page if setting max length option.
* Fix: IPN log of posted arguments
* Dev: Added filter 'ywsbs_add_shipping_cost_order_renew'

= 1.4.6 - Released on 30 Jul, 2018 =
* New: Integration with YITH Stripe Connect for WooCommerce from version 1.1.0
* New: Added payment method column and filter on Subscription List Table
* New: Added new option to show "Renew Now" button on My Account > Orders: if a renew order has at least one failed payment (not for all gateways)
* Update: Language files
* Fix: The "Pause" button was only being displayed to admin.

= 1.4.5 - Released on 09 Jul, 2018 =
* New: Edit details from subscription admin edit page ( only for subscription paid with YITH PayPal Express Checkout for WooCommerce )
* New: Added subscription number in order list
* Update: Plugin framework
* Update: Language files
* Fix: My Account Errors
* Fix: HTML Price when Max Length option is set
* Fix: My account endpoint
* Fix: Fixed columns in order list
* Fix: Fix api handler + refund for recurring payment standard PayPal

= 1.4.4 - Released on 29 May, 2018 =
* Update: Plugin framework
* Fix: The user can't cancel its subscription from My account page

= 1.4.3 - Released on 25 May, 2018 =
* Tweak: Support for GDPR compliance
* Update: Plugin framework
* Update: Localization files

= 1.4.2 - Released on 21 May, 2018 =
* Fix: Status of Subscription color in backend
* Fix: Subscription Email settings

= 1.4.1 - Released on 15 May, 2018 =
* Fix: Javascript Error on single product page
* Fix: Text domain wrong in two string

= 1.4.0 - Released on 15 May, 2018 =
* New: Support for WordPress 4.9.6
* New: Support for WooCommerce 3.4
* New: Privacy settings option
* New: Retain pending subscriptions option
* New: Retain cancelled subscriptions option
* New: One time shipping option in product editor Shipping tab
* New: Billing and Shipping Customer information editable by Administrator
* New: Shipping Customer information editable by Customer
* Update: Plugin framework
* Update: Localization files
* Fix: Order total calculation
* Fix: Activity log
* Fix: YITH WooCommerce Multi Vendor integration - fix for YITH WooCommerce Multi Vendor shipping method
* Fix: YITH WooCommerce Multi Vendor integration - vendor suborders *missing* the order note for new subscriptions
* Fix: YITH WooCommerce Multi Vendor integration - Added shipping cost in renew order (for vendors)
* Fix: Renew subscription from suborder
* Dev: New actions 'ywcsb_admin_subscription_data_after_billing_address' & 'ywcsb_admin_subscription_data_after_shipping_address'

= 1.3.2 - Released on 06 April 2018 =
* New: Integration with YITH WooCommerce Affiliate 1.2.4
* New: Dutch translation
* Tweak: Change hook prettyPhoto
* Update: Plugin framework
* Fix: Fixed get_price_html
* Fix: Order meta data
* Fix: Fixed taxes calculation when switching subscription
* Fix: Restore cart after cancel payment


= 1.3.1 - Released on 31 January 2018 =
* New: Support for WooCommerce 3.3.0
* Update: Plugin framework
* Fix: Issue when PayPal payment is cancelled

= 1.3.0 - Released on 29 January 2018 =
* New: Support for WooCommerce 3.3.0 RC2
* Update: Plugin framework
* Fix: Shipping taxes removed form WC settings
* Fix: PayPal IPN issue with PHP 7.1
* Fix: Prevent fatal error for WooCommerce < 3.0.0
* Dev: Added hook 'ywcsb_after_calculate_totals'
* Dev: Added hook 'ywsbs_change_prices'
* Dev: Added hook 'ywsbs_change_price_in_cart_html'
* Dev: Added hook 'ywsbs_change_price_current_in_cart_html'
* Dev: Added hook 'ywsbs_change_subtotal_price_in_cart_html'
* Dev: Added hook 'ywsbs_change_subtotal_price_current_in_cart_html'
* Dev: Added hook 'ywsbs_signup_fee_label'

= 1.2.9 - Released on 15 November 2017 =
* Fix: Javascript error in single product page
* Fix: WooCommerce Coupon when a subscription is on cart

= 1.2.8 - Released on 7 November 2017 =
* New: Support for WooCommerce 3.2.3
* Fix: Discount calculation when a Custom Coupon is applied
* Fix: Shipping Calculation for renew orders

= 1.2.7 - Released on 17 October 2017 =
* New: Support for WooCommerce 3.2.1
* Fix: Label on subscription product price

= 1.2.6 - Released on 12 October 2017 =
* New: Support for WooCommerce 3.2.0 Rc2
* New: German translation
* New: Added option ''Disable the reduction of stock in the renew order'
* Fix: Order item prices with YITH WooCommerce Product Add-Ons
* Fix: Prettyphoto.css removed font rules
* Fix: Subscription Status localization
* Fix: Removed the vendor taxonomy box in subscription CTP page
* Fix: Multiple coupons on cart
* Dev: Filter 'ywsbs_price_check'
* Dev: Added a check on content cart item key after order processed

= 1.2.5 - Released on 19 August 2017 =
* Fix: Create renew order manually
* Fix: changed plain text to html Subscription Status email

= 1.2.4 - Released on 16 August 2017 =
* New: Support for WooCommerce 3.1.0
* Update: Plugin framework
* Fix: Renew order shipping rate
* Fix: Compatibility with YITH WooCommerce Product Add-ons Premium 1.2.6
* Fix: Trial status after order complete
* Fix: Variation subscription price
* Fix: customer search on subscriptions list table
* Fix: wrong text domain in string
* Fix: subscription meta containing one subscription only
* Fix: Misspelled strings
* Dev: Added hook 'ywsbs_get_recurring_totals'

= 1.2.3 - Released on 26 May 2017 =
* Fix: Check on ipn_track_id due to Paypal issue
* Fix: Renew order fix

= 1.2.2 - Released on 05 May 2017 =
* Fix: Fixed renew shipping costs

= 1.2.1 - Released on 28 April 2017 =
* New: Support for WooCommerce 3.0.4
* New: Set customer notes in the renew from parent order
* Update: Plugin framework
* Fix: Custom billing and shipping address in the renew order
* Fix: Compatibility with php 5.4
* Dev: Changed endpoint hook
* Dev: Changed start time of cron jobs

= 1.2.0 - Released on 31 March 2017 =
* New: Support for WooCommerce 3.0 RC 2
* Update: Plugin Core
* Fix: Subtotal price on cart
* Dev: Added 'ywsbs_renew_subscription' action

= 1.1.7 - Released on 19 December 2016 =
* New: A new method of class 'YITH_WC_Subscription' that return the list of user's subscription
* New: Compatibility with YITH WooCommerce Product Add-ons Premium 1.2.0.8
* New: Support for WordPress 4.7
* New: Date picker in the metabox of subscription info in the backend
* New: Filter 'ywsbs_order_formatted_line_subtotal'
* Tweak: Price with taxes in the subscription related to an order
* Fix: The switch of subscriptions from the free to premium version
* Fix: "Max duration of pauses days" isn't saved properly
* Fix: Issues when the order of a subscription is deleted

= 1.1.6 - Released on 04 October 2016 =
* New: Filter 'ywsbs_trigger_email_before' to change the time to send the email before the expiration
* New: Italian translation
* New: Spanish translation
* Update: Plugin framework
* Fix: Coupons behavior when there's a trial period
* Fix: String localization


= 1.1.5 - Released on 20 July 2016 =
* New: Action to create a renewal order in subscription administrator details
* Tweak: Failed register payments
* Fix: Localization strings for trial period
* Fix: Activity log timestamp


= 1.1.4 - Released on 13 June 2016 =
* New: hook for actions on my subscriptions table
* New: Support for WooCommerce 2.6 RC1
* Update: Plugin framework

= 1.1.3 =
* New: Option Delete Subscription is extend also if the main order is deleted
* New: Email to customer when a payment fails
* Tweak: Changed method to retrieve billing and shipping info of a subscription
* Tweak: Customer info in the email
* Fix: Method is add_params_to_available_variation() and can_be_cancelled()
* Fix: Downgrade/upgrade variations


= 1.1.2 =
* Fix: Paypal IPN Request Fix when the renew order is not present
* Fix: Minor bugs


= 1.1.1 =
* Fix: Few missing and unknown textdomains
* Fix: Minor bugs

= 1.1.0 =
* New: Compatibility with YITH WooCommerce Stripe
* New: Paypal IPN validation amounts
* New: In On-hold orders list failed attempts
* New: Failed Attempts in Subscription list
* New: Enabled possibility to move in Trash or Delete subscriptions
* New: Options Overdue pending payment subscriptions after x hour(s)
* New: Suspend pending payment subscriptions after x hour(s)
* New: Option to Suspend a subscription if a recurring payment fail
* New: In Administrator Subscription Detail added the action "Active Subscription"
* New: In Administrator Subscription Detail added the action "Suspend Subscription"
* New: In Administrator Subscription Detail added the action "Overdue Subscription"
* New: In Administrator Subscription Detail added the action "Cancel Subscription"
* New: In Administrator Subscription Detail added the action "Cancel Subscription Now"
* New: In Administrator Subscriptions List Table added the views of subscription status
* New: Search box in Administrator Subscriptions List Table to search for ID or Product Name
* New: Option Delete subscription if the main order is cancelled
* Update: Dates in subscription details
* Fix: Admin can't add subscription if YITH WooCommerce Multi Vendor is enabled
* Fix: Start date if a Paypal Payment Method is 'echeck'
* Fix: Localization issue

= 1.0.1 =
* New: Compatibility with Wordpress 4.4
* New: Compatibility with WooCommerce 2.5 beta 3
* Update: Plugin framework
* Fix: Minor bugs

= 1.0.0 =
* Initial release

== Suggestions ==
If you have any suggestions concerning how to improve YITH WooCommerce Subscription, you can [write to us](mailto:plugins@yithemes.com "Your Inspiration Themes"), so that we can improve YITH WooCommerce Subscription.

