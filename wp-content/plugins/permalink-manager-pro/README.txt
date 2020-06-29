=== Permalink Manager Pro ===
Contributors: mbis
Donate link: https://www.paypal.me/Bismit
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: permalinks, custom permalinks, url editor, permalinks, woocommerce permalinks
Requires at least: 4.4.0
Requires PHP: 5.4
Tested up to: 5.4.1
Stable tag: 2.2.8.6

Permalink Manager Pro allows to easily change full URL addresses of posts, pages, custom post types, terms and WooCommerce links. You can also set different permalink formats per language or bulk change the URLs.

== Description ==

Permalink Manager is a most advanced and highly rated Wordpress permalink editor that helps Wordpress users to control the URL addresses of all posts, pages, custom post type elements (taxonomies are supported in Pro version). To avoid 404 or duplicated content errors after the new custom permalink is defined, the visitors trying to access the old permalink will be automatically redirected to the new custom URL.

The plugin supports all custom post types & custom taxonomies and popular 3rd party plugins including WooCommerce, Yoast SEO, WPML, and Polylang. To improve SEO performance even more, the plugin settings provide a possibility to disable the canonical redirect (used natively by Wordpress) and control the trailing slashes settings.

<a href="https://permalinkmanager.pro/docs/?utm_source=wordpressorg">Documentation</a> | <a href="https://permalinkmanager.pro/buy-permalink-manager-pro/?utm_source=wordpressorg">Permalink Manager Pro</a>

= All features =

* **Edit full permalinks**<br/>A completely custom permalink can be set for each post, page and public custom post type individually *(categories, tags & custom taxonomies terms permalinks can be edited in Permalink Manager Pro)*
* **Custom post types support**<br/>It is also possible to exclude specific post types & taxonomies to stop Permalink Manager from filtering their permalinks.
* **Custom permastructures**<br/>The plugin allows to specify how the custom permalinks should be formatted by default (when the new post/term is added or after the permalinks are regenerated)
* **Translate permalinks**<br/>Permalink Manager allows to translate and set different permalink format/structure per language when WPML or Polylang plugin is enabled.
* **Auto-redirect**<br/>Old (native) permalinks are redirected to new (custom) permalinks (in 301 or 302 mode) to prevent 404 error (SEO friendly).
* **Canonical redirects**<br/>Possibility to disable native canonical redirects.
* **Bulk editors**<br/>"Regenerate/Reset" + "Find and replace" tools that allow to bulk/mass change the permalinks (or native slugs).
* **Trailing slashes settings**<br/>They can be forced or removed from all permalinks.

= Need additional features & priority support? =
Buy <a href="https://permalinkmanager.pro?utm_source=wordpress">Permalink Manager Pro here</a>.

https://www.youtube.com/watch?v=KMOtAK5c7t8

= Additional features available in Permalink Manager Pro =

* **Taxonomies**<br/>Full support for taxonomies (categories, tags & custom taxonomies).
* **WooCommerce**<br/>Full support for WooCommerce (products, product tags, product categories). Permalink Manager allows to remove /product-category and /product from WooCommerce permalinks.
* **WooCommerce coupon URLs**<br/>Coupon codes may have their public URLs (eg. http://shop.com/BLACKFRIDAY) that will automatically apply the discount to the cart.
* **Custom fields**<br/>Custom fields can be used inside permalinks (compatible with Advanced Custom Fields).
* **Extra internal redirects**<br/>Multiple URLs can lead to a single post/term (they could be defined individually for each element).
* **External URL redirect**<br/>Posts/terms can redirect the visitors to external websites (the URLs could be defined individually for each element).
* **"Stop-words"**<br/>User-defined words can be automatically removed from default permalinks.
* **Custom Permalinks**<br/>Import custom permalinks saved with that plugin.
* **Priority support**<br/>All the support requests from Permalink Manager Pro users are handled in the first place.

Buy <a href="https://permalinkmanager.pro?utm_source=wordpress">Permalink Manager Pro here</a>.

= Translators =
* Japaneese - Shinsaku Ikeda

== Installation ==

Go to `Plugins -> Add New` section from your admin account and search for `Permalink Manager`.

You can also install this plugin manually:

1. Download the plugin's ZIP archive and unzip it.
2. Copy the unzipped `permalink-manager` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress

= Bulk URI editor =
After the plugin is installed you can access its dashboard from this page: `Tools -> Permalink Manager`.

= Single URI editor =
To display the URI editor metabox click on gray "Permalink Editor" button displayed below the post/page title.

== Frequently Asked Questions ==

= Can I define different permalink formats per each language.
Yes, it is possible if you are using either WPML or Polylang. You can find <a href="https://permalinkmanager.pro/docs/tutorials/how-to-translate-permalinks/">the full instructions here</a>.

= Can I use Permalink Manager to change the terms permalinks (eg. post or product categories)?
This feature is available only in Permalink Manager Pro.

= Will the old permalink automatically redirect the new custom ones?
Yes, Permalink Manager will automatically redirect the native permalinks (used when the plugin is disabled or before it was activated) to the actual, custom permalinks.

= Can I add custom fields to the permalinks?
This feature is available in paid version of plugin. Please go <a href="https://permalinkmanager.pro/docs/tutorials/how-to-use-custom-fields-inside-wordpress-permalinks/">here for more details</a>.

= Does this plugin support Buddypress?
Currently there is no 100% guarantee that Permalink Manager will work correctly with Buddypress.

= Can I delete/disable Permalink Manager after the permalinks are updated? =
Yes, if you used Permalink Manager only to regenerate the slugs (native post names). Please note that if you use custom permalinks (that differ from the native ones), they will no longer be used after the plugin is disabled.

It is because Permalink Manager overwrites one of the core Wordpress functionalities to bypass the rewrite rules ("regular expressions" to detect the posts/pages/taxonomies/etc. and another parameters from the URL) by using the array of custom permalinks (you can check them in "Debug" tab) that are used only by my plugin.

== Screenshots ==

1.	Permalink URI editor.
2.	Permalink URI editor in Gutenberg.
3.	"Find & replace" tool.
4.	"Regenerate/Reset" tool.
5.	A list of updated posts after the permalinks are regenerated.
6.	Permastructure settings.
7.	Permastructure settings (different permalink structure per language).
8.	Permalink Manager settings.

== Changelog ==

= 2.2.8.6 =
* Hotfix for Permalink_Manager_Helper_Functions::get_disabled_taxonomies() function
* New wrapper function with filter 'permalink_manager_post_statuses' for get_post_statuses()
* Extended support for "My Listing" theme (by 27collective)
* Hotfix for Gutenberg editor (broken HTML output)
* Extended support for permalinks stored in Yoast SEO database tables (Indexables)

= 2.2.8.4/2.2.8.5 =
* Hotfix for Permastructures (now the permalink formats are saved correctly)
* Hotfix for trailing slashes settings
* Improved setting fields descriptions
* Adjustments for search functionality in Bulk URI Editor
* Support for WPML Classic Translation Editor
* Adjustments for "Auto-update URI"
* Improvements for get_post_types_array() & get_taxonomies_array() functions used to list the content types supported by Permalink Manager

= 2.2.8.2/2.2.8.3 =
* Improved descriptions in the plugin settings
* Hotfix for endpoint redirect

= 2.2.8.1 =
* Hotfix for stop-words (now, the stop-words can be saved again)
* Support for Duplicate Page plugin

= 2.2.8.0 =
* Hotfix for multisite/network installations. Now, the plugin globals are reloaded whenever switch_blog() function is called.
* Hotfix for url_to_postid() function. The $pm_query global will no longer be altered.
* Hotfix for post/page revisions in custom permalink detect function
* Improved WP All Import Pro integration (better support for taxonomies)
* A different approach for WP Customize URLs
* New option available - "Old slug redirect"

= 2.2.7.6 =
* Code optimization for Bulk URI Editor
* Support for WooCommerce breadcrumbs
* A hotfix for WPForo plugin
* New filter "permalink_manager_chunk_size" that allows to control the chunk size in bulk tools ("Regenerate/reset", "Find & Replace")
* New filter "permalink_manager_sanitize_regex" that allows to adjust the function that sanitizes the custom permalinks
* Autoload for backup arrays is now disabled
* New option available - "Convert accented letters"

= 2.2.7.5 =
* CSS Hotfix. Now the redirects box is displayed correctly in the URI editor

= 2.2.7.4 =
* Support for "Primary category" set with SEOPress & RankMath plugins
* Support for breadcrumbs added by SEOPress & RankMath plugins
* Improved "trailing slashes" functionality - untrailingslashit() & trailingslashit() replaced with REGEX based functions
* Possibility to remove custom permalinks, redirects, permastructure settings directly from "Debug" section
* New filter "permalink_manager_duplicates_priority" that allows to decide what content type ("posts" or "terms") should be loaded when the custom permalink is duplicated
* A minor fix for url_to_postid() function

= 2.2.7.3 =
* Support for "Primary category" set with The SEO Framework
* Changes for URI Editor section backend (SQL queries + improvements for search box)
* Improved support for WooCommerce Wishlist plugin
* Improvements for slugs sanitization functions
* Possibility to exclude posts from bulk tools added to "Auto-update the URI" dropdown in URI Editor

= 2.2.7.1 =
* Hotfix for PHP Fatal error in permalink-manager-admin-functions.php file

= 2.2.7 =
* Force 404 for draft posts (for non-logged-in users)
* New feature: Force HTTPs/WWW in URLs
* New setting fields: "URI Editor role capability" & "Force HTTPS in URLs"
* Minor improvements

= 2.2.6 =
* More debug functions added
* Better support for Hebrew letters
* Support for location custom fields in WP Store Locator - CSV Manager plugin
* Improved support for Gutenberg editor (reported by Cedric Busuttil)

= 2.2.4/2.2.5 =
* Minor code improvements
* Yoast SEO Breadcrumbs - further improvements
* Hotfix for Toolset custom fields support (Permalink Manager Pro)
* Hotfix for Polylang URL modes

= 2.2.3 =
* Code improvements for WP All Import integration functions
* Hotfix for Elementor conflict with custom redirects function (Permalink Manager Pro)
* New field ("Do not automatically append the slug") in Permastructure settings added to each post type & taxonomy
* Basic support added for Mailster plugin
* New permastructure tag: "%monthname%"

= 2.2.2 =
* Code improvement for "Quick Edit" inline form
* Support for Yoast SEO breadcrumbs added
* Hotfix for Elementor

= 2.2.1.1/2.2.1.2/2.2.1.3/2.2.1.4 =
* Hotfix for function that detects custom URIs
* Custom URIs for WP All Import inported posts are now generated 30 seconds after the import job is completed

= 2.2.1 =
* Hotfix for Customizer (custom permalinks filters are disabled in admin panel)
* Minor UX improvements
* Partial support for TranslatePress plugin added
* Term permalinks are processed correctly when WPML enabled and "Adjust IDs for multilingual functionality" mode is activated
* New setting field separated from "Force custom slugs" - now, both the native slugs and special characters (.|_+) can be kept inside the slugs
* New filter - "permalink_manager_force_custom_slugs"

= 2.2.0 =
* Hotfix for WPML - ?lang query parameter is now appended correctly
* Support for comment pages endpoint
* Minor code adjustments
* Metabox for Gutenberg enabled also for CPT
* Further improvements for redirect hooks
* Hotfix for WP Customizer
* Native slugs are saved correctly in Gutenberg editor
* New filter - "permalink_manager_filter_permastructure" + Permastructures can be now translated from admin panel

= 2.1.2.1/2.1.2.2 =
* Hotfix for "Force custom slugs" option - now special characters are not removed if "Yes, use post/term titles + do not strip special characters: .|-+" mode is set.
* Hotfix for custom fields support in custom permalinks

= 2.1.2 =
* Hotfix for WP All Import - default permalinks are now assigned correctly to imported posts + possibility to disable WP All Import custom URI functions in Permalink Manager settings
* Hotfix for Yoast SEO - notice displayed on author pages
* Adjustments for sanitize slug functions
* Basic support for Gutenberg added

= 2.1.1 =
* Support for draft custom permalinks
* Support for WP All Import plugin, now the custom permalinks can be defined directly in XML, CSV, ZIP, GZIP, GZ, JSON, SQL, TXT, DAT or PSV import files.
* Hotfix for Permalink_Manager_Pro_Functions::save_redirects() method - now the custom redirects are correctly saved when a custom permalink is updated.
* Hotfix for "Language name added as a parameter" mode in "WPML Language URL format" settings.
* Hotfix for canonical redirect triggered by WPML.
* Better support for non-latin letters in custom URIs & redirects
* Better support for endpoints
* Searchbox in URI Editors

= 2.1.0 =
* Support for "url_to_postid" function
* Bulk tools use now AJAX & transients to prevent timeout when large number of posts/terms is processed
* Fix for multi-domain language setup in WPML

= 2.0.6.5 =
* Support for %__sku% permastructure tag (WooCommerce) added - now SKU number can be added to the custom permalinks (Permalink Manager Pro)
* Hotfix for license validation system

= 2.0.6.4 =
* Code optimization
* New filter: permalink_manager_fix_uri_duplicates
* Possibility to display the native slug field
* Hotfix for license validation functions

= 2.0.6.3.2 =
* Support added for Revisionize plugin
* Minor tweaks

= 2.0.6.2/2.0.6.3 =
* Japaneese translation added
* Some minor improvements
* New filters: permalink_manager_hide_uri_editor_term_{$term->taxonomy}, permalink_manager_hide_uri_editor_post_{$post->post_type} & permalink_manager_update_term_uri_{$this_term->taxonomy}, permalink_manager_update_post_uri_{$post->post_type}, permalink_manager_new_post_uri_{$post_object->post_type}
* Hotfix for default permalinks (no-hierarchical post types)
* Hotfix for attachments default permalinks + URI detect function

= 2.0.6.1 =
* Hotfix for endpoints in REGEX
* Minor bug fixed - native slugs are now correctly regenerated
* Hotfix for URI sanitization functions
* Hotfix for AMP plugin
* Full support for WPML multi-domain language setup
* Hotfix for VisualComposer + Yoast SEO JS functions
* Hotfix for WPML String Translation

= 2.0.6.0 =
* Minor bugs fixed
* New permastrutcure tag - %native_slug%
* "Force custom slugs" feature enhanced with new options
* Possibility to redirect the posts & terms to external URL (Permalink Manager Pro)

= 2.0.5.9 =
* New permastructure tags - %post_type% & %taxonomy%
* Support for "Taxonomy" custom field in ACF (Advanced Custom Fields)
* Minor fix for endpoints
* New hook - "permalink_manager-filter-permalink-base" used instead of "permalink-manager-post-permalink-prefix" & "permalink-manager-term-permalink-prefix"

= 2.0.5.7/2.0.5.8 =
* Hotfix for MultilingualPress plugin
* Hotfix & better support for attachment post type (Media Library)
* Custom redirects for old permalinks are now correctly saved in Permalink Manager Pro
* Support for WooCommerce Wishlist plugin

= 2.0.5.6 =
* The URIs for trashed posts are now correctly removed
* Better support for non-ASCII characters in URIs
* Minor fix for hierarchical post types
* Fix for coupon URL redirect
* New filter - "permalink-manager-force-hyphens"

= 2.0.5.5 =
* Discount URLs for WooCommerce - now the shop clients can use coupons' custom URIs to easily apply the discount to the cart
* Extra AJAX check for duplicated URIs in "Edit URI" box
* Wordpress CronJobs for "Automatically remove duplicates" functionality
* Extra improvements in "save_post/update_term" hooks
* Fix for terms permalinks added via "Edit post" page
* New filter - "permalink-manager-force-lowercase-uris"

= 2.0.5.4 =
* New filter - "permalink_manager_empty_tag_replacement"
* Fix for term placeholder tags in taxonomies permastructures
* Page pagination improvement (404 error page for non-existing pages)
* New settings field for pagination redirect
* Trailing slashes are no longer added to custom permalinks ended with extension, eg. .html, or .php

= 2.0.5.3 =
* Hotfix for redirects - redirect chain no longer occurs (WPML)
* Now $wp_query->is_404() is set to false when custom URI is detected
* Hotfix for ACF custom fields in terms
* Fix for trailing slash (in admin dashboard), also the trailing slashes are removed from permalinks containing GET parameters or hastags (often used by 3rd party plugins)

= 2.0.5.2.2 =
* Hotfix for admin requests (+ compatibility with WooCommerce TM Extra Product Options)
* Hotfix for no-ASCII characters in custom URIs
* Hotfix for attachments

= 2.0.5.2.1 =
* Hotfix for endpoints redirect

= 2.0.5.1/2.0.5.2 =
* Hotfix for REGEX rule
* yoast_attachment_redirect setting removed (it is no longer needed)
* yoast_primary_term setting replaced with "permalink-manager-primary-term" filter
* Hotfix for WP All Import
* Hotfix for WooCommerce endpoints
* Better support for Polylang
* Support for Theme My Login plugin

= 2.0.5 =
* Now, the duplicates and unused custom permalinks can be automatically removed
* Better support for endpoints
* "Disable slug appendix" field is no longer needed
* %{taxonomy}_flat% tag enhanced for post types permastructures
* Fix for WPML language prefixes in REGEX rule used to detect URIs
* Possibility to disable Permalink Manager functions for particular post types or taxonomies

= 2.0.4.3 =
* Hotfix for problem with custom URIs for new terms & posts

= 2.0.4.2 =
* Trailing slashes redirect adjustment

= 2.0.4.1 =
* Hotfix for Elementor and another visual editor plugins
* Support for endpoints parsed as $_GET parameters

= 2.0.4 =
* New settings field - "Deep detect"

= 2.0.3.1 =
* Fix for Custom Fields tag in permastructures

= 2.0.3 =
* Custom URI editor in "Quick Edit"
* "Quick/Bulk Edit" hotfix
* New permastrutcure tag %category_custom_uri%

= 2.0.2 =
* WooCommerce search redirect loop - hotfix

= 2.0.1 =
* WooCommerce endpoints hotfix
* Redirects save notices - hotfix

= 2.0.0 =
* Extra Redirects - possibility to define extra redirects for each post/term
* New "Tools" section - "Permalink Duplicates"
* UI improvements for taxonomies ("Custom URI" panel)
* Fixes for reported bugs

= 1.11.6.3 =
* Slug appendix fix
* Hotfix for WooCommerce checkkout

= 1.11.6 =
* Hotfix for taxonomy tags
* Hotfix for custom field tags
* Hotfix for Jetpack
* Suuport for WP All Import
* Support for Custom Permalinks

= 1.11.5.1 =
* Hotfix for "Custom URI" form
* Hotfix for Yoast SEO & Visual Composer
* Now it is possible to disable slugs appendix

= 1.11.4 =
* Hotfix for RSS feeds URLs

= 1.11.1 =
* Trailing slashes & Decode URIs - new settings
* Fix for "Bulk Edit" URI reset
* Partial code refactoring

= 1.11.0 =
* Hierarchical taxonomies fix
* New hook: "permalink_manager_filter_final_term_permalink"

= 1.10.2 =
* Taxonomies & permastructures fix

= 1.1.1 =
* Typo fix
* UI improvements
* Fix for canonical redirects in WPML

= 1.1.0 =
* Partial code refactoring
* "Auto-update" feature
* UI/UX improvements
* Support for AMP plugin by Automattic

= 1.0.3 =
* Another pagination issue - hotfix

= 1.0.2 =
* Post pagination fix
* Basic REGEX support
* 'permalink_manager_filter_final_post_permalink' filter added

= 1.0.1 =
* WPML support fixes

= 1.0.0 =
* Further refactoring
* WPML support added
* Some minor issues fixed
* "Sample permalink" support added

= 0.5.2/0.5.3 =
* Another hotfix

= 0.5.1 =
* Hotfix for "Settings" section

= 0.5.0 =
* Code refactoring completed
* Interface changes
* Hooks enabled

= 0.4.9 =
* Hook for removed posts (their URI is now automatically removed)

= 0.4.8 =
* Pagination bug - SQL formula fix (offset variable)

= 0.4.7 =
* Strict standards - fix for arrays with default values

= 0.4.6 =
* 302 redirect fix.
* Code optimization.

= 0.4.5 =
* Bug with infinite loop fixed.
* Bug with revisions ID fixed.

= 0.4.4 =
* Redirect for old URIs added.
* Debug tools added.

= 0.4.3 =
* Hotfix for "Screen Options" save process.

= 0.4.2 =
* Hotfix for bulk actions' functions - additional conditional check for arrays added.

= 0.4.1 =
* Hotfix for "Edit Post" URI input (the URIs were reseted after "Update" button was pressed).

= 0.4 =
* Rewrite rules are no longer used (SQL queries are optimized). The plugin uses now 'request' filter to detect the page/post that should be loaded instead.
* Now full URI (including slug) is editable.
* A few major improvements applied.
* Partial code optimization.

= 0.3.4 =
* Hotfix for not working custom taxonomies tags.
* Now the rewrite rules for custom post types are stored in different way.

= 0.3.3 =
* Hotfix for bug with dynamic function names in PHP7.

= 0.3.2 =
* Hotfix for front-end permalinks. The custom permastructures worked only in wp-admin.

= 0.3.1 =
* Hotfix for Posts & Pages permastructures

= 0.3 =
* Now all permalink parts can be edited - new "Permalink Base Editor" section added.
* Code optimization.
* Bugfixes for Screen Options & Edit links.

= 0.2 =
* First public version.

= 0.1 =
* A first initial version.
