=== WooCommerce Order Delivery ===
Contributors: woocommerce, themesquad
Tags: woocommerce, delivery, date
Requires at least: 4.4
Tested up to: 5.6
Stable tag: 1.8.5
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 3.0
WC tested up to: 4.9
Woo: 976514:beaa91b8098712860ec7335d3dca61c0

Choose a delivery date during checkout for the order.

== Description ==

This extension makes it easy for customers to choose a delivery date for their orders during the checkout process or simply notify them about the shipping and delivery estimated dates.

As the site owner, you can decide which dates are not available for shipping and delivery, these can be holidays or similar situations. In addition, you can disable specific delivery periods by country or state.

With the capture of the date delivery, you can process the orders more efficiently, improving your productivity and you will get customers that are satisfied.

= Features =

* Configure the settings with an intuitive and easy to use admin interface.
* Quick guide sections within the admin panel.
* Full localization support.
* Set your week days for shipping and delivery,
* the minimum number of days it takes for you to process and ship an order,
* the range of days it takes for you to deliver an order,
* and much more settings.
* Define the not allowed periods for ship and deliver orders, like your holidays or similar situations.
* Restricts specific delivery periods by *Country* or *States*.
* Display shipping and delivery information in the checkout page or let the customer to choose a delivery date.
* Include the delivery information in the order details, emails, etc.
* Manage the delivery date of your subscriptions ('WooCommerce Subscription' extension required).
* Sort your shop orders by delivery date.
* Integrated with the WooCommerce templates for extend/customize the delivery date sections in your theme.
* Developer friendly with tons of hooks and inline comments for extend the plugin's functionality easily.

== Installation ==

1. Unzip and upload the plugin’s folder to your /wp-content/plugins/ directory.
2. Activate the extension through the ‘Plugins’ menu in WordPress.
3. Go to WooCommerce > Settings > Shipping & Delivery to configure the plugin.

== Documentation & support ==

Visit our [Product page](http://docs.woocommerce.com/document/woocommerce-order-delivery/) to read the documentation and get support.

== Changelog ==

= 1.8.5 January 18, 2021 =
* Tweak - Exclude pending orders when checking the maximum number of orders per day/time frame.
* Tweak - Check the maximum number of orders allowed when validating a delivery date in the checkout form.
* Tweak - Tested compatibility with WC 4.9.
* Fix - The setting "Number of orders" was not being saved when entering a zero value.
* Fix - Deactivate a delivery date/time frame in the calendar when its maximum number of orders is reached.
* Fix - Fixed `array_merge()` warning when adding a product to an active subscription with the "All products for WC Subscriptions" extension.

= 1.8.4 November 26, 2020 =
* Tweak - Improved PHP 8 support.
* Tweak - Tested compatibility with WC 4.8.
* Tweak - Tested compatibility with WP 5.6.
* Fix - Fixed "What's new" link on the plugin list page.

= 1.8.3 November 5, 2020 =
* Fix - Wrong time frame assigned on checkout.

= 1.8.2 November 3, 2020 =
* Fix - Disabled delivery dates were enabled in the checkout calendar.

= 1.8.1 October 8, 2020 =
* Fix - Compatibility with WooCommerce Subscriptions.

= 1.8.0 October 7, 2020 =
* Feature - Limit the number of orders per day or time frame.

= 1.7.1 September 14, 2020 =
* Tweak - Tested compatibility with WC 4.5.
* Tweak - Tested compatibility with WP 5.5.

= 1.7.0 July 23, 2020 =
* Feature - Define different delivery ranges for shipping methods.
* Tweak - Enqueue minified Javascript files.
* Tweak - Tested compatibility with WC 4.3.
* Fix - Fixed wrong text-domain for some translatable strings.
* Dev - Set the minimum requirements to WP 4.4 and WC 3.0.

= 1.6.8 June 02, 2020 =
* Tweak - Tested compatibility with WC 4.2.
* Fix - Fixed warning "datepicker.css.map file not found".

= 1.6.7 April 28, 2020 =
* Tweak - Tested compatibility with WC 4.1.

= 1.6.6 April 6, 2020 =
* Fix - Assign delivery details to the subscription renewals with local pickup as the shipping method.
* Fix - Customers couldn't disable preferred delivery days for a subscription.

= 1.6.5 March 30, 2020 =
* Tweak - Tested compatibility with WC 4.0.
* Tweak - Tested compatibility with WP 5.4.

= 1.6.4 January 16, 2020 =
* Tweak - Tested compatibility with WC 3.9.
* Fix - Update calendar options when they change on already initialized calendars.

= 1.6.3 November 5, 2019 =
* Tweak - CSS styling changes for WP 5.3.
* Tweak - Tested compatibility with WP 5.3.
* Tweak - Tested compatibility with WC 3.8.
* Tweak - Tested compatibility with WC Subscriptions 3.0.
* Fix - Fixed issue which made the current day unavailable for shipping the order.

= 1.6.2 October 10, 2019 =
* Tweak - Disable the current day for delivery if all its time frames have expired.
* Fix - Fixed issue which allowed selecting expired dates in the calendar.
* Fix - Fixed issue when trying to print an invalid time-frame object.

= 1.6.1 August 6, 2019 =
* Tweak - Updated calendar styles for supporting the new styles of the 'Storefront' theme.
* Tweak - Added compatibility with WC 3.7.
* Fix - Fixed warning when fetching the rates of a 'Table Rate' shipping method and the extension 'Table Rate Shipping' is not activated.
* Fix - Refresh field values after saving a time frame form.

= 1.6.0 June 17, 2019 =
* Feature - Added compatibility with the extension 'WooCommerce Table Rate Shipping'.
* Feature - Enhanced 'Shipping methods' selector on settings pages.
* Tweak - Remove expired time frames from the select field when the delivery date is the current date.
* Tweak - The time frame field is also required when the delivery date field is required.
* Tweak - Assign the first available time frame when the delivery fields are in auto mode.
* Tweak - Assign the first available time frame during subscriptions renewal if the customer preferences can't be satisfied.
* Tweak - Added button for defining time frames from the 'Delivery days' setting.
* Tweak - Removed 'delivery days' form field when creating a time frame for a single delivery day.
* Tweak - Remove older update notices on plugin activation.
* Tweak - Added URL verification to the actions of the database updater.
* Tweak - Added blank content when there are no elements defined in the 'Time frames' field.
* Tweak - Improved the initialization of the plugin settings and now they are loaded on demand.
* Fix - Fixed PHP 5.2 compatibility adding the missing middle part in some ternary operators.
* Dev - Added classes for representing a delivery day, a time frame and collections of them.

= 1.5.7 May 23, 2019 =
* Tweak - Added missing version in some enqueued styles.
* Tweak - Tested compatibility with WP 5.2.
* Fix - Fixed conversion to object for the `datesDisabled` parameter in the calendar settings.
* Dev - Updated bootstrap-datepicker.js library to the version 1.9.0.
* Dev - Updated jquery.timepicker.js library to the version 1.11.15.

= 1.5.6 April 9, 2019 =
* Tweak - Added compatibility with WC 3.6.

= 1.5.5 March 26, 2019 =
* Tweak - Added 'Next week' and 'Next 2 weeks' options to the date filters in the orders list.
* Tweak - Use the 'maximum delivery days' setting as limit if its value is lower than the subscription billing interval.
* Tweak - Load the subscription delivery data in the checkout form during renewal.
* Tweak - Hide the delivery details for on-hold subscriptions.
* Fix - Fixed wrong available dates in the delivery calendar of the checkout form when renewing a subscription.
* Fix - Fixed wrong delivery values in the order created from a renewal of a subscription.
* Fix - Fixed margin-top for the `Delivery details` section in the order emails which contain a subscription.
* Dev - Changed context from `checkout` to `checkout-auto` when auto-calculating the first shipping date in the checkout form.
* Dev - Updated `emails/email-delivery-date.php` template.

= 1.5.4 March 14, 2019 =
* Tweak - Check the time limit for the `start_date` parameter instead of the current date when calculating the first shipping date.
* Tweak - Check the time limit for the next payment date when calculating the first shipping date of an order renewal.
* Tweak - Tested compatibility with WP 5.1.
* Fix - Hide the 'Time frame' label if not present on the order details page.
* Fix - Unlock the delivery dates beyond the subscription billing interval if the setting `subscriptions_limit_to_billing_interval` is not enabled.
* Dev - Updated default values for the arguments in the `wc_od_get_first_shipping_date` function.
* Dev - Updated `order/delivery-date.php` and `myaccount/edit-delivery.php` templates.

= 1.5.3 December 5, 2018 =
* Tweak - Added 'priority' parameter to the delivery fields in the checkout form.
* Tweak - Stop using the deprecated constant 'WC_TEMPLATE_PATH'.
* Fix - Check if the localization parameters exist before using them in the 'wc-od-checkout.js' script.

= 1.5.2 October 31, 2018 =
* Fix - Fixed 'call to undefined function get_current_screen()' issue.

= 1.5.1 October 23, 2018 =
* Fix - Fixed 'delivery date required' message when the delivery date field is required and the delivery fields are not displayed in the checkout form.

= 1.5.0 October 16, 2018 =
* Feature - Define time frames/time slots for each delivery day.
* Feature - Choose the available shipping methods for each delivery day and its time frames.
* Tweak - Added compatibility with WC 3.5.
* Tweak - Moved the delivery fields to their own meta box in the 'edit-order' and 'edit-subscription' screens.
* Tweak - Set the default value of 'end_date' parameter to the 'max_delivery_days' setting value when calculating the first delivery date.
* Tweak - Changed to 'internal' the note added to a subscription when the delivery details are updated due to a change of the next payment date.
* Tweak - Sanitized the template content before output them.
* Tweak - Minified the CSS files.
* Fix - Process renewal orders that come from a failed status.
* Fix - Convert 'truly' and 'falsy' parameters to booleans before use them to configure the datepicker in the javascript files.
* Fix - Replaced '$customer_note' variable by '$note' in the template 'emails/plain/admin-subscription-delivery-note.php'.
* Dev - Updated jquery.timepicker.js library to the version 1.11.14.
* Dev - Replaced boolean settings values from '0' and '1' to 'yes' and 'no'.
* Dev - Split the 'wc-od-functions.php' file into multiple files.
* Dev - Updated plugin templates.

= 1.4.1 July 27, 2018 =
* Tweak - Check the minimum requirements before initializing the extension.
* Tweak - Added setting to optionally display the 'Shipping & Delivery' section on the checkout page for the shipping method 'Local Pickup'.
* Tweak - Display the delivery details after the order details table in the emails.
* Fix - Display the delivery details in the plain text emails.
* Fix - Calculate the 'recommended' shipping date for the renewal orders of a subscription.
* Fix - Fixed calendar width for RTL languages.
* Dev - Updated 'emails/email-delivery-date.php' template.

= 1.4.0 July 5, 2018 =
* Feature - Calculate the 'recommended' shipping date for each order based on its delivery date.
* Feature - Added 'shipping_date' and 'delivery_date' filters to the shop order table list to filter the orders by these fields.
* Feature - Send an email to the merchant when a note related with the delivery is added to an order/subscription.
* Tweak - Added setting to deactivate the restriction that limits the available dates in the calendar to the billing interval of the subscription.
* Tweak - Hide the 'Shipping & delivery' section on the checkout page when the customer selects 'Local Pickup' as the shipping method.
* Tweak - Hide the 'Shipping & delivery' section on the checkout page when the 'WooCommerce Ship to Multiple Addresses' extension is used in the checkout page.
* Tweak - Removed the delivery date field placeholder by default.
* Tweak - Use the first delivery date as placeholder in the delivery date field when the date is auto-generated.
* Fix - Fixed 'required field' validation on virtual products.
* Dev - Set the minimum requirements to WP 4.4+ and WC 2.6+.
* Dev - All the checkout content is loaded and refreshed as order-review fragments by AJAX.

= 1.3.2 - May 23, 2018 =
* Tweak - Added compatibility with WC 3.4.
* Tweak - Hide virtual keyboard on mobile devices when the datepicker is open.
* Fix - Fixed warning when previewing the email template (The $email parameter is null).
* Fix - Fixed selectWoo interaction in the "Delivery Calendar" dialogs.

= 1.3.1 - January 30, 2018 =
* Tweak - Added compatibility with WC 3.3.
* Tweak - Added <time> HTML tag to the delivery_date column in the orders list table.
* Fix - Fixed typo in the Woo header.

= 1.3.0 - December 19, 2017 =
* Feature - Added compatibility with the extension 'WooCommerce Subscriptions'.
* Tweak - Include the delivery details in the 'customer_invoice' emails.
* Tweak - Minor tweaks in the calendar styles for a mayor compatibility with the themes.
* Fix - Fixed issue when rendering a delivery_date field and the 'return' parameter is false.
* Fix - Fixed issue with the date format when assigning a default value to a delivery_date field.
* Fix - Update the delivery date field value when the customer changes the date manually.

= 1.2.0 - December 4, 2017 =
* Tweak - Use the WP date format in the checkout calendar.
* Tweak - Added translations for the plugin calendars.
* Tweak - Use the WC date format in the 'Delivery Date' column of the shop order list.
* Tweak - Improved the performance by purging any expired event older than a year.
* Tweak - Added link to the plugin documentation in the plugin list.
* Tweak - Improved datepicker styles to add support to the Twenty Seventeen theme.
* Fix - Fixed ambiguity with the m/d/Y and d/m/Y date formats.
* Dev - Updated bootstrap-datepicker.js library to the version 1.7.1.
* Dev - Renamed WC_OD_Delivery_Event class to WC_OD_Event_Delivery.
* Dev - The function wc_od_get_disabled_days() always returns the dates in the ISO 8601 format.

= 1.1.3 - October 9, 2017 =
* Tweak - Added more contrast between the enabled and disabled days in the calendars.

= 1.1.2 - June 2, 2017 =
* Fix - Fixed issue when updating the delivery date and the status of an order at the same time. The delivery date in the emails was outdated.

= 1.1.1 - March 30, 2017 =
* Fix - Fixed empty value in the 'states' field for the events of the delivery calendar.
* Tweak - Added 'clear' option in the 'states' field for the events of the delivery calendar.
* Tweak - Renamed WooCommerce version 2.7 to 3.0.

= 1.1.0 - March 9, 2017 =
 * Feature - Added a setting to make the delivery date an optional, required or auto-generated field in the checkout form.
 * Fix - Missing delivery info in the 'customer_on_hold_order' emails.
 * Fix - Display always the 'Delivery Date' column before the 'Date' column in the order list.
 * Fix - Fixed the appearance of the 'help tips' icons on the settings page.
 * Dev - Added plugin constants.
 * Dev - Deprecated 'dir_path', 'dir_url', 'date_format', 'date_format_js' and 'prefix' properties in the main class.
 * Dev - Updated bootstrap-datepicker.js library to the version 1.6.4.
 * Dev - Added wc-od-datepicker.js script to abstract the datepicker library.
 * Dev - Checkout class rewritten to make it more extensible by developers.
 * Dev - Set the minimum requirements to WP 4.1+ and WC 2.5+.
 * Dev - Moved class loading (autoload) code to the 'WC_OD_Autoloader' class.
 * Dev - Refactored singleton pattern code in the 'WC_OD_Singleton' class.
 * Tweak - Added compatibility with WooCommerce 2.7.
 * Tweak - Removed Select2 and jquery.BlockUI assets. It only uses the libraries included with WooCommerce.
 * Tweak - Added the template 'emails/email-delivery-date.php' to display the delivery details on emails.
 * Tweak - Updated the templates 'order/delivery-date.php' and 'checkout/form-delivery-date.php' to make them more customizable.
 * Tweak - Avoid duplicate numbers when displaying a delivery range with the minimum value equal to the maximum value.
 * Tweak - Added singular string for the delivery range text displayed in the checkout form.
 * Tweak - Use the global variable '$wp_locale' to fetch the weekdays strings in the function 'wc_od_get_week_days'.
 * Tweak - Use the timezone of the site instead of UTC for all the date operations.
 * Tweak - Added hooks to customize the calendar styles.

= 1.0.6 - January 19, 2017 =
 * Tweak - Calculate the first shipping and delivery dates using the site's timezone instead of UTC for a more accurate result.

= 1.0.5 - November 30, 2016 =
 * Fix - Fixed bug calculating the first shipping date for orders with min_working_days > 0 and ordered after the time limit.
 * Fix - Fixed deprecated notice with the woocommerce_update_option_X action hook when saving the plugin settings.

= 1.0.4 - November 21, 2016 =
 * Fix - Fixed issue when checking the time limit to deliver orders on the same day.

= 1.0.3 - October 18, 2016 =
 * Fix - Fixed the earlier day for UTC minus timezones in the checkout calendar.

= 1.0.2 - June 28, 2016 =
 * Tweak - Added WooCommerce 2.6 compatibility.
 * Fix - Fixed datepicker styles for the themes: Storefront 2.0, Twenty Fifteen 1.5 and Twenty Sixteen 1.2.
 * Fix - Fixed typo when calling the 'woocommerce_email_subject_customer_processing_order' in the WC_OD_Order_Details class.

= 1.0.1 - December 14, 2015 =
 * Fix - Added required field validation in the checkout form.

= 1.0.0 - March 9, 2015 =
 * Initial release.

== Upgrade Notice ==

= 1.8 =
1.8 is a major update. It is important that you make backups and ensure you have installed WC 3.0+, and optionally, WooCommerce Subscriptions 2.2+ before upgrading.
