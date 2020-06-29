=== YITH WooCommerce Ajax Product Filter ===

Contributors: yithemes
Tags: woocommerce ajax product filter download, woocommerce, widget, ajax, ajax filtered nav, ajax navigation, ajax filtered navigation, woocommerce layered navigation, woocommerce layered nav, product filter, product filters, ajax product filter, woocommerce ajax product filter, woocommerce filters, sidebar filter, sidebar ajax filter, ajax price filter, price filter, product sorting, woocommerce filter, taxonomy filter, attribute filter, attributes filter, woocommerce product sort, ajax sort, woocommerce ajax product filter, advanced product filters, ajax product filters, filters, woocommerce ajax product filters, woocommerce product filters, woocommerce product filters, category filter, attribute filters, woocommerce products filter, woocommerce price filter, yit, yith, yithemes
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 3.11.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

YITH WooCommerce Ajax Product Filter offers you the perfect way to filter all products of your WooCommerce shop.

== Description ==

= Filter by the specific product you are looking for =

A powerful WooCommerce plugin: WooCommerce product filter widget, WooCommerce Ajax Product Filter lets you apply the filters you need to display the correct WooCommerce variations of the products you are looking for.
Choose among color, label, list and dropdown and your WooCommerce filtering search will display those specific products that suit perfectly your needs.
An extremely helpful WooCommerce plugin to help customers find what they really want.
All this can be done in a quick and very intuitive way that will certainly help your WooCommerce store improve in quality and usability.


Working demos for YITH WooCommerce Ajax Product Filter are available here:
**[LIVE DEMO](https://plugins.yithemes.com/yith-woocommerce-ajax-product-filter/)**

Full documentation for YITH WooCommerce Ajax Product Filter is available [here](https://docs.yithemes.com/yith-woocommerce-ajax-product-filter/).

**Main Features of YITH WooCommerce Ajax Product Filter:**

* Filter WooCommerce products with YITH WooCommerce Ajax Product Filter widget (4 layouts available)
 * List
 * Dropdown
 * Color
 * Label
* Reset all applied filters with YITH WooCommerce Ajax Reset Filter widget

= Premium features of YITH WooCommerce Ajax Product Filter: =

* Two additional layouts for the YITH WooCommerce Ajax Product Filter widget (BiColor, Tags), in addition to compatibility with the plugin YITH WooCommerce Brands
* Customizable reset button (in the YITH WooCommerce Ajax Reset Filter widget)
* WooCommerce Search filter for products of a specific price range available thanks to the YITH WooCommerce Ajax List Price Filter widget
* Search filter for products on sale/available
* Ajax sorting for products displayed in the page (by rate, price, popularity, most recent)
* Upload of an icon as customized loader
* Customization of the WooCommerce Price Filter widget


YITH WooCommerce Ajax Product Filter is available in combination with many other plugins in [**YITH Essential Kit for WooCommerce #1**](https://wordpress.org/plugins/yith-essential-kit-for-woocommerce-1/), a bundle of indispensable tools to make your WooCommerce site look more professional and be more user-friendly. Learn more about all of WooCommerce plugins included and boost your WooCommerce site with a simple click!


= Compatibility with WooCommerce plugins =

YITH WooCommerce Ajax Product Filter has been tested and compatibility is certain with the following WooCommerce plugins that you can add to your site:

* [YITH WooCommerce Multi Vendor](https://wordpress.org/plugins/yith-woocommerce-product-vendors/)
* [YITH WooCommerce Brands Add-On](https://wordpress.org/plugins/yith-woocommerce-brands-add-on/)
* [YITH Product Size Charts for WooCommerce](https://wordpress.org/plugins/yith-product-size-charts-for-woocommerce/)

Nevertheless, it could be compatible with many other WooCommerce plugins that have not been tested yet. If you want to inform us about compatibility with other plugins, please, [email to us](mailto:plugins@yithemes.com "Your Inspiration Themes").

== Installation ==

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce Ajax Product Filter` from Plugins page.

== Frequently Asked Questions ==

= Why isn't the widget displayed in my sidebar? =
In order to display the widget, you need to assign it to the sidebar in the Shop page and you also need to add WooCommerce Product Attributes to your product. Read the "Getting Started" section of the documentation to learn how to add them.

= Translation issue with the version 2.0.0 =
Dear users,
we would like to inform you that the plugin YITH WooCommerce Ajax Navigation will change its name into YITH WooCommerce Ajax Product Filter from the next update.
In addition to the name, with the new release the plugin, textdomain will change too from "yit" to "yith_wc_ajxnav".
This change solves issues concerning textdomain conflicts generated by some translation/multilanguage plugins you have identified in the past weeks.
It may be possible that, with the plugin update, some language files will not be recognized by WordPress. In this case, you will just have to rename the language files with the correct format, changing the old textdomain with the new one.
For example, if your language files were named yit-en_GB.po and yit-en_GB.mo, you will just have to rename them respectively as yith_wc_ajxnav-en_GB.po and yith_wc_ajxnav-en_GB.mo.
After renaming the files, you can update/translate the .po file following the classic procedure for translations.

= What are the main changes in plugin translation? =
= Translation issue with the version 2.4.0 =
Recently YITH WooCommerce Ajax Product Filter has been selected to be included in the "translate.wordpress.org" translate programme. In order to import correctly the plugin strings in the new system, we had to change the text domain form 'yith_wc_ajxnav' to 'yith-woocommerce-ajax-navigation'. Once the plugin will be imported in the translate.wordpress.org system, the translations of other languages will be downloaded directly from WordPress, without using any .po and .mo files. Moreover, users will be able to participate in a more direct way to plugin translations, suggesting texts in their languages in the dedicated tab on translate.wordpress.org. During this transition step, .po and .mo files will be used as always, but in order to be recognized by WordPress, they will need to have a new nomenclature, renaming them in: yith-woocommerce-ajax-navigation-.po yith-woocommerce-ajax-navigation-.mo. For example, if your language files were named yit-en_GB.po and yit-en_GB.mo, you will just have to rename them respectively as yith-woocommerce-ajax-navigation-en_GB.po and yith-woocommerce-ajax-navigation-en_GB.mo.

= The widget with WooCommerce filters is not working =
= The page doesn't update after clicking on a WooCommerce filter =

The issue could be related to the fact you are using a non-standard template for a WooCommerce shop page. To solve it, you should ask to the theme's author to use WooCommerce standard HTML classes.
As an alternative:
**For version prior to 2.2.0:**

you can use this piece of code in functions.php file of your theme:

`
if( ! function_exists( 'yith_wcan_frontend_classes' ) ){
	 function yith_wcan_frontend_classes(){
	  return array(
	            'container'    => 'YOUR_SHOP_CONTAINER',
	            'pagination'   => 'YOUR_PAGINATION_CONTAINER',
	            'result_count' => 'YOUR_RESULT_COUNT_CONTAINER'

	        );
	 }
}

add_filter( 'yith_wcan_ajax_frontend_classes', 'yith_wcan_frontend_classes' );
`

If you don't know which classes you should use, ask to the developer of your theme.

**From version 2.3.0 or later:**

You don't have to write manually the code anymore, as you can just go to YITH Plugin -> Ajax Product Filter -> Front End and set easily the parameters from the text fields.

If you don't know which classes you should use, ask to the developer of your theme.

= PAAMAYIM NEKUDOTAYIM Error after update 2.1.0 =

After the update 2.1.0, some users of YITH WooCommerce Ajax Product Filter are experiencing the error: "Parse error: syntax error, unexpected T_PAAMAYIM_NEKUDOTAYIM". This is caused by the PHP version of your server that is older than the 5.3. To solve the issue, you just have to update the plugin to the version 2.1.1.

= Is it compatible with all WordPress themes? =

Compatibility with all themes is impossible, because they are too many, but generally if themes are developed according to WordPress and WooCommerce guidelines, YITH plugins are compatible with them.
Yet, we can grant compatibility with themes developed by YIThemes, because they are constantly updated and tested with our plugins. Sometimes, especially when new versions are released, it might only require some time for them to be all updated, but you can be sure that they will be tested and will be working in a few days.


= How can I get support if my WooCommerce plugin is not working? =

If you have problems with our WooCommerce plugins or something is not working as it should, first follow this preliminary steps:

* Test the plugin with a WordPress default theme, to be sure that the error is not caused by the theme you are currently using.
* Deactivate all plugins you are using and check if the problem is still occurring.
* Ensure that you plugin version, your theme version and your WordPress and WooCommerce version (if required) are updated and that the problem you are experiencing has not already been solved in a later plugin update.

If none of the previous listed actions helps you solve the problem, then, submit a ticket in the forum and describe your problem accurately, specify WordPress and WooCommerce versions you are using and any other information that might help us solve your problem as quickly as possible. Thanks!


= How can I get more features for my WooCommerce plugin? =

You can get more features with the premium version of YITH WooCommerce Ajax Product Filter, available on [YIThemes page]( https://yithemes.com/themes/plugins/yith-woocommerce-ajax-product-filter/). Here you can read more about the premium features of the plugin and make it give it its best shot!


= How can I try the full-featured plugin? =

If you want to see a demonstration version of the premium plugin, you can see it installed on two different WooCommerce sites, either in [this page]( http://plugins.yithemes.com/yith-woocommerce-ajax-product-filter/?preview) or in [this page](http://preview.yithemes.com/bazar/shop/). Browse it and try all options available so that you can see how your plugin looks like.

== Screenshots ==

1. Admin - Appearance -> Widget: WooCommerce Filter Widget List Style
2. Admin - Appearance -> Widget: WooCommerce Filter Widget Color Style
3. Admin - Appearance -> Widget: WooCommerce Filter Widget Label Style
4. Admin - Appearance -> Widget: WooCommerce Filter Widget Dropdown Style
5. Admin - Appearance -> Widget: WooCommerce Filter Reset Button
6. Frontend: WooCommerce Widget in sidebar
7. Frontend: Dropdown style
8. Frontend: Reset button and active filters
9. Admin: YIT Plugins -> Ajax Product Filter -> Front end
10. Admin: YIT Plugins -> Ajax Product Filter -> Custom Style

== Changelog ==

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

= 3.8.4 - Released on 27 December 2019  =

* New: Support for WooCommerce 3.9
* Fix: Not updating filters when the customer apply a filter by price

= 3.8.3 - Released on 11 December 2019 =

* Fix: Filter not working on product tag page
* Update: plugin framework

= 3.8.2 - Released on 28 November 2019  =

* Update: plugin framework

= 3.8.1 - Released on 04 November 2019  =

* Update: plugin framework
* Fix: Undefined variable message on frontend

= 3.8.0 - Released on 30 October 2019  =

* New: Support for WordPress 5.3
* New: Support for WooCommerce 3.8
* Update: plugin framework
* Fix: Call get_query_object() on null
* Dev: New filter 'yith_wcan_skip_check_on_product_in_term'

= 3.7.1 - Released on 27th August, 2019  =

* Fix: curl_init() doesn't exist

= 3.7.0 - Released on 07th August, 2019  =

* New: Support for WooCommerce 3.7
* Fix: Style issues with Storefront theme

= 3.6.6 - Released on 12nd June, 2019 =

* Fix: Bug on plugin options

= 3.6.5 - Released on 12nd June, 2019 =

* Update: Plugin Core Framework

= 3.6.4 - Released on 06th June, 2019 =

* Tweak: WooCommerce 3.6 optimization
* Update: Plugin Core Framework
* Update: Languages file
* Fix: Unable to reset filter in subcategory page
* Fix: undefined variable term_id in attributes table

= 3.6.3 - Released on 23rd April, 2019 =

* Update: Plugin Core Framework

= 3.6.2 - Released on 08th April, 2019 =

* New: Support to WooCommerce 3.6
* Tweak: New Widgets name
* Update: Plugin Core Framework
* Fix: Undefined variable term_id in attributes table
* Fix: Wrong reset page with WooCommerce Filter by price widget
* Fix: Support to Aurum theme - Filter issue using AND operator
* Fix: Undefined index: source_tax in YITH Reset Filter widget
* Fix: Issue with reset filter in product categories page
* Fix: Reset button doesn't works if a variable use a slash char in query string
* Dev: new parameter for filter 'yith_wcan_dropdown_label'

= 3.6.1 - Released on 11th October, 2018 =

* Update: Plugin Core Framework

= 3.6.0 - Released on 11th October, 2018 =

* New: Support for WooCommerce 3.5
* New: Support for Aardvark theme
* New: Support for Aurum theme
* New: Support for UX Shop theme
* New: Support for YooTheme theme
* Fix: Wrong results with search query string
* Fix: Double chosen icon with themes that override layered navigation style
* Tweak: Widget reinit after ajax call
* Tweak: Flatsome optimization
* Tweak: Prevent untrailingslashit on filter url for SEO optimization
* Update: Spanish language
* Update: Italian Language
* Dev: yith_wcan_skip_no_products_label hook

= 3.5.1 - Released on 14th March, 2018 =

* Tweak: Removed old YITH_WCAN_Helper class
* Updated: Plugin core framework
* Fix: Unable to reset filters in product categories page
* Fix: PHP Notice: Undefined index: display
* Fix: $count not defined in loop

= 3.5.0 - Released: 01 Feb, 2018 =

* New: 100% German translation (Thanks to Thomas)
* New: Support for WooCommerce 3.3.0
* Fix: Undefined index "dropdown-type" in shop page
* Fix: Reset filter doesn't works if the user filter by categories and use only one filter
* Fix: Show only parent term with attrbute filter doesn't works
* Fix: $count doesn't exists with OR query type set
* Dev: yith_wcan_can_be_displayed hook
* Dev: yith_wcan_list_type_empty_filter_class hook

= 3.4.6 - Released: 12 Oct, 2017 =

* Fix: 500 internal server error if use with YITH WooCommerce Ajax Search plugin

= 3.4.5 - Released: 10 Oct, 2017 =

* New: Support to WooCommerce 3.2
* New: Label type of YITH WooCommerce Color and Label Premium integration

= 3.4.4 = Released: 26 Sep, 2017 =

* Fix: Support for $wpdb->prepare() with WordPress 4.8.2

= 3.4.3 - Released: 04 Jul, 2017 =

* Fix: No filtered results with Page Builder by SiteOrigin
* Fix: Warning with salient theme
* Fix: Reset filter use term_taxonomy_id instead of term_id

= 3.4.2 - Released: 05 Jun, 2017 =

* New: Support to Basel theme
* Fix: Unable to filter in vendor page

= 3.4.1 Released: 18 May, 2017 =

* Fix: Issue with reset button in product_cat page

= 3.4.0 - Released: 08 May, 2017 =

* New: Sorting option for free version
* Fix: Issue with current filter in product tag page
* Fix: Unable to filter with qTranslateX and Socute theme
* Fix: Unable to filter with qTranslateX and YITH theme FW 1.0
* Fix: Issue with reset button in product_tag page


= 3.3.2 - Released: 19 April, 2017 =

* New: Support for Salient theme
* New: Support for WooCommerce Grid/List view
* Tweak: Create dropdown style dynamically
* Fix: Filter by label and filter by dropdown lost current filter
* Fix: Reset button doesn't works fine on product category page
* Fix: Support to visibility taxonomy in WooCommerce 3.0.x

= 3.3.1 - Released: 20 MArch, 2017 =

* Fix: Filter by color lost current filter

= 3.3.0 - Released: 24 February, 2017 =

* New: Support to WooCoomerce 2.7-beta3
* Fix: missing argument 2 in yith_wcan_exclude_terms and yith_wcan_include_terms hook

= 3.2.0 - Released: 20 February, 2017 =

* Add: Back/Next browser button integration
* Tweak: Filter uri management
* Tweak: Add filter to force widget dropdown reinit
* Tweak: YITH Brands integration
* Fix: Unable to hide product count for attributes
* Fix: Lost brands on 2nd filter step
* Fix: Current filter disappears in label, dropdown and color style
* Fix: Filter by attribute (all styles) lost current category in product category page
* Fix: Missing queried object in filter type list
* Fix: Conflict in search page
* Dev: yith_wcan_unfiltered_product_ids hook


= 3.1.2 - Released: 17 January, 2017 =

* Tweak: Support for YITH WooCommerce Brands add-on FREE
* Fix: Product image disappears after filter with lazy load option enabled
* Fix: Empty li tag with query type OR in categories filter
* Fix: Reset widget doesn't works with categories
* Fix: Order by doesn't works with filter type list, label and dropdown
* Dev: yith_wcan_brands_enabled hook

= 3.1.1 - Released: 28 December, 2016 =

* Added: yit_get_terms_args hook
* Added: yith_wcan_skip_no_products_color hook
* Added: yith_wcan_show_no_products_attributes hook
* Added: yith_wcan_after_reset_widget hook
* Added: yith_wcan_before_reset_widget hook
* Removed: yith_wcan_hide_no_products_attributes hook

= 3.1.0 - Released: 05 December, 2016 =

* Added: Support to WordPress 4.7

= 3.0.12 - Released: 23 November, 2016 =

* Fixed: Url management with query type set to OR
* Fixed: Close dropdown widget before open another dropdown
* Fixed: Layout issue with color style

= 3.0.11 - Released: 05 October, 2016=

* Tweak: Removed deprecated taxonomy count WooCommerce transient
* Fixed: Wrong reset url if filter start from product category page

= 3.0.10 - Released: 29 September, 2016 =

* Fixed: Warning: in_array() expects at least 2 parameters, 1 given with query type set to OR
* Fixed: Widget dropdown doesn't works on Flatsome Theme

= 3.0.9 - Released: 31 Aug, 2016 =

* Added: Support to Ultimate Member plugin
* Fixed: Error on activation "the plugin required WooCommerce in order to works"
* Fixed: Get term issue with old WordPress version

= 3.0.8 - Released: Aug 11, 2016 =

* Added: Support to WordPress 4.6RC2
* Tweak: Removed deprecated arg to get_terms function
* Fixed: Empty filter doesn't hide after ajax call
* Fixed: Max execution time issue and 500 internal server error issue

= 3.0.7 - Released: Jul 29, 2016 =

* Added: yith_wcan_get_list_html_terms hook
* Fixed: Filter doesn't work with WooCommerce 2.6.x

= 3.0.6 - Released: Jul 21, 2016 =

* Fixed: Reset button doesn't show after click on a filter

= 3.0.5 - Released: Jul 20, 2016 =

* Fixed: style="displya:none"> text appears if the filters are empty
* Fixed: Argument #1 is not an array in frontend class
* Fixed: WP_Post object cannot convert to string in frontend class
* Fixed: Problem with cirillic charachter
* Fixed: Wrong count in filter widgets

= 3.0.4 - Released: Jul 8, 2016 =

* Fixed: Filters show all attributes in shop and product taxonomy pages

= 3.0.3 - Released: Jul 6, 2016 =

* Fixed: Wrong query object in layered nav query with WooCommerce 2.6.2

= 3.0.2 - Released: Jul 4, 2016 =

* Fixed: Filters disappears in sort count select on Avada theme
* Fixed: Filter by attributes doesn't works with WooCommerce 2.5
* Fixed: rtrim waring in untrailingslashit
* Fixed: Wrong filter on category page with WooCommerce 2.6.2

= 3.0.1 - Released: Jun 14, 2016 =

* Fixed: print empty li tag after update to version 3.0.0

= 3.0.0 - Released: Jun 13, 2016 =

* Added: Support to WooCommece 2.6
* Tweak: Layered navigation management
* Tweak: English Typo

= 2.9.2 - Released: May 16, 2016 =

* Fixed: Wrong reset button link in product category page

= 2.9.1 - Released: May 04, 2016 =

* Fixed: $class variable are not defined
* Fixed: Filter by list-attribute doesn't works in versio 2.9.0

= 2.9.0 - Released: May 02, 2016 =

* Fixed: Filtering issue with YITH WooCommerce Brands Add-on Premium
* Fixed: HTML5 Validation (attribute name not allowed in ul element)
* Fixed: z-index not set to -1 when user close dropdown filters with click in page area


= 2.8.1 - Released: Mar 10, 2016 =

* Fixed: Dropdown issue with Remy Theme
* Fixed: $.fn.slider is not a function after click on reset filter
* Fixed: Reset filter in product category page doesn't works
* Fixed: Plugin panel option style issue

= 2.8.0 - Released: Mar 1 - 2016 =

* Tweak: Plugin Core Framework
* Tweak: Term and Filter management

= 2.7.8 - Released: Feb 25 - 2016 =

* Fixed: Issue with filter in category and taxonomy pages

= 2.7.7 - Released: Feb 19 - 2016 =

* Added: Trigger window scroll event after ajax call
* Fixed: The page scroll down after filter has been applied in mobile
* Fixed: Duplicated query in Filter by categories
* Fixed: generated 404 link with in stock/on sale filter
* Fixed: YITH WooCommerce Product Slider Carousel doesn't work after a filter was applied
* Fixed: Widget doesn't work with multiple hierarchical terms

= 2.7.6 - Released: Feb 05 - 2016 =

* Added: Suppoort to quantity input in loop
* Added: yith-wcan-free body class
* Fixed: Filter button doesn't appear in WooCommerce Price Filter Slider

= 2.7.5 - Released: Jan 27 - 2016 =

* Fixed: Error on plugin activation

= 2.7.4 - Released: Jan 27 - 2016 =

* Added: New event yith-wcan-wrapped was triggered after container wrap
* Added: Support to WooCommerce 2.5
* Fixed: Stop activation free version if premium is enabled

= 2.7.3 - Released: Jan 12 - 2016 =

* Updated: Plugin core framework

= 2.7.2 - Released: Jan 07 - 2015 =

* Added: Support to WooCommerce 2.5-RC1
* Fixed: Reset Filter in category page
* Fixed: Filter doesn't work correctly in sub-categories

= 2.7.1 - Released: Dec 23 - 2015 =

* Fixed: The plugin shows empty filters in product category page
* Fixed: Reset filter doesn't works in product category page
* Fixed: WooCommerce price slider doesn't set to default value after filter reset

= 2.7.0 - Released: Dec 10 - 2015 =

* Fixed: Click on row to filter in dropdown style
* Removed: var_dump() in product filter widget

= 2.6.0 - Released: Nov 02 - 2015 =

* Added: yith_wcan_show_widget hook to manage the widgets display condition
* Added: yith_wcan_is_search hook to disable the widgets in search page
* Fixed: Disable widgets in search page

= 2.5.0 - Released: Oct 21 - 2015 =

* Added: yith_wcan_untrailingslashit hook for disable untrailingslashit function in filter link
* Tweak: Performance improved with new plugin core 2.0
* Tweak: Plugin don't apply filter in category page
* Fixed: Issuet with YITH Infinite Scrolling plugin
* Fixed: Filter widget don't show in product attribute page
* Fixed: Issue with WPML and Visual Composer plugins in admin

= 2.4.0 - Released: Sept, 25 - 2015 =

* Tweak: New wordpress translation text domain added
* Added: Language files called yith-woocommerce-ajax-navigation
* Removed: All language files called yith_wc_ajxnav

= 2.3.1 - Released: Sept, 17 - 2015 =

* Added: Support to YITH Infinite Scrolling plugin
* Fixed: No pagination container issue after filter applied
* Fixed: js error yit_wcan not defined
* Fixed: issue with blank label

= 2.3.0 - Released: Sept, 11 - 2015 =

* Added: Custom Style Section
* Added: New frontend options for script configuration
* Updated: Plugin Core Framework
* Updated: Languages file

= 2.2.0 - Released: Aug, 25 - 2015 =

* Added: Support to WordPress 4.3
* Updated: Language files
* Fixed: Color lost after change widget style with WordPress 4.3
* Fixed: Warning when switch from color to label style

= 2.1.2 - Released: Aug, 11 - 2015 =

* Added: Support to WooCommerce 2.4
* Updated: Plugin Framework
* Fixed: Tag list and child term support

= 2.1.1 - Released: July, 30 - 2015 =

* Tweak: Support to PAAMAYIM NEKUDOTAYIM in PHP Version < 5.3

= 2.1.0 - Released: July, 29 - 2015 =

* Added: Frontend classes option panel
* Added: yith_wcan_ajax_frontend_classes filter
* Added: plugin works in product category page
* Added: WPML and String translation support
* Updated: language pot file
* Updated: Italian translation
* Tweak: Shop uri management
* Fixed: wrong filter link in product category page
* Fixed: Widget doesn't work fine in Shop Category Page
* Fixed: Remove trailing slash in widget shop uri
* Fixed: Prevent double instance in singleton class
* Fixed: The widget doesn't work with WPML with Label and Color style

= 2.0.4 - Released: July, 13 - 2015 =

* Added: Filter 'yith_wcan_product_taxonomy_type' to widget product tax type
* Tweak: YITH WooCommerce Brands Add-on support in taxonomy page

= 2.0.3 - Released: July, 03 - 2015 =

* Added: Support to Sortable attribute
* Fixed: Color lost after change widget style

= 2.0.2 - Released: Jun, 25 - 2015 =

* Fixed: Empty filters appear after update to 2.0.0

= 2.0.1 - Released: Jun, 24 - 2015 =

* Fixed: Unable to active plugin

= 2.0.0 - Released: Jun, 24 - 2015 =

* Tweak: Plugin core framework
* Updated: Languages file
* Fixed: Prevent warning issue with no set color/label
* Fixed: Textdomain conflict
* Fixed: Filter doesn't work if shop page is on front
* Removed: old default.po catalog language file

= 1.4.1 - Released: Oct, 08 - 2014 =

* Fixed: Wrong attribute show with WooCommerce 2.2

= 1.4.0 - Released: Set, 16 - 2014 =

* Added: Support to WC 2.2
* Updated: Plugin Core Framework
* Fixed: Widget error on empty title
* Fixed: Ajax load on widget type switching

= 1.3.2 - Released: Jun, 05 - 2014 =

* Fixed: Wrong enqueue of the main css file
* Added: Filter yith_wcan_exclude_terms

= 1.3.1 - Released: Mar, 03 - 2014 =

* Added: Attribute order (All, Hieralchical or Only Parent style)
* Fixed: Dropdown Style on Firefox
* Fixed: Blank box on attribute without label (Label Style)
* Fixed: Blank box on attribute without color (Color Stle)

= 1.3.0 - Released: Feb, 12 - 2014 =

* Added: Support to WooCommerce 2.1.X
* Fixed: One filter bug on sidebar

= 1.2.1 - Released: Jan, 29 - 2014 =

* Fixed: Width of select dropdown too large

= 1.2.0 - Released: Jan, 10 - 2014 =

* Added: Dropdown style
* Added: Support to Wordpress 3.8
* Fixed: Error with non-latin languages
* Fixed: Improved WPML compatibility

= 1.1.2 - Released: Oct, 14 - 2013 =

* Added: Title to the color filters
* Removed: Limit of 3 characters in the label text input

= 1.1.1 - Released: Jul, 31 - 2014 =

* Minor bugs fixes

= 1.1.0 - Released: Jul, 19 - 2013  =

* Added new widget YITH WooCommerce Ajax Reset Navigation

= 1.0.0 - Released: Jun, 24 - 2013  =

* Initial release

== Translators ==

= Available Languages =
* English (Default)
* Italiano

If you have created your own language pack, or have an update for an existing one, you can send [gettext PO and MO file](http://codex.wordpress.org/Translating_WordPress "Translating WordPress")
[use](http://yithemes.com/contact/ "Your Inspiration Themes") so we can bundle it into YITH WooCommerce Ajax Navigation Languages.

== Documentation ==

Full documentation is available [here](https://docs.yithemes.com/yith-woocommerce-ajax-product-filter/).

== Upgrade notice ==

= 2.4.0 =

* Changing translation texdomain for new translate program: "translate.wordpress.org"

= 2.2.0 =

* WordPress 4.3 Support

= 2.1.2 =

* WooCommerce 2.4 Support

= 2.1.1 =

* Tweak: Support to PAAMAYIM NEKUDOTAYIM in PHP Version < 5.3

= 2.0.0 =

New plugin core added.

= 1.0.0 =

Initial release
