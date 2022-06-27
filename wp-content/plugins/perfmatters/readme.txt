=== Perfmatters ===
Contributors:
Donate link: https://perfmatters.io
Tags: perfmatters
Requires at least: 4.7
Requires PHP: 7.0
Tested up to: 6.0
Stable tag: 1.9.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Perfmatters is a lightweight performance plugin developed to speed up your WordPress site.

== Description ==

[Perfmatters](https://perfmatters.io/) is a lightweight web performance plugin designed to help increase Google Core Web Vitals scores and fine-tune how assets load on your site.

= Features =

* Easy quick toggle options to turn off resources that shouldn't be loading. 
* Disable scripts and plugins on a per post/page or sitewide basis with the Script Manager. 
* Defer and delay JavaScript, including third-party scripts.
* Automatically remove unused CSS.
* Preload resources, critical images, and prefetch links for quicker load times.
* Lazy load images and enable click-to-play thumbnails on videos.
* Host Google Analytics and Google Fonts locally.
* Change your WordPress login URL. 
* Disable and limit WordPress revisions.
* Add code to your header, body, and footer.
* Optimize your database.

= Documentation =

Check out our [documentation](https://perfmatters.io/docs/) for more information on how to use Perfmatters.

== Changelog ==

= 1.9.4 - 06.21.2022 =
* Updated EDD plugin updater class to version 1.9.2.
* Added default exclusion to REST API option for compatibility.

= 1.9.3 - 06.17.2022 =
* Remove Used CSS filter adjustment to fix an issue where certain WordPress post functions wouldn't be available when trying to selectively disable the feature.
* Rolled back minor plugin UI JavaScript addition, as it was interfering with entering data on multiple lines in certain input fields.

= 1.9.2 - 06.16.2022 =
* Added new perfmatters_used_css filter.
* Added new perfmatters_allow_buffer filter.
* Added a notice in the Script Manager when Testing Mode is enabled.
* Improved reliability of CSS Background Image function when child elements with additional background images are present.
* Script Manager style compatibility fixes.
* Fixed an issue where some post specific meta options were not being respected when determining if a feature should run.
* Fixed an issue where pressing enter on the main plugin settings page would trigger a specific form action instead of save settings.
* Changed CSS class initialization hook to be in the correct order with other output buffer functions.
* Made an adjustment to how we generate the local used stylesheet URL for better compatibility.
* Fixed an issue where loading attribute was still getting applied to images that were excluded from lazy loading.
* Fixed an issue where images inside an excluded picture element were not also getting excluded.
* Fixed an issue in the Script Manager where archives were not being grouped together with their respective post type.
* Additions to plugin UI JavaScript to allow for disabled sections to be hidden even when nested controllers are present.
* Moved background process library to composer autoloader.
* Removed BETA tag from Remove Unused CSS option.

= 1.9.1 - 05.23.2022 =
* Added new option to lazy load CSS Background Images.
* Added new option for Dual Tracking when using gtag.js in local analytics.
* Added new perfmatters_rest_api_exceptions filter.
* Fixed an issue where individually delayed local scripts would not get correctly rewritten to load from the CDN.
* Fixed an issue where lazy loading would run into an error if no px or % was specified with the threshold value.
* Fixed an issue with buffer validation that was conflicting with certain caching setups.
* Fixed an issue where existing font preconnect and prefetch tags were not being detected properly when using Local Fonts.
* Fixed an error related to cookie constants when running MU Mode in certain environments.
* Fixed multiple AMP validation errors and added additional checks to prevent certain functions from running on AMP URLs.
* Minor adjustment to CDN rewrite regex pattern to work with encoded quotation characters.
* Changed toggle CSS selectors to be more specific to prevent conflicts.
* Moved plugin settings header output to in_admin_header action hook for compatibility.
* Moved JS optimization functions to new class structure to be more inline with current codebase.
* Improvements to critical image preloading allowed for a move to a singular output buffer.

= 1.9.0 - 04.15.2022 =
* Fixed an issue that was causing excluded selectors to not be recognized properly after Used CSS was cleared.
* Minor adjustments to the new plugin UI.

= 1.8.9 - 04.13.2022 =
* Updated plugin settings UI.
* Added new post meta option to Clear Used CSS for an individual page or post type.
* Added new perfmatters_rucss_excluded_selectors filter.
* Fixed a lazy loading issue that was preventing some images from loading properly in Safari.
* Migrated Delay JS Timeout dropdown to a simpler on/off toggle that will default to 10 seconds. Our filter is also still available to set a custom timeout value.
* Fixed an issue with MU plugin that was interfering with rewrite rules in some instances.
* Added additional excluded page builder parameter for Flatsome UX.
* Moved restore default functionality to a separate option on the tools page.
* Code refactoring.
* Translation updates.

= 1.8.8 - 03.23.2022 =
* Changed default setting for Used CSS Method from file to inline, as we think this will be the more compatible solution for most users going forward. If you were previously using the file method, you may need to save that option again.
* Added width and height parameters to placeholder SVGs to prevent warnings for a ratio mismatch that would happen for some images.
* Fixed an issue where the noscript tags were getting malformed for some images inside picture tags after lazy loading.
* Removed placeholder SVGs on source tags since the image tag will already have one.
* Changed settings export file name date format to be easier to organize when managing multiples.
* Updated tooltip for Blank Favicon option to be more clear.


= 1.8.7 - 03.14.2022 =
* Added new Used CSS Method option to choose whether to load used CSS from a file or inline.
* Added new perfmatters_cache_path filter.
* Updated metabox functions to restrict metabox display to administrators only.
* Made some adjustments to custom login URL function to better support 3rd party tools using WP CLI.
* Added Fusion Builder query string parameters to excluded page builders array.
* Adjusted Unused CSS regex to be more consistent when stylesheets are placed in between other link tags.
* Changes to instances where ABSPATH was used to determine a directory location for better compatibility with certain hosts.
* Fixed an issue with Remove Global Styles option where duotone SVGs were not being removed on WordPress 5.9.2.
* Fixed an issue where WooCommerce block stylesheets were not getting correctly dequeued when Disable Scripts option was set.
* Fixed an issue that was causing the CSS Parser library not to get included correctly in certain cases.
* Translation updates.

= 1.8.6 - 02.10.2022 =
* Added new option to Remove Global Styles related to duotone filters.
* Added new perfmatters_script_manager_locale filter.
* Added new perfmatters_disable_woocommerce_scripts filter.
* Added new perfmatters_page_builders filter.
* Added new perfmatters_delay_js_behavior filter.
* Fixed an issue with the unused CSS parser that was incorrectly rewriting relative URLs if there was no query string present on the original stylesheet src.
* Added additional parameter to page builders array for compatibility.
* Fixed an issue that was causing the login URL disabled 404 behavior to result in an error if a 404 template was not found.
* Added some additional checks before creating cache directories for local fonts and used CSS.
* Fixed an issue that was causing the fade-in effect to conflict with child images inside a lazy loaded container.
* Fixed an undefined index warning coming from unused CSS settings update function.
* Added a default delay JS exclusion for admin only inline customize-support script.
* Refactored entire meta.php code to be more efficient (38% smaller) and in line with current structure.
* Translation updates.

= 1.8.5 - 01.19.2022 =
* Added new feature to Remove Unused CSS (BETA).
* Added new perfmatters_remove_unused_css filter.
* Adjusted CDN Rewrite buffer priority for better compatibility with other features.
* Made an improvement to the Disable XML-RPC function to return a 403 error when xmlrpc.php is accessed directly.
* Script Manager stylesheet updates for better compatibility.
* Fixed an issue in the Script Manager where the input controls were sometimes not displaying after toggling a script off.
* Added additional style for YouTube preview thumbnail play button to fix an alignment issue with certain setups.
* Buffer adjustments for compatibility.

= 1.8.4 - 12.19.2021 =
* Fixed an issue that was interfering with sitemap display in certain configurations.
* Added <a> element support for lazy loading inline background images.

= 1.8.3 - 12.13.2021 =
* Added new perfmatters_fade_in_speed filter.
* Fixed an issue that was preventing lazy loading fade in from working correctly with certain background images.
* Fixed an issue that was interfering with the display of certain inline SVG elements.
* Adjusted local analytics hook priority for better compatibility.
* Script Manager style updates for better compatibility.
* Translation updates.

= 1.8.2 - 12.08.2021 =
* New Lazy Loading option to Exclude Leading Images.
* New Lazy Loading option to add a Fade In effect.
* New option to Preload Critical Images (BETA).
* Expanded Disable XML-RPC function to also remove pingback link tag if it is present in the document.
* Added new Delay JavaScript checkbox to meta options in the post editor.
* Added additional integration with perfmatters_delay_js filter.
* Moved YouTube autoplay parameter placement on lazy loaded iframes for better compatibility with existing query strings.
* Optimizations to lazy loading inline CSS functions.
* Various optimizations and improvements to the output buffer.
* Migrated manual preload functionality to use the output buffer which will allow for easier integration with new features.
* Made some adjustments to MU plugin functions to more reliably detect post IDs when using specific permalink setups.
* Fixed an issue where some Current URL links in the Script Manager's Global View were not pointing to the right posts.
* Fixed an issue with a certain endpoint that was redirecting to the custom login URL.
* Fixed a PHP notice that was sometimes appearing when refreshing local fonts.
* Removed BETA tag from Delay All JS option.

= 1.8.1 - 10.26.2021 =
* Updated Local Google Font function to more effectively remove existing font preconnect and prefetch tags.
* Updated Local Google Font function for better compatibility with sites that still have remnants from a previous http to https migration.
* Fixed an issue in the Script Manager where the home page was being treated as a post if set to display the blog feed.

= 1.8.0 - 10.22.2021 =
* Fixed an issue with Delay All JS that was preventing certain async scripts from fully loading.

= 1.7.9 - 10.19.2021 =
* Added new options to the Script Manager to disable assets directly by post type, archive, user status, and device type.
* Added support for dynamic preloading by handle for enqueued scripts and styles.
* Added new perfmatters_lazyload filter.
* Added new perfmatters_cdn filter.
* Added new perfmatters_delay_js_timeout filter.
* Fix to Delay All JS script for better compatibility with certain page builder animations.
* Updated class initialization for better compatibility.
* Fixed an issue where the Script Manager was interpreting certain array keys as shortcodes if they were identical.
* Added an additional check to prevent the Script Manager from being able to load on top of a page builder.
* Fixed a PHP notice coming from the MU plugin.
* Made some changes to our plugin updater function that should help with auto-updates in a multisite environment.
* Translation updates.

= 1.7.8 - 09.16.2021 =
* Added new option to Add Missing Image Dimensions.
* Added the ability to delete individual Script Manager options from the Global View.
* Added new perfmatters_delay_js filter.
* Updated EDD plugin updater class to version 1.9.0.
* Translation updates.

= 1.7.7 - 08.25.2021 =
* Fixed a PHP warning related to JavaScript deferral for specific configurations.
* Fixed an issue with lazy loading exclusions not being loaded correctly in some cases.

= 1.7.6 - 08.24.2021 =
* Added new Delay Behavior dropdown with a new option to Delay All Scripts.
* Added new Lazy Loading Threshold option and adjusted the default value if not set to improve performance.
* Added confirmation message when manually running the database optimization tool.
* Updated disable emoji function to get rid of a PHP notice.
* Added additional check to MU Mode to only filter GET requests.
* Added new perfmatters_defer_js filter.
* Fixed an issue where Instant Page was attempting to run on the new widgets screen in WordPress 5.8.
* Fixed an issue with Local Google Fonts where certain invalid font URLs would still attempt to be downloaded and served.
* Removed BETA tag from fonts section.
* Delay JavaScript compatibility improvements.
* Added additional input validation functionality to plugin settings page.
* Translation updates.

= 1.7.5 - 07.13.2021 =
* Added new custom login URL options to change the Disabled Behavior and set a custom Message.
* Migrated CDN, Analytics, and Extras tab data to separate sections in the Options tab for better organization and easier access.
* CDN rewrite improvements to better handle sites with multiple domain URLs.
* Regex adjustments to Local Fonts function for better reliability.
* Added exclusion checks to individual <source> tags when using WebP images.
* Added function to disable capital_P_dangit filter.
* Fixed a lazy loading warning that was showing in Microsoft Edge.
* Removed loading attribute that was getting applied to <picture> tags in some cases when using WebP images.
* Plugin UI navigation performance improvements.
* Plugin UI style fixes.
* Added a conditional check to only show WooCommerce options when WooCommerce is installed and activated.
* Fixed an MU Mode issue where the Home URL did not trigger a match if a query string was present.
* Fixed an issue where the Customizer was getting certain optimizations applied.
* Fixed an issue where the Disable Embeds toggle was interfering with responsive video styles.
* Script Manager UI fixes.
* Updated uninstall function to remove Perfmatters cache folder.
* Added readme.txt file.

= 1.7.4 – 06.08.2021 =
* Re-enabled Local Google Fonts functionality.
* Refactoring of buffer-related code and various functions that were already using our main buffer filter.
* Translation updates.

= 1.7.3 – 06.03.2021 =
* Rolled back the latest changes related to the new universal buffer class and Local Google Fonts while we do some more in-depth testing. We’ll be working to release this feature next week using an alternative method.

= 1.7.2 – 06.02.2021 =
* Added new Fonts section inside of the main Options tab.
* Added new option to use Display Swap for Google fonts.
* Added new Local Google Fonts option which will attempt to download any Google Font files and serve them from your local server or CDN.
* Integrated new universal HTML buffer library to help going forward with plugin features that manipulate DOM elements.
* Migrated CDN Rewrite feature to the universal buffer class.
* Added new perfmatters_delayed_scripts filter to modify the Delay JavaScript input array before any scripts are delayed.
* Added new perfmatters_preload filter to modify the Preloads data array before anything is printed.
* Made some compatibility improvements to the inline lazy loading JavaScript.
* Added attributes to delayed scripts to exclude them from being picked up by Litespeed Cache.
* Added exclusion for SiteGround Optimizer to the main Script Manager JavaScript file.
* Added CodeMirror support to all code text area inputs in plugin settings.
* Removed license activation check and corresponding links from the plugins page to improve back-end performance. 

= 1.7.1 – 05.06.2021 =
* Added expiration date row to license tab in plugin settings.
* Added support for WooCommerce shop page when setting a preload location by post ID.
* Fixed an issue with device exceptions not working correctly in MU Mode.
* Fixed a query string encoding issue that was affecting some email templates when using a custom login URL.

= 1.7.0 – 04.26.2021 =
* Fixed an issue where Preload tags were still being printed on archive pages even if a location was set.
* Fixed a compatibility issue with older WordPress versions when using certain functions that check for a JSON request.
* Translation updates.

= 1.6.9 – 04.22.2021 =
* New additions to preload feature, allowing specification for device type and location.
* Script Manager improvements to allow for Regex disable to be used alongside Current URL disables for the same script.
* Added new Script Manager exception for device type.
* Add new Delay Timeout option when delaying JavaScript.
* Added new wheel event to user interaction script for delay function.
* Added new multisite network administration tool to apply default site settings to all subsites.
* Multiple improvements to WooCommerce disable scripts toggle for increased effectiveness.
* Added additional exclusions for JSON and REST requests to all asset optimization functions.
* Fixed an undefined index warning coming from local analytics function.
* Fixed an issue where YouTube preview thumbnails were getting a layout shift warning when using a theme with responsive embed support.
* Fixed a Script Manager bug that was not fully clearing exceptions when changing disable away from everywhere.
* Script Manager styling compatibility fixes.
* Translation updates.

= 1.6.8 – 03.10.2021 =
* Compatibility fixes for local analytics when using MonsterInsights.
* Local analytics improvements for multisite.
* Added alt tag to YouTube preview thumbnail images.
* Fixed a PHP undefined index notice coming from functions.php.
* Translation file updates.

= 1.6.7 – 03.02.2021 =
* Added new tool to Purge Perfmatters Meta Options.
* Added new Exclude Post IDs input for existing Disable Google Maps option.
* Added new gtag.js option to local analytics script type selection.
* Added new CDN URL input to local analytics options when using gtag.js.
* Added new option to Enable AMP Support to local analytics.
* Moved Use MonsterInsights option to gtag.js script type and updated script replacement hook. Important: If you were previously using analytics.js with MonsterInsights, please move to the gtag.js option.
* Added onload function to style preloads to prevent duplicate preloads from occurring.
* Added exception for WP Rocket script deferral to our lazy load script.
* Added exception for site health tool to disable heartbeat function.
* Fixed an issue where background images weren’t being lazy loaded if the style attribute was the first attribute declared on the element.
* Script Manager styling fixes.
* Fixed a PHP warning coming from settings.php.
* Translation file updates.

= 1.6.6 – 01.13.2021 =
* Added new Script Manager exception to select logged in or logged out users.
* Added new option in Script Manager settings to Display Dependencies.
* Added total plugin sizes in the Script Manager.
* Added new perfmatters_lazyload_threshold filter to adjust the distance at which lazy elements are loaded.
* Multiple Script Manager style and UI improvements.
* Fixed an issue where MU mode script was attempting to run on wp-login.php.
* Multiple page builder compatibility fixes.
* Made an adjustment to prevent YouTube preview thumbnails from getting picked up by Pinterest image hover tools.
* Removed deprecated plugin option to Remove Query Strings. Make sure to double-check your preloads as Google needs the exact URL when preloading.
* PHP 8 compatibility testing.
* Minor adjustments to lazy load inline scripts to fix invalid markup warnings.

= 1.6.5 – 12.04.2020 =
* Added new option to Delay JavaScript from loading until user interaction.
* Added new gtag.js v4 option to local analytics.
* Added new built-in option to Exclude from Lazy Loading which can be used in addition to the existing filter.
* Add new perfmatters_lazyload_youtube_thumbnail_resolution filter to adjust YouTube preview thumbnail quality.
* Optimized analytics updater function.
* Updated EDD plugin updater class which will now allow for WordPress auto-update support.
* Removed option to Defer Inline JavaScript which is now being replaced by the new Delay JavaScript option.
* Adjusted Script Manager hook priority for better compatibility.
* Compatability fix to the DOM Monitoring lazy load option.
* Added compatibility fix for jQuery fitVids to lazy loading function.
* Fixed an issue where lazy loading was attempting to run on AMP pages.

= 1.6.4 – 10.29.2020 =
* Fixed an issue that was causing the Reset Script Manager button to not work correctly.
* Fixed an issue where the Perfmatters meta box wouldn’t display if only using Lazy Loading.
* Adjusted Script Manager hook priority for better compatibility.
* Added additional checks to MU Mode plugin file to prevent it from interfering with certain REST API requests. (Fixes a bug when running the Yoast SEO data indexer.)
* Added additional checks to confirm user functions are available before verifying admin status.
* Updated translation files.

= 1.6.3 – 10.22.2020 =
* Added new Testing Mode option to the Script Manager settings.
* Rewrote script-manager.js entirely using vanilla JavaScript to get rid of the jQuery dependency on the back-end.
* Added additional MU Mode check to help prevent certain configurations from interfering with AJAX requests.
* Improved Script Manager form handling.
* Adjusted Script Manager disclaimer text and added a close button.
* Moved the Script Manager print function from the wp_footer hook to shutdown for better compatibility.
* Fixed an undefined index warning in the Lazy Load function.
* Added a Lazy Load exclusion for Gravity Forms iframes.
* Added a Rocket Loader exclusion to the Instant Page JS file.
* Added an exclusion to the CDN Rewrite for script-manager.js.
* Script Manager styling fixes for better compatibility.

= 1.6.2 – 09.24.2020 =
* Updated placeholder text in Preload UI.
* Fixed an issue where the Password Strength Meter script was getting disabled in the admin.
* Small tweak to JS Deferral buffer to make sure HTML is being filtered correctly.
* Translation updates.

= 1.6.1 – 09.23.2020 =
* New Local Analytics Script Type toggle with new Minimal Analytics options.
* New JavaScript Deferral options in Extras → Assets.
* New meta option to exclude JavaScript deferral, Instant Page, and Lazy Load per individual post/page.
* Updates to Cart Fragments and Password Strength Meter toggles to improve effectiveness.
* Multiple updates to Instant Page functionality for better compatibility.
* Multiple plugin admin UI updates and improvements.
* Script Manager style updates for better compatibility.
* MU Mode improvements for increased stability.
* Fixed an issue causing Preload and Preconnect settings to not save correctly in some cases.

= 1.6.0 – 08.17.2020 =
* Added a filter to disable WordPress’ native lazy loading when Perfmatters’ lazy loading is active.
* Adjusted Script Manager styles to more effectively overlay the entire page while still allowing admin bar functions to be fully available.
* Fixed an undefined index notice that was appearing on specific lazy loading and script manager functions.
* Updated translation files.

= 1.5.9 – 08.12.2020 =
* Added new Preloading section in the Extras tab, with new options for Instant Page and Preload.
* Added new perfmatters_lazyload_forced_attributes filter to allow for matched elements to be skipped when checking for exclusions.
* Added support for WooCommerce Shop page to show up as a Current URL option in the Script Manager.
* Added exclusions for REST and AJAX requests to MU Mode function.
* Fixed a bug that was causing the MU Mode function to still run even if the Script Manager was disabled.
* Fixed an issue where images were being prepped for lazy loading on feed URLs.
* Fixed an issue where lazy loading was breaking images in embeds from the same site.
* Compatibility fixes for lazy load script with Autoptimize and Litespeed Cache.

= 1.5.8 – 07.20.2020 =
* Added support for lazy loading background images, iframes, and videos.
* Added new lazy loading option to enable YouTube Preview Thumbnails.
* Removed native lazy loading in preparation for WordPress 5.5.
* Added multiple page builder exclusions to our lazy load functions.
* Added proper support for 404 templates in the Script Manager (non-MU).
* Fixed some minor styling issues in the Script Manager UI.
* Fixed an undefined index in the database optimizer class.
* Removed customer email row from the license tab.

= 1.5.7 – 06.22.2020 =
* Added new Database Optimization section in the Extras tab.
* Added new DOM Monitoring option to complement our existing lazy load settings.
* Added additional input styles in the Script Manager for better compatibility
* Made some changes to the Script Manager file include process for better compatibility.
* Fixed multiple undefined index notices.
* Updated translation files.

= 1.5.6 – 06.02.2020 =
* Plugin UI improvements, new tooltip styles.
* Licensing workflow improvements. Simpler UI, license no longer deactivated on plugin deactivation, license auto-activates on input.
* Moved Script Manager javascript back to a separate plugin file for better compatibility.
* Added Remove Query Strings exemption to the Script Manager javascript file.
* Code refactoring.

= 1.5.5 – 05.27.2020 =
* Added a new modified function to the MU plugin file which should be able to get the current post ID more effectively for certain types of URLs (custom post types, blog page, etc…).
* Made some improvements to the MU plugin file detection and update process.

= 1.5.4 – 05.26.2020 =
* Added additional tooltip warning text to the MU Mode toggle.
* Added mu_mode=off URL parameter to force the page to load with MU Mode settings disabled.
* Added an additional check to make sure MU Mode settings don’t run if the base Perfmatters plugin is not activated.

= 1.5.3 – 05.25.2020 =
* Added new MU Mode (BETA) feature in the Script Manager which can be used to disable plugins per page.
* Reworked main Script Manager update function to dynamically save settings via AJAX to prevent having to reload the page every time options are saved.
* Moved Script Manager javascript inline to better support further updates.
* Fixed an issue in the Script Manager where a Current URL disable would not function correctly for an individual script if the plugin’s scripts were disabled globally on a different Current URL.
* Changed hooks for Disable Google Maps and Disable Google Fonts toggles to prevent a conflict with the Block Editor (Gutenberg).
* Added an exclusion attribute to our LazyLoad script to prevent it from conflicting with WP Rocket’s JS deferral feature.
* Updated EDD Plugin Updater Class to version 1.7.1.
* Updated various translation files.

= 1.5.2 – 04.22.2020 =
* Added new options in Extras → Tools to Import and Export Plugin Settings.
* Updated Script Manager form input names to be more specific to prevent conflicts when saving Script Manager settings.
* Added compatibility fix for Beaver Builder to the Script Manager dequeue function.
* Updated French and German translation files.

= 1.5.1 – 04.02.2020 =
* Adjusted the Script Manager styles for better compatibility with other admin bar tools when the Script Manager UI is being displayed.
* Fixed an issue in the Script Manager that was causing individual script settings to not work correctly when the parent group had previously been disabled.
* Updated Russian (ru_RU) translation files.
* Updated plugin description.

= 1.5.0 – 03.20.2020 =
* Fixed a bug that was causing the Script Manager dequeue function to interfere with the query loop in certain cases.

= 1.4.9 – 03.18.2020 =
* Performance update to Script Manager form submission function which should help dramatically reduce the footprint when saving script configurations.
* Removed the Current URL option in the Script Manager when loaded on URLs without a valid post ID. (ex. dynamically generated archive templates)
* Added plugin settings page header with links to Contact and Support.
* Minor styling fixes in plugin settings UI.
* Updated Russian (ru_RU) translation files.

= 1.4.8 – 03.03.2020 =
* Added new ‘Body Code‘ box in the Extras tab to go along with our existing header + footer boxes to give some more control there.
* Added some limits to the Script Manager action links in WP Admin to ensure they are only showing up for public post types.
* Fixed a bug that was causing the admin stylesheet not to load on the network settings page when running on a multisite.
* Added Russian (ru_RU) translation files. (credit: Sergey Shljahov)

= 1.4.7 – 02.04.2020 =
* Added an exception for Gravity Forms to the Disable Heartbeat function.
* Added an exception for Contact Form 7 to the Disable REST API function.
* Added updated German (de_DE) translation files. (credit: Daniel Luttermann)

= 1.4.6 – 01.21.2020 =
* Added a specific and more generous threshold for lazy loading. This ensures butter-smooth loading of images while visitors scroll down the page. While raw performance is the objective, perceived performance (how quick a user thinks the site is) is also important.
* Added some additional dequeues to the Disable WooCommerce function to target inline CSS and JS.

= 1.4.5 – 12.08.2019 =
* Updated Disable Google Maps and Disable Google Fonts toggles to not run in WP Admin.
* Turned off native lazy loading by default and added new option to Use Native.
* Added perfmatters_lazyload_excluded_attributes filter which allows for an array of attribute strings to be given that if found will exclude the matched image/s from lazy loading.
* Made some compatibility improvements to the Script Manager function that gets the ID of the current post.
* Added perfmatters_get_current_ID filter which allows the user to extend or modify the functionality of the Script Manager’s current ID function.

= 1.4.4 – 10.20.2019 =
* Fixed undefined index PHP Notice coming from the Preconnect settings display function.
* Added additional compatibility with Elementor when using the Script Manager to disable certain Elementor scripts + styles.
* Added a ignore flag class to all Lazy Load functions. Simply add the ‘no-lazy’ class to any image element you want to be exempt from lazy loading.
* Added validation filter to Login URL input to prevent incompatible characters from being entered.

= 1.4.3 – 10.02.2019 =
* Fixed an issue with the Lazy Load function that was causing an error with some older PHP versions.

= 1.4.2 – 09.30.2019 =
* Added new option for Lazy Loading images (BETA).

= 1.4.1 – 08.18.2019 =
* New addition to the Preconnect option, you can now choose to whether or not to add the crossorigin property for each Preconnect URL.
* Optimization to the loading of Perfmatters admin scripts + styles.
* Added additional Script Manager styles for better compatibility.
* Added an additional function for the Custom Login URL to help rewrite certain wp-admin links in specific multisite setups.
* Reorganized plugin action links in the plugins table.

= 1.4.0 – 07.16.2019 =
* Fixed an issue where the Current URL Exceptions were not loading correctly after saving in the Script Manager.

= 1.3.9 – 07.14.2019 =
* Added new Extra options to Add Header Code and Add Footer Code.
* Added missing blank defaults for DNS Prefetch and Preconnect options.
* Added functionality to force the Admin Bar to display when the Script Manager is loaded.
* Script Manager styling adjustments.
* Added success message on save when the Script Manager options are updated.
* Added support for 404 pages when trying to disable or enable on the Current URL.

= 1.3.8 – 06.17.2019 =
* Added new option to Disable Comments.
* Updated a section of the Script Manager to better reflect the Current URL when determining if it is a match for the given regex pattern.

= 1.3.7 – 05.29.2019 =
* Added links to the Script Manager from the posts list page and post edit page which will take you to the front end and load the Script Manager for the corresponding post.
* Added warning notices for both WP_POST_REVISIONS and AUTOSAVE_INTERVAL if they are set in Perfmatters while also defined elsewhere.

= 1.3.6 – 04.21.2019 =
* Added new option to Disable Google Fonts.
* Removed option to Disable Completely from the Disable REST API dropdown due to core WordPress compatibility issues. Permission model is now the recommended method.
* Added additional object check to prevent PHP warning in certain cases when using the Separate Archives option in the Script Manager.
* Added some additional logic to filter duplicate scripts out of the Script Manager master array if they are present.
* CSS fixes in the Script Manager for better compatibility.
* Expanded the Script Manager current ID function for better reliability.

= 1.3.5 – 03.10.2019 =
* Added new Disable WordPress REST API option which will disable REST API requests and display an authentication error message if the requester doesn’t have permission.
* Added additional action removal to the Remove REST API Links function.
* Made some changes to the Script Manager save button. It is now fixed on the bottom of the screen for easier access without having to scroll.
* Additional Script Manager style adjustments.

= 1.3.4 – 02.13.2019 =
* Minor update to Remove Comment URLs function priority for better compatibility with theme templates.

= 1.3.3 – 02.13.2019 =
* Added new option to Remove Comment URLs.
* Added French (fr_FR) language translation. Props to @adbchris. 👏
* Fixed a PHP warning that would occur when saving Script Manager settings in some instances when Display Archives was also enabled.

= 1.3.2 – 01.13.2019 =
* Added new option to Add Blank Favicon in the Extras tab.
* Fixed an issue in the Script Manager Global View where options set for the home page would show up as a 0 with a broken link.
* Added some additional styles to the main Script Manager view for better compatibility.

= 1.3.1 – 12.07.2018 =
* Fixed a bug that would sometimes cause an enabled message to display on the front end when using the Regex option in the Script Manager.

= 1.3.0 – 11.23.2018 =
* Added new Regex option the Script Manager for both disables and exceptions.
* Added new Reset option in the Script Manager settings which allows for a complete wipe + reset of all configured Script Manager options.
* Added additional Script Manager styles to improve compatibility.
* Added new status message in Script Manager global view when no options have been set.

= 1.2.9 – 10.28.2018 =
* Updated uninstallation function to account for new Script Manager settings.
* Updated Google Analytics Disable Display Features function to work correctly with Google’s new format.
* Added support to Use MonsterInsights along with Perfmatters local analytics (analytics.js) hosting functionality. 🎉
* Added new option in Script Manager settings to Display Archives which will allow you to selectively enable scripts on generated archive pages.

= 1.2.8 – 09.23.2018 =
* Added mobile + responsive styles to the Script Manager navigation.
* Added additional styles to the Script Manager for compatibility.
* Script Manager javascript changes + improvements, specifically for compatibility with sites script minification plugins.
* Fixed a bug where the Script Manager disclaimer would not turn back on after being switched off.

= 1.2.7 – 09.09.2018 =
* Small patch to check for a required WP function and include core file if necessary for some setups.

= 1.2.6 – 09.09.2018 =
* All new Script Manager! View updated documentation at https://perfmatters.io/docs/disable-scripts-per-post-page/.
* Fix to remove Emoji DNS Prefetch when Emojis are disabled

= Version 1.2.5 – 07.31.2018 =
* Fixed an issue with the Change Login URL function that was causing an error when using WP-CLI.
* Added some additional compatibility styles to the Script Manager.

= 1.2.4 – 07.15.2018 =
* Fixed a bug in the Script Manager that caused Current URL Enable checkboxes to not save properly in certain situations.
* Updated EDD license functions to process proper SSL verification when calling the WordPress HTTP API.
* Updated perfmatters_default_options array with new options from recent updates.
* Removed BETA tag from Local Analytics option.
* Added more details to the Script Manager Global Settings to see which post IDs and post types have settings assigned to them.
* Additional styles added to the Script Manager for better compatibility.
* Updated .pot and translation files.

= 1.2.3 – 07.01.2018 =
* Bugfix – Rolled back some of the heartbeat changes from the previous update to do some additional testing. Should solve some plugin conflicts that popped up.

= 1.2.2 – 07.01.2018 =
* Added additional WooCommerce checks for WC specific pages before running disable functions.
* Changes to the Disable Heartbeat function to avoid causing a script dependency error.
* Added new Disable Password Strength Meter option.
* Fixed an issue that was causing Script Manger dropdown colors to not display correctly when jQuery was disabled.
* Modified admin notice to print our using ‘admin_notices’ hook. (credit: Christian Follmann)
* Made some adjustments to Script Manager copy to remove unnecessary HTML from the translations. (credit: Christian Follmann)
* Props to Hasan Basri (www.hasanbasri93.com) for Indonesian (id_ID) translation. 👏
* Updated translations based on the new .pot file.
* Various other minor tweaks + improvements.

= 1.2.1 – 05.20.2018 =
* Updated Local Analytics function to improve compatibility with different server setups.

= Version 1.2.0 – 05.17.2018 =
* New option to Enable Local Analytics, along with a new dedicated Google Analytics tab with various related options.
* Added some additional logic to redirect RSS Feed URLs when Disable RSS Feeds is toggled on.
* Fixed an issue that was causing certain email links not to work when using a Custom Login URL.
* Fixed a bug that was causing the password reset link not to function properly when using a Custom Login URL in a multisite environment.
* Made some adjustments to the Disable Self Pingbacks function to fix an issue with case sensitivity.
* Updated text domain for translations in the EDD Updater class.
* Fixed a bug where the Clean Uninstall option would still show up on individual sites in a multisite environment.
* Props to PDPK di Mauro Panzarola (https://pdpkapp.com) for Italian (it_IT) translation. 👏

= 1.1.9 – 04.16.2018 =
* Perfmatters is now translation ready! If you are interested in helping out with a translation, please contact us.
* Props to Christian Foellmann (cfoellmann@GitHub) for German (de_DE) translation. 👏
* Fixed a PHP undefined index warning in the Script Manager.
* Fixed a bug that was causing issues with the Change Login URL slug when using certain permalink settings.

= 1.1.8 – 03.27.2018 =
* Fixed a compatibility issue with Script Manager dequeue priority that could cause it to not function properly.
* Minor update to the uninstall function.

= 1.1.7 – 03.19.2018 =
* Fixed a bug that was causing the remove query strings option to conflict with files that have necessary query string parameters (Google Fonts).

= 1.1.6 – 03.18.2018 =
* Added new Clean Uninstall option in the extras tab.
* Added new Preconnect option in the extras tab.

= 1.1.5 – 02.26.2018 =
* Fixed multiple PHP warnings related to settings + option initialization.

= 1.1.4 – 02.20.2018 =
* Added multisite support with the ability to manage default network settings and network access control.
* Made some adjustments to plugin naming conventions throughout WordPress admin screens, menus, etc…
* Removed BETA tag on Change Login URL option.

= 1.1.3 – 01.11.2018 =
* Added new Change Login URL (BETA) feature to change your WordPress login URL and block the default wp-admin and wp-login endpoints from being directly accessed.
* Added new Disable Dashicons feature to disable Dashicons from the front-end when not logged in.

= 1.1.2 – 12.19.2017 =
* Added character masking to the license key input field.

= 1.1.1 – 12.07.2017 =
* Added new CDN URL Rewrite feature in a new settings tab with various settings to customize your configuration.
* Added new Global Settings section in the Script Manager with a visual representation of the Script Manager options set across the entire site.
* Made some updates to the Script Manager layout in preparation for future additional features.

= 1.1.0 – 10.23.2017 =
* Added new Disable Google Maps toggle.
* Added some backend logic to the Script Manager to hide scripts that have already been disabled sitewide via the main plugin settings.
* Update to the EDD license activation function variables to help prevent activation conflicts with other plugins.

= 1.0.9 – 10.11.2017 =
* Removed the toggle to disable WooCommerce reviews, as there is already a WooCommerce setting that provides that functionality.

= 1.0.8 –  10.11.2017 =
* Added new WooCommerce section to the options tab with multiple toggles to disable or limit certain WooCommerce scripts and functionality including the following:
* Disable WooCommerce scripts and styles
* Disable WooCommerce widgets
* Disable WooCommerce status meta box
* Disable WooCommerce cart fragments (AJAX) 
* Added some new styles to the plugin admin page to allow for clearer organization of different sections.
* Fixed an undefined index notice in the Script Manager.
* Added some additional styles to the checkboxes in the Script Manager to fix a theme compatibility issue.

= 1.0.7 – 09.03.2017 =
* Added functionality to remove the shortlink HTTP header when Remove Shortlink is toggled on.
* Added functionality to remove the xmlrpc.php link as well as the X-Pingback HTTP header when Disable XML-RPC is toggled on.

= 1.0.6 – 08.29.2017 =
* Removed BETA label from Script Manager.
* Added new DNS Prefetch option in the Extras tab.

= 1.0.5 – 08.22.2017 =
* Added new toggle to Remove REST API Links.
* Renamed ‘Remove Feed Links’ toggle for more clarification.
* UI improvements, hovering tooltips, more links to the web documentation, etc…
* Added version numbers to admin scripts to avoid caching on plugin update.
* Refactored a good portion of the settings initialization code.
* Removed “Beta” status for script manager. It has been fully tested now and is ready to use in production.

= 1.0.4 – 07.20.2017 =
* Fixed a few PHP warnings dealing with the Script Manager option array management.
* Fixed a UI bug in the Script Manager causing certain post type check boxes to not be selectable.
* Upgrade licensing feature added. You can now upgrade licenses from within your account and you are automatically prorated the new amount.

= 1.0.3 – 07.16.2017 =
* Introduced the new Script Manager feature to disable scripts on a per page/post basis.

= 1.0.2 – 06.05.2017 =
* Added Extras tab with a new option for Accessibility Mode. Enabling this will turn off the custom styles we use for our settings toggles and revert to standard HTML checkboxes.
* Additional accessibility improvements.
* A few style fixes.
* WordPress 4.8 support.

= 1.0.1 – 06.04.2017 =
* Accessibility improvements to the plugin settings page.

= 1.0.0 – 06.01.2017 =
* Plugin launched.