=== YITH WooCommerce Membership ===

== Changelog ==

= 1.3.24 - Released on 26 May 2020 =

* New: support for WooCommerce 4.2
* Update: plugin framework
* Update: language files

= 1.3.23 - Released on 28 April 2020 =

* New: support for WooCommerce 4.1
* Update: language files
* Update: plugin framework
* Fix: issue when editing membership dates due to timezone settings

= 1.3.22 - Released on 28 February 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: responsive tables on frontend
* Update: language files
* Update: plugin framework
* Fix: issue when overriding templates
* Tweak: prevent issues if 'date' column is not set
* Tweak: prevent issue if using shortcodes in wrong pages
* Dev: added yith_wcmb_download_table_title filter
* Dev: added yith_wcmbs_next_credits_update_date filter
* Dev: added yith_wcmb_force_showing_of_tab_contents filter
* Dev: added yith_wcmb_skip_not_downloadable_items filter
* Dev: added yith_wcmb_skip_check_product_needs_credits_to_download filter

= 1.3.21 - Released on 23 December 2019 =

* New: support for WooCommerce 3.9
* Update: plugin framework
* Update: language files
* Fix: WPML integration
* Fix: issue when adding products to order on backend
* Tweak: improved style
* Tweak: fixed issue when global variable $post is empty in combination with Gutenberg and Yoast SEO

= 1.3.20 - Released on 6 November 2019 =

* Update: plugin framework

= 1.3.19 - Released on 30 October 2019 =

* Update: plugin framework

= 1.3.18 - Released on 29 October 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: panel style
* Update: plugin framework
* Update: language files
* Fix: issue in combination with Avada theme
* Dev: added yith_wcmbs_show_plan_item_order_metabox filter
* Dev: added yith_wcmbs_show_plan_item_style_metabox filter
* Dev: added yith_wcmbs_frontend_woocommerce_product_actions filter
* Dev: added yith_wcmbs_frontend_woocommerce_product_shop_actions filter
* Dev: added yith_wcmbs_get_restricted_items_in_plan_parse_by_delay filter
* Dev: added yith_wcmbs_get_restricted_items_in_plan_exclude_hidden filter
* Dev: added yith_wcms_tab_contents_label filter
* Dev: added yith_wcmbs_show_restrict_access_metabox filter
* Dev: added yith_wcmbs_before_links_list hook
* Dev: added user_id attribute to the YITH_WCMBS_Welcome_Mail class
* Dev: added user_id attribute to the membership_history shortcode

= 1.3.17 - Released on 5 August 2019 =

* New: support to WooCommerce 3.7
* New: add CSS classes to body tag based on the memberships of the user
* Update: plugin framework
* Update: language files
* Fix: issue when overriding Multi Vendor options
* Fix: prevent issue if for some reasons the Shop Manager role doesn't exist
* Fix: duplicate select2 in Gutenberg editor for protected links
* Fix: issue with credits on membership activation


= 1.3.16 - Released on 29 May 2019 =

* New: options to include/exclude members of specific plans in Cart Rules of YITH WooCommerce Dynamic Pricing and Discount 1.5.3
* Fix: override Multi Vendor options in combination with Frontend Manager
* Update: plugin framework


= 1.3.15 - Released on 7 May 2019 =

* New: search for membership by user fields
* Update: plugin framework
* Dev: added membership object in emails


= 1.3.14 - Released on 17 April 2019 =

* New: support to WooCommerce 3.6
* New: support to YITH Mailchimp 2.1.1: unsubscribe email when the membership expires or is cancelled
* Tweak: fixed notices for membership emails
* Update: language files
* Update: plugin framework


= 1.3.13 - Released on 7 February 2019 =

* New: search memberships by order fields
* New: set a list of excluded membership plans in discount rules of YITH WooCommerce Dynamic Pricing and Discount
* Fix: protected media download issue
* Fix: js error in Membership plans
* Tweak: prevent issues in combination with some plugins using the_title filter such as WooCommerce PayPal Checkout Gateway
* Update: language files
* Update: plugin framework

= 1.3.12 - Released on 6 December 2018 =

* New:  support to WordPress 5.0
* New: expired membership email
* Update: plugin framework
* Update: language files
* Tweak: fixed issue when showing download links for admin
* Dev: added yith_wcmbs_membership_history_shortcode_no_membership_message filter

= 1.3.11 - Released on 23 October 2018 =

* New: integration with YITH Amazon S3
* Update: Plugin Framework

= 1.3.10 - Released on 15 October 2018 =

* New: support to WooCommerce 3.5.x
* New: possibility to edit Multi Vendor product limit based on Membership plan
* New: Membership Flat Rate shipping method
* New: download report IP address
* Fix: show alternative content in Shop if restricted
* Fix: support to YITH WooCommerce MailChimp
* Fix: prevent warning on empty array for filter posts method
* Tweak: prevent issues on copy and paste email in All Membership table
* Tweak: fixed style in All Memberships list
* Update: Italian language
* Update: Plugin Framework
* Dev: added yith_wcmbs_username_anchor_membership_list_table filter

= 1.3.9 - Released on 6 June 2018 =

* Fix: issue when manually assigning a new membership plan to users
* Fix: issue when sending emails
* Fix: template overriding issue
* Update: Spanish translation
* Dev: added yith_wcmbs_show_membership_edit_actions_in_users filter

= 1.3.8 - Released on 24 May 2018 =

* New: support to WordPress 4.9.6
* New: support to WooCommerce 3.4.0
* New: Privacy Policy Guide
* New: Polish language (thanks to Jakub Przetocki)
* Update: Italian language
* Fix: issue activating memberhip through variable products
* Fix: issue on email when getting billing address from order

= 1.3.7 - Released on 23 April 2018 =

* New: Persian translation (thank to Sadra)
* New: Dutch translation
* Fix: do_shortcode in alternative content for products
* Fix: plan metabox saving
* Fix: links
* Tweak: prevent notice in menu
* Dev: added yith_wcmbs_admin_profile_show_membership_count filter
* Dev: added yith_wcmbs_products_manager_add_products_in_plan_only_downloadable_check filter
* Dev: added yith_wcmbs_membership_notify filter
* Dev: added yith_wcmbs_membership_max_days_number_to_send_expiring_email filter

= 1.3.6 - Released on 31 January 2018 =

* New: support to WooCommerce 3.3
* New: default Alternative Content
* Update: Plugin Framework
* Fix: notice in Membership Welcome email
* Fix: tip-tip css style
* Fix: time offset in Membership History
* Fix: add to cart validation issue
* Fix: required registration for membership products issue
* Tweak: prevent theme issues
* Dev: added yith_wcmbs_get_alternative_content function
* Dev: added yith_wcmbs_get_alternative_content filter
* Dev: added yith_wcmbs_membership_history_shortcode_membership_plans_status filter
* Dev: added yith_wcmbs_admin_profile_membership_columns_membership_plans_args filter
* Dev: added yith_wcmbs_admin_profile_membership_columns_membership_plans_status filter
* Dev: added yith_wcmbs_membership_history_shortcode_membership_plans_args filter
* Dev: added yith_wcmbs_validate_product_add_to_cart_needs_membership_error_message filter

= 1.3.5 - Released on 27 December 2017 =

* New: translate plan title with WPML
* Tweak: added gettext to print membership note to make it translatable through WPML String Translations
* Update: language file
* Fix: integration with YITH WooCommerce Dynamic Pricing and Discounts 1.4.2
* Fix: Linked Plans field issue
* Fix: WPML issue
* Dev: added yith_wcmbs_membership_get_plan_title filter
* Dev: fixed yith_wcmbs_get_restricted_items_in_plan filter issue

= 1.3.4 - Released on 11 December 2017 =

* Update: Plugin Framework 3.0
* Fix: register post type issue, since it was registered only if is_admin
* Fix: membership free shipping hooks
* Fix: multiple membership issue when creating new one manually

= 1.3.3 - Released on 25 October 2017 =

* New: support to WooCommerce 3.2.1
* New: integration with YITH WooCommerce Mailchimp 1.1.1: the admin can set Mailchimp lists in Membership plans, so when an user becomes a member he/she will be added to the selected Mailchimp lists
* Fix: YITH WooCommerce Multi Vendor integration
* Fix: removed possibility to create post for Membership plans
* Fix: products in membership
* Dev: added yith_wcmbs_add_products_in_plan_cat_tag_args filter
* Dev: added yith_wcmbs_product_is_in_plans filter
* Tweak: delete cron when uninstalling

= 1.3.2 - Released on 11 October 2017 =

* New: support to Support to WooCommerce 3.2.0 RC2
* Fix: wordpress version number in WP_Compatibility class
* Fix: prevent fatal error in combination with YITH WooCommerce Multi Vendor
* Fix: Plan Item Order scrollable issue
* Tweak: added CSS classes in membership information in My Account
* Tweak: fixed yith_wcmbs_hide_price_and_add_to_cart filter name
* Tweak: new shortcode tab style
* Tweak: changed the_content filter priority to prevent issues with other plugins
* Tweak: added do_shortcode for alternative contents
* Dev: added yith_wcmbs_frontend_js_deps filter
* Dev: added yith_wcmbs_email_membership_status_expiration_date filter
* Dev: added yith_wcmbs_my_account_membership_status_expiration_date filter
* Dev: added yith_wcmbs_email_membership_status_expiration_date filter
* Dev: added yith_wcmbs_membership_created action
* Dev: added yith_wcmbs_not_enough_credits_message filter
* Dev: added yith_wcmbs_membership_get_remaining_days filter
* Dev: added yith_wcmbs_get_product_download_links filter
* Dev: added yith_wcmbs_sorted_plan_items filter
* Dev: added yith_wcmbs_shortcode_membership_download_product_links_name filter
* Dev: added yith_wcmbs_shortcode_membership_download_product_links_link filter
* Dev: added yith_wcmbs_shortcode_membership_download_product_links_data filter

= 1.3.1 - Released on 16 March 2017 =

* New: support to WooCommerce 3.0.0-RC1
* New: added German translation (thanks to Ninos Ego)
* Dev: added yith_wcmbs_get_restricted_items_in_plan filter
* Dev: added yith_wcmbs_check_if_external_file_exists filter
* Dev: added yith_wcmbs_non_allowed_post_ids_for_user filter


= 1.3.0 - Released on 2 January 2017 =

* New: more than one membership target product in membership plans
* Fix: issues with formatted dates
* Fix: tip-tip in reports
* Fix: display date in localized format
* Fix: issue with access restrictions in the shop page
* Fix: check if linked file exists to prevent displaying the link
* Fix: added WP retro compatibility
* Tweak: added membership info column in "downloads by user" reports
* Tweak: "Credits" field moved to Advanced tab in product (for variable products)
* Tweak: improved subscription information in membership tip-tip (in combination with YITH WooCommerce Subscription Premium)
* Dev: added hooks in reports
* Dev: added action yith_wcmbs_download_report_after_graphics


= 1.2.9 - Released on 7 November 2016 =

* New: Spanish language
* New: new reports (downloads by user and download details by user)
* Fix: issue in Edit Plan when adding ajax select2
* Fix: membership history view
* Fix: reports style in order page
* Dev: added yith_wcmb_update_membership_status_allowed filter
* Dev: added yith_wcmb_allow_status_management_by_subscription filter
* Dev: added yith_wcbms_remove_woocommerce_product_shop_actions action
* Dev: added yith_wcbms_restore_woocommerce_product_shop_actions action
* Dev: added yith_wcbms_remove_woocommerce_product_actions action
* Dev: added yith_wcbms_restore_woocommerce_product_actions action

= 1.2.8 - Released on 6 September 2016 =

* New: user option in membership_protected_content shortcode to display content to non-members or guests or logged users only
* Fix: shortcode issue
* Fix: membership activation issue in combination with polylang

= 1.2.7 - Released on 19 July 2016 =

* Fix: download link style
* Fix: protected link saving

= 1.2.6 - Released on 13 July 2016 =

* New: protected links in posts, pages and product descriptions
* New: protected contents through shortcode
* New: "copy to clipboard" for shortcodes in Membership Plan list
* New: improved CSS and JS inclusion
* Fix: YITH WooCommerce Multi Vendor compatibility (hide alternative content if user is not enabled to see it)

= 1.2.5 - Released on 16 June 2016 =

* New: Membership Free Shipping method since WooCommerce 2.6
* New: parameter to sort membership items in [membership_items] shortcode
* New: italian language

= 1.2.4 - Released on 31 May 2016 =

* New: possibility to associate automatically membership plans to newly registered users

= 1.2.3 - Released on 17 May 2016 =

* New: compatibility with YITH WooCommerce Dynamic Pricing and Discounts 1.1.0 to allow discounts for members
* New: shortcode to show downloaded product links
* Fix: bug in memberships with user_id = 0
* Fix: issue with creation of download report table on multisite installation
* Fix: memory issue in Membership Plan settings
* Fix: membership access issue
* Tweak: fixed strings

= 1.2.2 - Released on 15 March 2016 =

* Tweak: display membership access in Media Editor
* Tweak: fixed Multi Vendor suborder bug ( duplicate membership )
* Tweak: fixed minor bugs

= 1.2.1 - Released on 10 March 2016 =

* New: possibility to change the subscription id for every membership with membership advanced management enabled
* New: order info in membership list
* New: sorting for starting and expiring date in All Memberships WP List
* Fix: datepicker css style
* Fix: date bug in advanced membership management
* Fix: redirect bug for pages in membership

= 1.2.0 - Released on 25 February 2016 =

* New: membership advanced management
* New: possibility to hide product download links and use shortcode
* New: credit advanced management, admin can set different credits for every product (default is 1)
* New: possibility to set credits for the first term
* New: compatibility with Premium YITH WooCommerce Email Templates 1.2.0
* New: possibility to override membership email templates
* New: subscription status in All Memberships list
* New: possibility to hide price and add-to-cart button in Single Product Page, if members are allowed to download the product
* New: reports for memberships purchased with subscription
* Fix: duplicate membership plan
* Fix: CSS tooltip in frontend
* Fix: Membership can now be activated even when cancelled Subscription is payed
* Fix: subscription cancel-now bug
* Fix: issue concering product download by admin (check credit error); now Admin doesn't need credits to download products
* Tweak: added hierarchical structure in "chosen for product" and post categories
* Tweak: added buttons "Select All" and "Deselect All" for chosen field of post and product categories in Membership Plan Options
* Tweak: added action to manage PayPal and Stripe disputes
* Tweak: improved frontend style for membership history, download buttons and list of planned items
* Tweak: added admin tab shortcodes to explain shortcode usage
* Tweak: email classes and templates updated for WC 2.5
* Tweak: improved reports
* Tweak: changed status label from "Not Active" to "Suspended"
* Tweak: changed labels in admin membership plan
* Tweak: included child categories for products if parent category is selected in Membership Plan Options
* Tweak: fixed css for metabox chosen, select and descriptions
* Tweak: added style for download buttons in Single Product Page

= 1.1.1 - Released on 15 January 2016 =

* Tweak: fixed bug for current memberships without credits management

= 1.1.0 - Released on 13 January 2016 =

* New: download credits management for membership
* New: possibility to choose limit for membership downloads
* New: membership and download reports
* New: user download reports table and graphics in orders
* New: status filters in Memberships WP List
* New: compatibility with WooCommerce 2.5 RC2
* Tweak: fixed minor bug with bbPress
* Tweak: fixed membership bulk actions for users
* Tweak: fixed pot language file
* Tweak: changed menu name Memberships in All Memberships
* Tweak: fixed minor bugs

= 1.0.4 - Released on 7 December 2015 =

* New: possibility to hide items directly in membership plan settings page
* Tweak: better styling management for membership item list (shortcode)
* Tweak: improved compatibility with YITH WooCommerce Multi Vendor
* Tweak: improved cron performance
* Tweak: fixed end date calculation after pause
* Tweak: fixed minor bugs

= 1.0.3 - Released on 1 December 2015 =

* New: support for membership bought by guest users
* New: shortcode for showing membership history
* Tweak: improved compatibility with YITH WooCommerce Multi Vendor
* Tweak: added possibility to hide membership history in My Account page
* Tweak: improved download list

= 1.0.2 - Released on 17 November 2015 =

* Initial release