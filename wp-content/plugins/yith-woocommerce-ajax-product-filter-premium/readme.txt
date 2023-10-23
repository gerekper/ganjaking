=== YITH WooCommerce Ajax Navigation Premium ===

Contributors: yithemes
Tags: woocommerce, widget, ajax, ajax filtered nav, ajax navigation, ajax filtered navigation
Requires at least: 6.1
Tested up to: 6.3
Stable tag: 4.26.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Changelog =

= 4.26.0 - Released on 13 September 2023 =

* New: support for WooCommerce 8.1
* Update: YITH plugin framework
* Fix: deprecated "Automatic conversion of false to array" in PHP 8.1

= 4.25.0 - Released on 17 August 2023 =

* New: support for WooCommerce 8.0
* New: support for WordPress 6.3
* Update: YITH plugin framework

= 4.24.1 - Released on 18 July 2023 =

* Update: YITH plugin framework
* Fix: wrong percentage calculation on filter-item width

= 4.24.0 - Released on 13 July 2023 =

* New: support for WooCommerce 7.9
* Update: YITH plugin framework

= 4.23.0 - Released on 15 June 2023 =

* New: support for WooCommerce 7.8
* Update: YITH plugin framework
* Fix: close toggles on click only for horizontal presets
* Fix: style issue with Image selector on admin panel
* Dev: declared HPOS compatibilty (no actions needed)


= 4.22.0 - Released on 16 May 2023 =

* New: support for WooCommerce 7.7
* Update: YITH plugin framework
* Fix: make pagination url work together with filter session endpoint

= 4.21.0 - Released on 17 April 2023 =

* New: support for WooCommerce 7.6
* Tweak: changed trigger used to re-init wishlist elements after filtering
* Update: YITH plugin framework
* Fix: label not shown if text is "0"

= 4.20.1 - Released on 23 March 2023 =

* New: add option to hide Save button inside Filter popup in horizontal mode
* Update: YITH plugin framework

= 4.20.0 - Released on 13 March 2023 =

* New: support for WordPress 6.2
* New: support for WooCommerce 7.5
* Update: YITH plugin framework
* Fix: retrieve correctly the max price in the price filter

= 4.19.0 - Released on 08 February 2023 =

* New: support for WooCommerce 7.4
* Update: YITH plugin framework
* Tweak: make sure that formatted terms array is filtered before returning it
* Tweak: minor fix to Filter dependencies on admin panel
* Dev: replacing the on-off field in the Presets table with the plugin FW
* Dev: CodeSniffer fixes
* Dev: added yith_wcan_filter_title_html filter

= 4.18.0 - Released on 10 January 2023 =

* New: support for WooCommerce 7.3
* Update: updated Readme
* Update: YITH plugin framework
* Fix: avoid price slider to trigger immediate filtering after handle drag
* Fix: removed deprecated method used for Elementor compatibility
* Dev: added new filter yith_wcan_get_filters

= 4.17.0 - Released on 13 December 2022 =

* New: support for WooCommerce 7.2
* New: added Brazilian Portuguese as community language
* Tweak: minor improvments to input sanitization for price slider
* Tweak: refactored js that handles toggles in preset
* Fix: sass deprecated
* Dev: added yith_wcan_toggle_element trigger
* Dev: added new filter yith_wcan_in_stock_filter_url

= 4.16.0 - Released on 14 November 2022 =

* New: support for WooCommerce 7.1
* New: support for WodPress 6.1
* Tweak: add rel nofollow attribute to Active Labels anchor, when SEO option is enabled
* Tweak: check if preset has relevnt filters before showing it
* Tweak: prevent JS error with jQuery autocomplete library and filter dropdown
* Update: YITH plugin framework
* Fix: patched security vulnerability

= 4.15.0 - Released on 10 October 2022 =

* New: support for WooCommerce 7.0
* Update: YITH plugin framework
* Dev: remove auto-complete for dropdown search inputs

= 4.14.0 - Released on 14 September 2022 =

* New: support for WooCommerce 6.9
* Tweak: improved dependencies handling for filter items
* Tweak: added reload_on_back localized parameter
* Update: YITH plugin framework
* Fix: avoid error with assets dependencies on Salient theme compatibility class
* Fix: JS notice on init Ion Range Slider input (filter by Price slider)

= 4.13.0 - Released on 08 August 2022 =

* New: support for WooCommerce 6.8
* New: added Bricks compatibility
* Tweak: add rel=nofollow attribute to items inside dropdowns handled by plugin js
* Update: YITH plugin framework
* Dev: added yith_wcan_custom_css filter
* Dev: added new filter yith_wcan_set_supported_filter_design
* Dev: Removed 3rd party CSS libraries not strictly needed in the plugin
* Dev: new filter yith_wcan_filter_get_formatted_terms_for_{->get_formatted_taxonomy()} for filter specific taxonomy before print

= 4.12.0 - Released on 07 July 2022 =

* New: support for WooCommerce 6.7
* Tweak: refactored JS according to new wp-prettier standards
* Tweak: scroll top after filters on mobile too
* Tweak: add rel=nofollow attribute to items inside dropdowns handled by plugin js
* Update: YITH plugin framework
* Fix: moved premium-only action to premium class

= 4.11.0 - Released on 9 June 2022 =

* New: support for WooCommerce 6.6
* Tweak: remove reference to heading tag from CSS, and replaced with specific clases (allow to change filters titles tags without breaking appearance)
* Update: YITH plugin framework

= 4.10.0 - Released on 10 May 2022 =

* New: support for WordPress 6.0
* New: support for WooCommerce 6.5
* Update: YITH plugin framework
* Fix: add in stock status to filtered products query

= 4.9.0 - Released on 04 April 2022 =

* New: support for WooCommerce 6.4
* Tweak: improve toggle icon position on RTL
* Update: YITH Plugin Framework
* Fix: show price with taxes in product price slider filter
* Fix: JS error when rendering plugin blocks on Getenberg editor
* Fix: avoid duplicated entry on window history when shortcode.js script is loaded

= 4.8.0 - Released on 03 March 2022 =

* New: support for WooCommerce 6.3
* Tweak: minor improvement to label filters appearance
* Update: YITH Plugin Framework
* Fix: fixed save of checkboxes for the Legacy Widgets
* Fix: reload on history change now only happens when history was pushed by filters

= 4.7.0 - Released on 09 February 2022 =

* New: support for WooCommerce 6.2
* Tweak: reload location on popstate, to work with browser navigation
* Tweak: improved history handling
* Tweak: hide SEO url option, when Ajax filtering is disabled
* Update: YITH Plugin Framework
* Dev: added yith_wcan_filter_tax_is_term_hidden filter

= 4.6.0 - Released on 03 January 2022 =

* New: support for WooCommerce 6.1
* New: support for WordPress 5.9
* Tweak: close modal on mobile after clicking on Active filter label to disable filter
* Tweak: minor improvments to js code, to avoid duplicated handlers applied to same elements
* Update: YITH Plugin Framework
* Fix: terms sorting when retrieving terms arary indexed by term id
* Fix: undefined wp_query
* Dev: added yith_wcan_query_vars_to_merge filter

= 4.5.0 - Released on 01 December 2021 =

* New: support for WooCommerce 6.0
* New: removed usage of _register_controls deprecated method from Elementor integration
* Tweak: offered support for Elementor < 2.9.0
* Tweak: update originalSearch after filters
* Update: YITH Plugin Framework
* Fix: Terms options not showing on first load on preset edit page
* Fix: leave default query vars intact when not using permalink structure
* Fix: fallback method to make Active Labels work even when no preset is shown in the page
* Fix: deprecated elementor method _content_template and remove the phpcs:ignore
* Fix: Avoid Term Boxes with default values on Preset edit
* Fix: prevent warning when $_GET contains non-scalar values

= 4.4.0 - Released on 11 November 2021 =

* New: support for WooCommerce 5.9
* Tweak: improved terms handling when use all terms option is enabled
* Update: YITH Plugin Framework
* Fix: filter clone button icon
* Fix: checkbox not being correctly initialized after Load More action in admin
* Fix: wrong description in option panel
* Fix: children terms order, when Terms Order option is selected
* Fix: active price range issue with zero value
* Fix: retrive session during rquest parsing, and merge its query-vars to global $wp query-vars
* Fix: prevent notices when no session is set

= 4.3.0 - Released on 10 October 2021 =

* New: support for WooCommerce 5.8
* Tweak: init custom fields inside dropdown too
* Update: YITH Plugin Framework
* Dev: added yith_wcan_is_filtered filter

= 4.2.1 - Released on 27 September 2021 =

* Update: YITH Plugin Framework
* Fix: debug info feature removed for all logged in users

= 4.2.0 - Released on 24 September 2021 =

* New: support for WooCommerce 5.7
* Update: YITH plugin framework
* Tweak: improved plugin's internal cache management
* Tweak: improved integration with YITH WooCommerce Color & Label Variations, when showing variations on loop
* Tweak: price slider is considered active even if min/max are out of range, to keep customer's selected values across multiple filtering operations
* Tweak: improved appearance of terms hierarchy when in horizontal mode
* Tweak: suppress plugin's filters when retrieving in-stock products
* Tweak: better compatibility with products query (supports queries with more than one post type)
* Tweak: avoid duplicated ID for select filters
* Tweak: add filtered body class via JS, immediately after filtering action takes place
* Tweak: improved terms hierarchy appearance on RTL
* Tweak: delete plugin transients when C&L configuration changes
* Fix: apply changes to the query even when it retrieves a product taxonomy
* Fix: add disabled class on empty price ranges, when adoptive option is set to OR
* Fix: set correct 'include' parameter when retrieving terms' children
* Fix: prevent possible notice when handling terms hierarchy with use_all_terms enabled
* Fix: allow system to retrieve C&L term configuration, when Use all Terms is enabled
* Fix: prevent notice when filtering by price with 0 as min_price
* Dev: Reset Filters button now has its own set of filters, distinct from ones used for Apply Filters button
* Dev: added yith_wcan_filter_tax_label_image_size and yith_wcan_filter_tax_color_image_size filters
* Dev: added yith_wcan_filter_tax_label_image_attr and yith_wcan_filter_tax_color_image_attr filters
* Dev: added yith_wcan_supported_filters_parameters trigger
* Dev: added toggles_open_on_modal param to localize, and allow showing toggles as open on mobile modal
* Dev: added yith_wcan_doing_filters function to check if system is performing filters
* Dev: added yith_wcan_suppress_cache filter

= 4.1.1 - Released on 04 August 2021 =

* New: support for WordPress 5.8
* New: support for WooCommerce 5.6
* Update: YITH plugin framework
* Tweak: code refactoring
* Tweak: appearance of mobile widget close icon
* Tweak: avoid caching query_vars until wp performed main query, to be sure to include all parameters available
* Tweak: avoid possible error with Tax queries without terms
* Fix: prevent possible error on preset edit page when running PHP 8
* Fix: populateFilter method causing issues when loading more terms on backend
* Fix: avoid possible notice when shop has product taxonomies with no rewrite defined
* Fix: empty terms when using AutoPopulate options
* Fix: plugin can now disable filters with numeric slugs
* Fix: issue with terms Drag&Drop on backend
* Dev: added second parameter to yith_wcan_filter_tax_additional_item_classes filter
* Dev: added yith_wcan_pre_get_supported_taxonomies filter, to allow hijack array of supported taxonomies
* Dev: added yith_wcan_pre_reduce_tax_query filter, to allow programmatically skip reduce_tax_query execution

= 4.1.0 - Released on 18 June 2021 =

* New: support for WooCommerce 5.4
* New: horizontal layout
* New: filter by featured
* New: auto-populate taxonomy filters
* New: support for YITH WooCommerce Color and Label Variations
* New: support for Hello Elementor theme
* New: automatically use term image, when available
* New: layout options for review filter
* New: multiple selection for review filter
* New: layout options for price range filter
* New: multiple selections for price range filter
* New: layout options for price slider filter
* New: option to automatically populate min/max values for price sliders
* New: option to choose where to show term labels in color/label layouts
* New: added new sorting option for terms (make use of order meta set by WC)
* New: added cron to delete transient with the old cache version
* Update: YITH plugin framework
* Tweak: changed name of the flag used to suppress filters query processing
* Tweak: use svg instead of text for x icon in filters
* Tweak: improved compatibility with Porto theme
* Tweak: hide modal opener when no preset is added to the page
* Tweak: hide preset on mobile, when Modal is enabled
* Fix: close modal after resetting filters
* Fix: issue with query-vars processing, causing orderby option to break
* Fix: order by label when price-desc is selected
* Fix: avoid possible JS error Cannot use in operator to search in null
* Fix: url generation code not working when page body contains yith_wcan:sharing_url string in unexpected places
* Fix: multiple selection for categories, tags and brands does not work on OR mode in old widgets
* Fix: avoid filters overwriting after new page is loaded (index overriding)
* Dev: use babel to make scripts work on older browsers/devices
* Dev: added trigger yith_wcan_preset_initialized
* Dev: added new yith_wcan_query_post_in filter, to programmatically change products used by our plugin
* Dev: added specific body-class when filter appears in modal on mobile
* Dev: added new yith_wcan_show_mobile_modal_opener filter, to programmatically hide the button

= 4.0.4 - Released on 18 May 2021 =

* New: support for WooCommerce 5.3
* Update: YITH plugin framework
* Tweak: improved WPML metabox handling
* Tweak: added option to skip sanitization of url parameters
* Fix: pass all slugs to rawurldecode before printing them as HTML
* Fix: pass all terms coming from query string to sanitize_title
* Fix: possible error when adding all terms on backend filter
* Fix: prevent possible js error when not using Instant Filters
* Fix: method that tests orderby param is now able to detect price-desc
* Fix: wrong text domain on a couple of strings
* Fix: price slider font-family
* Fix: cast both property-to-remove and term-slug to string before comparison, to avoid errors when deactivating filters
* Dev: added method to retrieve original post in Preset object
* Dev: added filter yith_wcan_all_filters_label to allow third party code to change All label in select filters
* Dev: added trigger yith_wcan_filters_parameters
* Dev: added filter yith_wcan_is_filter_active

= 4.0.3 - Released on 20 April 2021 =

* New: support for WooCommerce 5.2
* Update: YITH plugin framework
* Update: language files
* Tweak: better handle multiple filters for the same taxonomy in the same preset
* Tweak: prevent redirect to product page when filtering a search page and getting a single result
* Tweak: added base url for filtering operation
* Tweak: improved performance by optimizing tax_query array
* Tweak: avoid unnecessary queries to post when just want to list presets
* Tweak: added new transient yith_wcan_object_in_terms to improve performance
* Tweak: execute legacy post processing only when old widgets are active
* Tweak: improved WPML compatibility for presets
* Tweak: added pagination to preset edit page
* Fix: possible error with PHP older than 7.x
* Fix: changed priority where to install endpoints, to include any custom taxonomy registered @init:10
* Fix: prevent system from removing original page querystring, when clearing filters
* Fix: correctly remove taxonomy parameters after disabling filter on frontend
* Fix: show collapse open on mobile when filter is active
* Fix: add visibility query to plugins tax_query
* Dev: added filter yith_wcan_remove_current_term_from_active_filters to include current category among Active Filters
* Dev: added yith_wcan_filter_reset_button_class hook to filter reset button class

= 4.0.2 - Released on 10 March 2021 =

* New: support for WordPress 5.7
* New: support for WooCommerce 5.1
* New: added German translation
* Update: YITH plugin framework
* Tweak: prevent js error when unable to locate the preset for scrolling
* Tweak: removed checks preventing from correctly using labels/reset shortcodes
* Tweak: improve filters on search page
* Tweak: manage separate caches per language
* Tweak: avoid to apply min/max price filters to unsupported queries
* Tweak: prevent duplicated contents when refreshing fragments with specific target
* Fix: bind stock transient to transient version
* Fix: filter_session endpoint not working on product taxonomies
* Fix: filter using /shop base url even on taxonomies page (current url is now used)
* Fix: show hierarchy containing currently active filters as expanded
* Fix: wrong condition for slider filter
* Fix: text color for labels not correctly applied
* Fix: price-desc sorting option not working as expected
* Fix: add check over Scroll Top option, before animating the body
* Fix: do widget upgrade failing from WooCommerce Tools window
* Fix: notice when executing widget upgrade procedure
* Dev: added show_current_children variable in localize, to choose whether to show hierarchy for current term as expanded by default
* Dev: added actions to preset edit view
* Dev: added yith_wcan_save_preset hook
* Dev: added new filters yith_wcan_in_stock_text and yith_wcan_on_sale_text
* Dev: added yith-wcan-ajax-loading trigger before replacing items in page
* Dev: added scroll_target variable to localize, to allow third party code filter scroll target after filtering (using yith_wcan_shortcodes_script_args filter)

= 4.0.1 - Released on 17 February 2021 =

* New: Greek translation
* Update: YITH plugin framework
* Fix: issue with legacy widgets counts (inverted parameter)
* Fix: disable by default suppress conditional tags, to avoid template problems
* Fix: improve suppress canonical_redirect, to apply whenever filtering
* Fix: query method can now retrieve all presets, instead of being limited to latest 5

= 4.0.0 - Released on 11 February 2021 =

* New: Plugin UI/UX restyling
* New: added filters preset
* New: added YITH AJAX Filters Preset widget
* New: added [yith_wcan_filters] shortcode
* New: added Gutenberg block to display Filters Preset in any page
* New: added Elementor widget to display Filters Preset in any page
* New: filters will now work on any page, for any loop of products
* New: added user friendly mobile layout
* New: added labels that shows active filters
* New: added [yith_wcan_reset_button] shortcode
* New: added Gutenberg block to display Reset Filters button in any page
* New: added Elementor widget to display Reset Filters button in any page
* New: added customization options for the Preset
* New: added custom appearance for key visual elements of the filters
* New: added automatic upgrade procedure from old system to new preset
* New: added custom urls filtering, for easy sharing and better SEO
* New: full compatibility to all products taxonomies, including custom ones
* Update: YITH plugin framework
* Tweak: major code refactoring and optimization

= 3.11.7 - Released on 09 February 2021 =

* New: support for WooCommerce 5.0
* Update: YITH plugin framework
* Fix: non-standard hooks
* Fix: jQuery.on( 'ready' ) was deprecated

= 3.11.6 - Released on 11 January 2021  =

* New: Support for WooCommerce 4.9
* Fix: Dropdown not working for 'select'
* Dev: yith_wcan_{$arg}_current_filter hook to exclude current category in filters

= 3.11.5 - Released on 03 December 2020  =

* New: Support for WooCommerce 4.8
* New: Support for WordPress 5.6
* New: Support for Twenty Twenty-One theme
* New: French translation
* New: German translation
* Dev: yith_wcan_categories_ancestors hook to filter parent categories on category query

= 3.11.4 - Released on 11 November 2020  =

* New: Support for WooCommerce 4.7
* New: Possibility to update plugin via WP-CLI
* New: Possibility to update plugin via ManageWP
* New: Greek translation
* Update: plugin framework

= 3.11.3 - Released on 16 October 2020  =

* New: Support for WooCommerce 4.6
* Update: plugin framework
* Dev: yith_wcan_ywccl_support hook to disable the integration with Colors and Labels plugin

= 3.11.2 - Released on 14 September 2020  =

* New: Support for WooCommerce 4.5
* New: Support for WordPress 5.5
* New: Support for YITH WooCommerce Wishlist
* Update: Plugin framework
* Fix: Dropdown filter disappear if a filter match with a single product
* Dev: yith_wcan_filter_label_text hook to filter label text

= 3.11.1 - Released on 22 June 2020  =

* New: Support for WooCommerce 4.3
* Update: plugin framework
* Fix: Security issues on form submit in admin area
* Fix: SortBy Widget doesn't work in search page

= 3.11.0 - Released on 01 June 2020  =

* New: Show product count in dropdown filter
* New: Support for WooCommerce 4.2
* Update: plugin framework
* Fix: Reset filter show category page instead of shop
* Dev: yit_get_filter_args hook for filter the filter url arg

= 3.10.0 - Released on 30 April 2020  =

* New: Support for WooCommerce 4.1
* Update: plugin framework
* Dev: yith_wcan_color_get_objects_in_term to filter the get_product_in_terms function

= 3.9.0 - Released on 10 March 2020  =

* New: Support for WordPress 5.4
* New: Support for WooCommerce 4.0
* Update: plugin framework
* Fix: Undefined index prices

= 3.8.2 - Released on 27 December 2019  =

* New: Support for WooCommerce 3.9
* Fix: Missing current tag in Stock and On sale filter
* Fix: Not updating filters when the customer apply a filter by price

= 3.8.1 - Released on 28 November 2019  =

* Tweak: Prevent plugin query in non WooCommerce pages on frontend
* Update: plugin framework
* Fix: Removed error log
* Fix: Variable $li_printend undefined on frontend

= 3.8.0 - Released on 30 October 2019  =

* New: Support for WordPress 5.3
* New: Support for WooCommerce 3.8
* Update: plugin framework
* Fix: Typo in Product Tag taxonomy
* Fix: Call get_query_object() on null
* Fix: Hierarchical style for category widget with Flatsome theme
* Fix: Current category filter style issue for hierarchical categories
* Fix: Wrong margin left in price filter
* Fix: dropdown feature doesn't work for WooCommerce Price Filter
* Fix: woocommerce price filter does not work without slider feature
* Dev: New filter 'yith_wcan_skip_check_on_product_in_term'

= 3.7.0 - Released on 07th August, 2019  =

* New: Support for WooCommerce 3.7
* New: Support for Themify theme
* Fix: Style issues with Storefront theme
* Fix: filter by price does not work correctly with float price interval
* Fix: Show all tags hierarchical doesn't works
* Dev: new filter 'yith_wcan_before_closing_list_item'
* Dev: pre_yith_wcan_wp_get_terms to filter get term lists

= 3.6.5 - Released on 29th May, 2019 =

* Fix: Unable to reset filter in subcategory page
* Dev: yith_wcan_display_type_select hook
* Dev: yith_wcan_select_type_query_arg hook
* Dev: yith_wcan_select_type_current_widget_check hook
* Dev: yith_wcan_select_filter_operator hook

= 3.6.4 - Released on 08th April, 2019 =

* New: Support to WooCommerce 3.6
* Update: Plugin Core Framework
* Fix: undefined variable term_id in attributes table

= 3.6.3 - Released on 20th February, 2018 =

* Fix: Wrong reset page with WooCommerce Filter by price widget
* Fix: Support to Aurum theme - Filter issue using AND operator
* Update: Plugin Core Framework
* Update: All language files (Italian, Dutch and Spanish)
* Tweak: New Widgets name
* Dev: yith_wcan_unfiltered_args hook to manage the unfiltered args for get_posts
* Dev: yith_wcan_skip_no_product_count_bicolor hook to skip show bicolor wit no products

= 3.6.2 - Released on 05th December, 2018 =

* New: Support for WordPres 5.0
* Tweak: Option description
* Fix: Undefined index: source_tax in YITH Reset Filter widget
* Fix: Issue with reset filter in product categories page
* Dev: new parameter for filter 'yith_wcan_dropdown_label'

= 3.6.1 - Released on 23rd October, 2018 =

* Update: Plugin core framework
* Tweak: Replace the old function remove_premium_query_arg with the new one yith_remove_premium_query_arg
* Fix: Reset button doesn't works if a variable use a slash char in query string
* Dev: new filter 'yith_wcan_term_name_to_show'

= 3.6.0 - Released on 11th October, 2018 =

* New: Support for WooCommerce 3.5
* New: Option to add rel="nofollow" to filter links
* New: Support for Aardvark theme
* New: Support for Aurum theme
* New: Support for UX Shop theme
* New: Support for YooTheme theme
* Fix: Wrong results with search query string
* Fix: Double chosen icon with themes that override layered navigation style
* Fix: Ghost query arg when try to deactivate category filter
* Tweak: Widget reinit after ajax call
* Tweak: Flatsome optimization
* Tweak: Prevent untrailingslashit on filter url for SEO optimization
* Update: Spanish language
* Update: Italian Language
* Dev: yith_wcan_skip_no_products_label hook

= 3.5.1 - Released on 14th March, 2018 =

* New: Support for Adrenalin theme by CommerceGurus
* Tweak: Removed old YITH_WCAN_Helper class
* Updated: Plugin core framework
* Fix: Unable to reset filters in product categories page
* Fix: Product tag filter doesn't works if shop display categories instead of products
* Fix: PHP Notice: Undefined index: display
* Fix: $count not defined in loop

= 3.5.0 - Released on 01st Febrary, 2018 =

* New: 100% German translation (Thanks to Thomas)
* New: Colorpicker type of YITH WooCommerce Color and Label Premium integration
* New: Support for WooCommerce 3.3.0
* Fix: In stock and on sale filters doesn't works if you show the product categories in shop page
* Fix: Undefined index "dropdown-type" in shop page
* Fix: Reset filter doesn't works if the user filter by categories and after that filter for stock or on sale products
* Fix: Reset filter doesn't works if the user filter by categories and use only one filter
* Fix: Show only parent term with attrbute filter doesn't works
* Fix: $count doesn't exists with OR query type set
* Fix: Unable to use filter by tag with hierarchical mode enabled
* Dev: yith_wcan_can_be_displayed hook
* Dev: yith_wcan_list_type_empty_filter_class hook

= 3.4.7 - Released on 17th October, 2017 =

* Tweak: Dropdown style optimizzation
* Fix: Unable to activate license
* Dev: add $instance arg in yith_wcan_dropdown_label hook

= 3.4.6 - Released on 12th October, 2017 =

* Fix: 500 internal server error if use with YITH WooCommerce Ajax Search plugin

= 3.4.5 - Released on 10th October, 2017 =

* New: Support to WooCommerce 3.2
* New: Label type of YITH WooCommerce Color and Label Premium integration

= 3.4.4 =

* Fix: Support for $wpdb->prepare() with WordPress 4.8.2
* Fix: Filter by category print an empty link if no products linked to parent category
* Fix: Filter by price disappears if product query return "no products found"
* Fix: Filter by price doesn't works in WordPress 4.8.2

= 3.4.3 =

* Fix: No filtered results with Page Builder by SiteOrigin
* Fix: Warning with salient theme
* Fix: Reset filter use term_taxonomy_id instead of term_id
* Fix: Unable to reset tag filter in product category page

= 3.4.2 =

* New: Support to Basel theme
* Fix: Unable to filter in vendor page
* Dev: yith_wcan_instock_filter_meta_query_args hook

= 3.4.1 =

* Fix: Issue with reset button in product_cat page

= 3.4.0 =

* New: Sorting option for free version
* Fix: Duplicated class in list price filter html tags
* Fix: Unable to use two or more price filter
* Fix: Issue with current filter in product tag page
* Fix: Unable to filter with qTranslateX and Socute theme
* Fix: Unable to filter with qTranslateX and YITH theme FW 1.0
* Fix: Issue with reset button in product_tag page

= 3.3.2 =

* New: Support for Salient theme
* New: Support for WooCommerce Grid/List view
* Tweak: Create dropdown style dynamically
* Fix: Filter by label and filter by dropdown lost current filter
* Fix: Reset button doesn't works fine on product category page
* Fix: Unable to remove brands filter
* Fix: Support to visibility taxonomy in WooCommerce 3.0.x
* Fix: Warning "missing term" in product cat and brands filter

= 3.3.1 =

* Fix: Filter by color lost current filter

= 3.3.0 =

* New: Support to WooCoomerce 2.7-beta3
* New: Hide product count for categories
* Tweak: Alphabetical not case sensitive order
* Fix: missing argument 2 in yith_wcan_exclude_terms and yith_wcan_include_terms hook

= 3.2.0 =

* New: Back/Next browser button integration
* New: wpml-config.xml file
* Tweak: Filter uri management
* Tweak: Add filter to force widget dropdown reinit
* Tweak: YITH Brands integration
* Fix: Unable to hide product count for attributes
* Fix: Unable to translate "Show all categories/tags" link
* Fix: Lost brands on 2nd filter step
* Fix: Unable to sort filter to menu order
* Fix: Current filter disappears in label, dropdown and color style
* Fix: Price and Sort By filter doesn't works in categories
* Fix: Filter by attribute (all styles) lost current category in product category page
* Fix: Missing queried object in filter type list
* Fix: Stock/On Sale and Price Filter css issue
* Fix: Filter by price lost current category in in product category page
* Fix: Sort By lost current category in in product category page
* Fix: Stock/On Sale lost current category in in product category page
* Fix: Reset button disappears in filter by categories if I filtered for 2 or more categories
* Fix: Conflict in search page
* Dev: yith_wcan_force_widget_init hook
* Dev: yith_wcan_unfiltered_product_ids hook

= 3.1.3 =

* Tweak: Support for YITH WooCommerce Brands add-on FREE
* Fix: undefined variable $_get_current_filter in premium widget
* Fix: Product image disappears after filter with lazy load option enabled
* Fix: Empty li tag with query type OR in categories filter
* Fix: Reset widget doesn't works with categories
* Fix: product_cat arg empty in query string if remove current category filter
* Fix: Order by doesn't works with filter type list, label and dropdown
* Fix: Order by doesn't works with filter type categories
* Dev: yith_wcan_skip_no_products_in_category hook
* Dev: yith_wcan_force_show_count_in_category hook
* Dev: yith_wcan_brands_enabled hook

= 3.1.2 =

* Added: yit_get_terms_args hook
* Added: yith_wcan_skip_no_products_color hook
* Added: yith_wcan_show_no_products_attributes hook
* Added: yith_wcan_after_reset_widget hook
* Added: yith_wcan_before_reset_widget hook
* Fixed: On Sale widget works only for current products in WordPress 4.7
* Removed: yith_wcan_hide_no_products_attributes hook

= 3.1.1 =

* Fixed: Dropdown option doesn't works with Avada theme
* Fixed: Unable to update to version 3.1.0

= 3.1.0 =

* Added: Support to WordPress 4.7
* Added: yith_wcan_hide_no_products_attributes hook
* Added: Don't show "On Sale filter" if no on sale products are available

= 3.0.13 =

* Fixed: Warning on current category check in filter
* Fixed: Url management with query type set to OR
* Fixed: $instance not defined warning on Categories filter
* Fixed: Close dropdown widget before open another dropdown
* Fixed: Layout issue with color style and round style

= 3.0.12 =

* Added: yith_wcan_dropdown_type hook
* Fixed: Plugin doesn't hide the Filter by price, filter by stock/on-sale, filter sort if no products was found

= 3.0.11 =

* Tweak: Removed deprecated taxonomy count WooCommerce transient
* Fixed: Wrong reset url if filter start from product category page

= 3.0.10 =

* Added: ScrollTop features in Desktop and Mobile
* Fixed: Filter by categories with "Only Parent" display option show all categories
* Fixed: Warning: in_array() expects at least 2 parameters, 1 given with query type set to OR
* Fixed: Widget dropdown doesn't works on Flatsome Theme
* Fixed: Filter by BiColor not show all attributes

= 3.0.9 =

* Added: Support to Ultimate Member plugin
* Fixed: Error on activation "the plugin required WooCommerce in order to works"
* Fixed: Get term issue with old WordPress version

= 3.0.8 =

* Added: Italian and Spanish language files available
* Added: Support to WordPress 4.6RC2
* Tweak: Removed deprecated arg to get_terms function
* Fixed: Empty filter doesn't hide after ajax call
* Fixed: Categories widget doesn't show all categories in archive page
* Fixed: Max execution time issue and 500 internal server error issue

= 3.0.7 =

* Added: yith_wcan_get_list_html_terms hook
* Added: yith_wcan_exclude_category_terms hook
* Fixed: Category widget doesn't show main parent category if this is empty
* Fixed: Wrong products count if "Hide out of stock items from the catalog" are enabled

= 3.0.6 =

* Fixed: Reset button doesn't show after click on a filter

= 3.0.5 =

* Fixed: Unable to override list price filter template by theme
* Fixed: style="displya:none"> text appears if the filters are empty
* Fixed: Argument #1 is not an array in frontend class
* Fixed: WP_Post object cannot convert to string in frontend class
* Fixed: Problem with cirillic charachter
* Fixed: Wrong count in filter widgets
* Fixed: Warning in error_log/debug.log file with latest version

= 3.0.4 =

* Fixed: Filters show all attributes in shop and product taxonomy pages

= 3.0.3 =

* Added: Support to Porto Theme
* Fixed: Wrong query object in layered nav query with WooCommerce 2.6.2

= 3.0.2 =

* Fixed: Filters disappears in sort count select on Avada theme
* Fixed: Filter by attributes doesn't works with WooCommerce 2.5
* Fixed: rtrim waring in untrailingslashit
* Fixed: Wrong filter on category page with WooCommerce 2.6.2

= 3.0.1 =

* Fixed: print empty li tag after update to version 3.0.0

= 3.0.0 =

* Added: Support to WooCommece 2.6
* Tweak: Layered navigation management
* Tweak: English Typo

= 2.9.2 =

* Fixed: Wrong reset button link in product category page

= 2.9.1 =

* Fixed: $class variable are not defined
* Fixed: Filter by list-attribute doesn't works in versio 2.9.0

= 2.9.0 =

* Added: Change browsers url option (in SEO tab)
* Added: Show current category in product category page (in general tab)
* Added: Support to Ultimate WooCommerce Brands PRO
* Added: Hierarchical tags management
* Added: See all tags link in tags widget id a filter was applied
* Added: See all categories link in categories widget if a filter was applied
* Tweak: Change checkboxes with radio button in Sort By Filter
* Fixed: Filtering issue with YITH WooCommerce Brands Add-on Premium
* Fixed: HTML5 Validation (attribute name not allowed in ul element)
* Fixed: The page doesn't scroll up in mobile
* Fixed: z-index not set to -1 when user close dropdown filters with click in page area

= 2.8.1 =

* Added: WooCommerce shop navigation in ajax
* Fixed: Dropdown issue with Remy Theme
* Fixed: $.fn.slider is not a function after click on reset filter
* Fixed: Reset filter in product category page doesn't works
* Fixed: Plugin panel option style issue

= 2.8.0 =

* Added: Hierarchical Product Category management

= 2.7.9 =

* Fixed: Reset filter doesn't show with Brands and Categories
* Fixed: Unable to unset Brands filter

= 2.7.8 =

* Added: New option to set the scroll top anchor html element for mobile
* Added: Trigger window scroll event after ajax call
* Fixed: The page scroll down after filter has been applied in mobile
* Fixed: Duplicated query in Filter by categories
* Fixed: generated 404 link with in stock/on sale filter
* Fixed: YITH WooCommerce Product Slider Carousel doesn't work after a filter was applied
* Fixed: Widget doesn't work with multiple hierarchical terms

= 2.7.7 =

* Added: Suppoort to quantity input in loop
* Added: yith-wcan-pro body class
* Fixed: SEO option doesn't works with category filter
* Fixed: SEO option doesn't works with in stock/on sale filter

= 2.7.6 =

* Fixed: Error on activation

= 2.7.5 =

* Added: New event yith-wcan-wrapped was triggered after container wrap
* Added: Support to WooCommerce 2.5
* Fixed: Stop activation free version if premium is enabled

= 2.7.4 =

* Updated: Plugin core framework

= 2.7.3 =

* Added: Support to WooCommerce 2.5-RC1
* Added: Checkboxes style for filter
* Added: Sort by number of products contained or alphabetically
* Fixed: Reset Filter in category page
* Fixed: Filter doesn't work correctly in sub-categories
* Fixed: Filter by tag and Filter by categories doesn't show in sidebar
* Fixed: Add specific class in hieralchical categories

= 2.7.2 =

* Fixed: Unable to filter by categories and brands at same time
* Fixed: Filter by categories widget issue
* Fixed: select/unselect all don't works with wordpress 4.4
* Fixed: The plugin shows empty filters in product category page
* Fixed: Reset filter doesn't works in product category page
* Fixed: WooCommerce price slider doesn't set to default value after filter reset

= 2.7.1 =

* Fixed: Customer can't reset brands filter

= 2.7.0 =

* Fixed: Sort By dropdown lost style in shop page
* Fixed: Hierarchical categories filter doesn't works
* Fixed: Wrong link on brands filter
* Fixed: Loader image don't change
* Fixed: Reset doesn't show with filter by categories
* Fixed: Click on row to filter in dropdown style
* Removed: var_dump() in product filter widget

= 2.6.1 =

* Added: Instant WooCommerce price filter with slider

= 2.6.0 =

* Added: Filter by categories
* Added: yith_wcan_show_widget hook to manage the widgets display condition
* Added: yith_wcan_is_search hook to disable the widgets in search page
* Fixed: SEO option issue with tag filter
* Fixed: Disable widgets in search page
* Fixed: Hierarchical option in Filter By Brand type

= 2.5.0 =

* Added: SEO Tab to add follow and index option for filtered page
* Added: yith_wcan_dropdown_class hook for dropdown classes
* Added: yith_wcan_untrailingslashit hook for disable untrailingslashit function in filter link
* Tweak: Performance improved with new plugin core 2.0
* Tweak: Plugin don't apply filter in category page
* Fixed: Issuet with YITH Infinite Scrolling plugin
* Fixed: Filter widget don't show in product attribute page
* Fixed: Filter by price doesn't work without page reload
* Fixed: Dropdown icon doesn't display
* Fixed: Issue with WPML and Visual Composer plugins in admin

= 2.4.0 =

* Added: Language files called yith-woocommerce-ajax-navigation
* Removed: All language files called yith_wc_ajxnav
* Tweak: New wordpress translation text domain added
* Fixed: Dropdown issue with sort by and price filter widget
* Fixed: Widget title option doesn't work
* Fixed: Issue with price filter widgets if no price range was set

= 2.3.1 =

* Added: Support to YITH Infinite Scrolling plugin
* Fixed: No pagination container issue after filter applied
* Fixed: js error yit_wcan not defined
* Fixed: issue with blank label

= 2.3.0 =

* Added: New frontend options for script configuration
* Added: Custom Style Section
* Updated: Plugin Core Framework
* Updated: Languages file
* Fixed: Warning in list price filter without price

= 2.2.0 =

* Added: Support to WordPress 4.3
* Updated: Language files
* Fixed: Color lost after change widget style with WordPress 4.3
* Fixed: Tag list show after save widget option in other style
* Fixed: Tag list disappear after save option in tags style
* Fixed: Warning when switch from color to label style

= 2.1.2 =

* Added: Support to WooCommerce 2.4
* Updated: Plugin Framework
* Fixed: Tag list and child term support
* Fixed: Dropdown options doesn't work in WordPress 4.2.4

= 2.1.1 =

* Tweak: Support to PAAMAYIM NEKUDOTAYIM in PHP Version < 5.3

= 2.1.0 =

* Added: Frontend classes option panel
* Added: yith_wcan_ajax_frontend_classes filter
* Added: plugin works in product category page
* Added: Select tags to use in filter
* Added: WPML and String translation support
* Updated: language pot file
* Updated: Italian translation
* Tweak: Shop uri management
* Fixed: in stock/on sale works only with all option enable
* Fixed: wrong filter link in product category page
* Fixed: Widget filter by tag does not combine properly filters
* Fixed: Widget doesn't work fine in Shop Category Page
* Fixed: Remove trailing slash in widget shop uri
* Fixed: Prevent double instance in singleton class
* Fixed: The widget doesn't work with WPML with Label, Color and BiColor style

= 2.0.4 =

* Added: Filter 'yith_wcan_product_taxonomy_type' to widget product tax type
* Tweak: YITH WooCommerce Brands Add-on support in taxonomy page

= 2.0.3 =

* Added: Support to Sortable attribute
* Tweak: Yithemes Themes support
* Fixed: Color lost after change widget style

= 2.0.0 =

Initial Release
