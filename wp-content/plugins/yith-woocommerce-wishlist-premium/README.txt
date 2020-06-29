=== YITH WooCommerce Wishlist ===

Contributors: yithemes
Tags: wishlist, woocommerce, products, themes, yit, e-commerce, shop
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 3.0.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://yithemes.com/docs-plugins/yith-woocommerce-wishlist

== Changelog ==

= 3.0.10 - Released on 07 May 2020 =

* New: support for WooCommerce 4.1
* New: prevent some UserAgents from triggering wishlist handling (avoid spam)
* New: added minor css fixes for Storefront theme
* New: added option to choose whether to automatically close feedback popup or not
* Update: plugin framework
* Tweak: added product_url placeholder for promotional email body
* Tweak: review add process, to avoid unnecessary items update
* Tweak: items are now counted per ID instead of user_id
* Tweak: show variation attributes on Popular table
* Tweak: changed 'Price' by 'Unit price' on wishlist modern view
* Tweak: improved localized date on wishlist table
* Tweak: added wishlist as gutenberg block in new wishlist page
* Tweak: added "Wishlist page" post status
* Tweak: added new check to avoid "Cannot read property contains of undefined" error
* Tweak: added search box to All Wishlist view
* Tweak: added default values for email contents on plugin options
* Tweak: user can now delete also default wishlist
* Tweak: minor changes to 'manage modern' layout
* Tweak: added view > and close links to confirmation popup
* Fix: solved issue with item counts, when filtering per product
* Fix: fixed billing last name value on 'promotional', 'on sale' and 'back in stock' emails
* Fix: show remove button on list mobile when at least one of the two buttons is shown on desktop
* Fix: notice due to undefined widgets attributes
* Fix: fatal error on empty wishlist page
* Fix: added some checks to avoid fatal errors in back in stock email
* Dev: added yith_wcwl_create_wishlist_button_label filter
* Dev: added yith_wcwl_wishlist_download_url filter
* Dev: added yith_wcwl_is_wishlist_responsive filter, to allow developers disable responsive behaviour for the wishlist
* Dev: added yith_wcwl_generated_default_wishlist action
* Dev: added yith_wcwl_default_wishlist filter
* Dev: added yith_wcwl_add_notice wrapper function, to avoid possible fatal errors when calling wc_add_notice

= 3.0.9 - Released on 09 March 2020 =

* Tweak: use wp_kses_post instead of esc_html for browse wishlist text
* Update: plugin framework

= 3.0.8 - Released on 04 March 2020 =

* Tweak: use wp_kses_post sanitization instead of esc_html for button labels to allow developers to add HTML to them
* Tweak: minor improvements for OceanWP theme style
* Tweak: improved widgets style when they are applied via Elementor
* Fix: notice on empty wishlist page (thanks to ashimhastech)
* Fix: removed var_dump

= 3.0.7 - Released on 03 March 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: edit title & change privacy on Manage view are now performed via ajax
* New: improved checks on user capabilities
* New: added wishlist widgets to Elementor
* Update: plugin framework
* Tweak: added check on user permission level for all wishlist actions
* Tweak: show 404 page when non-owner user tries to visit private wishlists
* Tweak: hide share buttons on private wishlists (thanks to Jory)
* Tweak: escape output on templates
* Fix: non-owner users cannot sort wishlist any longer
* Fix: custom css not being loaded in the page
* Fix: added check to avoid fatal error in the popular users table
* Fix: compatibility with YITH Infinite Scrolling when ajax loading is enabled
* Fix: avoid notice when 'ask an estimate button' is not showing in the template
* Fix: hide share section on wishlist page when "Share wishlist" option is disabled
* Fix: assign correct css rule to border color for Wishlist Table
* Fix: added pagination links to all wishlist templates
* Dev: added yith_wcwl_reload_fragments trigger to refresh wishlist fragments
* Dev: added yith_wcwl_remove_hidden_products_via_query filter
* Dev: added yith_wcwl_show_add_to_wishlist filter, to allow dev selectively hide Add to Wishlist buttons
* Dev: added yith_wcwl_create_wishlist_title_label filter
* Dev: added yith_wcwl_search_wishlist_title_label filter
* Dev: added yith_wcwl_manage_wishlist_title_label filter
* Dev: new actions on wishlist-view.php template (thanks to Jory)
* Dev: added .editorconfig (thanks to Jory)

= 3.0.6 – Released on 04 February 2020 =

* Tweak: avoid redirect for guest users if wishlist page is set to my-account
* Tweak: allow popup timeout to be filtered via code
* Tweak: using yith_wcwl_l10n.popup_timeout for wishlist messages too
* Tweak: minor improvements to localization
* Tweak: promotion email preview can now be scrolled
* Tweak: update wrong text domains
* Tweak: changed default value for ATW icons
* Tweak: set wishlist session cookie JIT
* Tweak: use secure cookie for sessions, when possible (thanks to Ahmed)
* Tweak: improved cache handling for get_default_wishlist method
* Tweak: even if system cannot set session cookie, calculate session_id and use it for the entire execution
* Tweak: improved privacy labels for the wishlists
* Update: Italian language
* Update: plugin framework
* Fix: prevent error if list doesn't exists
* Fix: issue with wishlist_id query param
* Fix: items query now search for product in original language
* Fix: Create promotion button for single products view
* Fix: fatal error after saving promotional email draft
* Fix: prevent fatal error when sending Promotional Email
* Fix: returning correct wishlist and user id to yith_wcwl_added_to_wishlist and yith_wcwl_removed_from_wishlist actions (thanks to danielbitzer)
* Fix: issue with default value for yith_wcwl_positions option
* Fix: ask an estimate label not being shown on frontend
* Fix: added key name to avoid DB error during install or update procedure
* Dev: added yith_wcwl_shortcode_share_link_url filter
* Dev: added yith_wcwl_popup_timeout filter

= 3.0.5 - Released on 23 December 2019 =

* New: support for WooCommerce 3.9
* Update: plugin framework
* Tweak: register original product id instead of translated one, when saving item in DB
* Fix: customer not being redirected to cart after clicking Add to Cart button in wishlist

= 3.0.4 - Released on 19 December 2019 =

* Tweak: added isset on wishlist data store, to prevent notice
* Tweak: removed transients for items count, to avoid load on wp-options table
* Tweak: improved dependencies animation
* Tweak: restored $atts variable inside template, for better compatibility with themes
* Tweak: handling for redirect_to param in $_REQUEST for form-handler class
* Update: plugin framework
* Fix: default variation not being added to wishlist
* Fix: add default variation to wishlist when Ajax loading is enabled
* Fix: count_all_products not retrieving correct number
* Dev: added yith_wcwl_wishlist_view_images_columns filter
* Dev: added yith_wcwl_wishlist_delete_url filter
* Dev: added yith_wcwl_before_wishlist_create and yith_wcwl_after_wishlist_create inside create popup

= 3.0.3 - Released on 12 December 2019 =

* Tweak: prevent yith_setcookie to process if cookie name is not set
* Tweak: refactored session class to set up session cookie name on demand, when needed (avoid empty cookie name)
* Tweak: minor improvements to functions that require session (count_products, get_default_wishlist..) as a consequence of changes applied to session class

= 3.0.2 - Released on 11 December 2019 =

* Update: plugin framework
* Tweak: added defaults for yith_wcwl_add_to_cart_text option (thanks to ecksiteweb)
* Tweak: changed placeholder for Wishlist Name field on Add to Wishlist popup template
* Fix: prevent fatal error when switching from cookies to session

= 3.0.1 - Released on 10 December 2019 =

* Update: language files
* Tweak: restored global $yith_wcwl

= 3.0.0 - Released on 09 December 2019 =

* New: option to show Add to Wishlist button on loops
* New: Add to Wishlist button style when placed over product image
* New: Add to Wishlist button can now turn into Remove from Wishlist after addition
* New: Add to Wishlist button can now turn itno Move to another wishlist after addition
* New: added new layouts for wishlist shortcode (Modern and Images grid)
* New: plugin will add variation to wishlist, if user selected one before pressing the button
* New: count of users that added item in the wishlist
* New: option to load wishlist templates via Ajax
* New: select add to wishlist icon and/or upload  custom image
* New: guest wishlists are now stored on db, within session id
* New: unified experience for guests and logged in users
* New: added new layout for manage wishlist view (Modern)
* New: create new wishlist can now be opened as a popup
* New: customization for social icons
* New: added tooltips for wishlist buttons
* New: wizard to configure promotional email
* New: email sent when an item of the wishlist is back in stock
* New: email sent when an item of the wishilist is on sale
* Tweak: improved admin panel, and settings UX
* Dev: code refactoring of the entire plugin
* Dev: new YITH_WCWL_Wishlist and YITH_WCWL_Wishlist_Item objects
* Dev: now using Data_store classes to handle db operations
* Dev: added filter yith_wcwl_loop_positions
* Dev: added filter yith_wcwl_custom_css_rules
* Dev: added filter yith_wcwl_session_cookie
* Dev: added filter yith_wcwl_item_formatted_price
* Dev: added filter yith_wcwl_wishlist_formatted_title
* Dev: added filter yith_wcwl_wishlist_get_items
* Dev: added filter yith_wcwl_user_cannot_add_to_wishlist_message
* Dev: added filter yith_wcwl_can_user_add_to_wishlist
* Dev: added filters yith_wcwl_add_wishlist_{property}
* Dev: added filters yith_wcwl_adding_to_wishlist_{property}

= 2.2.13 – Released on 11 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* Update: plugin framework
* Update: Italian language
* Update: Dutch language
* Tweak: wrapped Add to Wishlist button label into span HTML tag
* Fix: removed occurrences of deprecated properties on promotional email class
* Dev: added new yith_wcwl_product_already_in_wishlist_text_button and yith_wcwl_product_added_to_wishlist_message_button filters
* Dev: added new yith_wcwl_out_of_stock_label and yith_wcwl_in_stock_label filters

= 2.2.12 - Released on 12 August 2019 =

* New: WooCommerce 3.7.0 RC2 support
* New: input to copy wishlist link and share it anywhere
* Update: internal plugin framework
* Update: Italian language
* Fix: redirect url if there is more than one parameter on the url
* Fix: changed escape for share link, to properly escape url special characters

= 2.2.11 - Released on 18 July 2019 =

* Update: internal plugin framework
* Tweak: improved performance on wishlist page, when user is a guest and store has huge catalog (thanks to Dave)
* Dev: add filter yith_wcwl_wishlist_correctly_created on add_wishlist function

= 2.2.10 - Released on 29 May 2019 =

* Tweak Prevent undefined index: user_id when user is loggin
* Fix: Fixed active status for default wishlist when WPML is active
* Fix: Fixed active status for default wishlist when WPML is active
* Fix: fixed the default wishlist name in the multi wishlist select
* Fix: widget not recognizing current wishlist when WPML is active
* Fix: notice when sending Promotional email, due to access to legacy attributes
* Dev: new filter yith_wcwl_wishlist_disabled_for_unauthenticated_user_message_condition
* Dev: New action 'yith_wcwl_default_user_wishlist'

= 2.2.9 - Released on 11 April 2019 =

* New: WooCommerce 3.6.x support
* New: added a WhatsApp share button on mobile
* New: add new shortcode yith_wcwl_show_public_wishlist
* Tweak: using add_inline_style to include custom css code
* Tweak: no longer adding empty style tag to the page
* Update: Spanish language
* Fix: get the correct value for wishlist name
* Fix: deprecated notice caused by product id attribute being accessed directly

= 2.2.8 - Released on 11 February 2019 =

* New: added support to WooCommerce 3.5.4
* Update: internal plugin framework
* Update: Dutch translation
* Fix: added explicit array casting on shortcode to avoid warning
* Fix: don't add custom classes to body if wishlist page is not set
* Fix: changed a wrong method in the enqueue style
* Dev: add filter yith_wcwl_email_share_subject

= 2.2.7 - Released on 21 December 2018 =

* Fix: possible warning when Add to Wishlist shortcode is called with no params

= 2.2.6 - Released on 21 December 2018 =

* New: added support to WordPress 5.0
* New: added support to WooCommerce 3.5.3
* New: added Gutenberg blocks for plugin shortcodes
* Update: internal plugin framework
* Update: italian language
* Fix: preventing sql error when hidden products list just contains 0 id
* Fix: problem with sprintf on backend
* Dev: added product param to yith_free_text filter
* Dev: added yith_wcwl_login_register_redirect filter to let third party code filter redirect uri for unauthenticated users

= 2.2.5 - Released on 24 October 2018 =

* New: updated plugin framework

= 2.2.4 - Released on 04 October 2018 =

* New: added support to WooCoommerce 3.5
* New: added support to WordPress 4.9.8
* New: added method that returns localization variables
* New: updated plugin framework
* Tweak: type attribute from <script> tag
* Update: Spanish language
* Update: Italian language
* Dev: added new filter yith_wcwl_localize_script to let third party dev filter localization variables
* Dev: added new filter yith_wcwl_column_default
* Dev: added new filter yith_wcwl_wishlist_column
* Dev: added new filter yith_wcwl_share_conditions to display the share buttons for no logged users
* Dev: added new filter yith_wcwl_set_cookie to let third party code skip cookie saving
* Dev: added new filter yith_wcwl_wishlist_visibility_string_value to the wishlist visibility value
* Dev: added new filter yith_wcwl_manage_wishlist_title
* Dev: added new filter yith_wcwl_create_wishlist_title
* Dev: added new filter yith_wcwl_search_wishlist_title
* Dev: added new filter yith_wcwl_result_wishlist
* Dev: added new filter yith_wcwl_empty_search_result
* Dev: added new filter yith_wcwl_wishlist_param to change query-string param
* Dev: added new filter yith_wcwl_remove_product_wishlist_message_title

= 2.2.2 - Released on 28 May 2018 =

* New: WooCommerce 3.4 compatibility
* New: WordPress 4.9.6 compatibility
* New: updated plugin framework
* New: GDPR compliance
* New: register dateadded field for the lists
* Tweak: replaced create_function with a proper class method, to improve compatibility with PHP 7.2 and avoid warnings
* Fix: js error when switching from Premium version to Free
* Fix: preventing add_rewrite_rule when WPML is active, to avoid possible Internal Server Error (thanks to Adri & Bruno)
* Fix: icon replacement not working on variable Add to Cart
* Fix: preventing warning "Illegal string offset" when get_availability() returns empty string instead of array
* Update: Italian language
* Dev: added filter yith_wcwl_redirect_url
* Dev: added filter yith_wcwl_login_notice

= 2.2.1 - Released on 31 January 2018 =

* New: tested with WooCommerce 3.3.0
* Fix: issue with Add to Wishlist shortcode when global $product not defined

= 2.2.0 - Released on 11 January 2018 =

* New: WooCommerce 3.2.6 compatibility
* New: plugin-fw 3.0
* New: added js compatibility to Infinite Scrolling
* New: added "Last promotional email sent on" info, for admins
* New: added option to export users that added a specific product to their wishlists, using csv format
* New: added Swedish - SWEDEN translation (thanks to Suzanne)
* New: added Dutch - NETHERLANDS translation
* Tweak: improved wishlist-view template checks and params
* Tweak: wishlist now registers (and shows) "date added" param for unauthenticated users too
* Tweak: added check over product object, to avoid Fatal when printing Add to Wishlist shortcode
* Fix: fixed security vulnerability, causing possible SQL Injections (huge thanks to John C. and Sucuri Vulnerability Research team)
* Dev: added filter yith_wcwl_estimate_additional_data to let developers add custom data to print in Estimate Email template
* Dev: added yith_wcwl_removing_from_wishlist / yith_wcwl_removed_from_wishlist hooks
* Dev: added params to main triggers in wishlist js code

= 2.1.2 - Released on 11 May 2017 =

* Tweak: updated FontAwesome to 4.7.0
* Fix: possible warning when empty rewrite rules
* Fix: problem with custom CSS inclusion, when not located in child theme
* Fix: using current_product instead of global product when retrieving product type (prevents a Fatal error when placing Add to Wishlist outside the loop)

= 2.1.1 - Released on 24 April 2017 =

* Tweak: improved endpoints creation, with dynamic flush
* Tweak: added check over wc_print_notices existence, to avoid possible fatal errors
* Tweak: updated plugin-fw
* Fix: problem with duplicated meta
* Fix: product created wince WC 3.0.x not being shown on wishlist
* Dev: added yith_wcwl_admin_table_show_empty_list filter to show empty lists on admin

= 2.1.0 - Released on 03 April 2017 =

* New: WooCommerce 3.0-RC2 compatibility
* New: WordPress 4.7.3 compatibility
* New: Ask an Estimate for unauthenticated users
* New: added action_params param to yith_wcwl_wishlist shortcode, to let administrators show different wishlist views on different pages
* New: redirect to wishlist after login from "Login Notice" in wishlist page
* New: {product_url} and {wishlist_url} within promotion email replacements
* New: flush rewrite rules when installing plugin
* Tweak: added urlencode to mail content in mailto share link
* Tweak: count query of count_all_products
* Tweak: improved WPML list content handling (thanks to Adri)
* Tweak: double check over wc_add_to_cart_params exists and not null
* Tweak: added wishlist meta inside wishlist table data attr also for not logged in users (used for shared wishlist)
* Tweak: remove prettyPhoto-init library
* Tweak: implemented custom code to enable prettyPhoto on Wishlist elements
* Tweak: fixed typo in wishlist-view template
* Tweak: added urlencode to all sharing links
* Tweak: minimized endpoint usage when not required
* Tweak: removed unused check for WC_Product_Bundle
* Fix: "Move to another Wishlist" message, when moving to default wishlist
* Fix: get_template_directory for custom wishlist js
* Fix: global yith_wcwl_wishlist_token (false for default wishlists)
* Fix: check before "additional info" popup in wishlist_view template
* Fix: stock_status not existing when stock column isn't shown
* Dev: added filter yith_wcwl_create_new_wishlist_title on wishlist-manage.php
* Dev: added filter yith_wcwl_ask_an_estimate_text
* Dev: action as second param for yith_wcwl_wishlist_page_url filter
* Dev: applied filter yith_wcwl_no_product_to_remove_message also for message on wishlist-view template
* Dev: added filter yith_wcwl_add_wishlist_user_id
* Dev: added filter yith_wcwl_add_wishlist_slug
* Dev: added filter yith_wcwl_add_wishlist_name
* Dev: added filter yith_wcwl_add_wishlist_privacy
* Dev: added yith_wcwl_promotional_email_thumbnail_size filter
* Dev: added filters yith_wcwl_estimate_sent & yith_wcwl_estimate_missing_email

= 2.0.16 - Released on 14 June 2016 =

* Added: WooCommerce 2.6 support
* Tweak: changed uninstall procedure to work with multisite and delete plugin options
* Tweak: removed description and image from facebook share link (fb doesn't allow anymore)
* Fixed: product query (GROUP By and LIMIT statement conflicting)
* Fixed: to print "Sent Manually" on WC Emails

= 2.0.15 - Released on 04 April 2016 =

* Added: filter yith_wcwl_is_product_in_wishlist to choose whether a product is in wishlist or not
* Added: filter yith_wcwl_cookie_expiration to set default wishlist cookie expiration time in seconds
* Tweak: updated plugin-fw
* Fixed: get_products query returning product multiple times when product has more then one visibility meta

= 2.0.14 - Released on 21 March 2016

Added: yith_wcwl_is_wishlist_page function to identify if current page is wishlist page
Added: filter yith_wcwl_settings_panel_capability for panel capability
Added: filter yith_wcwl_current_wishlist_view_params for shortcode view params
Added: "defined YITH_WCWL" check before every template
Added: check over existance of $.prettyPhoto.close before using it
Added: method count_add_to_wishlist to YITH_WCWL class
Added: function yith_wcwl_count_add_to_wishlist
Tweak: Changed ajax url to "relative"
Tweak: Removed yit-common (old plugin-fw) deprecated since 2.0
Tweak: Removed deprecated WC functions
Tweak: Skipped removed_from_wishlist query arg adding, when external product
Tweak: Added transients for wishist counts
Tweak: Removed DOM structure dependencies from js for wishlist table handling
Tweak: All methods/functions that prints/counts products in wishlist now skip trashed or not visible products
Fixed: shortcode callback setting global product in some conditions
Fixed: typo in hook yith_wccl_table_after_product_name (now set to yith_wcwl_table_after_product_name)
Fixed: notice appearing when wishlist page slug is empty
Fixed: "Please login" notice appearing right after login
Fixed: email template for WC 2.5 and WCET compatibility

= 2.0.13 - Released on 17 December 2015 =

* Added check over adding_to_cart event data existance in js procedures
* Added compatibility with YITH WooCommerce Email Templates
* Added 'yith_wcwl_added_to_cart_message' filter, to customize added to cart message in wishlist page
* Added 'yith_wcwl_action_links' filter, to customize action link at the end of wishlist pages
* Added nofollow to "Add to Wishlist" links, where missing
* Added 'yith_wcwl_email_share_subject' filter to customize share by email subject
* Added 'yith_wcwl_email_share_body' filter to customize share by email body
* Added function "yith_wcwl_count_all_products"
* Fixed plugin-fw loading

= 2.0.12 - Released on 23 October 2015 =

* Added: method to count all products in wishlist
* Tweak: Added wishlist js handling on 'yith_wcwl_init' triggered on document
* Tweak: Performance improved with new plugin core 2.0
* Fixed: occasional fatal error for users with outdated version of plugin-fw on their theme

= 2.0.11 - Released on 21 September 2015 =

* Updated: changed text domain from yit to yith-woocommerce-wishlist
* Updated: changed all language file for the new text domain

= 2.0.10 - Released on 12 August 2015 =

* Added: Compatibility with WC 2.4.2
* Tweak: added nonce field to wishlist-view form
* Tweak: added yith_wcwl_custom_add_to_cart_text and yith_wcwl_ask_an_estimate_text filters
* Tweak: added check for presence of required function in wishlist script
* Fixed: admin colorpicker field (for WC 2.4.x compatibility)

= 2.0.9 - Released on 24 July 2015 =

* Added: WooCommerce class to wishlist view form
* Added: spinner to plugin assets
* Added: check on "user_logged_in" for sub-templates in wishlist-view
* Added: WordPress 4.2.3 compatibility
* Added: WPML 3.2.2 compatibility (removed deprecated function)
* Added: new check on is_product_in_wishlist (for unlogged users/default wishlist)
* Tweak: escaped urls on share template
* Tweak: removed new line between html attributes, to improve themes compatibility
* Updated: italian translation
* Fixed: WPML 3.2.2 compatibility (fix suggested by Konrad)
* Fixed: regex used to find class attr in "Add to Cart" button
* Fixed: usage of product_id for add_to_wishlist shortcode, when global $product is not defined
* Fixed: icon attribute for yith_wcwl_add_to_wishlist shortcode

= 2.0.8 - Released on 29 May 2015 =

* Added: support WP 4.2.2
* Added: redirect to wishlist after login
* Added: check on cookie content
* Added: Frequently Bought Together integration
* Added: text domain to page links
* Tweak: moved cookie update before first cookie usage
* Updated: Italian translation
* Removed: control to unable admin to delete default wishlists
* Removed: login_redirect_url variable

= 2.0.7 - Released on 30 April 2015 =

* Added: WP 4.2.1 support
* Added: WC 2.3.8 support
* Added: "Added to cart" message in wishlist page
* Added: promotional email functionality
* Added: email tab under wishlist panel
* Added: "Move to another wishlist" select
* Added: option to show "Already in wishlist" when multi-wishlist enabled
* Updated: revision of all templates
* Fixed: vulnerability for unserialize of cookie content (Warning: in this way all the old serialized plugins will be deleted and all the wishlists of the non-logged users will be lost)
* Fixed: Escaped add_query_arg() and remove_query_arg()
* Fixed: wishlist count on admin table
* Removed: use of pretty permalinks if WPML enabled

= 2.0.6 - Released on 07 April 2015 =

* Added: system to overwrite wishlist js
* Added: trailingslashit() to wishlist permalink
* Added: "show_empty" filter to get_wishlists() method
* Added: "user that added this product" view
* Added: admin capability to delete default wishlist
* Tweak: removed email from wishlist search
* Tweak: removed empty wishlist from admin table
* Tweak: removed "Save" button from manage template, when not needed
* Fixed: "user/user_id" endpoint
* Fixed: count wishlist items
* Fixed: problem with price inclusive of tax
* Fixed: remove from wishlist for not logged user
* Fixed: twitter share summary

= 2.0.5 - Released on 18 March 2015 =

* Added: option to show create/manage/search links after wishlist table
* Added: option to let only logged user to use wishlist
* Added: option to show a notice to invite users to log in, before wishlist table
* Added: option to add additional notes textarea when sendin e quote request
* Added: popular section on backend
* Added: checkbox to add multiple items to cart from wishlist
* Added: icl_object_id to wishlist page id, to translate pages
* Tweak: updated rewrite rules, to include child pages as wishlist pages
* Tweak: moved WC notices from wishlist template to yith_wcwl_before_wishlist_title hook
* Tweak: added wishlist table id to .load(), to update only that part of template
* Fixed: yith_wcwl_locate_template causing 500 Internal Server Error

= 2.0.4 - Released on 04 March 2015 =

* Added: Options for browse wishlist/already in wishlist/product added strings
* Added: rel nofollow to add to wishlist button
* Tweak: moved wishlist response popup handling to separate js file
* Updated: WPML xml configuration
* Updated: string revision

= 2.0.3 - Released on 19 February 2015 =

* Tweak: set correct protocol for admin-ajax requests
* Tweak: used wc core function to set cookie
* Tweak: let customization of add_to_wishlist shortcodes
* Fixed: show add to cart column when stock status disabled
* Fixed: product existing in wishlist

= 2.0.2 - Released on 16 February 2015 =

* Updated: font-awesome library
* Fixed: option with old font-awesome classes

= 2.0.1 - Released on 13 February 2015 =

* Added: spinner image on loading
* Added: flush rewrite rules on database upgrade
* Fixed: wc_add_to_cart_params not defined issue


= 2.0.0 - Released on 12 February 2015 =

* Initial release
