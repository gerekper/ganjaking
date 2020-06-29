=== YITH WooCommerce Affiliates ===

Contributors: yithemes
Tags:  affiliate, affiliate marketing, affiliate plugin, affiliate tool, affiliates, woocommerce affiliates, woocommerce referral, lead, link, marketing, money, partner, referral, referral links, referrer, sales, woocommerce, wp e-commerce, affiliate campaign, affiliate marketing, affiliate plugin, affiliate program, affiliate software, affiliate tool, track affiliates, tracking, affiliates manager, yit, yith, yithemes, yit affiliates, yith affiliates, yithemes affiliates
Requires at least: 4.0.0
Tested up to: 5.4
Stable tag: 1.7.2
License: GPLv2 or later
Documentation: https://yithemes.com/docs-plugins/yith-woocommerce-affiliates

== Changelog ==

= 1.7.2 - Released on 08 May 2020 =

* New: support for WooCommerce 4.1
* Update: plugin framework
* Tweak: removed max attribute from payment threshold field
* Tweak: hotfix paypal return url, to set back affiliate cookie when getting back to site after cancelling order
* Fix: removed translation on screen id, that was causing missing assets on admin on non-english sites
* Dev: added yith_wcaf_withdraw_amount_allow_exceeding_max filter

= 1.7.1 - Released on 20 April 2020 =

* New: added list of associated users to affiliate detail screen
* Update: plugin framework
* Update: Italian language
* Tweak: moved script localization just after script registration
* Tweak: minor improvements to frontend layouts, for better theme integration
* Tweak: removed not-pertinent CSS rules (this styling should be demanded by theme)
* Tweak: changed all doubleval to floatval function
* Tweak: added affiliate dashboard shortcode as gutenberg block on brand new Dashboard page
* Fix: fixed escaped labels of Term and Conditions (changed to wp_kses)
* Dev: added yith_wcaf_check_affiliate_validation_error filter

= 1.7.0 - Released on 09 March 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: Greek translation
* New: added option to set up affiliates cookie via AJAX call (to better work with cache systems)
* New: added Elementor widgets
* Tweak: included variations in Excluded products field
* Tweak: include commissions and commissions history metaboxes into WC Subscription edit page
* Tweak: code reformat and improvements for PHPCS
* Update: plugin framework
* Fix: pending commission email for admin is not sent if the commission amount is zero or not exists
* Fix: removed duplicated id from form-referrer box
* Dev: added new filter yith_wcaf_customer_status_change_dashboard_url
* Dev: added new filter yith_wcaf_show_message_wc_print_notice

= 1.6.9 – Released on 23 December 2019 =

* New: support for WooCommerce 3.9
* Update: plugin framework
* Update: Italian language
* Update: Greek language
* Update: Dutch language
* Fix: system not recognizing correct value for "Pay only commission older than" option
* Dev: added yith_wcaf_website_type filter

= 1.6.8 - Released on 12 December 2019 =

* New: added link generator on Affiliate details page, on backend
* Update: Greek translation
* Update: plugin framework

= 1.6.6 – Released on 29 November 2019 =

* New: added category column to commissions table and commissions CSV file
* Tweak: check if dependencies are registered in order to prevent error in gutenberg pages
* Update: Italian language
* Update: notice handler
* Update: plugin framework
* Fix: prevent warning when global $post do not contain WP_Post object

= 1.6.5 - Released on 06 November 2019 =

* Tweak: changed Fontello class names to avoid conflicts with themes
* Tweak: added checks before Fontello style inclusion, to load it just when needed

= 1.6.4 – Released on 05 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: Added affiliates export as CSV feature
* New: Added social sharing for referral link
* Update: Italian language
* Update: Spanish language
* Update: Dutch language
* Tweak: allow showing affiliate menu on coupons section
* Tweak: added cache for commission status count
* Tweak: reviewed endpoint handling to prevent 404 errors on coupon section when it is hidden to affiliates that do not have coupons
* Tweak: optimized has_unpaid_commissions method
* Tweak: optimized affiliates per_status_count, using wp_cache
* Fix: notices related to missing variables, or unhandled exception return values
* Fix: issue with ban & reject affiliate bulk actions
* Fix: fixed user edit link and avatar image from rates table
* Fix: reset button not appearing on commission page when filtering by status
* Fix: exclude trashed commissions from commission count on the commission page
* Dev: added new filter yith_wcaf_process_become_an_affiliate_request_correctly
* Dev: added new filter yith_wcaf_ipn_listener_force_ssl_v4 and changed force_ssl_v4 of IPN listener to false
* Dev: added new filter yith_wcaf_link_generator_generated_url
* Dev: added new filter yith_wcaf_show_dashboard_links_withdraw for withdraw template to show menu items
* Dev: added new filter yith_wcaf_display_symbol
* Dev: added new action yith_wcaf_process_checkout_with_affiliate

= 1.6.3 - Released on 09 August 2019 =

* New: WooCommerce 3.7.0 RC2 support
* New: added integration with WC Subscription
* Tweak: added coupon meta data as placeholders for new affiliate coupon email
* Tweak: changed doubleval for floatval
* Tweak: regenerate invoice now saves submitted values as invoice profile
* Tweak: using publishable url for download invoice on backend
* Update: internal plugin framework
* Update: Italian language
* Fix: new condition to accept Terms & Conditions in withdraw panel
* Fix: array to string conversion when regenerating affiliate invoice
* Fix: allow copy button from iphone/ipad
* Fix: wrong value was used in affiliate selection dropdown, on user edit page, after saving a user as Associated affiliate
* Dev: added new filter yith_wcaf_withdraw_valid_payment_email for payment email of withdraw tab
* Dev: added new filter yith_wcaf_become_an_affiliate_check
* Dev: added new filter yith_wcaf_show_coupon_section
* Dev: added new filter yith_wcaf_check_affiliate_val_error
* Dev: added new filter yith_wcaf_check_affiliate_val_error_premium
* Dev: added new filter yith_wcaf_dashboard_navigation_menu
* Dev: added new action yith_wcaf_process_withdraw_request
* Dev: added new action yith_wcaf_referrer_set

= 1.6.2 - Released on 31 May 2019 =

* Fix: added missing plugin-upgrade directory

= 1.6.1 - Released on 29 May 2019 =

* New: Switch to Cancelled bulk action for payments
* Tweak: improved uninstall procedure
* Tweak: rel nofollow to anchors with query strings
* Tweak: improved click handling for users that does not have yith_wcaf_click_enabled option registered in db
* Update: .pot file
* Update: Dutch version
* Update: plugin-fw
* Fix problem translation on dashboard-withdraw template
* Fix: affiliates not being auto-enaled after registration
* Fix: minimum withdraw conditions
* Dev: New action 'yith_wcaf_after_set_cookie'
* Dev: filter yith_wcaf_payment_table_column_default
* Dev filter yith_wcaf_payments_table_get_columns
* Dev: Fixed action 'yith_wcaf_after_set_cookie'
* Dev: Added new parameters in do_action 'woocommerce_email_header' and 'woocommerce_email_footer' for new affiliate email

= 1.6.0 - Released on 03 April 2019 =

* New: WooCommerce 3.6.0 RC1 support
* New: admin can now disable Click handling
* New: current affiliate shortcode
* New: admin can now regenerate invoice for affiliates
* New: admin can now assign coupons to affiliates
* New: affiliates can receive commissions by coupons
* New: email sent when admin assign a coupon to an affiliate
* Update: internal plugin framework
* Update: Spanish language
* Tweak: improved withdraw handling
* Fix: change Generate Link URL in customer emails
* Fix: default value for new_status and old_status in email class
* Fix: billing country on invoices
* Fix: fixed issue with hidden sections (generate link not removed from affiliate dashboard menu)
* Fix: DB error on backend
* Dev: added new filter yith_wcaf_prepare_items_commissions

= 1.5.1 - Released on 31 January 2019 =

* New: WooCommerce 3.5.3 support
* Tweak: replacing state code with state name when available in invoices
* Update: Spanish translation
* Update: internal plugin framework
* Fix: totals shown in affiliate details page
* Fix: prevent fatal error Can't use method return value in write context
* Dev: added yith_wcaf_email_currency to let third party code filter currencies showed plugin emails
* Dev: added do action yith_wcaf_refeal_totals_table
* Dev: added filter yith_wcaf_add_affiliate_role

= 1.5.0 - Released on 12 December 2018 =

* New: support to WordPress 5.0
* New: support to WooCommerce 3.5.2
* New: Gutenberg block for yith_wcaf_registration_form shortcode
* New: Gutenberg block for yith_wcaf_affiliate_dashboard shortcode
* New: Gutenberg block for yith_wcaf_link_generator shortcode
* Tweak: improved can_user_see_section method
* Tweak: added autocomplete for withdraw fields
* Tweak: updated plugin framework
* Fix: notice in affiliate dashboard
* Fix: notice "trying to retrieve user_login from non-object" on commission table
* Fix: issue with Withdraw for countries that do not require state
* Fix: prevent Notice when get_userdata returns a non-object
* Fix: doubled input fields on custom registration form
* Fix: section title in withdraw template
* Dev: added missing actions on link generator template

= 1.4.1 - Released on 24 October 2018 =

* New: added yith_wcaf_show_withdraw shortcode
* New: email sent to affiliates when account is banned
* Tweak: updated plugin framework
* Tweak: improved layout of the Withdraw template
* Tweak: improved email sent when affiliate account changes status
* Updated: dutch language
* Fix: minor issues introduced with last update

= 1.4.0 - Released on 03 October 2018 =

* New: support to WooCommerce 3.5-RC1
* New: support to WordPress 4.9.8
* New: updated plugin framework
* New: added new Reject status for affiliates
* New: affiliates receives an email on account status change
* New: added commissions Trash
* New: affiliates can now request commissions withdraws
* New: affiliates can now upload invoices for their withdraw requests
* New: affiliates can now generate invoices for their withdraw requests
* New: added affiliate details page
* Fix: affiliate backend creation
* Fix: fixed some queries on various admin views
* Tweak: improved balance calculation
* Dev: added filter get_referral_url filter

= 1.3.1 - Released on 19 July 2018 =

* New: support to YITH PayPal Payouts for WooCommerce
* New: added new fields during affiliate registration
* New: admin can now exclude products/users from affiliate program
* Tweak: improved filters, counters, views and redirection on affiliates admin panel
* Tweak: manual payment are now registered by default as on-hold
* Fixed: warning occurring when WooCommerce does not send all params to woocommerce_email_order_meta action
* Dev: added filter yith_wcaf_dashboard_affiliate_message

= 1.3.0 - Released on 28 May 2018 =

* New: WooCommerce 3.4 compatibility
* New: WordPress 4.9.6 compatibility
* New: updated plugin-fw
* New: GDPR compliance
* New: admin can now ban Affiliates
* Update: Italian Language
* Update: Spanish language
* Tweak: improved pagination of dashboard sections
* Fix: preventing notice when filtering by date payments

= 1.2.4 - Released on 05 April 2018 =

* New: added "process orphan commissions" procedure
* New: added shortcodes for Affiliate Dashboard sections ( [yith_wcaf_show_clicks], [yith_wcaf_show_commissions], [yith_wcaf_show_payments], [yith_wcaf_show_settings] )
* New: added handling for subscription renews (YITH WooCommerce Subscription 1.3.2 required)
* Dev: added yith_wcaf_requester_link filter to let third party code change requester link

= 1.2.3 - Released on 02 March 2018 =

* New: "yith_wcaf_show_if_affiliate" shortcode
* Tweak: remove user_trailingslashit from get_referral_url to improve compatibility
* Tweak: improved user capability handling, now all admin operations require at least manage_woocommerce capability (edited)
* Dev: new filter "yith_wcaf_panel_capability" to let third party code change minimum required capability for admin operations
* Dev: added "order_id" param for "yith_wcaf_affiliate_rate" filter
* Update: italian translation

= 1.2.2 - Released on 01 February 2018 =

* New: added WooCommerce 3.3.x support
* New: added WordPress 4.9.2 support
* New: added Dutch translation
* New: pay commissions every day
* New: pay only commissions older than a certain number of days
* Tweak: added SAMEORIGIN header to Affiliate Dashboard page
* Tweak: fixed error with wrong Affiliate ID when adding new affiliate to database
* Fix: preventing fatal error on commission details view when order meta are retrieved as objects (WC 3.0+)
* Dev: added yith_wcaf_commissions_csv_heading and yith_wcaf_commissions_csv_row filters to let third party developers change output of csv export operation

= 1.2.1 - Released on 14 November 2017 =

* Fix: added check over user before adding role

= 1.2.0 - Released on 10 November 2017 =

* New: WooCommerce 3.2.x support
* New: new affiliate role
* New: added login form in "Registration form" template
* New: added copy button for generated referral url
* New: added export csv procedure for commissions
* Tweak: added "Commissions table" to new order admin email
* Fix: removed profile panel when customer have permissions lower then shop manager
* Fix: problem with manual order affiliate assignment, when there are no previous commissions to delete
* Dev: added yith_wcaf_settings_form_start action
* Dev: added yith_wcaf_settings_form action
* Dev: added yith_wcaf_save_affiliate_settings action
* Dev: added yith_wcaf_show_dashboard_links filter to let dev show navigation menu on all affiliates dashboard pages

= 1.1.0 - Released on 03 April 2017 =

* New: WordPress 4.7.3 compatibility
* New: WooCommerce 3.0-RC2 compatibility
* New: field to user profile, to let admin set current permanent affiliate token for the user
* New: option to let admin choose that referral cookie won't change once set, till its expiration
* New: capability for the admin to set an affiliate for an unassigned order
* New: capability for the admin to remove an affiliate and relative commissions from an order
* New: Delete bulk action for payments
* New: option to force commissions deletion
* New: added Hungarian - HUNGARY translation (thanks to Szabolcs)
* Tweak: text domain to yith-woocommerce-affiliates. IMPORTANT: this will delete all previous translations
* Tweak: send paid email at yith_wcaf_commission_status_paid
* Tweak: complete revision for paid commissions emails triggers
* Tweak: delete notes while deleting commission
* Fix: email replacements
* Fix: delete method for payments
* Fix: commission paid email trigger
* Fix: commission delete process
* Fix: commission notes delete process
* Dev: added yith_wcaf_notify_user_pending_commission filter to let third party plugin prevent or enable pending commission notification
* Dev: added yith_wcaf_notify_user_paid_commissions filter to let third party plugin prevent or enable paid commission notification
* Dev: added yith_wcaf_affiliate_rate filter to let third party plugin customize affiliate commission rate
* Dev: added yith_wcaf_use_percentage_rates filter to let switch from percentage rate to fixed amount (use it at your own risk, as no control over item total is performed)
* Dev: added yith_wcaf_become_an_affiliate_redirection filter to let third party plugin customize redirection after "Become an Affiliate" butotn is clicked
* Dev: added yith_wcaf_become_affiliate_button_text filter to let third party plugin change Become Affiliate button label
* Dev: added yith_wcaf_persistent_rate filter to let third party plugin enable/disable persistent rate
* Dev: added yith_wcaf_payment_email_required filter to let third party plugin to remove payment email from affiliate registration form
* Dev: added yith_wcaf_create_order_commissions filter, to let dev skip commission handling
* Dev: added filters yith_wcaf_before_dashboard_section and yith_wcaf_after_dashboard_section
* Dev: added hooks after payment status change
* Dev: added yith_wcaf_get_current_affiliate_token function to get current affiliate token
* Dev: added yith_wcaf_get_current_affiliate function to get current affiliate object
* Dev: added yith_wcaf_get_current_affiliate_user function to get current affiliate user object

= 1.0.8 - Released on 08 June 2016 =

* Added: support WC 2.6 RC1
* Added: italian translation
* Added: spanish translation
* Added: attributes to affiliate_dashboard shortcode (will be passed to single section shortcode callbacks)
* Added: current_page attribute to all shortcodes that implements pagination
* Added: per page input in affiliate dashboard
* Added: style to #yith_wcaf_order_referral_commissions, #yith_wcaf_payment_affiliate, #yith_wcaf_commission_payments
* Tweak: added controls to show variation everywhere a variable product may be print
* Tweak: let rate set for Variable Product to apply to all variations
* Tweak: added filter yith_wcaf_is_hosted to filter check over submitted host / server name match in link_generator callback
* Fixed: Order links class/query vars

= 1.0.7 - Released on 05 May 2016 =

* Added: WordPress 4.5.x support
* Added: option to avoid referral cookie to be deleted after first customer checkout
* Added: new stat in Stas panel (sum of all affiliation earnings since program start)
* Fixed: removed useless library invocation
* Fixed: generate link shortcode (removed protocol before check for local url)

= 1.0.6 - Released on 05 April 2016 =

* Added: check over product existence on product table rates print method
* Added: capability for the admin to set commissions completed, without using any gateway
* Added: WooCommerce 2.5.x compatibility
* Added: WordPress 4.4.x compatibility
* Added: Users can now enter affiliate code from checkout page
* Added: Permanent token can now be locked, so to not be changed when a new affiliation link is visited
* Tweak: Performance improved with new plugin core 2.0
* Fixed: order awaiting payment handling
* Fixed: problems with views, due to new YITH menu name
* Fixed: generate link shortcode (url parsing improvements)
* Fixed: affiliate search method
* Fixed: default WC emails templates not found

= 1.0.5 - Released on 16 October 2015 =

* Added: Option to prevent referral cookie to expire
* Added: Option to prevent referral history cookie to expire
* Tweak: Increased expire seconds limit
* Tweak: Changed disabled attribute in readonly attribute for link-generator template
* Fixed: Corrected email templates
* Fixed: Option for auto-enable affiliates not showing on settings page
* Fixed: Commissions/Payment status now translatable from .po files
* Fixed: Fatal error occurring sometimes when using YOAST on backend

= 1.0.4 - Released on 13 August 2015 =

* Added: Compatibility with WC 2.4.2
* Tweak: Added missing text domain on link-generator template (thanks to dabodude)
* Tweak: Updated internal plugin-fw

= 1.0.3 - Released on 05 August 2015 =

* Initial release
