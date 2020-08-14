=== YITH WooCommerce Recover Abandoned Cart  ===
Requires at least: 3.5.1
Tested up to: 5.4
Stable tag: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

YITH WooCommerce Recover Abandoned Cart helps you manage easily and efficiently all the abandoned carts of your customers.

== Description ==
Your customers often fill their carts and leave them: thanks to YITH WooCommerce Recover Abandoned Cart you will be able to contact them and remind what they were purchasing and invite them to complete their action.
Set the time span to consider a cart abandoned and customize a contact email that you can send to your customer: a direct contact to make them see what they were ready to purchase!


== Installation ==
Important: First of all, you have to download and activate WooCommerce plugin, which is mandatory for YITH WooCommerce Recover Abandoned Cart to be working.

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce Recover Abandoned Cart` from Plugins page.


= Configuration =
YITH WooCommerce Recover Abandoned Cart will add a new tab called "Abandoned Carts" in "YIT Plugins" menu item.
There, you will find all YITH plugins with quick access to plugin setting page.


== Changelog ==

= 1.4.4 -  Released on 26 May 2020 =
New: Support for WooCommerce 4.2
New: Added French translation
Update: Plugin framework
Dev: Added new filter "ywrac_cart_link_args"

= 1.4.3 -  Released on 24 April 2020 =
New: Support for WooCommerce 4.1
Tweak: Added WooCommerce Multicurrency (WOOMC) support
Tweak: Added button to reset Report in Dasboard Panel
Update: Plugin framework
Fix: Fixed function to delete the applied coupons

= 1.4.2 -  Released on 9 March 2020 =
New: Support for WordPress 5.4
New: Support for WooCommerce 4.0
Update: Plugin framework
Update: Language files

= 1.4.1 -  Released on 24 December 2019 =
* New: Support for WooCommerce 3.9
* Update: Plugin framework

= 1.4.0 - Released: November 26, 2019  =
Update: Plugin framework

= 1.3.9 - Released: October 30, 2019  =
Update: Plugin framework

= 1.3.8 - Released: October 24, 2019  =
New: Support for WordPress 5.3
New: Support for WooCommerce 3.8.0
Tweak: Integration with WooCommerce Multi Currency Premium (by VillaTheme)
Update: Plugin framework
Update: Italian language
Dev: New filter 'ywrac_skip_register_cart' to allow skip cart registration

= 1.3.7 - Released: August 01, 2019  =
New: Support for WooCommerce 3.7.0
Update: Plugin framework
Update: Italian language
Fix: Check there are more then one cart when send the email

= 1.3.6 - Released: May 29, 2019  =
Update: Plugin framework
Fix: Delete expired coupons

= 1.3.5 - Released: April 09, 2019  =
New: Support for WooCommerce 3.6.0 RC1
Update: Plugin framework
Update: Language files

= 1.3.4 - Released: March 12, 2019  =
Update: Plugin framework
Fix: Integration with YITH WooCommerce Subscription
Fix: Text fields and email sanitization

= 1.3.3 - Released: January 31, 2019  =
Update: Plugin framework
Fix: Cart template in metabox

= 1.3.2 - Released: December 05, 2018  =
New: Support for WordPress 5.0
Update: Plugin framework
Fix: Fixed data to send email
Fix: Fixed abandoned cart content metabox, issue with vat and wrong price
Fix: Wrong cart subtotal in the email
Dev: Added filter 'ywrac_get_coupon_code'

= 1.3.1 - Released: November 08, 2018  =
Update: Update Core Framework 3.0.36
Fix: Fixed price formatting
Update: Language files

= 1.3.0 - Released: November 06, 2018  =
Update: Plugin framework
Update: Language files
Fix: Fix possible fatal error in Abandoned Cart Details

= 1.2.9 - Released: October 23, 2018  =
Tweak: Now using taxes on cart-content.php template
Update: Plugin framework
Update: Language files

= 1.2.8 - Released: October 16, 2018  =
New: Support for WooCommerce 3.5 RC2
Update: Plugin framework
Fix: Fix Guest cart issue

= 1.2.7 - Released: September 26, 2018  =
Update: Plugin framework
Fix: Issue with YITH WooCommerce Deposits & Down Payments
Fix: Shop Manager Option

= 1.2.6 - Released: August 5, 2018  =
New: Support for WordPress 4.9.8
New: French translation (credits to Josselyn Jayant)
Tweak: Unsubscribe system
Tweak: Load css frontend only under condition
Tweak: Email content cart
Update: Plugin framework
Update: Language files

= 1.2.5 - Released: May 25, 2018  =
Tweak: Support for GDPR compliance
Update: Localization files
Update: Plugin framework
Fix: Show/hide privacy textarea option

= 1.2.4 - Released: May 24, 2018  =
Fix: Privacy message position

= 1.2.3 - Released: May 24, 2018  =
New: Support for WordPress 4.9.6
New: Support for WooCommerce 3.4.0
New: Support for GDPR compliance - Export personal data
New: Support for GDPR compliance - Erase personal data
Tweak: Wait an hour from an order creation before a new cart of customer is registered
Update: Plugin framework
Update: Localization files
Fix: Check if a coupon exists before create a new one
Fix: Session cart when a cart is recovered
Fix: Aelia compatibility

= 1.2.2 - Release on March 29, 2018 =
Update: Plugin framework
Fix: Multi currency Issue
Fix: Delete cart after that a customer completed an order
Fix: Default option value
Dev: Added filter 'ywrac_allow_current_user'

= 1.2.1 - Release on January 30, 2018 =
New: Support for WooCommerce 3.3.0 RC2
Update: Plugin framework
Fix: Coupon creation
Fix: Email subject

= 1.2.0 - Release on December 21, 2017 =
Update: Plugin framework
Dev: Added filter 'ywrac_get_timestamp_with_gtm'

= 1.1.9 - Release on November 29, 2017 =
New: Added search by customer in Cart Abandoned and Pending Orders Tabs
New: Add the existent order pending in the main counter of Pending Orders when the plugin is activated for the first time
Update: Localization files
Fix: Conflicts with Mandrill when an email is rejected
Fix: Multiple email sent for Pending Orders
Dev: Added filter 'ywrac_recurrence'

= 1.1.8 - Release on October 30, 2017 =
New: Dutch translation
Update: Localization files
Fix: Thumbnails of variations in recover cart email

= 1.1.7 - Release on October 10, 2017 =
New: Support for WooCommerce 3.2
Update: Localization files
Update: Plugin framework
Fix: Emails not sent

= 1.1.6 - Release on September 14, 2017 =
New: Spanish translation (Fernando Tellado)
New: Italian translation (A.Mercurio)
Update: Plugin framework
Fix: _emails_sent meta when using PHP 7.1

= 1.1.5 - Release on July 03, 2017 =
New: Support for WooCommerce 3.1
Update: Plugin framework

= 1.1.4 - Release on June 08, 2017 =
New: Support for WooCommerce 3.0.8
Update: Plugin framework
Fix: WPML email issue
Fix: Image size on cart list
Dev: New filter ywrac_template_content

= 1.1.3 - Release on April 26, 2017 =
New: Support for WooCommerce 3.0.4
Tweak: Improved the query to send email
Tweak: Phone number catch for guest
Update: Plugin framework
Fix: Display of thumbnails in some email clients
Fix: Emails for pending orders

= 1.1.2 - Release on April 13, 2017 =
Update: Plugin framework
Fix: Delete cart after an order is submitted

= 1.1.1 - Release on April 06, 2017 =
New: Option to enable the shop manager capabilities to the plugin options
Update: Plugin framework


= 1.1.0 - Release on March 29, 2017 =
New: Pending order options
New: Support for WooCommerce 3.0 RC 2
Dev: Filter 'ywrac_recover_cart_link' to change the recover cart link
Fix: format email test
Fix: Shortocode list in the Email Template editor
Fix: Admin Ajax Url
Update: Plugin framework


= 1.0.7 - Released on June 10, 2016 =
New: Compatibility with WooCommerce 2.6 RC1
Tweak: Encripted the url of cart
Fix: Cart change status method

= 1.0.6 - Released on May 04, 2016 =
Fix:  Compatibility with WooCommerce Currency Switcher

= 1.0.5 - Released on May 02, 2016 =
New: Compatibility with Wordpress 4.5.1
New: Compatibility with WooCommerce Currency Switcher
Tweak: List Table width in the administrator panel
Fix: Compatibility with YITH WooCommerce Email Templates Premium
Fix: Cancel Recover abandoned cart coupon after use

= 1.0.4 - Released on December 30, 2015 =
New: Support for WooCommerce 2.5 beta 3
Fix: Replaced time() with current_time() function
Fix: Send manual email in the detail page of Abandoned Cart item

= 1.0.3 - Released on December 9, 2015 =
New: Support for Wordpress 4.4
Fix: fixed removing abandoned cart for guest
Update: Changed Text Domain from 'ywrac' to 'yith-woocommerce-recover-abandoned-cart'
Update: Plugin Core Framework

= 1.0.2 - Released on December 1, 2015 =
New: Added phone number on cart abandoned
Update: Changed Text Domain from 'ywrac' to 'yith-woocommerce-recover-abandoned-cart'
Update: Plugin Core Framework
Fix: Removing abandoned cart for guest
Fix: Minor bugs


= 1.0.1 - Released on August 13, 2015 =
Fix: Cookie check in checkout page
Fix: Spaces in Email layout
New: Support for WooCommerce 2.4.2
Update: Plugin Core Framework

= 1.0.0 - Released: Julyy 30, 2015 =
Initial release

== Suggestions ==
If you have any suggestions concerning how to improve YITH WooCommerce Recover Abandoned Cart, you can [write to us](mailto:plugins@yithemes.com "Your Inspiration Themes"), so that we can improve YITH WooCommerce Recover Abandoned Cart.
