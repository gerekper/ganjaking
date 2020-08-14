== CHANGELOG ==

= 1.5.11 = Released on 03 June 2020

* New: Support for WooCommerce 4.2
* Update: Plugin framework

= 1.5.10 = Released on 06 May 2020

* New: Support for WooCommerce 4.1
* New: Support for WordPress 5.4.1
* Update: Plugin framework

= 1.5.9 = Released on 10 March 2020

* New: Support for WooCommerce 4.0
* New: Support for WordPress 5.4
* Update: Plugin framework

= 1.5.8 = Released on 24 December 2019

* New: Support for WooCommerce 3.9
* New: Support for WordPress 5.3.2
* Update: Plugin framework
* Fix: Wrong shortcode attribute name for category ids

= 1.5.7 = Released on 29 November 2019

* New: Added param 'category' to most viewed shortcode. It will allow you show most viewed products by category
* Update: Notice handler
* Update: Plugin framework

= 1.5.6 = Released on 05 November 2019

* Update: Plugin framework

= 1.5.5 = Released on 30 October 2019

* Update: Plugin framework

= 1.5.4 = Released on 28 October 2019

* New: Support for WooCommerce 3.8
* New: Support for WordPress 5.3
* Update: Plugin framework
* Fix: preventing fatal error under certain circumstances, when wc_print_notices is not defined
* Fix: does not show section with empty title
* Dev: new filter 'yith_wrvp_widget_product_list'
* Dev: new filter 'yith_wrvp_coupon_individual_use'
* Dev: new filter 'yith_wrvp_force_slider_view'

= 1.5.3 = Released on 06 August 2019

* New: Support to WooCommerce 3.7.0 RC
* Update: Plugin Core
* Update: Italian language
* Fix: Removed useless option from plugin email settings

= 1.5.2 = Released on 10 June 2019

* New: Support to WooCommerce 3.6.4
* Update: Plugin Core
* Fix: Undefined object $current_email when used in combination with YITH WooCommerce Email Templates
* Fix: Missed email unsubscribe notice also for guest customers
* Fix: Prevent issue in case of multiple sliders inside the same page
* Tweak: Suspended cache invalidation when tracking global product views

= 1.5.1 = Released on 09 April 2019

* New: Support to WooCommerce 3.6.0 RC1
* Update: Plugin Core
* Update: Spanish language
* Update: Dutch language
* Fix: Do not show shortcode frontend notice if option is empty
* Fix: Removed outdated visibility condition from main query

= 1.5.0 = Released on 05 March 2019

* New: Support to WordPress 5.1
* New: Support to WooCommerce 3.5.5
* New: New shortcode [yith_most_viewed_products] to show a list of globally most viewed products
* New: Option to order products by date of view
* Update: Plugin Core
* Update: Languages files
* Fix: Products list must be always an array
* Fix: Use WC meta wc_last_active instead of the plugin dedicated meta
* Dev: Filter "yith_wrvp_track_product_views" to avoid product views count
* Tweak: Improved usability for "Create Shortcode" setting's tab

= 1.4.7 = Released on 30 January 2019

* New: Support to WooCommerce 3.5.4
* Update: Plugin Core
* Update: Dutch translation
* Fix: Avoid duplicated products in plugin email

= 1.4.6 = Released on 19 December 2018

* Update: Plugin Core
* Update: Slick slider script to version 1.9.0

= 1.4.5 = Released on 06 December 2018

* New: Support to WooCommerce 3.5.2
* New: Support to WordPress 5.0.0
* New: Support to Gutenberg
* Update: Plugin Core
* Update: Languages files
* Update: Dutch translation

= 1.4.4 = Released on 26 October 2018

* New: Support to WooCommerce 3.5.0
* Update: Plugin Core
* Update: Languages files
* Fix: Force coupon expire date to integer before pass it to date function

= 1.4.3 = Released on 26 September 2018

* New: Support to WooCommerce 3.4.5
* Update: Plugin Core
* Update: Italian language
* Update: Dutch language

= 1.4.2 = Released on 23 August 2018

* New: Support to WooCommerce 3.4.4
* New: Support to WordPress 4.9.8
* New: Now is possible to choose to use an existing coupon code for email instead of create it
* Update: Plugin Core
* Update: Language files
* Fix: Add version to scripts and styles
* Fix: Compatibility fix with older WooCommerce version ( older then 3.0 )
* Tweak: Changed cron time scheduled from once a day to hourly
* Dev: New class YITH_WRVP_Helper used for common methods
* Dev: Changed cron name from 'mail_action_schedule' to 'yith_wrvp_mail_action_schedule'

= 1.4.1 = Released on 31 May 2018

* New: Support to WooCommerce 3.4.1
* New: Privacy Policy DPA
* Update: Plugin Core
* Update: Spanish translation

= 1.4.0 = Released on 15 May 2018

* New: Support to WooCommerce 3.4 RC1
* New: General Data Protection Regulation (GDPR) compliance
* New: Italian translation
* New: Spanish translation
* Update: Plugin Core
* Update: Language files
* Dev: New filter yith_wrvp_templates_query_args for shortcode template args

= 1.3.0 = Released on 31 January 2018

* New: Support to WooCommerce 3.3.0
* New: Support to WordPress 4.9.2
* New: Dutch translation
* Update: Plugin Core
* Update: Language file
* Fix: Slider responsive
* Fix: Load frontend classes only when needed

= 1.2.0 = Released on 12 October 2017

* New: Support to WooCommerce 3.2.0
* New: Support to WordPress 4.8.2
* New: Add widget option for order products by "Viewed Order" or "Newest"
* New: Option to show/hide free products from Recently Viewed products
* Update: Plugin Core
* Update: Language file
* Fix: Products image on email do not display correctly
* Tweak: Added "Products selector" to localized variables for Recently Viewed script
* Dev: added filter yith_wrvp_products_selector to let third party developers change default products selector

= 1.1.0 = Released on 04 April 2017

* New: Support to WooCommerce 3.0.0
* New: Support to WordPress 4.7.3
* Update: Plugin Core
* Update: Language file
* Fix: Missing Autoplay Speed param on products slider
* Dev: Add filter "yith_wrvp_coupon_code_image_email" for filter coupon image on email
* Dev: Add filter "yith_wrvp_coupon_code_html_email" for filter coupon code html on email
* Dev: Add filter "yith_wrvp_unsubscribe_link_url" for filter unsubscribe link return url

= 1.0.4 = Released on 16 February 2016

* New: Integration of Google Analytics campaign for email link
* New: Option for choose to show the shortcode on single product page
* Update: Plugin Core
* Update: Language file

= 1.0.3 = Released on 05 February 2016

* New: Compatibility with YITH WooCommerce Email Template Premium
* Update: Plugin Core

= 1.0.2 = Released on 14 January 2016

* New: Compatibility with WooCommerce 2.5 RC
* Update: Plugin Core
* Update: Language file
* Fix: Responsive for products slider

= 1.0.1 = Released on 30 November 2015

* New: Link in email for unsubscribe to mailing list
* New: Track visit with IP for no logged in customer
* New: Option for get similar products by tags, categories ot both
* New: Specify View All link url instead of standard page link
* Update: Plugin Core
* Update: Language file
* Fix: Scheduled emails are sent multiple times to customer

= 1.0.0 = Released on 13 October 2015

* Initial release