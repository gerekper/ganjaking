=== YITH WooCommerce Mailchimp ===

Contributors: yithemes
Tags: mailchimp, woocommerce, checkout, themes, yit, e-commerce, shop, newsletter, subscribe, subscription, marketing, signup, order, email, mailchimp for wordpress, mailchimp for wp, mailchimp signup, mailchimp subscribe, newsletter, newsletter subscribe, newsletter checkbox, double optin
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 2.1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://yithemes.com/docs-plugins/yith-woocommerce-mailchimp

== Changelog ==

= 2.1.7 - Released on 11 June 2020 =

* New: support for WooCommerce 4.2
* Update: plugin framework
* Dev: added yith_wcmc_subscribe_on_completed filter
* Dev: added yith_wcmc_register_mailchimp_groups_registration_form filter
* Dev: added yith_wcmc_handle_checkout_manager_custom_fields filter

= 2.1.6 - Released on 12 May 2020 =

* New: support for WooCommerce 4.1
* Update: plugin framework
* Update: Italian language
* Tweak: improved WPML support for field placeholder translations
* Fix: prevent notice on advanced integration items
* Fix: prevent notice when printing "User preferences" metabox

= 2.1.5 - Released on 12 March 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: added Elementor widget
* Update: Spanish language
* Update: plugin framework
* Tweak: refactored store class to allow dev remove unwanted post_types from the sync
* Tweak: add privacy label to WPML config
* Fix: allow admin to pick up variations in advanced settings
* Fix: prevent notice when syncing cart on MC
* Dev: added yith_wcmc_supported_post_type_to_sync filter
* Dev: added filter yith_wcmc_process_item, to programmatically skip processing for some store items

= 2.1.4 - Released on 24 December 2019 =

* New: support for WooCommerce 3.9
* New: added support for address type fields at checkout
* Update: plugin framework

= 2.1.3 - Released on 06 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* Tweak: delete store transients when deleting store, even if API call fails
* Tweak: avoid using SELECT *
* Update: Plugin framework
* Fix: preventing fatal error when it is not possibile to retrieve product object from order item
* Fix: improved check on campaign data before sending a registered order to MailChimp
* Fix: changed hook to avoid ajax call and duplicate checkbox on checkout page
* Fix: issue with persistent cart (undefined index line_total | line_subtotal)

= 2.1.2 - Released on 09 August 2019 =

* New: WooCommerce 3.7.0 RC2 support
* New: integration with Checkout Manager field
* Tweak: now carts are registered even if customer does not have a billing email
* Update: Italian language
* Updated dutch language
* Fix: subscribe interests groups in the shortcode
* Fix: issue with custom option saving & printing on WC panels with WC 3.7
* Dev: new action yith_wcmc_register_customer_subscribed_list
* Dev: new action yith_wcmc_user_subscribed_in_form_subscribe
* Dev: new param to action yith_wcmc_user_subscribed_in_form_subscribe
* Dev: new added 'yith_wcmc_get_checkout_fields' filter.

= 2.1.1 - Released on 29 May 2019 =
* New: Support to WordPress 5.2
* Update: Plugin framework
* Tweak: Remove subscribers permanently when privacy erase process is triggered

= 2.1.0 - Released on 10 April 2019 =

* New: WooCommerce 3.6 support
* New: added option to subscribe users on registration
* Tweak: added new admin-text 'yith_wcmc_shortcode_success_message' in wpml-config
* Tweak: improved Mailchimp class inclusion
* Update: internal plugin framework
* Update: Italian language
* Update: Spanish language
* Update: Dutch language
* Fix: form not shown when no merge-var defined for the list
* Fix: fatal error caused by missing WC_Background_Process in older versions of WC

= 2.0.0 - Released on 07 February 2019 =

* New: WooCommerce 3.5.4 support
* New: support to MailChimp API 3.0
* New: Store integration
* New: sync procedure, to register existing customers and orders to MailChimp
* New: Gutenberg block for subscription form shortcode
* Update: internal plugin framework
* Tweak: reviewed method that performs API calls
* Fix: "at least one in cart" condition, not working as expected
* Dev: added filter yith_wcmc_privacy_policy_shortcode_label for privacy policy checkbox label
* Dev: added yith_wcmc_process_order_args filter
* Dev: added yith_wcmc_process_product_args filter
* Dev: added yith_wcmc_process_product_variant_args filter
* Dev: added yith_wcmc_process_promo_rule_args filter
* Dev: added yith_wcmc_process_cart_args filter
* Dev: added yith_wcmc_process_customer_history filter

= 1.1.5 - Released on 24 October 2018 =

* New: WooCommerce 3.5 support
* Tweak: updated plugin framework

= 1.1.4 - Released on 16 October 2018 =

* New: WooCommerce 3.5-rc support
* New: WordPress 4.9.8 support
* Tweak: updated plugin framework
* Update: Italian language
* Fix: possible Fatal Error during checkout
* Fix: missing space in email field

= 1.1.3 - Released on 28 May 2018 =

* New: WooCommerce 3.4 compatibility
* New: WordPress 4.9.6 compatibility
* New: updated plugin framework
* New: GDPR compliance
* Tweak: added check over user capability before registering Dashboard Widget
* Update: Italian language
* Fix: preventing possible Fatal Error: Call to undefined function wc_get_chosen_shipping_method_ids()

= 1.1.2 - Released on 01 February 2018 =

* New: WooCommerce 3.3.0 support
* New: update internal plugin-fw
* New: added Dutch translation
* Tweak: improved performance of code that prints shortcode

= 1.1.1 - Released on 25 October 2017 =

* New: WooCommerce 3.2.1 support
* New: WordPress 4.8.2 support
* New: update internal plugin-fw
* Tweak: added check over wc_get_notices existence
* Tweak: avoided double form handler execution, adding return false at the end of handler
* Dev: created subscribe wrapper for subscription process and refactored code
* Dev: moved cachable requests init to init hook, to let third party code filter them

= 1.1.0 - Released on 05 May 2017 =

* Add: WooCommerce 3.0.x compatibility
* Add: WordPress 4.7.4 compatibility
* Tweak: hidden being emptied when form is clened
* Dev: added yith_wcmc_use_placeholders_instead_of_labels filter, to let use placeholders instead of labels for fields & groups in subscription form (where applicable)

= 1.0.10 - Released on 28 November 2016 =

* Add: empty all form fields on successful subscription
* Add: spanish translation
* Tweak: changed text domain to yith-woocommerce-mailchimp
* Tweak: updated plugin-fw version

= 1.0.9 - Released on 13 June 2016 =

* Added: WooCommerce 2.6-RC1 support
* Added: capability for the admin to export to MailChimp Waiting Lists (require YITH WooCommerce Waiting Lists Premium installed)
* Added: option to set MailChimp field where product waiting for slugs should be exported
* Added: capability for the admin to export Waiting Lists via CSV (require YITH WooCommerce Waiting Lists Premium installed)
* Added: trigger yith_wcmc_form_subscription_result after ajax call success
* Tweak: changed sanitize function to let users enter html code in success message
* Tweak: added check over group data, before calling wp_list_pluck

= 1.0.8 - Released on 26 April 2016 =

* Added: check to avoid warning when MailChimp returns status failure on group retrieving
* Fixed: Warning related to missing check over formatted data structure in plugin option

= 1.0.7 - Released on 12 April 2016 =

* Added: WooCommerce 2.5.5 compatibility
* Added: WordPress 4.5 compatibility
* Added: capability for admins to select groups to prompt on frontend for the the user to choose among
* Added: action yith_wcmc_after_subscription_form_title
* Added: yith_wcmc_after_subscription_form_notice action in mailchimp-subscription-form.php template
* Tweak: Updated internal plugin-fw
* Fixed: Changed lists/list request, to get all available lists, and not only first page
* Fixed: error with interests groups containing commas in their names
* Fixed: typo in batch-subscribe request (export to MailChimp)
* Fixed: checkout checkbox position option, causing unexpected results
* Fixed: custom css not working for widget
* Fixed: widget class

= 1.0.6 - Released on 14 December 2015 =

* Added: option to hide form after successful registration, in shortcodes and widgets
* Added: options to customize success message, in shortcodes and widgets
* Added: check over MailChimp class existence, to avoid Fatal Error with other plugins including that class
* Added: MailChimp error translation via .po archives
* Tweak: improved plugin import procedure
* Tweak: Updated internal plugin-fw

= 1.0.5 - Released on 23 October 2015 =

* Tweak: Performance improved with new plugin core 2.0
* Fixed: eCommerce 360 campaign data workflow

= 1.0.4 - Released on 10 September 2015 =

* Fixed: WC general notices print in MailChimp widget / shortcode
* Fixed: Missing file on activation

= 1.0.3 - Released on 12 August 2015 =

* Added: Compatibility with WC 2.4.2
* Tweak: Updated internal plugin-fw
* Tweak: Improved wpml compatibility
* Tweak: Removed class row from subscription form
* Tweak: Removed un-needed nl from types templates
* Fixed: Removed call to deprecated $this->WP_Widget method
* Fixed: added nopriv ajax handling for form subscription
* Removed: control on "show" flag for fields

= 1.0.2 - Released on 04 May 2015 =

* Added: WP 4.2.1 support
* Fixed: "Plugin Documentation" appearing on all plugins
* Fixed: various minor bug

= 1.0.1 =

* Initial release