=== WooCommerce Waitlist ===
Requires at least: 4.2.0
Tested up to: 5.7.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 3.0.0
WC tested up to: 5.2.2

This plugin enables registered users to request an email notification when an out-of-stock product comes back into stock. It tallies these registrations in the admin panel for review and provides details.

== Description ==
The WooCommerce Waitlist extension lets you track demand for out-of-stock and backordered items, making sure your customers feel informed, and therefore more likely to buy.
With the WooCommerce Waitlist extension, customers of your site can sign up to be notified by email when an out-of-stock product becomes available. As the site owner you can also review which users are on the waiting list for which products, and sort your products by the number of people registered on the waiting list.

== Installation ==
1) Unzip and upload the plugin’s folder to your /wp-content/plugins/ directory
2) Activate the extension through the ‘Plugins’ menu in WordPress

== Frequently Asked Questions ==
Can a customer view all the products they are on a waiting list for?
There is an experimental shortcode [woocommerce_my_waitlist] which will display a table listing all the products that the currently logged in user is waiting for.

Are customers put on a waitlist in a particular order?
Customers are added in the order they sign up to the waitlist. In the admin you can see the date each user joined next to their email address.

Does this work for affiliate products?
No. At the moment stock status has no bearing on the output of an affiliate product listing so these have been left well alone.

Does this work for variable products?
There is a known issue when using WooCommerce Waitlist in conjunction with variable products that prevents the ‘Join Waitlist’ button from being displayed  when the ‘Out of Stock Visibility’ option is set to ON. The only current solution to this problem is to turn this option off.

How do I change the subject / content of emails?
The content of the email and the subject line are both editable via the WooCommerce email system. WooCommerce Waitlist adds a new section to the ‘Emails’ tab of WooCommerce Settings where this can be managed. For more information please see the WooCommerce Documentation.

What if I don’t want users to be automatically emailed when a product is back in stock?
We’ve got you covered. Add the following snippet to your functions.php file in your theme and no email will be sent out and users will remain on the waitlist.
add_filter( 'wcwl_automatic_mailouts_are_disabled', '__return_true' );

What if I want to email users automatically, but don’t want them to be removed from the waitlist?
We’ve got that one too. Add the following snippet to your functions.php file in your theme and users will remain on the waitlist until they purchase that product.
add_filter( 'wcwl_persistent_waitlists_are_disabled', '__return_false' );

Why does the Waitlist only show up for some products?
If you’re using the Advanced Notifications extension make sure you disable the backorder setting.

== Changelog ==

2021.05.04 - version 2.2.5
* Fix: Waitlist form can be submitted on instock bundles (now it is removed from the DOM)

2021.03.05
* Fix: Waitlist not showing for [product_category] shortcode
* Fix: Mailouts triggering when WC product import processing
* Added: success/fail hooks after waitlist notification email is sent

2021.03.01 - version 2.2.4
* Fix: updated "woocommerce_email_subject_" filters to include object and email class
* Updated language (.pot) file

2021.02.10 - version 2.2.3
* Fix: issue with plain text emails not displaying as expected
* Fix: fatal error with events plugin add-on 'Enhance Woo Order Templates'
* Fix: removed waitlist notice from product page if it was empty
* Fix: php8 deprecation notice fix
* Fix: events ticket class checks updated as new classes have been added/changed (checkbox HTML)
* Added: action hooks before and after waitlist mailout process
* Added: filter to customise when mailout should be processed
* Added: user flag for guests in admin panel row
* Added: include wcwl prefix to ticket variable names to avoid potential conflicts

2020.10.20 - version 2.2.2
* Fix: priority for supported products array adjustment as some products not showing waitlist tab in admin

2020.10.19 - version 2.2.1
* Fix: adjusted product types filter to a later hook to ensure you could use it within theme filters
* Fix: adjusted stock requirement check for variables to always return all variations
* Fix: updated checks for global sitepress to ensure functions are not carried out when function does not exist

2020.10.12 - version 2.2.0
* Fix: Admin user getting logged out when user created in back end
* Fix: opt-in option not hidden when processing waitlist request
* Fix: array check warning for multisite when switching versions with WC beta plugin
* Fix: adjusted javascript to re-use existing styles to display elements on show()
* Fix: error when trying to use functions from WPML extensions that are not active
* Fix: export not organising emails correctly
* Fix: some events showing duplicate waitlists
* Fix: console error when events data not defined
* Added: filter to adjust/add more admin email recipients
* Added: guest sign up for Waitlists
* Added: customer signup email (with unsubscribe link) when not creating users
* Added: option to force login when user is created
* Added: waitlist panel to events edit page
* Added: javascript for frontend AJAX actions
* Added: "You have been removed from waitlist" emails

2020.07.14 - version 2.1.24
* Fix: waitlist elements not showing with events tickets (4.12)

2020.07.07 - version 2.1.23
* Fix: updated language.pot file

2020.06.19 - version 2.1.22
* Patch Fix: Rollback to avoid mailouts being unintentionally sent to users on bundled products with last version
* Fix: Bundled product creation sometimes duplicating products

2020.06.15 - version 2.1.21
* Fix: Bundled product creation sometimes duplicating products

2020.06.09 - version 2.1.20
* Fix: Plugin stock check on mailout now takes minus numbers into consideration
* Fix: WPML incompatibility on templates
* Fix: Updated notices for new WC template structure on account page
* Fix: Duplicating products contained in bundles due to running "in stock" earlier than expected
* Added: Product ID to parameters passed to admin sign up mailout
* Added: Admin email as parameter passed to admin sign up mailout

2020.04.08 - version 2.1.19
* Updated plugin author header and version compatibility

2020.02.27 - version 2.1.18
* Fix: Update required for compatibility with WPML & WCML

2020.02.07 - version 2.1.17
* Fix: New user created hook now includes user ID and not user to keep consistency throughout plugin

2020.02.07 - version 2.1.16
* Fix: Waitlist not being hidden correctly on frontend when selecting different variations
* Added: New hook after customer is created via the waitlist plugin

2020.01.27 - version 2.1.15
* Fix: Bundled products not showing waitlist for backorder products/variables
* Added: Filter to customise when to hide waitlist on frontend ('wcwl_waitlist_is_required')

2019.12.28 - version 2.1.14
* Fix: waitlist not showing for events when particular setup used, haved altered initial check to target parent div

2019.12.25 - version 2.1.13
* Fix: waitlist not showing for event tickets after events calendar frontend rework

2019.12.15 - version 2.1.12
* Fix: with persistent waitlists enabled guest (non-logged in) users not removed from waitlists when purchasing product

2019.12.05 - version 2.1.11
* Fix: fatal error when sending translated emails with WPML

2019.11.18 - version 2.1.10
* Fix: issue with account template not working with WPML translated products
* Fix: waitlist not activated when WooCommerce netwrok activated on mutlisite
* Fix: issue with some product types throwing fatal error when not specified as a supported product

2019.10.10 - version 2.1.9
* Fix: issue with shortcode showing too early
* Fix: Updated translation (.pot) file
* Fix: Bumped email template numbers to avoid issues with custom templating extensions

2019.09.04 - version 2.1.8
* Fix: conflict with WooCommerce Google Analytics Pro Plugin
* Fix: waitlist CSV export not working in firefox
* Fix: conflict with Product Filters for WooCommerce plugin
* Fix: default minimum stock setting not setup on first install
* Fix: waitlist not showing on quickview for variable products
* Fix: waitlist admin column appearing off screen
* Added: only send in-stock notifications if product is published

2019.08.15
* Fix: product ID not being extracted properly from WC shortcode

2019.07.31 - Version 2.1.6
* Fix - fatal error when using WC's "products" shortcode

2019.07.30 - Version 2.1.5
* Fix - product shortcode not loading waitlist

2019.07.22
* Fix - bug that prevented users signing up to waitlists in another language
* Fix - archive pages not showing languages correctly

2019.07.12
* Enhancement: refactored frontend JS to allow global access to waitlist functions

2019.07.04
* Fix - waitlist elements not displaying properly when multiple product shortcodes used on one page

2019.07.03 - Version 2.1.4
* Fix: Waitlist elements not always displaying properly for custom built pages (js tweaks for targetting correct elements)

2019.07.02 - Version 2.1.3
* Fix: Waitlist notices not showing on event pages

2019.06.25 - Version 2.1.2
* Fix: Waitlist not showing for bundle products with only optional bundled products

2019.06.21 - Version 2.1.1
* Fix: waitlist not showing for "instock" grouped products
* Fix: waitlist not showing in quickview window (working again for simple products only)
* Added: option to WooCommerce settings for user to easily adjust waitlist my account endpoint

2019.06.19 - version 2.1.0
* Added first stage support for WooCommerce Product Bundles (see FAQs for more information)

2019.06.06 - version 2.0.14
* Fix: fatal error with certain themes due to missing parameter for email heading hook

2019.05.31
* Fix: waitlist elements not showing on all archive pages

2019.05.29
* Fix: js targetting and removing elements outside of plugin scope
* Fix error on new waitlist signup email header & footer

2019.03.29
* Fix: dismiss icons not showing on notifications for logged out users

2019.03.27 - version 2.0.13
* Fix: duplicate admin notices from archive pages
* Fix: moved archiving function to after user is unregistered instead of after notifications are sent
* Fix: Waitlist links not always working on custom archive pages
* Fix: 'confirm' string not getting translated properly
* Updated create customer functionality to utilise wc_create_customer throughout plugin
* Updated options to include setting to retain data when deleting the plugin
* Added support for WC Brand archive pages

2019.03.15 - version 2.0.12
* Fix: Ensure waitlist users who purchase the product are always removed from the waitlist
* Fix: Admin email notifications not going out when customer joins waitlist
* Moved setting for admin email address over to the email settings page

2019.02.28 - version 2.0.10
* Fix: CSV export not processing "#" character (currently removing this character from product title)

2019.02.27 - version 2.0.9
* Fix: CSV export not properly escaping special characters in product titles

2019.02.26 - Version 2.0.8
* Fix bug with waitlists not always showing on grouped products
* Updated mailout logic (users are only to be notified when product is back in stock OR stock quantity increases past set limit)
* Reverted delay on mailouts back to seconds to avoid duplicates only (under review)

2019.02.22
* Fix: bug with mailout error notices persisting
* Increased delay between instock notifications to 3 days by default

2019.02.20 - version 2.0.7
* Fix: filter missing for "leave waitlist" and "email already on waitlist" message text

2019.02.20 - version 2.0.6
* fix bug where new users cannot create account when certain WooCommerce options are selected

2019.02.14 - version 2.0.5
* fix bug where out of stock message not shown for variable products

2019.02.12 - version 2.0.4
* fix bug where users are not sent password upon account creation
* Added filter to allow users to hide waitlist by product ID

2019.02.06 - version 2.0.3
* fix: Users receiving error when creating account via waitlist signup with auto generate password disabled
* fix: users not redirected to product page when taken to account page to sign in

2019.02.05 - version 2.0.2
* fix: fatal error when viewing unsupported product types

2019.01.31 - version 2.0.0
* fix: elements persisting when notice shown on archive pages
* fix: bug with duplicate emails and factored admin email into own class
* fix: issue with Google Analytic codes not returning correctly
* fix: issue with google indexing waitlist pages multiple times
* Added: Support for The Events Calendar plugin (WooCommerce Tickets)
* Added: AJAX support for all frontend requests
* Added: Templates for displaying waitlist elements wherever required
* Added: setting to display waitlist for events if the The Events Calendar is enabled
* Added: filters to email analytic tracking codes
* Updated: translation file

2018.11.26 - version 1.8.9
* Fix: error with account menu not displaying correctly due to insufficient checks in the plugin

2018.11.01 - version 1.8.8
* Fix: fatal error when unregistered user joined waitlist on WPML translated product

2018.10.18 - version 1.8.7
* Added rel="nofollow" to internal waitlist links to prevent crawling

2018.10.18 - version 1.8.6
* Ensured backwards compatibility for waitlist templates

2018.10.17
* Fixed bug where users were not archived if in stock mail failed to send
* Added common errors to waitlist panel when mailouts failed

2018.10.11
* Added exporter and eraser for personal data (included in WordPress 4.9.6)

2018.10.08
* Fix: bug with mailouts sometimes causing error due to too few parameters on header/footer actions

2018.10.05
* Updated template loading path to utilise WooCommerce defaults (templates should now be placed in the "woocommerce" theme folder)

2018.10.04
* Added functionality to export waitlists
* Ensured frontend JS was available on any page
* Minified assets

2018.10.01 - Version 1.8.5
* Fix: Minimum stock amount not working due to typo on option name

2018.09.25
* Fix: Shortcode was only working from post/page content. It is now globally accessible
* Added filter to waitlist tab name on my account page

2018.09.21 - Version 1.8.4
* Fix: Users not always removed from waitlists when they make a purchase for the prouct
* Fix: Mailouts are now only sent when the stock level increases
* Added filter for adjustment of time delay between when a user can receive another waitlist notification

2018.08.09
* Fix: Users not being removed from product waitlists after purchasing the product

2018.08.08
* Fix: Broken links for product edit screen for variations

2018.08.02
* Enhancement: Added support for Tribe's Event's Calendar (template override required where events offer single ticket otherwise elements are not displayed on frontend)

2018.07.31
* Fix: Updated WC out of date message to version 3.0

2018.07.26
* Enhancement: Removed some restrictions on where to load waitlist. Enables loading of waitlist elements on custom product/archive pages for example

2018.07.25 - Version 1.8.3
* Fix: Error on product page shortcode
* Fix: Duplicate waitlist emails sent out when products set to managing stock

2018.07.17 - Version 1.8.2
* Added filters to include paths for admin templates

2018.06.18
* Fix: resolved conflict with WooCommerce Quickview plugin

2018.06.01 - Version 1.8.1
* Fix: Updated user account template with WC function to print notices
* Fix: made sure users were added to archive when sent in stock notifications from admin
* Fix: Analytics data not always being appended to URLs in emails

2018.05.21 - Version 1.8.0
* Updated the product waitlist tab so waitlist, archive and options can be easily adjusted from one location
* Added functionality to update waitlists etc via ajax to avoid updating product everytime
* Added options to display an opt-in and notice to users that an account will be created using their email address
* Added options for adding google analytic code to waitlist mailouts for tracking returns
* Added option to toggle witlist on/off per product
* Added option to apply a minimum stock level before waitlist mailouts will be sent to users
* Added option to send an admin user an email when a customer registers for a waitlist
* Added option to show waitlist elements on product archive pages (shop, categories etc)
* Added filters for opt-in messages

2018.05.15 - Version 1.7.7
* fix: emails not always sending out in the correct language
* fix: fatal error when triggering mailouts when using WooCommerce Multilingual version 4.2.10

2018.04.18
* Fix issue where updates (counts & metadata) were hitting server limits

2018.04.12 - Version 1.7.6
*Fix: Heavy function running on plugin activation causing installs with lots of products to timeout

2018.03.28
* Fix: Bug where product not returned when using WPML without WCML

2018.03.19 - Version 1.7.5
* Fix: Email headers and subjects not updating when the settings are changed

2018.03.13 - Version 1.7.4
* Fix: Polylang & WPML conflict when sending waitlist emails
* Added domain path to plugin header

2018.03.06
* Ensured use of yoda conditionals throughout plugin

2018.03.05 - version 1.7.3
* Fix: conflict with Polylang using WPML API and WPML not installed

2018.02.22
* Updated user query to make it more efficient

2018.02.08
* Fix: bug where language files (.po and .mo) not loading properly
* Fix: issue where waitlist admin tab was showing before it was clicked
* Fix: stopped user waitlist queries running when they aren't required
* Added filterable link to shop in users waitlist template
* Added links to settings, docs and support on the plugins page
* Added dismissable notice if archiving is switched off on product tab

2018.02.02 - version 1.7.2
* Fix: bug where post metadata not updating correctly for archives
* Fix: bug where users weren't being unregistered from waitlists when they were deleted
* Fix: bug with variable product archives not showing all child variations
* Fix: bug with user ID not passing when checking a user is on a waitlist
* Fix: bug with waitlist button not giving correct URL for WC "product_page" shortcode
* Added button to update postmeta relating to Waitlists on the admin settings page
* Added conditional so that archive data is only returned for the user if metadata has been updated on the my account page
* Added JS warning to alert users to backup their database before running waitlist updates
* Added admin nag for updating waitlist metadata for 1.7.2+
* Updated translation file (.pot)

2018.02.01
* Fix: bug where post metadata not updating correctly for archives
* Added button to update postmeta relating to Waitlists on the admin settings page
* Added conditional so that archive data is only returned for the user if metadata has been updated on the my account page

2018.01.31 - version 1.7.1
* Fix: bug with user waitlist tab showing some items that the user is not on the waitlist for
* Fix: bug with some user waitlist template text not showing up
* Fix: bug with waitlist tab on my account page causing 404s
* Added filter for waitlist tab endpoint on my account page
* Fix: bug with waitlist items not showing on waitlist account tab
* Fix: deprecated notice for method being called incorrectly on waitlist tab

2018.01.26 - version 1.7.0
* Added new tab to WooCommerce My Account page for the users waitlist
* Fix: Duplicate success notices when joining waitlists ona  grouped product page
* Fix: Updated frontend code to work with product_page shortcode
* Fix: Updated waitlist button URLs to redirect user to current page rather than product page (in case shortcode is in use)
* Removed GDPR changes (temporarily - on hold until future release)

2018.01.16
* Updated plugin to meet GDPR requirements

2018.01.15
* Updated script/style enqueues to use current plugin version to avoid caching issues

2018.01.12
* Removed waitlist options for variables when viewing a grouped product

2018.01.11 - version 1.6.2
* Fix: Bug with waitlist not always retrieving correct WPML master post and thus waitlists not functioning properly

2017.11.28 - version 1.6.1
* Fix: Bug with woocommerce sticky add to cart plugin
* Added support for WPML

2017.11.17 - version 1.6.0
* Fix: Updated shortcode to work with all product types
* Fix: Issue with emails not being added correctly to variable products on frontend for new users
* Fix: Large numbers of variations not properly loading waitlist elements
* Fix: Updated waitlist count meta to ensure counts stay updated
* Fix: Waitlist notices persisting across pages
* Fix: Waitlist updates not working for grouped products
* Refactored frontend class to enable separate classes for each product type
* Added filter to enable users to add their own product types
* Restructured frontend JS to fix some bugs and tidy up URL query string
* Added full support for WooCommerce Quick View
* Removed waitlist tab from grouped products admin page
* Added new test suite with frontend tests

2017.11.10 - version 1.5.84
* Fix: added checks to ensure products are available when required to avoid errors

2017.11.08 - version 1.5.83
* Fix: waitlist functionality not working for large numbers of variations (when get_variation ajax function is required on product pages)

2017.10.27 - version 1.5.82
* Updated translation file

2017.10.25 - version 1.5.8
* Fix: updated archive validation
* Fix: add user to archive not working if archives not returning as array

2017.08.14
* Fix: added check that array is returned when returning existing waitlist archives

2017.08.07 - version 1.5.7
* Fix: fixed issue where waitlist counts were showing as 1 by default when updated
* Fix: made settings text translatable
* Fix: fixed bug where updating waitlist counts when no products exist
* Fix: updated order->user_id to use new format order->get_user_id()
* Added in setting to update waitlist counts manually
* Added support for multiple parent products (grouped products)
* Added product ID to filter for disabling persistent waitlists
* Added product ID to filter for disabling automatic mailouts
* Removed auto updates of waitlist counts to avoid issues with large product databases

2017.07.26 - version 1.5.6
* Fix: Adjusted mailouts for variable products so the parent variable can control mailouts for child variations if managing stock
* Fix: Adjusted how grouped products are handled to avoid waitlist adjustments on page reloads
* Fix: Added appropriate message for logged out users when registration is required

2017.07.26
* Fix: Adjusted mailouts for variable products so the parent variable can control mailouts for child variations if managing stock

2017.05.31 - version 1.5.5
* Fix: Deprecated function notice for product tab
* Added a filter to disable automatic waitlist count updates "wcwl_disable_auto_waitlist_updates"

2017.05.09 - version 1.5.4
* Fix: email hook not firing

2017.05.02 - version 1.5.3
* Fix: adjusted frontend JS to reference the email input field properly to avoid users not being added when logged out in some cases
* Fix: removed check for parent variables when updating waitlist counts as counts were sometimes showing inaccurately
* Fix: fatal error when working with bundled products, currently not working with this extension

2017.04.22
* Fix: added conditional checks to fix potential bug of product not found when deleting users
* Fix: changed selector to "this" on frontend JS for adding logged out users to waitlist after bug report

2017.04.11
* Fix: Added check for product object before loading waitlist for product

2017.04.10
* Fix: Activation hooks not firing

2017.04.07 - version 1.5.2
* Fix: bug with upgrade process causing fatal error

2017.04.03 - version 1.5.1
* Fix: definitions not loading early enough for registration hooks
* fully updated and tested for woocommerce 3.0

2017.03.28
* Fix: initial waitlist counts starting at 1

2017.03.25
* Fix: waitlist not always saving for variable products on frontend
* Fix: compatibility for woocommerce filters
* Refactor of code for handling logged out users

2017.03.24
* Added compatibility class to ensure compatibility with WooCommerce
* Refactor of main plugin class
* Refactor frontend class

2017.03.11
* Fixed: waitlist count sortable columns not showing all products
* Fixed: shortcode not working for variations
* Fixed: waitlist column not reliably sorting by amount of users on waitlist
* Added postmeta for waitlist count for all products to reliably store amount of users on waitlist
* Added function to update postmeta for waitlist counts if they don't exist
* Added functionality for variable and grouped products to reliably store a count for child waitlists

2017.03.10
* Added "date added" postmeta for when users are added to waitlist
* Fixed issues with new date added field conflicting with older waitlist versions
* Adjusted and re-styled product tab table

2017.02.02
* CSS tweak, changed waitlist icon on product edit screen
* Added product ID to email template

2017.02.02 - version 1.5.0
* Added support for WC Subscriptions
* Fixed bug where simple subscriptions were not showing email field for logged out users
* Fixed bug where mailouts were not triggered for variable subscriptions

2017.01.30
* Fixed bug where users were added across multiple inputs on admin waitlists
* Added email validation in admin

2017.01.28
* Fixed archiving - now each user is archived once an email is sent
* Adjusted archives to record per day rather than time, all records for each day are now collated

2017.01.27 - version 1.4.14
* Fixed bug where archiving wasn't working for new installs as options weren't loading in by default

2016.04.18 - version 1.4.13
* Fixed bug where user accounts were created but user wasn't always sent a password
* Fixed bug where archiving waitlists wasn't switched on by default
* Fixed bug that caused users to be added/removed from waitlist on page refresh in the frontend
* Fixed bug that prevented waitlists from being shown when product was in stock but waitlist still had users
* Adjusted hooks for mailouts when products come back in stock - now triggered when stock status/level changes
* Fixed bug that prevented API stock changes to send required mailouts

2016.04.14 - version 1.4.12
* Fixed bug with variable product upsells
* Fixed bug with join waitlist button not working on some variable products

2016.04.12 - version 1.4.11
* Fixed bug where product object was not verified before being used on the frontend

2016.03.14 - version 1.4.10
* Adjusted the way stock status was stored for variable products
* Fixed JS error for grouped product waitlist data not allowing users to be added
* Fixed bug with add to cart button displaying when it shouldn't
* Adjusted when to display waitlists on the product page; now show when product is out of stock or if a user is present on the waitlist

2016.03.01 - version 1.4.9
* Fixed javascript bug preventing variable products from being added to cart

2016.03.01 - version 1.4.8
* Fixed email address bug preventing logged out users from joining a waitlist

2016.02.25 - version 1.4.7
* Fixed wcwl_data undefined bug causing variable products and other javascript based extensions to fail

2016.02.17 - version 1.4.6
* Fixed bug with JS adding user email multiple times to query string on front end

2015.11.23 - version 1.4.5
* Updated translation template (.pot)

2015.10.21 - version 1.4.4
* Updated docblock
* Removed debugging code on edit product screen

2015.10.21 - version 1.4.3
* Fixed bug where waitlists weren't showing on product edit screen when persistent waitlists were enabled
* Added notification text to product edit screen when persistent waitlists are enabled

2015.10.18 - version 1.4.2
* Fixed update bug for variations, conflict with another plugin

2015.10.14 - version 1.4.1
* Fixed version numbers

2015.10.12 - version 1.4.0
* Added new feature - Waitlist Archives for recording a history of mailed out waitlists

2015.09.18 - version 1.3.13
* Fixed bug with mailouts not working when updating stock when using WC 2.4

2015.08.12 - version 1.3.12
* Fixed bug with updating waitlists for variables when using WooCommerce 2.4

2015.07.08 - version 1.3.11
* filtered text on "new account" tab of woocommerce email settings with gettext

2015.07.08 - version 1.3.10
* removed meta-box and instead added a waitlist tab to the product data panel on product edit pages
* added readme

2015.05.25 - version 1.3.9
* changed Admin UI to show the waitlist meta-box if there are any users on the waitlist, rather than if the product is out of stock

2015.05.25 - version 1.3.8
* fixed settings bug, settings now being updated correctly
* updated translation functions
* fixed frontend button bug, now outputting same button type if user is logged in or not

2015.04.21 - version 1.3.7
* Fix - Potential XSS with add_query_arg

2015.01.11 - version 1.3.6
* added notice and deactivated plugin if WooCommerce is not at least version 2.0
* removed functionality for WooCommerce versions less than 2.0
* updated settings functions for woocommerce v2.3.0

2014.12.19 - version 1.3.5
* fix \"Email Address\" hard coded string

2014.12.01 - version 1.3.4
* Fixed bug where deleting users triggered a PHP error
* Fixed bug causing php notice when updating with quick edit

2014.11.26 - version 1.3.3
* WordPress 4.0.1 compatability
* Fixed bug removing users from waitlist when \'enable stock management\' was ticked on certain products (related to quickedit bug)
* Refactored and annotated all functions
* Fixed \'woocommerce_my_waitlist\' shortcode so it can be displayed on any page
* Fixed bug with mailouts not sending when product was updated using quick edit

2014.11.19 - version 1.3.2
 * fix \"Join Waitlist\" hard coded string

2014.09.11 - version 1.3.1
 * fix version number, causing endless update loop

2014.09.05 - version 1.3.0
* WordPress 4.0 compatability
* WooCommerce 2.2 compatability
* Added support for non-registered and logged out users to join waitlists
* Added support for Admin to add users to waitlist from product page
* Added support for users to be removed from waitlists when they are deleted from wordpress
* Added frontend fixes for variable and grouped products and css and jquery

2014.03.03 - version 1.2.0
* Added support for WC_Mail templates
* Added support for Bulk Stock Management
* Fixed ‘call to member function on non-object’ notice in Frontend_UI notice

2014.02.25 - version 1.1.8
* Added filterable version of automatic mailout control

2014.02.24 - version 1.1.7
* Fix deprecated call to WooCommerce->add_message
* Fix broken link to Inventory Settings after 2.1 change

2014.02.18 - version 1.1.6
 * Fix in security issue in wp-admin

2013.11.06 - version 1.1.5
 * [woocommerce_my_waitlist] only displays for logged in users
 * [woocommerce_my_waitlist] not dependent on WP numberposts setting

2013.10.31 - version 1.1.4
 * Patch fixed the error with 1.1.3 - no closures in PHP 5.2 dummy! Happy Halloween everyone

2013.10.29 - version 1.1.3
 * Added a beta shortcode to display a user waitlist using [woocommerce_my_waitlist]

2013.02.21 - version 1.1.2
 * Fixed a bug that prevented in-stock variable products from being added directly after an out-of-stock variation was clicked

2013.02.21 - version 1.1.1
 * Added filterable version of persistent waitlist support

2013.01.24 - version 1.1.0
 * Added support for waitlists on product variations
 * Added control to auto waitlist creation to allow it to be turned off
 * Added dismissable admin nag alerting shop managers to turn off \'Hide out of stock products\' setting
 * Replace WCWL_HOOK_PREFIX constant with greppable string
 * Added correct plugin URI to plugin meta
 * Improved WC 2.0 compat
 * Improved PHPDocs

2012.01.04 - version 1.0.4
 * WC 2.0 compat
 * Added several missing translatable strings
 * Improved efficiency on activaton task that was causing memory issues on stores with many products
 * Re-instated WCWL_SLUG

2012.12.04 - version 1.0.3
 * New updater

2012.12.03 - version 1.0.2
 * Fixed a bug that caused the mailto: value to be empty when emailing all users on a waitilist
 * Removed some debugging output that hadn\'t been cleaned up properly!
 * Removed WCWL_SLUG for codestyling localisation
 * WC future compat
 * Login URL switch to my account page

2012.11.08 - version 1.0.1
 * Fixed a bug that caused only products with an existing waitlist to be displayed when sorting by waitlist
 * Fixed a bug that caused no products to be displayed when sorting by waitlist on some installs
 * Refined waitlist custom column display to be more coherent with existing Admin UI
 * Added cleanup on uninstall

2012.10.01 - version 1.0
 * First Release
