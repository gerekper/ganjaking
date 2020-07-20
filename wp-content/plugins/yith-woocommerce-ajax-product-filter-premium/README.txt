=== YITH WooCommerce Ajax Navigation Premium ===

Contributors: yithemes
Tags: woocommerce, widget, ajax, ajax filtered nav, ajax navigation, ajax filtered navigation
Requires at least: 4.0
Tested up to: 5.4

Stable tag: 3.11.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Changelog =

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
