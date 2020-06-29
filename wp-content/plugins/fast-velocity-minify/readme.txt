=== Fast Velocity Minify ===
Contributors: Alignak
Tags: PHP Minify, Lighthouse, GTmetrix, Pingdom, Pagespeed, CSS Merging, JS Merging, CSS Minification, JS Minification, Speed Optimization, HTML Minification, Performance, Optimization, FVM
Requires at least: 4.7
Requires PHP: 5.6
Stable tag: 2.8.9
Tested up to: 5.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Improve your speed score on GTmetrix, Pingdom Tools and Google PageSpeed Insights by merging and minifying CSS, JavaScript and HTML, setting up HTTP preload and preconnect headers, loading CSS async and a few more options. 
 

== Description ==
WP speed optimization plugin for developers and advanced users. This plugin reduces HTTP requests by merging CSS & Javascript files into groups of files, while attempting to use the least amount of files as possible. It minifies CSS and JS files with PHP Minify (no extra requirements).

Minification is done on the frontend during the first uncached request. Once the first request is processed, any other pages that require the same set of CSS and JavaScript files, will be served that same (static) cache file.

This plugin includes options for developers and advanced users, however the default settings should work just fine for most sites.
Kindly read our faqs about possible issues with you theme or specific plugins.

= Aditional Optimization =

I can offer you aditional `custom made` optimization on top of this plugin. If you would like to hire me, please visit my profile links for further information.


= Features =

*	Merge JS and CSS files into groups to reduce the number of HTTP requests
*	Google Fonts merging, inlining and optimization
*	Handles scripts loaded both in the header & footer separately
*	Keeps the order of the scripts even if you exclude some files from minification
*	Supports localized scripts (https://codex.wordpress.org/Function_Reference/wp_localize_script)
*	Minifies CSS and JS with PHP Minify only, no third party software or libraries needed.
*	Option to defer JavaScript and CSS files, either globally or pagespeed insights only.
*	Creates static cache files in the uploads directory.
*	Preserves your original files, by duplicating and copying those files to the uploads directory 
*	View the status and detailed logs on the WordPress admin page.
*	Option to Minify HTML, remove extra info from the header and other optimizations.
*	Ability to turn off minification for JS, CSS or HTML (purge the cache to see it)
*	Ability to turn off CSS or JS merging completely (so you can debug which section causes conflicts and exclude the offending files)
*	Ability to manually ignore JavaScript or CSS files that conflict when merged together (please report if you find some)
*	Support for conditional scripts and styles, as well as inlined code that depends on the handles
*	Support for multisite installations (each site has its own settings)
*	Support for gzip_static on Nginx
*	Support for preconnect and preload headers
*	CDN option, to rewrite all static assets inside the JS or CSS files
*	WP CLI support to check stats and purge the cache
*	Auto purging of cache files for W3 Total Cache, WP Supercache, WP Rocket, Cachify, Comet Cache, Zen Cache, LiteSpeed Cache, Nginx Cache (by Till Krüss ), SG Optimizer, HyperCache, Cache Enabler, Breeze (Cloudways), Godaddy Managed WordPress Hosting and WP Engine (read the FAQs)
*	and some more...


= WP-CLI Commands =
*	Purge all caches: `wp fvm purge`
*	Purge all caches on a network site: `wp --url=blog.example.com fvm purge`
*	Purge all caches on the entire network (linux): `wp site list --field=url | xargs -n1 -I % wp --url=% fvm purge`
*	Get cache size: `wp fvm stats`
*	Get cache size on a network site: `wp --url=blog.example.com fvm stats`
*	Get cache size on each site (linux): `wp site list --field=url | xargs -n1 -I % wp --url=% fvm stats`


= Notes =
*	The JavaScript minification is by [PHP Minify](https://github.com/matthiasmullie/minify)
*	Compatible with Nginx, HHVM and PHP 7
*	Minimum requirements are PHP 5.5 and WP 4.4, from version 1.4.0 onwards


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory or upload the zip within WordPress
2. Activate the plugin through the `Plugins` menu in WordPress
3. Configure the options under: `Settings > Fast Velocity Minify` and that's it.


== Screenshots ==

1. The Status and Logs page.
2. The Settings page.
3. The Pro settings.
4. The Developers settings.

== Frequently Asked Questions ==

= Can I update plugins and themes after installing FVM? =
FVM doesn't touch your original files. It copies those files to the cache directory, minifies that copy and merges them together under a different name. If you install new plugins, change themes or do plugin updates, FVM will purge its cache as well as some of the most popular cache plugins.

= After installing, why did my site feels slow to load? =
Please see the question below.

= Why are there lots of JS and CSS files listed on the status page and why is the cache directory taking so much space? =
Some themes combine and enqueue their CSS using a PHP script with a query string that changes on every pageload... (this is to bust cache, but it's bad practice since it prevents caching at all). When FVM sees a different url being enqueued, it will consider that as a new file and try to create a new set of files on every pageview as well. You must then exclude that dynamic url via the Ignore List on the settings for your cache to be efficient and stop growing. Also note, if your pages enqueue different styles or  javascript in different pages (fairly common), that is "one set" of files to be merged. Pay attention to the logs header and look for the page url where those files have ben generated. If you have multiple files generated for the same url, you have some css/js that keeps changing on every pageview (and thus needs exclusion).


= How can I exclude certain assets? =
Each line on the ignore list will try to match a substring against all CSS or JS files, for example `//yoursite.com/wp-content/plugins/some-plugin/js/` will ignore all files inside that directory. You can also shorten the URL like `/some-plugin/js/` and then it will match any css or js URL that has `/some-plugin/js/` on the path. Obviously, doing `/js/` would match any files inside any "/js/" directory and in any location, so to avoid unexpected situations please always use the longest, most specific path you can use. There is no need to use asterisks or regex code (it won't work).


= Why is the ignore list not working? =
The ignore list "is" working, just try to use partial paths (see previous faq) and use relative urls only without any query vars. 


= Is it compatible with other caching plugins? =
You must disable any features on your theme or cache plugins which perform minification of css, html and js. Double minification not only slows the whole process, but also has the high potential of causing conflicts in javascript. The plugin will try to automatically purge several popular cache plugins, however if you have a cache on the server side (some hosting services have this) you may need to purge it manually, after you purge FVM to see the results you expect. The automatic purge is active for the following plugins and hosting: W3 Total Cache, WP Supercache, WP Rocket, Cachify, Comet Cache, Zen Cache, LiteSpeed Cache, Cache Enabler, SG Optimizer, Breeze (Cloudways), Godaddy Managed WordPress Hosting and WP Engine


= Do you recommend a specific Cache Plugin? =
Currently we recommend the "Cache Enabler" plugin, for it's simplicity, compatibility with most systems and performance. Alternatively, W3 Total Cache is a great choice as well.


= Is it resource intensive, or will it use too much CPU on my shared hosting plan? =
Unless you are not excluding dynamic CSS files that change the url in every pageload, its not heavy at all. On the first run, each single file is minified into an intermediate cache. When a new group of CSS/JS files is found on a new page, it reuses those files and merges them into a new static cache file. All pages that request the same group of CSS or JS files will also make use of that file, thus regeneration only happens once. In addition, gz and br files will be pre-compressed (if supported).


= How do I use the pre-compressed files with gzip_static or brotli_static on Nginx? =
When we merge and minify the css and js files, we also create a `.gz` file to be used with `gzip_static` on Nginx. You need to enable this feature on your Nginx configuration file if you want to make use of it. Likewise, if you have Nginx compiled with brotli and have enabled the php-ext-brotli extension for PHP, you can enable the brotli_static option and FVM will also generate .br files for you :)


= Is it compatible with multisites? =
Yes, it generates a new cache file for every different set of JS and CSS requirements it finds, but you must enable and configure FVM settings for each site in your network separatly (no global settings for all sites).


= Is it compatible with AdSense and other ad networks? =
If you are just inserting ads on your pages, yes. If you are using a custom script to inject those ads, please double check if it works. 


= After installing, why are some images, sections, sliders, galleries, menus (etc) not working? =

a) You cannot do double minification, so make sure you have disabled any features on your theme or other plugins that perform minification of css, html and js files.

b) If you enabled the option to defer JS or CSS, please note that some themes and plugins need jQuery and other libraries to be render blocking. If you enable the option to defer, any javascript code on the page will trigger an "undefined" error on the google chrome console log after page load.

c) The plugin relies on PHP Minify to minify JavaScript and css files, however it is not a perfect library and there are plugins that are already minified and do not output a "min.js" or "min.css" filename (and end up being minified again). Try to disable minification on JS and CSS files and purge the cache, then either dequeue it and enqueue an alternative file or add it to the ignore list.

d) Sometimes a plugin conflicts with another when merged (look at google chrome console log for hints). Try to disable CSS processing first and see if it works. Disable JS processing second and see if it works. Try to disable HTML minification last and see if it works. If one of those work, you know there is a conflict when merging/minifying.

e) If you have a conflict, try to add each CSS and each JS file to the ignore list one by one, until you find the one that causes the conflict. If you have no idea of which files to add, check the log file on the "status page" for a list of files being merged into each generated file.

f) If you coded some inline JS code that depends on a JS file being loaded before it's execution (render blocking), try to save that code into an external file and enqueue it as a dependency. It will be merged together and run after the other file, thus no longer being "undefined".


= Why are some of the CSS and JS files not being merged? =
The plugin only processes JS and CSS files enqueued using the official WordPress api method - https://developer.wordpress.org/themes/basics/including-css-javascript/ -as well as files from the same domain (unless specified on the settings). 


= Can I merge files from other domains? =
Yes and no. You can for example, merge js files such as jQuery if they are loading from a CDN and it will work, because it doesn't matter where those files are being served from. However, stuff like Facebook and other social media widgets, as well as tracking codes, widgets and so on, cannot usually be merged and cached locally as they may load something different on every pageload, or anytime they change something. Ads and widgets make your site slow, so make sure you only use the minimum necessary plugins and widgets.


= How to undo all changes done by the plugin? =
The plugin itself does not do any "changes" to your site and all original files are untouched. It intercepts the enqueued CSS and JS files just before printing your HTML, copies them and enqueues the newly optimized cached version of those files to the frontend. As with any plugin... simply disable or uninstall the plugin, purge all caches you may have in use (plugins, server, cloudflare, etc.) and the site will go back to what it was before installing it. The plugin does not delete anything from the database or modify any of your files. 


= I have disabled or deleted the plugin but my design is still broken! =
Some "cheap" (or sometimes expensive) "optimized" hosting providers, implement a (misconfigured) aggressive cache on their servers that caches PHP code execution and PHP files. I've seen people completely deleting all WordPress files from their host via SFTP/FTP and the website kept working fine for hours. Furthermore, very often they rate limit your cache purge requests... so if you delete FVM and are still seeing references to FVM files on the "view-source:https://example.com" please be patient and contact your web hosting to purge all caches. Providers known to have this issue are some plans on hostgator and iPage (please report others if you find them).


= Why is my Visual Composer or Page Editor not working? =
Some plugins and themes need to edit the layout and styles on the frontend. If you have trouble with page editors, please enable the "Fix Page Editors" option on FVM and purge your caches. Note: You will only see the FVM minification working when you're logged out or using another browser after this setting. 


= What are the recommended cloudflare settings for this plugin? =
On the "Speed" tab, deselect the Auto Minify for JavaScript, CSS and HTML as well as the Rocket Loader option as there is no benefit of using them with our plugin (we already minify things). Those options can also break the design due to double minification or the fact that the Rocket Loader is still experimental (you can read about that on the "Help" link under each selected option on cloudflare).


= How can I load CSS async? =
You are probably a developer if you are trying this. The answer is: make sure FVM is only generating 1 CSS file, because "async" means multiple files will load out of order (however CSS needs order most of the times). If FVM is generating more than 1 CSS file per mediatype, try to manually dequeue some of the CSS files that are breaking the series on FVM (such as external enqueued files), or add their domain to the settings to be merged together. Please note... this is an advanced option for skilled developers. Do not try to fiddle with these settings if you are not one, as it will almost certainly break your site layout and functionality.

= Why is FVM using defer instead of async javascript? =
The answer is simple. For compatibility reasons and to avoid some undefined javascript errors, we need to preserve the order of scripts. Async means that any js files will load in parallel without waiting for each other or without following a specific order. If FVM generates multiple JS files for your site, using Async could cause footer scripts to load before the header scripts in an inconsistent manner. By using defer, we make sure the scripts load in order, as defined by each plugin and theme developer.
Your ads or scripts wich are already specifically async will continue to be so, unless you specifically mark them to be merged as well.

= I have a complaint or I need support right now. =
Before getting angry because you have no answer within a few hours (even with paid plugins, sometimes it takes weeks...), please be informed about how wordpress.org and the plugins directory work. The plugins directory is an open source, free service where developers and programmers contribute (on their free time) with plugins that can be downloaded and installed by anyone "at their own risk" and are all released under the GPL license. While all plugins have to be approved and reviewed by the WordPress team before being published (for dangerous code, spam, etc.) this does not change the license or add any warranty. All plugins are provided as they are, free of charge and should be used at your own risk (so you should make backups before installing any plugin or performing updates) and it is your sole responsibility if you break your site after installing a plugin from the plugins directory. For a full version of the license, please read: https://wordpress.org/about/gpl/

= Why haven't you replied to my topic on the support forum yet? =
Support is provided by plugin authors on their free time and without warranty of a reply, so you can experience different levels of support level from plugin to plugin. As the author of this plugin I strive to provide support on a daily basis and I can take a look and help you with some issues related with my plugin, but please note that this is done out of my goodwill and in no way I have any legal or moral obligation for doing this. Sometimes I am extremely busy and may take a few days to reply, but I will always reply. 

= But I really need fast support right now, is there any other way? =
I am also available for hiring if you need custom-made speed optimizations. After you have installed the plugin, check the "Help" tab for contact information, or check my profile links here on WordPress. 


= Where can I report bugs? =
You can get support on the official WordPress plugin page at https://wordpress.org/support/plugin/fast-velocity-minify 
Alternatively, you can reach me via info (at) fastvelocity.com for security or other vulnerabilities.

= How can I donate to the plugin author? =
If you would like to donate any amount to the plugin author (thank you in advance), you can do it via PayPal at https://goo.gl/vpLrSV


== Upgrade Notice ==

= 2.5.9 =
Minor bug fixes

= 3.0 =
Please backup your site before updating. Version 3.0 will have a major code rewrite to improve JS and CSS merging. 


== Changelog ==

= 2.8.9 [2020.06.23] =
* new filter for wp hide compatibility

= 2.8.8 [2020.05.01] =
* bug fixes for woocommerce, which could result in 403 errors when adding to cart under certain cases

= 2.8.7 [2020.04.30] =
* fixed the sourceMappingURL removal regex introduced on 2.8.3 for js files and css files

= 2.8.6 [2020.04.30] =
* fixed an error notice on php

= 2.8.5 [2020.04.30] =
* bug fixes and some more minification default exclusions

= 2.8.4 [2020.04.24] =
* added frontend-builder-global-functions.js to the list of minification exclusions, but allowing merging (Divi Compatibility)

= 2.8.3 [2020.04.17] =
* Removed some options out of the autoload wp_option to avoid getting cached on the alloptions when using OPCache 
* Removed the CDN purge option for WP Engine (not needed since FVM automatically does cache busting)
* Added support for Kinsta, Pagely, Pressidum, Savvii and Pantheon
* Better sourcemaps regex removal from minified css and js files

= 2.8.2 [2020.04.13] =
* Skip changing clip-path: url(#some-svg); to absolute urls during css minification
* Added a better cronjob duplicate cleanup task, when uninstalling the plugin

= 2.8.1 [2020.03.15] =
* added filter for the fvm_get_url function

= 2.8.0 [2020.03.10] =
* improved compatibility with Thrive Architect editor
* improved compatibility with Divi theme

= 2.7.9 [2020.02.18] =
* changed cache file names hash to longer names to avoid colisions on elementor plugin

= 2.7.8 [2020.02.06] =
* updated PHP Minify with full support for PHP 7.4
* added try, catch wrappers for merged javacript files with console log errors (instead of letting the browser stop execution on error)
* improved compatibility with windows servers
* improved compatibility for font paths with some themes

= 2.7.7 [2019.10.15] =
* added a capability check on the status page ajax request, which could show the cache file path when debug mode is enabled to subscribers

= 2.7.6 [2019.10.10] =
* bug fix release

= 2.7.5 [2019.10.09] =
* added support to "after" scripts added via wp_add_inline_script 

= 2.7.4 [2019.08.18] =
* change to open JS/CSS files suspected of having PHP code via HTTP request, instead of reading the file directly from disk

= 2.7.3 [2019.07.29] =
* Beaver Builder compatibility fix

= 2.7.2 [2019.07.29] =
* fixed a PHP notice when WP_DEBUG mode is enabled on wordpress
* small improvements on google fonts merging

= 2.7.1 [2019.07.27] =
* fixed an AMP validation javascript error

= 2.7.0 [2019.07.23] =
* some score fixes when deferring to pagespeed is enabled

= 2.6.9 [2019.07.15] =
* custom cache path permissions fix (thanks to @fariazz)

= 2.6.8 [2019.07.06] =
* header preload fixes (thanks to @vandreev)

= 2.6.7 [2019.07.04] =
* added cache purging support for the swift cache plugin
* changed cache directory to the uploads directory for compatibility reasons
* better cache purging checks

= 2.6.6 [2019.06.20] =
* cache purging bug fixes
* php notice fixes

= 2.6.5 [2019.05.04] =
* fixed cache purging on Hyper Cache plugin
* removed support for WPFC (plugin author implemented a notice stating that FVM is incompatible with WPFC)
* improved the filtering engine for pagespeed insights on desktop

= 2.6.4 [2019.03.31] =
* fixed subdirectories permissions

= 2.6.3 [2019.03.30] =
* fixed another minor PHP notice

= 2.6.2 [2019.03.27] =
* fixed a PHP notice on urls with query strings that include arrays on keys or values

= 2.6.1 [2019.03.26] =
* fixed compatibility with the latest elementor plugin
* fixed adding duplicate cron jobs + existing duplicate cronjobs cleanup
* fixed duplicate "cache/cache" directory path
* changed the minimum PHP requirements to PHP 5.5

= 2.6.0 [2019.03.02] =
* fixed cache purging with the hypercache plugin
* fixed a bug with inline scripts and styles not showing up if there is no url for the enqueued handle
* changed the cache directory from the wp-content/uploads to wp-content/cache
* improved compatibility with page cache plugins and servers (purging FVM without purging the page cache should be fine now)
* added a daily cronjob, to delete public invalid cache files that are older than 3 months (your page cache should expire before this)

= 2.0.0 [2017.05.11] =
* version 2.x branch release

= 1.0 [2016.06.19] =
* Initial Release
