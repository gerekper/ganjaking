=== YITH WooCommerce Wishlist ===

Contributors: yithemes
Tags: wishlist, woocommerce, products, yit, e-commerce, shop, ecommerce wishlist, yith, woocommerce wishlist, shop wishlist
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 3.0.11
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

YITH WooCommerce Wishlist add all Wishlist features to your website. Needs WooCommerce to work.
WooCommerce 4.2.x compatible.

== Description ==

The wishlist is one of the most powerful and popular tools in an ecommerce shop. Thanks to the wishlist, users can:

* Save their favourite products, find them quickly and easily at a later time and buy them.
* Share the wishlist with relatives and friends for Christmas, birthdays and similar occasions so they can buy them one of the products from the list.
* Share the wishlist on social networks and get indirect advertising for your store.

This means that you’ll be able to loyalise customers, push them to buy and attract new customers any time a wishlist is shared. Not bad for one plugin only, don’t you think?

Our YITH WooCommerce Wishlist has more than **700,000 active installations** and that’s why it’s **the most popular wishlist plugin ever.**

To celebrate this record and say thanks to all the plugin users, we’ve decided to release a new 3.0 version that has improved the design tremendously  and added many new options.

**If you like the new design, please, leave a review to help the plugin grow!**

[Free version live demo >](https://plugins.yithemes.com/yith-woocommerce-wishlist-free/)
[Documentation >](https://docs.yithemes.com/yith-woocommerce-wishlist)

= Basic features =

* Select a page for your wishlist
* Select where to show the shortcode ‘Add to wishlist’
* Show the ‘Remove from wishlist’ button when the product is in the Wishlist
* Show the ‘Add to wishlist’ button also on the Shop page
* Customise columns that will be displayed in the wishlist table
* Product variation support (if the user selects a specific color or size and then adds it to the wishlist, this details will be saved)

= Premium features =

[Premium version live demo >](https://plugins.yithemes.com/yith-woocommerce-wishlist/)

The free version of our plugin works like a charm, but the premium one is an even more powerful tool to increase sales and conversions. By upgrading to the premium version, you can:

* View the wishlists created by logged-in customers
* View a list of popular products (added to wishlists)
* Send promotionals email to users who have added a specific product to their wishlist
* Show the ‘Ask for an estimate’ button to let customers send the content of their wishlist to the admin and get a quotation
* Add optional notes to the quote request
* Enable/disable the wishlist features for unlogged users
* Show a notice to unlogged users: invite them to log in to benefit from all the wishlist functionalities
* Allow users to create as many wishlists as they want
* Allow users to manage wishlists, rename and delete them, add or remove items
* Allow users to search and see registered wishlists
* Allow users to set visibility options for each wishlist, by making them either public (visible to everyone), private (visible to the owner only) or shared (visible only to people it has been shared with)
* Allow users to manage the item quantity in the wishlist
* Show multiple ‘Add to Cart’ buttons in the wishlist table
* Show product price variations (Amazon style)
* Allow users to move an element from one wishlist to another, right from the wishlist table
* Allow users to drag and drop products to arrange their order in the wishlist
* Choose modern & beautiful layouts for the wishlist page and tables
* Provide your customers with nice widgets to help them find their wishlist quickly and easily.

[GET THE PREMIUM VERSION HERE with a 100% Money Back guarantee >](https://yithemes.com/themes/plugins/yith-woocommerce-wishlist/)

== Installation ==

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce Wishlist` from Plugins page

YITH WooCommerce Wishlist will add a new submenu called "Wishlist" under "YITH Plugins" menu. Here you are able to configure all the plugin settings.

== Frequently Asked Questions ==

= Does YITH WooCommerce Wishlist allows adding an “add to wishlist” button on the products on shop page and archive pages? =
Yes, from version 3.0 the plugin also allows showing the Add to wishlist button on your **shop page, category pages, product shortcodes, product sliders,** and all the other places where the WooCommerce products’ loop is used.

= Can I customize the wishlist page? =
Yes, the page is a simple template and you can override it by putting the file template "wishlist.php" inside the "woocommerce" folder of the theme folder.

= Can I move the position of "Add to wishlist" button? =
Yes, you can move the button to another default position or you can also use the shortcode inside your theme code.

= Can I change the style of "Add to wishlist" button? =
Yes, you can change the colors of background, text and border or apply a custom css. You can also use a link or a button for the "Add to wishlist" feature.

= Wishlist page returns a 404 error? =
Try to regenerate permalinks from Settings -> Permalinks by simply saving them again.

= Have you encountered anomalies after plugin update, that did not exist in the previous version? =
This might depend on the fact that your theme overrides plugin templates. Check if the developer of your theme has released a compatibility update with version 3.0 or later of YITH WooCommerce Wishlist. As an alternative you can try the plugin in WordPress default theme to leave out any possible influences by the theme.

= I am currently using Wishlist plugin with Catalog Mode enabled in my site. Prices for products should disappear, yet they still appear in the wishlist page. Can I remove them? =
Yes, of course you can. To avoid Wishlist page to show product prices, you can hide price column from wishlist table. Go to YITH -> Wishlist -> Wishlist Page Options and disable option "Product price".

== Screenshots ==

1. The page with "Add to wishlist" button
2. The wishlist page
3. Show the ‘Add to Cart button in the Wishlist table
4. Show the date when the product has been added to the wishlist (only for logged-in users)
5. Share the wishlist on social channels (available also for guest users)
6. Fully customizable appearance
7. Responsive design
8. Wishlist settings page (1/3)
9. Wishlist settings page (2/3)
10. Wishlist settings page (3/3)


== Changelog ==

= 3.0.11 - Released on 08 Jun 2020 =

* New: support for WooCommerce 4.2
* Update: plugin framework
* Tweak: added WordPress among blocked bot user agents
* Tweak: make sure to finalize session when possible
* Tweak: added link to product in wishlist mobile template
* Fix: Prevent error if default wishlist doesn't exists
* Fix: correctly applied yith_wcwl_is_wishlist_responsive filter to yith_wcwl_is_mobile function
* Fix: avoid to use cache that cannot be invalidated (stop caching queries results, use cache for user wishlists)
* Fix: clear_caches method wasnt properly cleaning cache for guest users
* Dev: added yith_wcwl_add_to_wishlist_icon_html filter
* Dev: added yith_wcwl_add_to_wishlist_heading_icon_html filter
* Dev: added yith_wcwl_add_to_wishlist_data trigger, to allow third party code change data submitted with ATW ajax call

= 3.0.10 - Released on 07 May 2020 =

* New: support for WooCommerce 4.1
* New: prevent some UserAgents from triggering wishlist handling (avoid spam)
* New: added minor css fixes for Storefront theme
* Update: plugin framework
* Tweak: review add process, to avoid unnecessary items update
* Tweak: improved localized date on wishlist table
* Tweak: added wishlist as gutenberg block in new wishlist page
* Tweak: added "Wishlist page" post status
* Tweak: added new check to avoid "Cannot read property contains of undefined" error
* Tweak: added search box to All Wishlist view
* Fix: show remove button on list mobile when at least one of the two buttons is shown on desktop
* Fix: fatal error on empty wishlist page
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
* Fix: notice on empty wishlist page (thanks to ashimhastech)

= 3.0.7 - Released on 03 March 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* New: improved checks on user capabilities
* New: added wishlist widgets to Elementor
* Update: plugin framework
* Tweak: added check on user permission level for all wishlist actions
* Tweak: escape output on templates
* Fix: custom css not being loaded in the page
* Fix: compatibility with YITH Infinite Scrolling when ajax loading is enabled
* Fix: hide share section on wishlist page when "Share wishlist" option is disabled
* Fix: assign correct css rule to border color for Wishlist Table
* Dev: added yith_wcwl_reload_fragments trigger to refresh wishlist fragments
* Dev: added yith_wcwl_remove_hidden_products_via_query filter
* Dev: added yith_wcwl_show_add_to_wishlist filter, to allow dev selectively hide Add to Wishlist buttons
* Dev: new actions on wishlist-view.php template (thanks to Jory)
* Dev: added .editorconfig (thanks to Jory)

= 3.0.6 – Released on 04 February 2020 =

* Tweak: avoid redirect for guest users if wishlist page is set to my-account
* Tweak: minor improvements to localization
* Tweak: update wrong text domains
* Tweak: changed default value for ATW icons
* Tweak: set wishlist session cookie JIT
* Tweak: use secure cookie for sessions, when possible (thanks to Ahmed)
* Tweak: improved cache handling for get_default_wishlist method
* Tweak: even if system cannot set session cookie, calculate session_id and use it for the entire execution
* Update: Italian language
* Update: plugin framework
* Fix: prevent error if list doesn't exists
* Fix: issue with wishlist_id query param
* Fix: items query now search for product in original language
* Fix: returning correct wishlist and user id to yith_wcwl_added_to_wishlist and yith_wcwl_removed_from_wishlist actions (thanks to danielbitzer)
* Fix: issue with default value for yith_wcwl_positions option
* Fix: added key name to avoid DB error during install or update procedure
* Dev: added yith_wcwl_shortcode_share_link_url filter

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
* Dev: added yith_wcwl_wishlist_delete_url filter

= 3.0.3 - Released on 12 December 2019 =

* Tweak: prevent yith_setcookie to process if cookie name is not set
* Tweak: refactored session class to set up session cookie name on demand, when needed (avoid empty cookie name)
* Tweak: minor improvements to functions that require session (count_products, get_default_wishlist..) as a consequence of changes applied to session class

= 3.0.2 - Released on 10 December 2019 =

* Update: plugin framework
* Tweak: added defaults for yith_wcwl_add_to_cart_text option (thanks to ecksiteweb)
* Fix: prevent fatal error when switching from cookies to session

= 3.0.1 - Released on 10 December 2019 =

* Update: language files
* Tweak: restored global $yith_wcwl

= 3.0.0 - Released on 09 December 2019 =

* New: option to show Add to Wishlist button on loops
* New: Add to Wishlist button style when placed over product image
* New: Add to Wishlist button can now turn into Remove from Wishlist after addition
* New: plugin will add variation to wishlist, if user selected one before pressing the button
* New: option to load wishlist templates via Ajax
* New: select add to wishlist icon and/or upload  custom image
* New: guest wishlists are now stored on db, within session id
* New: unified experience for guests and logged in users
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

= 2.2.17 – Released on 29 November 2019 =

* Update: notice handler
* Update: plugin framework

= 2.2.16 – Released on 11 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* Update: plugin framework
* Update: Italian language
* Update: Dutch language
* Tweak: wrapped Add to Wishlist button label into span HTML tag
* Fix: removed occurrences of deprecated properties on promotional email class
* Dev: added new yith_wcwl_product_already_in_wishlist_text_button and yith_wcwl_product_added_to_wishlist_message_button filters
* Dev: added new yith_wcwl_out_of_stock_label and yith_wcwl_in_stock_label filters

= 2.2.15 - Released on 05 November 2019  =

* Update: Plugin framework

= 2.2.14 - Released on 30 October 2019 =

* Update: Plugin framework

= 2.2.13 - Released on 12 August 2019 =

* New: WooCommerce 3.7.0 RC2 support
* New: input to copy wishlist link and share it anywhere
* Update: internal plugin framework
* Fix: redirect url if there is more than one parameter on the url
* Fix: changed escape for share link, to properly escape url special characters

= 2.2.12 - Released on 18 July 2019 =

* Update: internal plugin framework
* Tweak: improved performance on wishlist page, when user is a guest and store has huge catalog (thanks to Dave)
* Dev: add filter yith_wcwl_wishlist_correctly_created on add_wishlist function

= 2.2.11 - Released on 13 June 2019 =

* Update: internal plugin framework
* Tweak: Prevent undefined index user_id when user is logging
* Dev: New action yith_wcwl_default_user_wishlist

= 2.2.10 - Released on 23 April 2019 =

* Update: internal plugin framework

= 2.2.9 - Released on 11 April 2019 =

* New: WooCommerce 3.6.x support
* New: added a WhatsApp share button on mobile
* Tweak: using add_inline_style to include custom css code
* Tweak: no longer adding empty style tag to the page
* Update: Spanish language
* Fix: get the correct value for wishlist name
* Fix: deprecated notice caused by product id attribute being accessed directly

= 2.2.8 - Released on 11 February 2019 =

* New: added support to WooCommerce 3.5.4
* Update: internal plugin framework
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

= 2.2.5 - Released on 24 October 2018 =

* New: updated plugin framework

= 2.2.4 - Released on 04 October 2018 =

* New: added support to WooCoommerce 3.5
* New: added support to WordPress 4.9.8
* New: updated plugin framework
* New: added method that returns localization variables
* Tweak: type attribute from <script> tag
* Update: Spanish language
* Update: Italian language
* Dev: added new filter yith_wcwl_localize_script to let third party dev filter localization variables
* Dev: added new filter yith_wcwl_share_conditions to display the share buttons for no logged users
* Dev: added new filter yith_wcwl_set_cookie to let third party code skip cookie saving
* Dev: added new filter yith_wcwl_wishlist_param to change query-string param
* Dev: added new filter yith_wcwl_remove_product_wishlist_message_title

= 2.2.3 - Released on 26 July 2018 =

* Update: Plugin core.
* Update: Translation file.

= 2.2.2 - Released on 28 May 2018 =

* New: WooCommerce 3.4 compatibility
* New: WordPress 4.9.6 compatibility
* New: updated plugin framework
* New: GDPR compliance
* Tweak: replaced create_function with a proper class method, to improve compatibility with PHP 7.2 and avoid warnings
* Fix: js error when switching from Premium version to Free
* Fix: preventing add_rewrite_rule when WPML is active, to avoid possible Internal Server Error (thanks to Adri & Bruno)
* Fix: icon replacement not working on variable Add to Cart
* Fix: preventing warning "Illegal string offset" when get_availability() returns empty string instead of array

= 2.2.1 - Released on 31 January 2018 =

* New: tested with WooCommerce 3.3.0
* Fix: issue with Add to Wishlist shortcode when global $product not defined

= 2.2.0 - Released on 11 January 2018 =

* New: WooCommerce 3.2.6 compatibility
* New: plugin-fw 3.0
* New: added js compatibility to Infinite Scrolling
* Tweak: improved wishlist-view template checks and params
* Tweak: wishlist now registers (and shows) "date added" param for unauthenticated users too
* Tweak: added check over product object, to avoid Fatal when printing Add to Wishlist shortcode
* Fix: fixed security vulnerability, causing possible SQL Injections (huge thanks to John C. and Sucuri Vulnerability Research team)
* Dev: added yith_wcwl_removing_from_wishlist / yith_wcwl_removed_from_wishlist hooks
* Dev: added params to main triggers in wishlist js code

= 2.1.2 - Released on 11 May 2017 =

* Tweak: updated FontAwesome to 4.7.0
* Fix: possible warning when empty rewrite rules
* Fix: problem with custom CSS inclusion, when not located in child theme
* Fix: using current_product instead of global product when retrieving product type (prevents a Fatal error when placing Add to Wishlist outside the loop)

= 2.1.1 - Released on 21 April 2017 =

* Tweak: improved endpoints creation, with dynamic flush
* Tweak: added check over wc_print_notices existence, to avoid possible fatal errors
* Tweak: updated plugin-fw
* Fix: problem with duplicated meta
* Fix: product created wince WC 3.0.x not being shown on wishlist

= 2.1.0 - Released on 03 April 2017 =

* New: WooCommerce 3.0-RC2 compatibility
* New: WordPress 4.7.3 compatibility
* New: Korean translation (thanks to kocne)
* New: Croatian translation (thanks to Barbara V.)
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
* Fix: get_template_directory for custom wishlist js
* Fix: stock_status not existing when stock column isn't shown
* Dev: action as second param for yith_wcwl_wishlist_page_url filter
* Dev: applied filter yith_wcwl_no_product_to_remove_message also for message on wishlist-view template
* Dev: added filter yith_wcwl_add_wishlist_user_id
* Dev: added filter yith_wcwl_add_wishlist_slug

= 2.0.16 - Released on 14 June 2016 =

* Added: WooCommerce 2.6 support
* Tweak: changed uninstall procedure to work with multisite and delete plugin options
* Tweak: removed description and image from facebook share link (fb doesn't allow anymore)
* Fixed: product query (GROUP By and LIMIT statement conflicting)

= 2.0.15 - Released on 04 April 2016 =

* Added: filter yith_wcwl_is_product_in_wishlist to choose whether a product is in wishlist or not
* Added: filter yith_wcwl_cookie_expiration to set default wishlist cookie expiration time in seconds
* Tweak: updated plugin-fw
* Fixed: get_products query returning product multiple times when product has more then one visibility meta

= 2.0.14 - Released on 21 March 2016 =

* Added: Dutch translation (thanks to w.vankuipers)
* Added: Danish translation (thanks to Morten)
* Added: yith_wcwl_is_wishlist_page function to identify if current page is wishlist page
* Added: filter yith_wcwl_settings_panel_capability for panel capability
* Added: filter yith_wcwl_current_wishlist_view_params for shortcode view params
* Added: "defined YITH_WCWL" check before every template
* Added: check over existance of $.prettyPhoto.close before using it
* Added: method count_add_to_wishlist to YITH_WCWL class
* Added: function yith_wcwl_count_add_to_wishlist
* Tweak: Changed ajax url to "relative"
* Tweak: Removed yit-common (old plugin-fw) deprecated since 2.0
* Tweak: Removed deprecated WC functions
* Tweak: Skipped removed_from_wishlist query arg adding, when external product
* Tweak: Added transients for wishist counts
* Tweak: Removed DOM structure dependencies from js for wishlist table handling
* Tweak: All methods/functions that prints/counts products in wishlist now skip trashed or not visible products
* Fixed: shortcode callback setting global product in some conditions
* Fixed: typo in hook yith_wccl_table_after_product_name (now set to yith_wcwl_table_after_product_name)
* Fixed: notice appearing when wishlist page slug is empty

= 2.0.13 - Released on 17 December 2015 =

* Added: check over adding_to_cart event data existance in js procedures
* Added: 'yith_wcwl_added_to_cart_message' filter, to customize added to cart message in wishlist page
* Added: nofollow to "Add to Wishlist" links, where missing
* Added: 'yith_wcwl_email_share_subject' filter to customize share by email subject
* Added: 'yith_wcwl_email_share_body' filter to customize share by email body
* Added: function "yith_wcwl_count_all_products"
* Fixed: plugin-fw loading

= 2.0.12 - Released on 23 October 2015 =

* Added: method to count all products in wishlist
* Tweak: Added wishlist js handling on 'yith_wcwl_init' triggered on document
* Tweak: Performance improved with new plugin core 2.0
* Fixed: occasional fatal error for users with outdated version of plugin-fw on their theme

= 2.0.11 - Released on 21 September 2015 =

* Added: spanish translation (thanks to Arman S.)
* Added: polish translation (thanks to Roan)
* Added: swedish translation (thanks to Lallex)
* Updated: changed text domain from yit to yith-woocommerce-wishlist
* Updated: changed all language file for the new text domain

= 2.0.10 - Released on 12 August 2015 =

* Added: Compatibility with WC 2.4.2
* Tweak: added nonce field to wishlist-view form
* Tweak: added yith_wcwl_custom_add_to_cart_text and yith_wcwl_ask_an_estimate_text filters
* Tweak: added check for presence of required function in wishlist script
* Fixed: admin colorpicker field (for WC 2.4.x compatibility)

= 2.0.9 - Released on 24 July 2015 =

* Added: russian translation
* Added: WooCommerce class to wishlist view form
* Added: spinner to plugin assets
* Added: check on "user_logged_in" for sub-templates in wishlist-view
* Added: WordPress 4.2.3 compatibility
* Added: WPML 3.2.2 compatibility (removed deprecated function)
* Added: new check on is_product_in_wishlist (for unlogged users/default wishlist)
* Tweak: escaped urls on share template
* Tweak: removed new line between html attributes, to improve themes compatibility
* Fixed: WPML 3.2.2 compatibility (fix suggested by Konrad)
* Fixed: regex used to find class attr in "Add to Cart" button
* Fixed: usage of product_id for add_to_wishlist shortcode, when global $product is not defined
* Fixed: icon attribute for yith_wcwl_add_to_wishlist shortcode

= 2.0.8 - Released on 29 May 2015 =

* Added: support WP 4.2.2
* Added: Persian translation
* Added: check on cookie content
* Added: Frequently Bought Together integration
* Tweak: moved cookie update before first cookie usage
* Updated: Italian translation
* Removed: login_redirect_url variable

= 2.0.7 - Released on 30 April 2015 =

* Added: WP 4.2.1 support
* Added: WC 2.3.8 support
* Added: "Added to cart" message in wishlist page
* Added: Portuguese translation
* Updated: revision of all templates
* Fixed: vulnerability for unserialize of cookie content (Warning: in this way all the old serialized plugins will be deleted and all the wishlists of the non-logged users will be lost)
* Fixed: Escaped add_query_arg() and remove_query_arg()
* Removed: use of pretty permalinks if WPML enabled

= 2.0.6 - Released on 08 April 2015 =

* Added: system to overwrite wishlist js
* Added: trailingslashit() to wishlist permalink
* Added: chinese translation
* Added: "show_empty" filter to get_wishlists() method
* Fixed: count wishlist items
* Fixed: problem with price inclusive of tax
* Fixed: remove from wishlist for not logged user
* Fixed: twitter share summary

= 2.0.5 - Released on 19 March 2015 =

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

= 2.0.2 - Released on 17 February 2015 =

* Updated: font-awesome library
* Fixed: option with old font-awesome classes

= 2.0.1 - Released on 13 February 2015 =

* Added: spinner image on loading
* Added: flush rewrite rules on database upgrade
* Fixed: wc_add_to_cart_params not defined issue

= 2.0.0 - Released on 12 February 2015 =

* Added: Support to woocommerce 2.3
* Added: New color options
* Tweak: Add to cart button from woocommerce template
* Tweak: Share links on template
* Tweak: Code revision
* Tweak: Use wordpress API in ajax call instead of custom script
* Updated: Plugin core framework


= 1.1.7 - Released on 03 December 2014 =

* Added: Support to WooCommerce Endpoints (@use yit_wcwl_add_to_cart_redirect_url filter)
* Added: Filter to shortcode html
* Added: Title to share

= 1.1.6 - Released on 16 September 2014 =

* Updated: Plugin Core Framework
* Updated: Languages file
* Tweek:   WPML Support Improved

= 1.1.5 - Released on 30 June 2014 =

* Added: Share wishlist by email

= 1.1.4 - Released on 26 June 2014 =

* Fixed: wrong string for inline js on remove link
* Fixed: wrong string for inline js on add to cart link

= 1.1.3 - Released on 05 June 2014 =

* Added: Options Tabs Filter
* Fixed: Various Bugs

= 1.1.2 - Released on 21 March 2014 =

* Fixed: Warnings when Show Stock Status is disabled
* Fixed: Restored page options on WooCommerce 2.1.x

= 1.1.1 - Released on 26 February 2014 =

* Fixed: Inability to unistall plugin
* Fixed: Redirect to cart page from wishlist page

= 1.1.0 - Released on 13 February 2014 =

* Added: Support to WooCommerce 2.1.x
* Added: Spanish (Mexico) translation by Gabriel Dzul
* Added: French translation by Virginie Garcin
* Fixed: Revision Italian Language po/mo files

= 1.0.6 - Released on 18 November 2013 =

* Added: Spanish (Argentina) partial translation by Sebastian Jeremias
* Added: Portuguese (Brazil) translation by Lincoln Lemos
* Fixed: Share buttons show also when not logged in
* Fixed: Price shows including or excluding tax based on WooCommerce settings
* Fixed: Better compatibility for WPML
* Fixed: Price shows "Free!" if the product is without price
* Fixed: DB Table creation on plugin activation

= 1.0.5 - Released on 14 October 2013 =

* Added: Shared wishlists can be seens also by not logged in users
* Added: Support for WPML String translation
* Updated: German translation by Stephanie Schlieske
* Fixed: Add to cart button does not appear if the product is out of stock

= 1.0.4 - Released on 04 September 2013 =

* Added: partial Ukrainian translation
* Added: complete German translation. Thanks to Stephanie Schliesk
* Added: options to show/hide button add to cart, unit price and stock status in the wishlist page
* Added: Hebrew language (thanks to Gery Grinvald)

= 1.0.3 - Released on 31 July 2013 =

* Fixed: Minor bugs fixes

= 1.0.2 - Released on 24 June 2013 =

* Fixed: Fatal error to yit_debug with yit themes

= 1.0.1 - Released on 30 May 2013 =

* Tweak: Optimized images
* Updated: internal framework

= 1.0.0 - Released on 23 May 2013 =

* Initial release
