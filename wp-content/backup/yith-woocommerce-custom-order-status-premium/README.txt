=== YITH WooCommerce Custom Order Status ===

== Changelog ==

= 1.2.1 - Released on 21 May 2020 =

* New: support for WooCommerce 4.2
* Update: plugin framework
* Update: language files

= 1.2.0 - Released on 27 April 2020 =

* New: support for WooCommerce 4.1
* New: moved 'Order Statuses' menu into YITH > Custom Order Status panel
* Update: language files
* Update: plugin framework
* Fix: language issue when sending emails to customer with an admin account with a different language than the site one

= 1.1.19 - Released on 28 February 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: possibility to translate email heading and subject in combination with WPML
* Update: language files
* Update: plugin framework
* Fix: issues in combination with third party plugins
* Fix: issue when translating email custom message in combination with WPML
* Tweak: show shipping date using WordPress time format (integration with YITH WooCommerce Order Tracking)
* Dev: added yith_wccos_email_heading filter
* Dev: added yith_wccos_email_subject filter

= 1.1.18 - Released on 23 December 2019 =

* New: support for WooCommerce 3.9
* Update: plugin framework
* Update: language files
* Tweak: improved style
* Tweak: modified bulk edit status behavior, now it's WooCommerce that handles it

= 1.1.17 - Released on 7 November 2019 =

* Update: plugin framework

= 1.1.16 - Released on 30 October 2019 =

* Update: plugin framework

= 1.1.15 - Released on 28 October 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: panel and metabox style
* New: translate Email Custom Message for each custom order status through WPML
* Update: plugin framework

= 1.1.14 - Released on 1 August 2019 =

* New: support to WooCommerce 3.7
* New: support to YITH WooCommerce Order Tracking 1.5.7: you can use placeholders to show order tracking information in Custom Order Status emails
* Update: plugin framework
* Update: language files

= 1.1.13 - Released on 29 May 2019 =

* Update: plugin framework

= 1.1.12 - Released on 12 April 2019 =

* New: support to WooCommerce 3.6
* Fix: status icon and text for Multi Vendor suborders
* Fix: Multi Vendor integration: remove admin and customer emails as recipients in suborder emails
* Update: plugin framework
* Update: language files

= 1.1.11 - Released on 5 February 2019 =

* New: support to YITH WooCommerce Multi Vendor: possibility to send emails for custom statuses to Vendors
* Fix: issue when using plain emails for custom order status notifications
* Update: plugin framework
* Update: language files

= 1.1.10 - Released on 6 December 2018 =

* New:  support to WordPress 5.0
* New: set status as paid
* Fix: issue in combination with YITH WooCommerce Deposits and Down Payments
* Fix: order ID issue when sending multiple emails through bulk actions
* Update: plugin framework
* Update: language files
* Dev: added yith_wccos_email_trigger_args filter

= 1.1.9 - Released on 23 October 2018 =

* Update: Plugin Framework

= 1.1.8 - Released on 10 October 2018 =

* New: support to WooCommerce 3.5.x
* Fix: WPML status translation in order notes
* Tweak: show actions in orders by default
* Update: Plugin Framework

= 1.1.7 - Released on 31 May 2018 =

* New: support to WooCommerce 3.4.x
* New: support to WordPress 4.9.6
* New: enable/disable Custom Order Status management for Shop Manager
* New: send email to Administrator, Customer and Custom Email Address
* Fix: issue when saving custom order statuses
* Fix: issue when upgrading from free version
* Update: Italian language
* Update: Spanish language
* Update: Dutch language
* Update: plugin framework
* Dev: added yith_wcos_sent_to_admin_for_custom_recipient filter
* Dev: added yith_wccos_email_recipients filter

= 1.1.6 - Released on 30 January 2018 =

* New: support to WooCommerce 3.3.0 RC2
* New: WPML integration: now you can translate the Custom Order Status titles
* New: Dutch language
* Update: Plugin Framework
* Fix: show 'complete' action when processing if processing status is not customized
* Dev: added yith_wccos_metabox_options_params filter

= 1.1.5 - Released on 13 December 2017 =

* Fix: icons

= 1.1.4 - Released on 12 December 2017 =

* New: import custom statuses
* Update: Plugin Framework 3.0.0
* Tweak: replaced checkboxes with on-off buttons

= 1.1.3 - Released on 11 October 2017 =

* New: support to Support to WooCommerce 3.2.0 RC2

= 1.1.2 - Released on 28 September 2017 =

* Fix: issue when Next Actions is empty
* Fix: issue with 'Show always in Actions' option
* Tweak: changed Next Actions field to select2

= 1.1.1 - Released on 13 September 2017 =

* New: show information in Custom Order Status list table
* New: show a status in all Order Actions
* New: added custom order meta handling in email class; admin can now set placeholders for each meta of the order
* Fix: restore stock issue
* Fix: download permissions for custom order statuses
* Dev: added yith_wccos_add_all_custom_order_status_actions filter

= 1.1.0 - Released on 13 March 2017 =

* New: support to WooCommerce 2.7.0-RC1

= 1.0.21 - Released on 9 February 2017 =

* New: restore stock through custom status
* Fix: disable slug editing for overriding of WooCommerce statuses

= 1.0.20 - Released on 13 December 2016 =

* New: edit the custom order status slug
* Tweak: slug creation changed

= 1.0.19 - Released on 10 October 2016 =

* Fix: issue with custom payment status, which did not automatically switched to completed

= 1.0.18 - Released on 26 September 2016 =

* Fix: issue in combination with WooCommerce Pretty Emails
* Fix: text of WooCommerce order action buttons when the custom order status graphic style is set to "text"

= 1.0.17 - Released on 1 September 2016 =

* New: support to WooCommerce Pretty Emails

= 1.0.16 - Released on 30 August 2016 =

* Fix: "items purchased" number in WooCommerce reports

= 1.0.15 - Released on 5 August 2016 =

* New: Spanish language
* Fix: refunded orders in the report

= 1.0.14 - Released on 15 June 2016 =

* New: possibility to allow payment for a custom status
* New: support to WooCommerce 2.6
* New: Italian language
* Fix: enqueue style and script protocol
* Fix: bug with email for custom order statuses
* Fix: hidden deleted custom statuses
* Fix: prevent icon display issue
* Fix: icon css style

= 1.0.13 - Released on 10 March 2016 =

* Fix: frontend minor bugs

= 1.0.12 - Released on 3 March 2016 =

* Fix: order detail bug in email on change status by WooCommerce REST API
* Fix: bug on order list by status with WooCommerce REST API
* Fix: order status list with WooCommerce REST API

= 1.0.11 - Released on 1 March 2016 =

* Fix: email bug on change status by WooCommerce REST API

= 1.0.10 - Released on 22 February 2016 =

* Fix: Setting url doesn't work

= 1.0.9 - Released on 18 February 2016 =

* New: support to WooCommerce 2.5.2
* New: support to WordPress 4.4.2
* New: support to YITH WooCommerce Email Templates Premium
* Tweak: improved emails

= 1.0.8 - Released on 31 December 2015 =

* New: support to WooCommerce 2.5 BETA 3
* Tweak: fixed minor bug

= 1.0.7 - Released on 14 December 2015 =

* New: support to WordPress 4.4
* New: support to WooCommerce 2.4.12
* Tweak: fixed minor bug

= 1.0.6 - Released on 10 November 2015 =

* Tweak: improved the generation of the slug of order status

= 1.0.5 - Released on 28 October 2015 =

* Fix: minor bug

= 1.0.4 - Released on 19 October 2015 =

* Fix: minor bug

= 1.0.3 - Released on 14 September 2015 =

* Fix: minor bug

= 1.0.2 - Released on 18 August 2015 =

* New: Support to WooCommerce 2.4.4

= 1.0.1 - Released on 12 August 2015 =

* New: Support to WordPress 4.2.4
* New: Support to WooCommerce 2.4.2
* Fix: minor bug

= 1.0.0 - Released on 10 July 2015 =

* Initial release