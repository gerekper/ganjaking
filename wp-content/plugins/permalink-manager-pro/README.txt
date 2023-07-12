=== Permalink Manager Pro ===
Contributors: mbis
Donate link: https://www.paypal.me/Bismit
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: permalinks, custom permalinks, permalink, woocommerce permalinks, url editor
Requires at least: 4.4.0
Requires PHP: 5.4
Tested up to: 6.3.0
Stable tag: 2.4.1.2

Permalink Manager lets you customize the complete URL addresses of your posts, pages, custom post types, terms, and WooCommerce links with ease without touching any core files.

== Description ==

Permalink Manager is a highly rated WordPress permalink editor that allows users to customize post, page, and custom post type URLs (taxonomies are supported in Pro version).

If you want your website to be optimized for search engines, you must give careful consideration to the structure of your URLs. When you use Permalink Manager, it is a piece of cake as **you have complete control over your WordPress permalinks!**

The plugin works with all custom post types and taxonomies, as well as many popular third-party plugins like as WooCommerce, Yoast SEO, WPML, and Polylang. To improve user experience and eliminate 404 or duplicated content errors, the original permalinks will automatically redirect your visitors to the new customized URL. What is more, using the plugin options you can modify the redirect and trailing slashes functions, which further improves SEO performance.

<a href="https://permalinkmanager.pro/docs/?utm_source=wordpressorg">Documentation</a> | <a href="https://permalinkmanager.pro/buy-permalink-manager-pro/?utm_source=wordpressorg">Buy Permalink Manager Pro</a>

= Features =

* **Edit the individual permalinks as you choose**<br/>For a consistent and SEO-friendly URL structure, you may customize and <a href="https://permalinkmanager.pro/docs/basics/change-permalinks/">change the permalink</a> of each post, page, and custom post type item.  *Categories, tags & custom taxonomies terms permalinks can be edited in Permalink Manager Pro.*
* **Edit URLs in bulk using permalink formats**<br/>In order to speed up the process of bulk URL modification, the plugin allows you to choose the default format for custom URLs using "Permastructures" settings. The new format will be applied automatically when a new post/term is added or once the old permalinks are regenerated.
* **Custom post types support**<br/>You may easily remove post type rewrite (base) slugs from your WordPress permalinks, for example. The plugin may be configured to filter just specified post types and taxonomies permalinks, excluding the rest of your content types.
* **Translate permalinks**<br/>If you have the WPML or Polylang plugins installed on your website, Permalink Manager allows you to translate the slug and specify different permalink format/structure for each language.
* **Remove parent slugs**<br/>Looking for a simple solution to shorten lengthy, hierarchical URL addresses? The plugin may be used to <a href="https://permalinkmanager.pro/docs/basics/bulk-edit-custom-permalinks/#remove-parent-slugs/">remove parent slugs from WordPress permalinks</a>.
* **Add category slug to post permalinks**<br/>Do you want to <a href="https://permalinkmanager.pro/docs/tutorials/add-category-slug-wordpress-permalinks/">add category slugs in your post permalinks</a>? Permalink Manager is the most convenient way to create a silo structure for your URL addresses.
* **Auto-redirect old URLs**<br/>An old (original) URL is automatically forwarded to an updated URL to avoid the 404 error and to improve the user experience.

= Additional features available in Permalink Manager Pro =

The free version covers all the necessary functions, while the premium version adds a few handy functionalities that can improve the process of adjusting WordPress permalinks.

Click here for additional information and to purchase <a href="https://permalinkmanager.pro?utm_source=wordpress">Permalink Manager Pro</a>.

* **Taxonomies support**<br/>Taxonomies are fully supported in the premium version (categories, tags & custom taxonomies). You may adjust individual term permalinks or change them all at once using "Permastructures".
* **WooCommerce support**<br/>Permalink Manager Pro may be used to change the URL addresses of WooCommerce products, tags, categories, and attributes. For example, you may use the plugin to <a href="https://permalinkmanager.pro/docs/tutorials/remove-product-category-woocommerce-urls/">remove /product/ and /product-category/ from WooCommerce URL</a>.
* **Custom fields support**<br/>Only Permalink Manager makes it possible to <a href="https://permalinkmanager.pro/docs/tutorials/how-to-use-custom-fields-inside-wordpress-permalinks/">add custom fields to WordPress permalinks</a> without the need for any technical skills on the part of the user.
* **Extra redirects**<br/>You can define extra 301 redirects (aliases) for any post, page, or term. Additionally, you may assign a redirect URL to each post/term, which will take users to any external URL address. For each element, the redirect URLs might be specified separately.

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

= Can I use the plugin to edit the category permalinks?
This feature is available only in Permalink Manager Pro.

= Is it possible to use Permalink Manager on large websites?
When the Permalink Manager was first designed, it was supposed to be used for a typical WordPress website, which usually has <strong>less than a few thousand subpages</strong>. As a result, all custom permalinks are <a href="https://permalinkmanager.pro/docs/filters-hooks/how-the-custom-uris-and-redirects-are-stored/">saved in a single row in the database</a> in order to avoid slowing down the pageload with multiple SQL queries to the database. This is the most effective approach for small and medium-sized websites, without affecting site speed.

While this data structure works for the vast majority of WordPress sites, it may not be optimal if you want to use the plugin to rewrite <strong>tens of thousands of permalinks</strong>. What works well for a smaller website may not scale well for a megasite. When the number of addresses on your site exceeds tens of thousands, the custom permalinks array may become quite huge, and any operations on it can have an effect on pageload time.

To summarize, the plugin is suitable for small and medium-sized websites. It will not slow down your pageload time or affect its usability in any way. However, if you want to use it on a much bigger website with thousands of permalinks (more than 60.000), please consider excluding content types that do not require customized permalink format in order to lower the custom permalinks array. For further details on the plugin's performance, please <a href="https://permalinkmanager.pro/docs/basics/performance/">visit this post</a>.

= Can I define different permalink formats per each language.
Yes, it is possible if you are using either WPML or Polylang. You can find <a href="https://permalinkmanager.pro/docs/tutorials/how-to-translate-permalinks/">the full instructions here</a>.

= Will the old permalink automatically redirect the new ones?
Yes, Permalink Manager will automatically redirect the native permalinks (used when the plugin is disabled, or before it was activated) to the actual, custom permalinks.

= Does this plugin support Buddypress?
Currently, there is no 100% guarantee that Permalink Manager will work correctly with Buddypress.

= Can I remove the plugin after the permalinks are updated? =
Yes, if you used Permalink Manager only to regenerate the slugs (native post names). Please note that if you use custom permalinks (that differ from the native ones), they will no longer be used after the plugin is disabled.

It is because Permalink Manager overwrites one of the core WordPress functionalities to bypass the rewrite rules ("regular expressions" to detect the posts/pages/taxonomies/etc. and another parameters from the URL) by using the array of custom permalinks (you can check them in "Debug" tab) that are used only by the plugin.

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

= 2.4.1.2 (June 28, 2023) =
* Dev - Draft posts no longer automatically generate custom permalinks, but users may set them manually if necessary, or they will be generated when the post is published
* Fix - Duplicated REST API calls from Gutenberg JS functions are now ignored when custom permalinks are generated
* Fix - The 'High-Performance order storage (COT)' declaration for the WooCommerce has been fixed

= 2.4.1 (May 22, 2023) =
* Dev - The function that adds the "Permalink Manager" button via 'get_sample_permalink_html' filter has been updated
* Dev - The function that controls permalink trailing slashes has been refactored and improved
* Dev - When WPML is enabled, Permalink Manager uses "term_taxonomy_id" instead of "term_id" for language mismatch functions to avoid compatibility issues
* Dev - To avoid problems with other 3rd party plugins, the function that places the "Permalink Manager" button below the title editor field in Classic Editor mode no longer overwrites the whole HTML
* Enhancement - The plugin interface's text descriptions and label names have been simplified for readability
* Enhancement - Added new section "Exclusion settings" with a field to manually enter IDs of posts/terms to be ignored by Permalink Manager
* Fix - The compatability problem that caused "fatal error" for some RankMath users has been resolved

= 2.4.0 (April 12, 2023) =
* Dev - Improved custom permalink detection function
* Dev - Minor code improvements for the breadcrumbs filter function
* Dev - Minor CSS changes
* Dev - New filter added - 'permalink_manager_excluded_element_id'
* Dev - New filter added - 'permalink_manager_duplicate_uri_policy'
* Dev - Now users may select in "WPML/Polylang fix language mismatch" settings field between loading translation or triggering the canonical redirect to the detected item
* Dev - Support for WooCommerce 'High-Performance order storage (COT)' declared
* Fix - The RankMath redirection function is disabled if custom permalink is detected to prevent redirect loop
* Fix - The "Exclude drafts & pending posts" setting field has been changed to allow for greater control in generating and editing custom permalinks for draft and pending posts

= 2.3.1.1 (February 16, 2023) =
* Dev - Hotfix for "Quick Edit" URI editor

= 2.3.1 (February 13, 2023) =
* Dev - Bulk tools ("Regenerate/Reset" and "Find & replace") and "Quick Edit" code was refactored
* Dev - Minor code improvements
* Dev - New filter field - 'permalink_manager_ate_uri_editor'
* Dev - Improved compatibility with WPML's Advanced Translation Editor
* Fix - The /feed/ endpoint returns 404 error if 'feeds' in rewrite property of requested post type object is set to false
* Fix - The canonical redirect is no longer forced for LearnPress front-end pages

= 2.3.0 (December 14, 2022) =
* Dev - For improved readability, the plugin's code has been reformatted and more comments have been added to match WordPress PHP Coding Standards
* Dev - To simplify the codebase, redundant functions and variables were removed
* Fix - The post/term titles in Bulk URI Editor are protected from XSS (Cross-site scripting) attacks by sanitizing the displayed titles
* Fix - Improved compatibility with Groundhogg plugin
* Fix - Improved compatibility with BasePress plugin
* Fix - Minor improvements for WPML compatibility
* Fix - The bug that caused the message "You are not allowed to remove Permalink Manager data!" to show up randomly in the admin dashboard has been fixed

= 2.2.20.4 (November 23, 2022) =
* Fix - The "URI Editor" for individual term pages is now called later to ensure that all custom taxonomies are registered
* Dev - The "nonce" field has been renamed for clarity
* Dev - New filter added - 'permalink_manager_get_language_code'

= 2.2.20.2/2.2.20.3 (November 15, 2022) =
* Fix - A nonce field has been added to debug tools code for increased security
* Fix - The "Fix language mismatch" function now functions exactly the same way in Polylang as it does in WPML

= 2.2.20.1 (October 31, 2022) =
* Fix - Security fix for BAC vulnerability found in the debug function that allowed unauthorized removal of single URIs

= 2.2.20 (October 10, 2022) =
* Fix - The URLs with duplicated slashes (eg. example.com/sample-page////) are now handled correctly and forwarded to the canonical URL
* Fix - The redirect problem was resolved with WPForo versions after 2.0.1
* Dev - Improved compatibility with the WP All Import plugin functions
* Dev - Improved compatibility with Polylang plugin
* Dev - Better support for ACF Relationship fields
* Dev - The plugin no longer (by default) supports custom post types & taxonomies that do not have the "query_var" and "rewrite" properties
* Enhancement - In "Exclude drafts" mode, the URI Editor field in the "Quick Edit" section becomes "read-only" for the "Draft" posts

= 2.2.19.3 (August 11, 2022) =
* Dev - New filter added - 'permalink_manager_pre_sanitize_title'
* Fix - The old slugs are saved in the '_wp_old_slug' meta key even if the native slugs are changed in the URI Editor in the Gutenberg mode.
* Fix - Extra security check in the "Debug" section to prevent unauthorized users (CSRF) from removing the plugin's data.

= 2.2.19.2 (July 8, 2022) =
* Fix - JS conflict fixed ("Cannot read properties of null (reading 'isSavingMetaBoxes')")

= 2.2.19.1 (June 27, 2022) =
* Fix - JS conflict fixed ("Cannot read property 'isSavingPost' of null")

= 2.2.19 (June 27, 2022) =
* Fix - The term custom permalink is now returned in the correct language
* Fix - In Gutenberg mode, the custom permalinks are saved correctly and are not changed back to the default format ("URI Editor" is now only reloaded once the post has been saved and the metaboxes have been refreshed)
* Enhancement - Old URIs are saved as "extra redirects" if content is updated with WP All Import
* Dev - Additional minor improvements in code (including changes to make it work with PHP 8.1)

= 2.2.18 (May 18, 2022) =
* Fix - The "permalink_manager_filter_permastructure" filter can now also be used before the "Permastructure" settings are saved in the database
* Enhancement - Improved support for RankMath breadcrumbs
* Dev - License notification function has been improved (Permalink Manager Pro)
* Dev - Additional minor improvements in code

= 2.2.17 (March 22, 2022) =
* Fix - Permalink Manager supports WPML's "Post Types & Taxonomy Translation" settings and returns the permalink of the fallback post/term with the correct language code
* Fix - When the auto-update mode for categories is disabled, the manually adjusted permalinks are no longer overwritten by the default ones
* Enhancement - Permalink Manager now allows you to rewrite just chosen articles and terms while leaving the rest untouched (See '"Auto-update" permalinks' settings field)
* Enhancement - Improved support for SEOPress breadcrumbs
* Enhancement - "Auto-update permalinks" setting is now replaced with "URI update mode" to give users better control on how Permalink Manager generates and saves the custom permalinks
* Dev - Additional minor improvements in code

= 2.2.16 (January 23, 2022) =
* Enhancement - Improved support for "Primary category" feature included in Yoast SEO
* Enhancement - Added support for Avia/Enfold breadcrumbs filter
* Enhancement - Further optimisation and improvements for Permalink_Manager_Core_Functions->new_uri_redirect_and_404() function
* Fix - Permalink Manager now recognises the "Explore" listing page in MyListing theme properly

= 2.2.15.1 (January 14, 2022) =
* Fix - "Regenerate/reset" tool works correctly again in Permalink Manager Lite

= 2.2.15 (January 12, 2022) =
* Enhancement - UI Improvements for Regenerate/reset tool
* Dev - WPML_URL_Filters->permalink_filter() hook is also used by Permalink Manager to filter custom permalinks
* Enhancement - wp_make_link_relative() function is used to prevent redirect loops in new_uri_redirect_and_404() (suggested by mgussekloo)
* Fix - Adjustments to the debug function's security to prevent XSS injection

= 2.2.14 (October 20, 2021) =
* Enhancement - Improvements for Gutenberg Editor
* Dev - Tippy.js (by atomiks) updated to version 6.3.2
* Fix - From now on, the user role selected in “URI Editor role capability” is respected in “Quick Edit” box hooks (reported by @lozeone)
* Dev - Further security improvements inside WP-Admin dashboard (reported by Vlad Vector)

= 2.2.13.1 (September 20, 2021) =
* Dev - Minor security improvements inside WP-Admin dashboard
* Fix - Allow canonical redirect for default language if "Hide URL language information for default language" is turned on in Polylang settings
* Enhancement - New settings field - "Primary category support"
* Enhancement - "Force 404 on non-existing pagination pages" works now with archive pages

= 2.2.12 (August 17, 2021) =
* Dev - New filters added - 'permalink_manager_excluded_post_ids' & 'permalink_manager_excluded_term_ids'
* Dev - Additional minor changes in the codebase
* Fix - Canonical permalinks for blog pagination is now correctly filtered (if Yoast SEO is used)
* Fix - Better support for 'private' posts & pages

= 2.2.11 (June 24, 2021) =
* Fix - The function that automatically removes the broken URIs is no longer triggered when WP Rocket is turned on and non-logged-in user tries to access the broken URL.

= 2.2.10 (June 7, 2021) =
* Enhancement - New settings field - "Copy query parameters to redirect target URL" & "Extra redirects (aliases)"
* Enhancement - UI improvements in settings section
* Dev - Improved support for WPML's Classic Translation Editor
* Dev - Additional minor changes in the codebase

= 2.2.9.9 (April 26, 2021) =
* Fix - Hotfix for AMP WP integration

= 2.2.9.8 (April 26, 2021) =
* Fix - The old native slug is now correctly saved after it is changed in URI Editor.
* Enhancement - The post type archives are now also added to the filtered breadcrumbs trail
* Enhancement - Basic support added for WP All Export plugin
* Enhancement - Basic support added for AMP for WP
* Dev - (Permalink Manager Pro only) "Plugin Update Checker" by YahnisElsts library updated to 4.11 version

= 2.2.9.7 (March 11, 2021) =
* Enhancement - Support for WooCommerce CSV Product Importer/Exporter added
* Enhancement - Better support for relationship field (ACF)
* Fix - The custom redirects are now case-insensitive

= 2.2.9.6 (February 8, 2021) =
* Fix - Hotfix for WooCommerce coupon related functions

= 2.2.9.5 (February 8, 2021) =
* Fix - The custom permalink is generated properly if the product is duplicated in WooCommerce dashboard
* Enhancement - New settings field - "Exclude drafts"
* Enhancement - Minor code improvements

= 2.2.9.4 =
* Fix - The language prefix for default language is now added again when "Use directory for default language" mode is turned on in WPML settings ("Language URL format")

= 2.2.9.3 =
* Fix - The custom permalinks are now saved correctly for new posts with 'wp_insert_post' hook
* Fix - The custom permalinks are deleted when 'delete_post' hook is called
* Fix - WPML - language switcher on posts (blog) page works correctly now
* Fix - WooCommerce Subscription - the switch subscription URL is no longer overwritten
* Fix - The URLs with duplicated trailing slashes are now redirected to the canonical permalink
* Enhancement - Basic support for Ultimate Member plugin added
* Enhancement - UI improvements
* Enhancement - Support for "comment-page" endpoint added
* Enhancement - New filter added - 'permalink_manager_control_trailing_slashes'

= 2.2.9.2 =
* Dev - Improvements for Permalink_Manager_Core_Functions::control_trailing_slashes() function
* Dev - Minor codebase improvements
* Fix - Hotfix for "Automatically fix broken URIs" function
* Fix - Underscores are now by default allowed in the custom permalinks
* Enhancement - Better support for GeoDirectory plugin
* Fix - 'permalink_manager_allow_new_post_uri' & 'permalink_manager_allow_update_post_uri' filter replaced 'permalink_manager_new_post_uri_{$post_object->post_type}' and 'permalink_manager_update_post_uri_{$post->post_type}'

= 2.2.9.0/2.2.9.1 =
* Enhancement - Basic support for BasePress added
* Enhancement - Added support for custom product attributes in products' permalinks (WooCommerce)
* Fix - "Trailing slash redirect" is now disabled on front pages (to prevent redirect loop on Polylang/WPML language front pages)
* Dev - The taxonomy term used in custom permalinks is selected differently
* Dev - Performance improvements (duplicate-check function)
* Dev - Further improvements for the function used to sanitize the custom permalinks
* Dev - Codebase improvements

= 2.2.8.8/2.2.8.9 =
* Fix - Hotfix for 'redirect_canonical' function (causing a redirect loop)
* Fix - The custom canonical permalink set with Yoast SEO is now no longer overwritten
* Fix - The custom permalinks are no longer saved if the post/term has no title
* Fix - Hotfix for Gutenberg related JS errors
* Fix - Hotfix for Groundhogg plugin
* Fix - Hotfix for "Customize" admin bar menu link
* Fix - Hotfix for WPML's language switcher on posts page
* Fix - Hotfixes for WP 5.5 - blog/posts page + draft template is now loaded correctly
* Dev - Trailing slash redirect code adjustments
* Enhancement - Added support for GeoDirectory plugin

= 2.2.8.7 =
* Dev - Improved breadcrumbs hook (better compatibility with WPML/Polylang)
* Fix - Hotfix for permalinks used in language switcher on blog/posts page (WPML)
* Fix - Hotfix for cart URL in WooCommerce's mini-cart widget (now the permalink is translated correctly when WPML/Polylang is used)
* Dev - Improved support for WPML's Advanced Translation
* Dev - Improved support for pagination & embed endpoints
* Fix - Hotfix for attachments permalinks
* Fix - Improved url_to_postid() hook
* Fix - Added support for Dokan /edit/ endpoint

= 2.2.8.6 =
* Fix - Hotfix for Permalink_Manager_Helper_Functions::get_disabled_taxonomies() function
* Dev - New wrapper function with filter 'permalink_manager_post_statuses' for get_post_statuses()
* Enhancement - Extended support for "My Listing" theme (by 27collective)
* Fix - Hotfix for Gutenberg editor (broken HTML output)
* Dev - Extended support for permalinks stored in Yoast SEO database tables (Indexables)

= 2.2.8.4/2.2.8.5 =
* Fix - Hotfix for Permastructures (now the permalink formats are saved correctly)
* Fix - Hotfix for trailing slashes settings
* Dev - Improved setting fields descriptions
* Dev - Adjustments for search functionality in Bulk URI Editor
* Enhancement - Support for WPML Classic Translation Editor
* Dev - Adjustments for "Auto-update URI"
* Dev - Improvements for get_post_types_array() & get_taxonomies_array() functions used to list the content types supported by Permalink Manager

= 2.2.8.2/2.2.8.3 =
* Dev - Improved descriptions in the plugin settings
* Fix - Hotfix for endpoint redirect

= 2.2.8.1 =
* Fix - Hotfix for stop-words (now, the stop-words can be saved again)
* Enhancement - Support for Duplicate Page plugin

= 2.2.8.0 =
* Fix - Hotfix for multisite/network installations. Now, the plugin globals are reloaded whenever switch_blog() function is called.
* Fix - Hotfix for url_to_postid() function. The $pm_query global will no longer be altered.
* Fix - Hotfix for post/page revisions in custom permalink detect function
* Dev - Improved WP All Import Pro integration (better support for taxonomies)
* Dev - A different approach for WP Customize URLs
* Enhancement - New option added: "Old slug redirect"

= 2.2.7.6 =
* Dev - Code optimization for Bulk URI Editor
* Enhancement - Support for WooCommerce breadcrumbs
* Fix - A hotfix for WPForo plugin
* Enhancement - New filter "permalink_manager_chunk_size" that allows to control the chunk size in bulk tools ("Regenerate/reset", "Find & Replace")
* Enhancement - New filter "permalink_manager_sanitize_regex" that allows to adjust the function that sanitizes the custom permalinks
* Dev - Autoload for backup arrays is now disabled
* Enhancement - New option added: "Convert accented letters"

= 2.2.7.5 =
* Fix - CSS adjustments. Now the redirects box is displayed correctly in the URI editor

= 2.2.7.4 =
* Enhancement - Support for "Primary category" set with SEOPress & RankMath plugins
* Enhancement - Support for breadcrumbs added by SEOPress & RankMath plugins
* Dev - Improved "trailing slashes" functionality - untrailingslashit() & trailingslashit() replaced with REGEX based functions
* Enhancement - Possibility to remove custom permalinks, redirects, permastructure settings directly from "Debug" section
* Enhancement - New filter "permalink_manager_duplicates_priority" that allows to decide what content type ("posts" or "terms") should be loaded when the custom permalink is duplicated
* Fix - A minor fix for url_to_postid() function

= 2.2.7.3 =
* Enhancement - Support for "Primary category" set with The SEO Framework
* Dev - Changes for URI Editor section backend (SQL queries + improvements for search box)
* Enhancement - Improved support for WooCommerce Wishlist plugin
* Dev - Improvements for slugs sanitization functions
* Enhancement - Possibility to exclude posts from bulk tools added to "Auto-update the URI" dropdown in URI Editor

= 2.2.7.1 =
* Fix - Hotfix for PHP Fatal error in permalink-manager-admin-functions.php file

= 2.2.7 =
* Dev - Force 404 for draft posts (for non-logged-in users)
* Enhancement - New setting fields: "URI Editor role capability" & "Force HTTPS in URLs"
* Dev - Minor improvements

= 2.2.6 =
* Dev - More debug functions added
* Dev - Better support for Hebrew letters
* Enhancement - Support for location custom fields in WP Store Locator - CSV Manager plugin
* Enhancement - Improved support for Gutenberg editor (reported by Cedric Busuttil)

= 2.2.4/2.2.5 =
* Dev - Minor code improvements
* Dev - Yoast SEO Breadcrumbs - further improvements
* Fix - Hotfix for Toolset custom fields support (Permalink Manager Pro)
* Fix - Hotfix for Polylang URL modes

= 2.2.3 =
* Dev - Code improvements for WP All Import integration functions
* Fix - Hotfix for Elementor conflict with custom redirects function (Permalink Manager Pro)
* Enhancement - New field ("Do not automatically append the slug") in Permastructure settings added to each post type & taxonomy
* Enhancement - Basic support added for Mailster plugin
* Enhancement - New permastructure tag: "%monthname%"

= 2.2.2 =
* Dev - Code improvement for "Quick Edit" inline form
* Enhancement - Support for Yoast SEO breadcrumbs added
* Fix - Hotfix for Elementor

= 2.2.1.1/2.2.1.2/2.2.1.3/2.2.1.4 =
* Fix - Hotfix for function that detects custom URIs
* Dev - Custom URIs for WP All Import inported posts are now generated 30 seconds after the import job is completed

= 2.2.1 =
* Fix - Hotfix for Customizer (custom permalinks filters are disabled in admin panel)
* Dev - Minor UX improvements
* Enhancement - Partial support for TranslatePress plugin added
* Fix - Term permalinks are processed correctly when WPML enabled and "Adjust IDs for multilingual functionality" mode is activated
* Enhancement - New setting field separated from "Force custom slugs" - now, both the native slugs and special characters (.|_+) can be kept inside the slugs
* Enhancement - "permalink_manager_force_custom_slugs" filter added

= 2.2.0 =
* Fix - Hotfix for WPML - ?lang query parameter is now appended correctly
* Fix - Support for comment pages endpoint
* Dev - Minor code adjustments
* Enhancement - Metabox for Gutenberg enabled also for CPT
* Dev - Further improvements for redirect hooks
* Fix - Hotfix for WP Customizer
* Fix - Native slugs are saved correctly in Gutenberg editor
* Enhancement - "permalink_manager_filter_permastructure" filter added
* Enhancement - Permastructures can be now translated from admin panel

= 2.1.2.1/2.1.2.2 =
* Fix - Hotfix for "Force custom slugs" option - now special characters are not removed if "Yes, use post/term titles + do not strip special characters: .|-+" mode is set.
* Fix - Hotfix for custom fields support in custom permalinks

= 2.1.2 =
* Fix - Hotfix for WP All Import - default permalinks are now assigned correctly to imported posts + possibility to disable WP All Import custom URI functions in Permalink Manager settings
* Fix - Hotfix for Yoast SEO - notice displayed on author pages
* Dev - Adjustments for sanitize slug functions
* Enhancement - Basic support for Gutenberg added

= 2.1.1 =
* Enhancement - Support for draft custom permalinks
* Enhancement - Support for WP All Import plugin, now the custom permalinks can be defined directly in XML, CSV, ZIP, GZIP, GZ, JSON, SQL, TXT, DAT or PSV import files.
* Fix - Permalink_Manager_Pro_Functions::save_redirects() method - now the custom redirects are correctly saved when a custom permalink is updated.
* Fix - Hotfix for "Language name added as a parameter" mode in "WPML Language URL format" settings.
* Fix - Hotfix for canonical redirect triggered by WPML.
* Dev - Better support for non-latin letters in custom URIs & redirects
* Dev - Better support for endpoints
* Enhancement - Searchbox in URI Editors

= 2.1.0 =
* Enhancement - Support for "url_to_postid" function
* Dev - Bulk tools use now AJAX & transients to prevent timeout when large number of posts/terms is processed
* Fix - Fix for multi-domain language setup in WPML

= 2.0.6.5 =
* Enhancement - Support for %__sku% permastructure tag (WooCommerce) added - now SKU number can be added to the custom permalinks (Permalink Manager Pro)

= 2.0.6.4 =
* Dev - Code optimization
* Enhancement - 'permalink_manager_fix_uri_duplicates' filter added
* Enhancement - Possibility to display the native slug field
* Fix - License validation functions fixed

= 2.0.6.3.2 =
* Enhancement - Support added for Revisionize plugin
* Fix - Minor tweaks

= 2.0.6.2/2.0.6.3 =
* Enhancement - Japaneese translation added
* Dev - Some minor improvements
* Enhancement - New filters: permalink_manager_hide_uri_editor_term_{$term->taxonomy}, permalink_manager_hide_uri_editor_post_{$post->post_type} & permalink_manager_update_term_uri_{$this_term->taxonomy}, permalink_manager_update_post_uri_{$post->post_type}, permalink_manager_new_post_uri_{$post_object->post_type}
* Fix - Hotfix for default permalinks (no-hierarchical post types)
* Fix - Hotfix for attachments default permalinks + URI detect function

= 2.0.6.1 =
* Fix - Hotfix for endpoints in REGEX
* Fix - Minor bug fixed - native slugs are now correctly regenerated
* Fix - Hotfix for URI sanitization functions
* Fix - Hotfix for AMP plugin
* Enhancement - Full support for WPML multi-domain language setup
* Fix - Hotfix for VisualComposer + Yoast SEO JS functions
* Fix - Hotfix for WPML String Translation

= 2.0.6.0 =
* Fix - Minor bugs fixed
* Enhancement - New permastrutcure tag - %native_slug%
* Enhancement - "Force custom slugs" feature enhanced with new options
* Enhancement - Possibility to redirect the posts & terms to external URL (Permalink Manager Pro)

= 2.0.5.9 =
* Enhancement - New permastructure tags - %post_type% & %taxonomy%
* Enhancement- Support for "Taxonomy" custom field in ACF (Advanced Custom Fields)
* Fix - Minor fix for endpoints
* Enhancement - New hooks: "permalink_manager-filter-permalink-base" used instead of "permalink-manager-post-permalink-prefix" & "permalink-manager-term-permalink-prefix"

= 2.0.5.7/2.0.5.8 =
* Fix - MultilingualPress plugin
* Fix - Hotfix & better support for attachment post type (Media Library)
* Fix - Custom redirects for old permalinks are now correctly saved in Permalink Manager Pro
* Enhancement - Support for WooCommerce Wishlist plugin

= 2.0.5.6 =
* Fix - The URIs for trashed posts are now correctly removed
* Dev - Better support for non-ASCII characters in URIs
* Fix - Minor fix for hierarchical post types
* Fix - Coupon URL redirect
* Enhancement - New filter - "permalink-manager-force-hyphens"

= 2.0.5.5 =
* Enhancement - Discount URLs for WooCommerce - now the shop clients can use coupons' custom URIs to easily apply the discount to the cart
* Enhancement - Extra AJAX check for duplicated URIs in "Edit URI" box
* Enhancement - WordPress CronJobs for "Automatically remove duplicates" functionality
* Dev - Extra improvements in "save_post/update_term" hooks
* Fix - Terms permalinks added via "Edit post" page
* Enhancement - "permalink-manager-force-lowercase-uris" filter added

= 2.0.5.4 =
* Enhancement - "permalink_manager_empty_tag_replacement" filter added
* Enhancement - New settings field for pagination redirect
* Enhancement - Trailing slashes are no longer added to custom permalinks ended with extension, eg. .html, or .php
* Fix - Term placeholder tags in taxonomies permastructures
* Fix - Page pagination improvement (404 error page for non-existing pages)

= 2.0.5.3 =
* Fix - Hotfix for redirects - redirect chain no longer occurs (WPML)
* Fix - Hotfix for ACF custom fields in terms
* Fix - "Trailing slashes" mode setting added, also the trailing slashes are removed from permalinks containing GET parameters or anchors (often used by 3rd party plugins)

= 2.0.5.2.2 =
* Fix - Hotfix for admin requests (+ compatibility with WooCommerce TM Extra Product Options)
* Fix - Hotfix for no-ASCII characters in custom URIs
* Fix - Hotfix for attachments

= 2.0.5.2.1 =
* Fix - Hotfix for endpoints redirect

= 2.0.5.1/2.0.5.2 =
* Dev - yoast_attachment_redirect setting removed (it is no longer needed)
* Dev - "yoast_primary_term" setting replaced with "permalink-manager-primary-term" filter
* Fix - REGEX rules
* Fix - Hotfix for WP All Import
* Fix - Hotfix for WooCommerce endpoints
* Dev - Better support for Polylang
* Enhancement - Support for Theme My Login plugin

= 2.0.5 =
* Enhancement - Now, the duplicates and unused custom permalinks can be automatically removed
* Enhancement - %{taxonomy}_flat% tag enhanced for post types permastructures
* Enhancement - Possibility to disable Permalink Manager functions for particular post types or taxonomies
* Dev - Better support for endpoints
* Dev - "Disable slug appendix" field is no longer needed
* Fix - Fix for WPML language prefixes in REGEX rule used to detect URIs

= 2.0.4.3 =
* Fix - Hotfix for problem with custom URIs for new terms & posts

= 2.0.4.2 =
* Trailing slashes redirect adjustment

= 2.0.4.1 =
* Fix - Hotfix for Elementor and another visual editor plugins
* Dev - Support for endpoints parsed as $_GET parameters

= 2.0.4 =
* Enhancement - New settings field - "Deep detect"

= 2.0.3.1 =
* Enhancement - Custom fields tags in permastructures settings

= 2.0.3 =
* Enhancement - Custom URI editor in "Quick Edit"
* Enhancement - New permastrutcure tag %category_custom_uri%
* Fix - "Quick/Bulk Edit" hotfix

= 2.0.2 =
* Fix - WooCommerce search redirect loop - hotfix

= 2.0.1 =
* Fix - WooCommerce endpoints hotfix
* Fix - Redirects save notices - hotfix

= 2.0.0 =
* Enhancement - Extra Redirects - possibility to define extra redirects for each post/term
* Enhancement - New "Tools" section - "Permalink Duplicates"
* Enhancement - UI improvements for taxonomies ("Custom URI" panel)
* Fix - Fixes for user reported bugs

= 1.11.6.3 =
* Fix - Slug appendix fix
* Fix - Hotfix for WooCommerce checkout

= 1.11.6 =
* Fix - Hotfix for taxonomy tags
* Fix - Hotfix for custom field tags
* Fix - Hotfix for Jetpack
* Enhancement - Support for WP All Import
* Enhancement - Support for Custom Permalinks

= 1.11.5.1 =
* Fix - "Custom URI" form issues
* Fix - for Yoast SEO & Visual Composer
* Enhancement - Possibility to choose if slugs should or should not be added to the default custom permalinks

= 1.11.4 =
* Fix - Hotfix for RSS feeds URLs

= 1.11.1 =
* Enhancement - Trailing slashes & Decode URIs - new settings
* Fix - "Bulk Edit" URI reset
* Dev - Partial code refactoring

= 1.11.0 =
* Fix - Hierarchical taxonomies fix
* Enhancement - 'permalink_manager_filter_final_term_permalink' filter added

= 1.10.2 =
* Fix - Taxonomies & permastructures fix

= 1.1.1 =
* Dev - UI improvements
* Fix - Fix for canonical redirects in WPML

= 1.1.0 =
* Dev - Partial code refactoring
* Dev - UI/UX improvements
* Enhancement - "Auto-update" feature
* Enhancement - Support for AMP plugin by Automattic

= 1.0.3 =
* Fix - Another pagination issue - hotfix

= 1.0.2 =
* Fix - Post pagination fix
* Enhancement - Basic REGEX support
* Enhancement - 'permalink_manager_filter_final_post_permalink' filter added

= 1.0.1 =
* Fix - WPML support fixes

= 1.0.0 =
* Dev - Further refactoring
* Dev - Some minor issues fixed
* Enhancement - WPML support added
* Enhancement - "Sample permalink" support added

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
