=== CHANGELOG ===

== Version 2.6.4 === Released on 14 July 2020

* New: Support for WooCommerce 4.3
* New: Integration with YITH Proteo style
* New: French translations
* Update: Plugin framework
* Fix: Issue on select endpoint icon

== Version 2.6.3 === Released on 01 June 2020

* New: Support for WooCommerce 4.2
* Update: Plugin framework
* Fix: Removed wrong YITH WooCommerce Account Funds endpoint
* Dev: New filter 'yith_wcmap_change_wishlist_view_params'

== Version 2.6.2 === Released on 04 May 2020

* New: Support for WooCommerce 4.1
* Update: Plugin framework

== Version 2.6.1 === Released on 15 April 2020

* Update: Plugin framework
* Fix: Missing "resend email to verify account" link on login error message

== Version 2.6.0 === Released on 09 March 2020

* New: Support for WooCommerce 4.0
* New: Support for WordPress 5.4
* New: Compatibility with YITH WooCommerce Booking
* New: Compatibility with YITH WooCommerce Account Funds
* Update: Plugin framework
* Update: Language files
* Fix: Avoid dashboard endpoint content from being empty

== Version 2.5.18 === Released on 27 December 2019

* New: Support for WooCommerce 3.9
* Update: Plugin framework
* Update: Spanish language

== Version 2.5.17 === Released on 19 December 2019

* New: Support to YITH WooCommerce Wishlist 3.0.4
* Update: Dutch language
* Fix: Typo for erase_avatar method

== Version 2.5.16 === Released on 29 November 2019

* Tweak: Improved RTL support
* Update: Italian language
* Update: Notice handler
* Update: Plugin framework
* Fix: Captcha does not work on registration form
* Dev: New filter 'yith_wcmnap_hide_default_endpoint_content'

== Version 2.5.15 === Released on 05 November 2019

* Tweak: prevent php warning when $options is not an array
* Update: Plugin framework

== Version 2.5.14 === Released on 30 October 2019

* Update: Plugin framework

== Version 2.5.13 === Released on 29 October 2019

* New: Support for WooCommerce 3.8
* New: Support for WordPress 5.3
* New: compatibility with YITH Advanced Refund System for WooCommerce Premium
* New: compatibility with WooCommerce API Manager plugin
* Tweak: z-index property for upload avatar popup
* Update: Plugin framework
* Update: Spanish language
* Fix: compatibility with WooCommerce Subscription (subscription details not accessible due to a fatal error)
* Fix: paragraph not rendered correctly in the my account content
* Fix: stripslashes content before print


== Version 2.5.12 === Released on 09 August 2019

* New: Support to WooCommerce 3.7.0
* Update: Plugin Core
* Update: Italian language

== Version 2.5.11 === Released on 26 June 2019

* New: Compatibility with Smart Coupons for WooCommerce by WebToffee
* New: Compatibility with TI WooCommerce Wishlist Plugin by TemplateInvaders
* Update: Plugin Core
* Update: Language files

=== Version 2.5.10 === Released on 10 June 2019

* New: Support to WooCommerce 3.6.4
* New: Translate also endpoint content using WPML String Translations
* Update: Plugin Core
* Update: Dutch language
* Dev: New filter yith_wcmap_filter_avatar_size

=== Version 2.5.9 === Released on 17 April 2019

* New: Support to WooCommerce 3.6.0
* New: Compatibility with YITH WooCommerce Membership. Now is possible show the endpoint in according to membership plan purchased by the customer
* Update: Plugin Core
* Update: Language files
* Fix: Added escape to the admin tooltips

=== Version 2.5.8 === Released on 02 April 2019

* New: Support to WooCommerce 3.6.0 RC1
* Update: Plugin Core

=== Version 2.5.7 === Released on 27 March 2019

* New: Support to WooCommerce 3.5.7
* Update: Plugin Core
* Update: Spanish language
* Tweak: Add recaptcha script only in my account page

=== Version 2.5.6 === Released on 19 Mar 2019

* Update: Plugin Core
* Fix: Avoid missing endpoints on plugin update from versions older then 2.3.0

=== Version 2.5.5 === Released on 12 March 2019

* New: Compatibility with YITH Stripe Connect for WooCommerce plugin
* Update: Dutch language file
* Fix: Compatibility issue with WooCommerce Subscription
* Fix: Missing type for plugin items
* Fix: Prevent redirect to same location
* Dev: New filter ywcmap_skip_verification

=== Version 2.5.4 === Released on 31 January 2019

* Update: Plugin Core
* Fix: Prevent redirect to dashboard in Customize section using Smart Email plugin

=== Version 2.5.3 === Released on 14 January 2019

* Update: Plugin Core
* Fix: Compatibility issue with WooCommerce Membership plugin

=== Version 2.5.2 === Released on 02 January 2019

* Fix: Custom endpoint content missing
* Fix: Compatibility issue with WooCommerce Subscriptions plugin
* Dev: New filter yith_wcmap_my_account_have_menu

=== Version 2.5.1 === Released on 31 December 2018

* New: Integration with WSDesk - WordPress Support Desk plugin (version 3.5.6)
* New: Support to WordPress 5.0.2
* Update: Plugin Core
* Update: Dutch translations
* Fix: Missing custom endpoints into "WooCommerce endpoints" metabox in Appearance > Menus
* Tweak: Changed the way how plugin get the default WooCommerce endpoints to prevent error with payment methods

=== Version 2.5.0 === Released on 12 December 2018

* New: Support to WooCommerce 3.5.2
* New: Support to WordPress 5.0.0
* New: Option to add Google reCaptcha (v2) to register form in my account
* New: Option to add the account email address verification on register
* New: Add an option to block register account to specific email domains
* New: Support to Teams for WooCommerce Memberships plugin (version 1.0.6)
* New: Integration with YITH PayPal Payout for WooCommerce
* Update: Plugin Core
* Tweak: Compatibility with YITH WooCommerce Gift Cards. Usage of default shortcode as content of the endpoint
* Fix: Remove additional endpoint printed by WooCommerce Subscription plugin

=== Version 2.4.0 === Released on 29 October 2018

* New: Support WooCommerce 3.5.0
* New: Support to Beaver Builder plugin
* Update: Plugin Core
* Update: Languages files
* Dev: Refactoring frontend class
* Tweak: Improve menu items manage for better performance

=== Version 2.3.5 === Released on 30 September 2018

* New: Compatibility with Elementor 2.2.4
* Fix: Layout issue with Avada Theme

=== Version 2.3.4 === Released on 27 September 2018

* Update: Plugin Core

=== Version 2.3.3 === Released on 21 September 2018

* Fix: Endpoint link not saved correctly when assigned to a group

=== Version 2.3.2 === Released on 19 September 2018

* New: Option to add additional classes to menu item
* Update: Plugin Core
* Update: Language files
* Update: Deprecated endpoints slug
* Fix: Notice for missing endpoint slug
* Fix: Remove deprecated endpoint "saved-cards" for YITH WooCommerce Stripe
* Dev: Refactoring of plugin's options and admin templates

=== Version 2.3.1 === Released on 04 September 2018

* Fix: WPML label and url translation for link

=== Version 2.3.0 === Released on 22 August 2018

* New: Support to WooCommerce 3.4.4
* New: Support to WordPress 4.9.8
* New: Compatibility with Polylang plugin
* New: Add external url link to my account menu
* New: Add icon on group item in menu
* Update: Plugin Core
* Update: Language files

=== Version 2.2.10 === Released on 30 May 2018

* New: Support to WooCommerce 3.4.1
* New: Russian translation
* Update: Spanish language
* Update: Plugin Core
* Fix: Function "yith_wcmap_endpoint_already_exists" return always true if value of item is zero

=== Version 2.2.9 === Released on 15 May 2018

* New: Support to WooCommerce 3.4 RC1
* New: General Data Protection Regulation (GDPR) compliance
* New: Polish translation (thanks to Igor Zborowski)
* Update: Language file
* Update: Plugin core
* Fix: Prevent incompatibility with page builder that encode []

=== Version 2.2.8 === Released on 27 February 2018

* New: Shortcode default_dashboard_content to print the default dashboard content
* New: Support to Elementor builder
* New: Support to WordPress 4.9.4
* New: Support to WooCommerce 3.3.3

=== Version 2.2.7 === Released on 02 February 2018

* New: Support to WooCommerce 3.3.0
* New: Support to WordPress 4.9.2
* Update: Plugin Core
* Update: Language Files
* Fix: Wrong text domain for some strings

=== Version 2.2.6 === Released on 28 December 2017

* New: Support to WooCommerce 3.2.6
* Update: Plugin Core

=== Version 2.2.5 === Released on 05 December 2017

* New: Support to WooCommerce 3.2.5
* New: Support to WordPress 4.9.1
* Update: Plugin Core
* Fix: Compatibility issue with WooCommerce Membership older then 1.9.0
* Fix: Missing strings in languages files

=== Version 2.2.4 === Released on 13 November 2017

* New: Support to WooCommerce 3.2.3
* New: Compatibility with YITH WooCommerce Gift Cards Premium
* Update: Plugin Core
* Fix: User roles multiselect on endpoint options
* Fix: Force a tab to be open if an endpoint inside is active

=== Version 2.2.3 === Released on 18 October 2017

* Update: Plugin Core
* Fix: Fontawesome icons list incomplete

=== Version 2.2.2 === Released on 16 October 2017

* New: Support to WooCommerce 3.2.1
* Update: Plugin Core
* Update: Fontawesome version to 4.7.0
* Fix: Compatibility issue with WooCommerce Membership version 1.9.0

=== Version 2.2.1 === Released on 27 September 2017

* New: Support to WooCommerce 3.1.2
* New: Support to WordPress 4.8.2
* New: Dutch translation
* New: German translation (thanks to Alexander Cekic)
* Update: Plugin Core
* Fix: Compatibility issue with WooCommerce Membership version 1.9.0

=== Version 2.2.0 === Released on 13 July 2017

* New: Support to WooCommerce 3.1.1
* New: New avatar upload popup
* Update: Plugin Core

=== Version 2.1.1 === Released on 02 July 2017

* New: Support to WooCommerce 3.1
* Fix: Using new WC templates orders.php and downloads.php instead of older
* Fix: Compatibility issue with plugin WooComposer
* Update: Plugin Core
* Dev: New filter yith_wcmap_default_endpoint for the default endpoint slug

=== Version 2.1.0 === Released on 03 March 2017

* New: Support to WooCommerce 2.7 RC 1
* New: Hungarian translation (thanks to Szabolcs Égerházi)
* Update: Plugin Core
* Update: Languages files

=== Version 2.0.1 === Released on 10 January 2017

* Fix: Option for tab visibility based on user roles

=== Version 2.0.0 === Released on 15 December 2016

* New: Support to WooCommerce 2.6.9
* New: Support to WordPress 4.7
* New: Button to reset default account avatar in avatar popup
* New: Hide endpoints based on user roles
* New: You can now create group of endpoints
* New: Integration with WooCommerce Membership
* New: Integration with YITH WooCommerce Membership
* New: Integration with YITH WooCommerce Subscription
* Update: Plugin Core
* Fix: Default endpoint option

=== Version 1.1.1 === Released on 17 August 2016

* New: Support to WooCommerce 2.6.4
* New: Support to WordPress 4.6
* New: Support to YITH WooCommerce Account Funds
* New: Spanish Translation
* Update: Plugin Core
* Fix: If the endpoint slug is changed, now the tab content loads correctly

=== Version 1.1.0 === Released on 14 June 2016

* New: Support to WooCommerce 2.6 RC2
* New: Support to WooCommerce Subscription plugin
* New: Italian Translation
* Update: Plugin Core

=== Version 1.0.9 === Released on 19 April 2016

* New: Compatibility with YITH WooCommerce Waiting List Premium
* Fix: Error undefined functions yit_wpml_string_translate and yit_wpml_register_string

=== Version 1.0.8 === Released on 14 April 2016

* New: Compatibility to Wordpress 4.5
* New: Added minimized js files. Plugin loads full files version if the constant "SCRIPT_DEBUG" is defined and is true
* Fix: Logout from my account sidebar does not work
* Fix: Default tab redirection

=== Version 1.0.7 === Released on 12 April 2016

* Update: Plugin Core
* Update: Language file
* Fix: WPML Language Switcher now get correct page link also with custom endpoints
* Fix: Compatibility issue with YITH WooCommerce Request a Quote
* Fix: Default Tab option

=== Version 1.0.6 === Released on 11 March 2016

* Update: Plugin Core
* New: Compatibility with YITH WooCommerce Request a Quote Premium
* Fix: Tab title translation with WPML String Translation

=== Version 1.0.5 === Released on 04 March 2016

* New: Compatibility with YITH Themes that already implements a sidebar on my-account
* Update: Language file .pot
* Update: Plugin Core
* Fix: Endpoint slug must be not empty
* Fix: When custom content for dashboard has been set "edit address" doesn't work anymore

=== Version 1.0.4 === Released on 04 February 2016

* New: Compatibility with YITH WooCommerce One Click Checkout
* New: Compatibility with YITH WooCommerce Stripe
* New: Compatibility with WPML
* New: Option for choose the default my account tab
* Update: Language file .pot
* Update: Plugin Core

=== Version 1.0.3 === Released on 11 January 2016

* New: Compatibility to WooCommerce 2.5 RC
* Update: Language file .pot
* Update: Plugin Core
* Fix: Avatar issue on woocommerce review section
* Fix: Avatar missing on admin wordpress settings

=== Version 1.0.2 === Released on 01 December 2015

* Fix: Missing stripslashes for custom endpoint content

=== Version 1.0.1 === Released on 01 December 2015

* Update: Plugin Core
* Fix: Custom content for dashboard endpoint overwrite other endpoints

=== Version 1.0.0 === Released on 10 November 2015

 * Initial Release