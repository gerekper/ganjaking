=== YITH WooCommerce Multi-step Checkout Premium ===

Contributors: yithemes
Tags: woocommerce, multi-step checkout, yith, checkout
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Changelog ==

= 2.0.0 - Released on 01 July 2020 = 

* New: Support for WooCommerce 4.3
* New: Plugin options panel layout
* New: Checkout style
* New: Checkout style on mobile
* New: SVG Icons set
* New: Ability to choose a different style for mobile
* New: Ability to merge billing and shipping in a single step
* New: Ability to merge order info and payment in a single step
* New: Ability to show icons and step numbers for all steps
* New: Layout options for text style
* New: Option to save the checkout fields when users leaves the checkout page
* New: Choose to hide or set a default or a custom icon, to identify each step
* New: Set the checkout page width in relation to the width of the steps timeline
* Update: Plugin framework
* Update: Language files
* Fix: Minor layout issues with selectWoo library
* Fix: Unable to go to the next step if 'create account' option is unchecked
* Fix: Dashicons not available on frontend for not logged-in users
* Fix: Layout issues in Style 2 and Style 3 on mobile view
* Dev: yith_wcmv_have_mobile_timeline to check if current style have a dedicated mobile timeline
* Dev: yith_wcmv_max_mobile_width to filter the max-width value for mobile device

= 1.9.3 - Released on 28 May 2020 =

* New: Support for WooCommerce 4.2
* Update: Plugin framework
* Fix: Unable to validate the form with YITH WooCommerce Share for Discount enabled on checkout page

= 1.9.2 - Released on 30 April 2020 =

* New: Support for WooCommerce 4.1
* New: Support for Electro Theme
* Update: Plugin framework

= 1.9.1 - Released on 30 March 2020 =

* New: Support for YITH Proteo theme

= 1.9.0 - Released on 09 March 2020 =

* New: Support for WordPress 5.4
* New: Support for WooCommerce 4.0
* Fix: Unable to translate message shown to returning customers
* Update: Italian language

= 1.8.2 - Released on 23 December 2019 =

* New: Support for Twenty Twenty theme
* New: Support for WooCommerce 3.9
* Update: Plugin framework

= 1.8.1 - Released on 11 December 2019 =

* New: Option to hide "Back to cart" button in the last step
* Update: Plugin framework
* Update: Spanish language
* Update: Italian language

= 1.8.0 - Released on 28 October 2019 =

* New: Support for WordPress 5.3
* New: Support for WooCommerce 3.8
* Update: Plugin framework
* Update: All language files
* Removed: Enable plugin option

= 1.7.4 - Released on 10 October 2019 =

* New: Support for Porto Theme
* Dev: add hook yith_wcms_payment_step_section_title to change the Payment section label

= 1.7.3 - Released on 07 August 2019 =

* Tweak: Project structure refactoring

= 1.7.2 - Released on 01 August 2019 =

* New: Support for WooCommerce 3.7
* Update: Italian language
* Update: Plugin Core Framework

= 1.7.1 - Released on 15 June 2019 =

* Fix: Show coupon disappear in order info step
* Fix: Translation issue

= 1.7.0 - Released on 06 June 2019 =

* New: Option to change step separator in timeline text style
* New: Option to change returning customer message on login step
* New: Option to enable login step
* New: My Account style for login step
* Update: All languages file
* Tweak: Code refactoring
* Tweak: Disable navigation button if required fields is empty/not valid
* Fix: Various style issue

= 1.6.12 - Released on 29 May 2019 =

* Update: Italian language
* Dev: new filter 'yith_wcms_template_path_checkout_form'

= 1.6.11 - Released on 08 April 2019 =

* New: Support for YITH Multiple Shipping Addresses for WooCommerce Premium
* New: Support for GDPR plugin
* New: Support for WooCommerce 3.6

= 1.6.10 - Released on 24 January 2019 =

* Update: All language files
* Update: Plugin Framework

= 1.6.9 - Released on 24 December 2018 =

* New: Support for Astra theme

= 1.6.8 - Released on 05 December 2018 =

* New: Support for WordPress 5.0
* Fix: Ajax validation doesn't works for not valid emails

= 1.6.7 - Released on 23 October 2018 =

* Update: Plugin Core Framework

= 1.6.6 - Released on 09 October 2018 =

* New: Support for WooCommerce 3.5-RC1
* Update: Italian language
* Fix: Validation issue with Town, City and Province with WooCommerce 3.5
* Fix: Style prev and next button on Twenty Seventeen

= 1.6.5 - Released on 27 July 2018 =

* Update: Dutch translation
* Tweak: New plugin core framework
* Fix: Typo issue

= 1.6.4 - Released on 24 May 2018 =

* New: Privacy Policy Content Support
* Update: Italian language
* Fix: Wrong style for action button style on mobile device
* Fix: Live validation doesn't works fine with YITH WooCommerce Delivery Date Premium requried fields
* Dev: yith_wcms_remove_step hook to allow dev to remove a step in checkout process

= 1.6.3 - Released on 16 February 2018 =

* New: Support for YITH WooCommerce Points and Rewards Premium
* Tweak: Tested up to WooCommerce 3.3.1

= 1.6.2 - Released on 20 December 2017 =

* Update: Plugin core framework version 3
* Fix: Checkout fields validation doesn't works for not logged in users

= 1.6.1 - Released on 10 November 2017 =

* New: Enable the My Account login/register form in login step instead of "Returning Customer" form
* Tweak: Replace the jQuery.size() with the jQuery.length in frontend script
* Fix: ScrollTop doesn't works with latest WooCommerce version (3.2.x)

= 1.6.0 - Released on 10 October 2017 =

* New: Support for WooCommerce 3.2
* New: Add support for WooCommerce Amazon Payments plugin
* New: 100% Dutch translation
* Fix: Double timeslot box with Delivery Date Plugin

= 1.5.0 =

* Add: New option to remove shipping step from checkout
* Tweak: YITH WooCommerce Delivery Date Premium Support
* Fix: Order amount style on checkout page if the terms and conditions option is active
* Update: Language files
* Fix: Text-domain in thankyou.php template
* Fix : Wrong value for skip current value on multistep premium

= 1.4.5 =

* Tweak: Compatibility with Avada 5.2
* Dev: Added hook to yith_wcms_skip_shipping_method
* Dev: Added hook to yith_wcms_skip_payment_method

= 1.4.4 =

* Fix: thankyou.php template use deprecated method for $order object

= 1.4.3 =

* New: Option to set the scrollTop anchor
* New: Option to enable scrollTop features
* Fix: scrollTop doesn't works with latest WooCommerce update

= 1.4.2 =

* New: Back to cart button in checkout page
* Fix: Prevent to place an order if the user click Enter button

= 1.4.1 =

* Update: Language files
* Fix: Live fields validation doesn't works with additional fields added by YITH WooCommerce Checkout Manager
* Fix: Uncaught TypeError: jQuery.split is not a function

= 1.4.0 =

* New: Support to WooCoomerce 2.7-beta3
* New: Show order total amount in Payment tab
* New: Add support to WooCommerce checkout add-ons
* Tweak: New cookies management with js.cookie library
* Tweak: Live validation for radio button
* Fix: Date of birth validation with YITH WooCommerce Coupon Email System Premium plugin
* Fix: Customers can't use points for discount with WooCommerce Points and Rewards
* Fix: Ajax validation trigger blur event on skip login step
* Fix: Unable to validate extra fields added by WooCommerce Checkout Field Editor Pro

= 1.3.12 =

* Added: Support to WooCommerce Points ad Rewards
* Fixed: Vertical style doesn't works with WooCommerce points and rewards plugin
* Fixed: yith_wcms_use_cookie hook doesn't works

= 1.3.11 =

* Added: Support to WordPress 4.7
* Fixed: Unable to deactivate free version

= 1.3.10 =

* Added: Support to The Retailer theme
* Added: Support to Storefront theme
* Added: Payment section header
* Added: German language 21% (only frontend) by Rolf
* Fixed: Removed duplicate alt class in next/prev button

= 1.3.9 =

* Added: Support to Avada 5
* Added: Support to WooCommerce SecureSubmit Payment Gateway plugin
* Added: Support to WooCommerce CurabillCw Payment Gateway plugin
* Added: Support to WooCommerce PostFinanceCw Payment Gateway plugin
* Added: Support to WooCommerce BarclaycardCw Payment Gateway plugin
* Added: Option to change the label for next button in login step
* Tweak: Prevent duplicate payments id in for-checkout php DOM
* Fixed: Missing $checkout object in woocommerce_before_checkout_shipping_form action
* Fixed: Empty Shipping tab
* Fixed: Empty Order review and Payment tab on Avada 5.0.3

= 1.3.8 =

* Added: Option to set fadeIn/fadeOut transition
* Tweak: Add support to deprecated method WC()->cart->get_checkout_url()
* Tweak: Removed old options record
* Added: yith_wcms_form_checkout_login_message hook for return customer message on login step
* Fixed: No address filled in after login at step
* Fixed: Skip billing step with ajax live validaton if user click on timeline

= 1.3.7 =

* Added: Italian and Spanish language files available
* Fixed: Delete step and form cookie after order complete

= 1.3.6 =

* Added: Support to WooCommerce Payments Discounts
* Fixed: Timeline issue with iPhone
* Fixed: Payments doesn't show if multistep checkout are deactivated
* Fixed: Payments box disappears after select a payment type
* Fixed: Payments box loop reload issue after select a payment type

= 1.3.5 =

* Added: Support to Avada theme Version 4.0.x
* Added: Support to WooCommerce checkout add-ons
* Tweak: Code revision for increase performance
* Fixed: Timeline Style issue on mobile device
* Fixed: Blank screen on Payment tab
* Fixed: Blank screen on Payment tab with some payment gateway
* Fixed: Previous button appears after click on next step if "Display returning customer login reminder on the "Checkout" page" option is disabled

= 1.3.4 =

* Fixed: Billing address doesn't show for logged in users

= 1.3.3 =

* Fixed: Billing address are shown in login step

= 1.3.2 =

* Added: Support to WooCommerce 2.6-beta-2
* Fixed: Unable to click on next step wityh guest checkout enabled
* Fixed: Unable to remove returning customer login reminder on the "Checkout" page

= 1.3.1 =

* Fixed: Users can't login in checkout

= 1.3.0 =

* Added: Support to YITH WooCommerce Gift Cards
* Added: Support to YITH WooCommerce Customize My Account Page Premium
* Tweak: Disabled timeline if a live validation is enabled
* Tweak: Cached javascript file doesn't reload after plugin update
* Fixed: Translation problem in login form
* Fixed: Return to current step after page refresh or apply a coupon or a gift cards

= 1.2.1 =

* Updated: Language files
* Fixed: Unable to set timeline with WordPress 4.5

= 1.2.0 =

* Added: Support to WooCommerce Ship to Multiple Addresses
* Fixed: Duplicated coupon box with Avada Theme
* Fixed: Unable to go to next step for not logged in users with Chrome browser

= 1.1.3 =

* Added: yith_wcms_load_checkout_template_from_plugin hook to enable main template overriding by theme
* Added: Support to WooCommerce Gateway Stripe plugin
* Fixed: Place order button text doesn't work

= 1.1.2 =

* Fixed: Checkout as guest doesn't work

= 1.1.1 =

* Updated: Plugin core framework

= 1.1.0 =

* Added: Support to WooCommerce 2.5-RC1
* Added: Support to WordPress 4.4.1
* Added: wpml-config.xml file for WPML Support
* Added: Disable previous button in last step
* Tweak: Plugin core framework
* Updated: All language files

= 1.0.8 =

* Tweak: Required checkout fields get correct class from localize script
* Updated: Text domain from yith_wcms to yith-woocommerce-multi-step-checkout

= 1.0.7 =

* Fixed: issue in paying old unpaid orders

= 1.0.6 =

* Added: Partial Spanish translation (by Daniel Aparisi)
* Tweak: Performance improved with new plugin core 2.0

= 1.0.5 =

* Fixed: Fatal error on form-checkout.php template if overwrite by theme

= 1.0.4 =

* Added: Italian translation (By Lidia Cirrone)
* Fixed: jQuery Issue With Payment Methods

= 1.0.3 =

* Added: Support to WooCommerce 2.4

= 1.0.2 =

* Fixed: Warning on checkout login page

= 1.0.1 =

* Initial release
