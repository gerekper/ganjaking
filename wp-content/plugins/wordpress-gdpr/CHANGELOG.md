# Changelog
======
1.9.31
======
- NEW:	Save privacy Settings
		https://imgur.com/a/wgCNcH1
- FIX:	Privacy settings backdrop not clickable
- FIX:	Accesibility errors

======
1.9.30
======
- NEW:	Export guest user consent logs
		https://imgur.com/a/dTpUEYe
- NEW:	Added class "wordpress-gdpr-popup-privacy-settings-service-category-active"
		when clicked on service category in popup
- FIX:	PHP notice

======
1.9.29
======
- FIX:	Caching plugin caused nonce issue

======
1.9.28
======
- NEW:	Consent log for logged out users by masekd IP
		https://imgur.com/a/Erun5ZA
		You may need to disable & activate our plugin again
		to create the table

======
1.9.27
======
- FIX:	Only EU Visitors Popup not working

======
1.9.26
======
- NEW:	Added an editor field for the terms & conditions on each form text
- NEW: 	Added an option to disable the terms & conditions check
- FIX:	XSS security issue

======
1.9.25
======
- NEW:	Support for TTDSG law
- NEW:	Button background color is now black for
		all button types (accept / decline)

======
1.9.24
======
- NEW:	Disable Privacy Settings Backdrop Click
		https://imgur.com/a/oJG76sr
- NEW:	Popup close icon empty by default

======
1.9.23
======
- FIX:	WPML Keys updated

======
1.9.22
======
- FIX:	Decline button had privacy center trigger class

======
1.9.21
======
- NEW:	Added bulgarian language
- FIX:	Popup showing except setting change

======
1.9.20
======
- FIX:	Cookie popup not showed when next page opened
- FIX:	Updated translation files

======
1.9.19
======
- NEW:	own text fields for popup accept / decline all services
		https://imgur.com/a/ul8fuhc
- FIX:	Moved all styles into footer

======
1.9.18
======
- FIX:	Overlay cookie height
- FIX:	Local storage loading

======
1.9.17
======
- NEW:	Backdrop for full width cookie popup
		https://imgur.com/a/rVwgPw5

======
1.9.16
======
- NEW:	Performance improvements by using users local storage (this will get rid of all AJAX calls except the first one)
- FIX:	Privacy settings not saved when enabled by default and only one service applied

======
1.9.15
======
- NEW:	Added WooCommerce Kadence Email customizer support

======
1.9.14
======
- FIX:	Caching issue

======
1.9.13
======
- NEW:	Manage my Preferences button in popup
		https://imgur.com/a/VNUC1vO
		This will be now the default behaviour

======
1.9.12
======
- FIX:	License & update manager completly in weLaunch Framework

======
1.9.11
======
- NEW:	Dropped Redux Framework support and added our own framework 
		Read more here: https://www.welaunch.io/en/2021/01/switching-from-redux-to-our-own-framework
		This ensure auto updates & removes all gutenberg stuff
		You can delete Redux (if not used somewhere else) afterwards
		https://www.welaunch.io/updates/welaunch-framework.zip
		https://imgur.com/a/BIBz6kz
- FIX:	PHP 5.6 support

======
1.9.10
======
- NEW:	Performance (AJAX only performed once now / HTML not generated every time)
- NEW:	Trigger is now disabled by default
- FIX:	Alt tag missing in cookie settings
- FIX:	New cookie popup style mobile bug

======
1.9.9
======
- NEW:	New Cookie popup style: Full width buttons right
		https://imgur.com/a/fZU439r
- NEW:	When close icon empty in popup settings it will not display
- NEW:	Added white space normal CSS for Uncode theme support

======
1.9.8
======
- FIX:	First time allow all cookies did not allow declining single services afterwards

======
1.9.7
======
- FIX:	Body script not loaded when service was set to enable by default

======
1.9.6
======
- NEW:	Usability improvements (switched buttons and colors)

======
1.9.5
======
- FIX:	Pages not selectable in admin panel

======
1.9.4
======
- NEW:	Performance increase in admin panel through AJAX loading
		!! MAKE SURE YOU ARE ON LATEST VERSION OF REDUX FRAMEWORK !!

======
1.9.3
======
- NEW:	WPML suppress filters
- NEW:	Improved admin panel
- FIX:	Updated Bot Regular expression to avoid cumultative layout shift issue
		https://imgur.com/a/UqarMvG

======
1.9.2
======
- NEW:	Renamed GDPR Users to Consent Log
- NEW:	Moved consent log to GDPR menu as subitem

======
1.9.1
======
- FIX:	Transient clear when plugin activated
- FIX:	Get posts instead of get pages function
- FIX:	Popup issue when oriental smarthpoen & close icon issue CSS fix
- FIX:	Transient cleared when new page created / pages installed

======
1.9.0
======
- NEW:	Improved Plugin page speed significantly
- FIX:	Compatible with latest WP and Woo

======
1.8.11
======
- FIX:	Mobile privacy settings improvements

======
1.8.10
======
- FIX:	Acceptance text fix

======
1.8.9
======
- FIX:	Added terms acceptance string to translations
- FIX:	Updated POT file

======
1.8.8
======
- FIX:	Added alt tag to GDPR popup logo image
- FIX:	setcookie() expects parameter 2 to be string, array given 

======
1.8.7
======
- FIX:	GEO Ip service issue

======
1.8.6
======
- FIX:	Font Awesome 5.8.1 Privacy Policy Icon

======
1.8.5
======
- FIX:	Upgraded font awesome to 5.8.1
- FIX:	Replaced font awesome "external-link" with "external-link-alt"
- FIX:	Removed the option to disable a service by default, because all
		services are disabled by default
- FIX:	Enabled by default has overwritten

======
1.8.4
======
- NEW:	Refreshing page does not allow all cookies when using
		the Allow all Cookies for users continue visiting your site function

======
1.8.3
======
- FIX:	More checks for JS get_current_page_id function

======
1.8.2
======
- FIX:	Removed depr console log
- FIX:	Added a exist check to get_current_page_id function in JS

======
1.8.1
======
- NEW:	For each service you can enable or disable it by default

======
1.8.0
======
- NEW:	Allow all cookies when user continues website
		General > Allow all Cookies for users continue visiting your site

======
1.7.2
======
- FIX:	Duplicate tags when first time allowed and user clicked on accept all

======
1.7.1
======
- FIX:	DMCA page missing from WPML keys

======
1.7.0
======
- NEW:	Font Awesome 5 Support (see general options)
- NEW:	Improved the Live Checkbox Service Loading process
- FIX:	Switching a checkbox in privacy settings
		still opens up the cookie popup on next page

======
1.6.8
======
- NEW:	Privacy Policy acceptance saved to auto enable checkbox on
		Buddypress, Review and Comment forms
- NEW:	AJAX saving privcay policy accept on comment form and live alert if not checked

======
1.6.7
======
- FIX:	Data Rectification Page not in WPML keys

======
1.6.6
======
- NEW:	Better notification when no services / service categories have been created

======
1.6.5
======
- FIX:	!!Important!! Replaced geoip service with https://ip.nf/me.jso
- FIX:	Undefinied index action in admin.php

======
1.6.4
======
- NEW:	Allow First time Cookies now keeps the popup beeing displayed
		All services gets loaded and activated
- FIX:	Removed Target Blank from Privacy Settings Popup
- FIX:	Wrong links in the privacy center if shortcode & popup enabled
- FIX:	Updated POT File

======
1.6.3
======
- NEW:	Gravity Forms now also do not store the IP
- FIX:	Flamingo DB integration deprecated, version 5.0.3 adds a new option
		for consent storage, please see here: https://www.welaunch.io/wordpress-gdpr/documentation/faq/cf7/

======
1.6.2
======
- FIX:	Performance increase by 300%

======
1.6.1
======
- FIX:	Privacy popup / shortcode not selectable in backend options

======
1.6.0
======
- NEW:	Brought back the [[wordpress_gdpr_privacy_settings]] shortcode
		See settings > Privacy Settings > Use shortcode

======
1.5.16
======
- FIX:	Responsive CSS options
- FIX:	Services not set when privacy settings disabled
- FIX:	Options panel setting parameters

======
1.5.15
======
- FIX:	Performance Improvements
- FIX:	Undefined offset in admin class

======
1.5.14
======
- FIX:	Polylang support fot ctp & taxonomies
- FIX:	Undefined Offset 5 in Admin Class

======
1.5.13
======
- FIX:	Data Rectification could not be sent when Use Wp Core function activated
- FIX:	PHP Notice in admin file

======
1.5.12
======
- FIX:	Performance caching
- FIX:	WPML Issue

======
1.5.11
======
- FIX:	Performance Increase for AJAX calls for sites with many pages

======
1.5.10
======
- FIX:	Service Categories order by Filter Query
- FIX:	Updated SV & FR Translations

======
1.5.9
======
- FIX:	Duplicate scripts when User outside of EU & IP Setting enabled

======
1.5.8
======
- FIX:	Comment submit button removed when comments integration disabled
- FIX:	Service categories not reorderable

======
1.5.7
======
- NEW:	Cookie Whitelist for removal only allowed cookies (see expert > Cookie Whitelist)
- NEW:	Video About GDPR Services:
		https://www.youtube.com/watch?v=M12970IL3E4
- FIX:	Service Category term order only affect service category taxonomy

======
1.5.6
======
- FIX:	PHP Notice when cookies array empty
- FIX:	First time cookie allowance not working for Pixelyoursite

======
1.5.4
======
- NEW:	Added multiple necessary cookies to default check
- FIX:	Privacy settings not shoing when cookie popup disabled

======
1.5.3
======
- NEW:	Updated Video containing all Features:
		https://www.youtube.com/watch?v=G6NfGtpWcIg&feature=youtu.be
- FIX:	Exclude Pages for Cookie Popup not working
- FIX:	Removed Privacy Settings from Center if disabled
- FIX:	Brought back the Users menu

======
1.5.2
======
- FIX:	Service Button Colors were revised versa
- FIX:	Updated POT file

======
1.5.1
======
- NEW:	Styling Options:
		- Cookie Popup Accept Link & Background Color
		- Cookie Popup Decline Link & Background Color
		- Privacy Settings Agree Link & Background Color
		- Privacy Settings Decline Link & Background Color
- NEW:	Moved all Backend Menus (like Settings, Requests & Services) into ONE "GDPR"-Menu
- NEW:	Option to show Cookie Policy, Privacy Policy and Privacy Center in Privacy Settings Menu
- NEW:	After Clicking Accept / Decline all Services, Privacy Settings Modal will Close
- FIX:	Links in Cookie Popup now displayed inline
- FIX:	Removed the Border Radius from Buttons by Default 

======
1.5.0.4
======
- FIX:	Important Cookie Service Management update
- FIX:	Updated GR Translations
		Thanks to Thomas Davliakos
- FIX:	Updated ES Translations
		Thanks to Henry Alcaly

======
1.5.0.3
======
- FIX:	Fix one bug, bigger bug pops up ... 
		https://imgur.com/gallery/pPeBAIJ
- FIX:	Checkboxes style does not get overwritten by themes

======
1.5.0.2
======
- NEW:	When no service categories / services created popup does not work
		Either remove Trigger & Disable popup OR create services
- FIX:	Added Quform to Service Migration (Button)
- FIX:	Quform cookie could not be set

======
1.5.0.1
======
- FIX:	JS Issue 
- FIX:	PHP Notices when no services created

======
1.5.0
======
- NEW:	Interactive Privacy Settings Modal 
		!! Important !!
		Watch the following Video for new features & how to migrate:
		https://www.youtube.com/watch?v=6mxtXaRVhec&feature=youtu.be
			
		Quick Migration:
		1. Update Plugin
		2. Go to Settings > General > Click on Migrate Services
		3. Check Migrated Services
		!! Important !!
- NEW:	Migrate old Integrations to GDPR Services
- NEW:	Own GDPR Services Post type
		- Service Name (e.g. Analytics)
		- Description (e.g. Reason)
		- Code for Head and Body
		- Code for Head and Body
- NEW:	Added template content for Pages when clicking on install pages:
		- Privacy Policy
		- Cookie Policy
		- Terms & Conditions
- NEW:	Completly rewritten public code
- FIX:	Improved Cookie Deleting
- FIX:	Performance Increase
- DEPRECATED: privacy_settings shortcode

======
1.5.0-beta3
======
- NEW:	Added Service Category Description
- FIX:	Privacy Settings Modal CSS issue
- FIX:	Categories not editable

======
1.5.0-beta2
======
- NEW:	Accept / decline all Cookie Services
- NEW:	Migrate Button automatically assigns Services to Categories now
- FIX:	Multiple Bug fixes

======
1.5.0-beta1
======
- FIX:	Privacy Popup could not be opened again
- FIX:	Cookie Length of 4000 was reached
- FIX:	Migrate Services not working because of permission

======
1.4.0.2
======
- FIX:	Autoptimize issue
- FIX:	After decline cookies turn back on issue

======
1.4.0.1
======
!IMPORTANT
- FIX:	Fixes an JS error in 1.4.0 - update important! 

======
1.4.0
======
- NEW:	Privacy Policy Acceptance
		See	Privacy Policy > Show Accept Checkbox
- NEW:	Terms & Conditions Acceptance
		See	Terms & Conditions > Show Accept Checkbox
- NEW:	Popup Close icon can now be styled
- NEW:	Do not show Popup for Robots / Crawlers 

======
1.3.10
======
- NEW:	Video explaining Integrations
		https://www.youtube.com/watch?v=YsseLicaQ78
- FIX:	Yes / No Translations
- FIX:	Request Status 

======
1.3.9
======
- FIX:	Issue on Multisite Activation
- FIX:	WPML string translation issue

======
1.3.8
======
- FIX:	Privacy Center Text has an own CSS Class
- FIX:	Missing Translation strings
- FIX:	Privacy Settings Popup now also gets hidden for non EU users
		where style was set to overlay
- FIX:	Updated POT

======
1.3.7
======
- NEW:	Video how to setup our plugin:
		https://www.youtube.com/watch?v=fO7pa3Kf5Qg&feature=youtu.be
- FIX:	Install pages redirect
- FIX:	Updated POT File

======
1.3.6
======
- FIX:	Issue 500 with certain wp installations

======
1.3.5
======
- NEW:	PixelYourSite now fully supported if Facebook Integration enabled
- FIX:	Performance & Stable issue
- FIX:	Redux Framework check now by Class (if your theme already has it, you do
		not need to install it again)

======
1.3.4
======
- NEW:	Media Credits Page
- NEW:	Added Media Credits & Data Recitifcation to install pages
- FIX:	Updated POT
- FIX:	Updated DE Translations

======
1.3.3
======
- NEW:	Added Data Rectification
- NEW:	Added regular expression check for allowed cookies
		Make sure you add the following custom Cookies to your options:
		"wordpress_test_cookie,wordpress_logged_in_,wordpress_sec"
- FIX:	Pixelyoursite Hook does not get correct Cookies (whyever)
		We now use general _ga cookie to see if tracking cookies are allowed

======
1.3.2
======
- NEW:	Option to exclude pages for the popup appear
- NEW:	Improved Polylang Support
- FIX:	Privacy Settings Modal Flyout option not visible in admin panel
- FIX:	Responsive overlay fix
		Thanks to ahumadomayte
- FIX:	User check 

======
1.3.1
======
- FIX:	Reponsive Overlay fix
- FIX:	Added Z-Index to Privacy Popup when on bottom
- FIX:	CSS Classes

======
1.3.0
======
- NEW:	Faster & More Secure Cookie Checks
- NEW:	Option to add custom technical allowed cookies
		This will be shown as technical required cookies in privacy settings
		See general Settings > Custom Allowed Cookies
- NEW:	Overlay Popup Style (Popup will be centered and background grey)
		See Popup > Popup Style > Overlay
- NEW:	Privacy Settings Popup can now be used even with Cookie Popup on Top 

======
1.2.10
======
- FIX:	Performance Update

======
1.2.9
======
- FIX:	Removed last margin on privacy center items
- FIX:	Updated missing translations & POT File (po file needs to be synced)
- FIX:	Emails for data breach & updated policy now use Single recipients headers

======
1.2.8
======
- NEW:	You can now automatically create all Pages 
		See Settings > General
- NEW:	Create all Pages automatically
- NEW:	Splitted one Checkbox for WooCommerce Acceptances into two:
		One for Checkout
		One for Register Account
- NEW:	Added span element to text after checkboxes
- FIX:	Forget me button not displayed when checkbox active
- FIX:	WooCommerce Registration checkbox hook changed to woocommerce_register_form
- FIX:	Last logged in time in readable format
- FIX:	Popup settings not saved when cookies accepted / declined
- FIX:	Privacy center item not removed when disabled (only by empty page)
- FIX:	Updated Translations

======
1.2.7
======
- NEW:	You can now add as many custom integrations as you want
		See Settings > Integrations > Custom Integrations (at the bottom)
		FAQ: https://www.welaunch.io/wordpress-gdpr/documentation/faq/custom-integrations/
- FIX:	PHP notice after send export
- FIX:	Responsive issues on Mobile
- FIX:	Updates Translation POT file

======
1.2.6
======
- NEW:	Added an option to allow all cookies for first time users
		Many big companies do this even after GDPR - lawyers are
		not 100% sure if this compliant - but this allows you to
		track Analytics for example until user explicitly opted out
- NEW:	Added DMCA Page
		See Settings > DMCA
- FIX:	PHP notice

======
1.2.5
======
- NEW:	Option to use GEO IP to show popup only to EU citizens
		See Settings > Expert
- NEW:	Filter for privacy center items:
		apply_filters('wordpress_gdpr_privacy_center_items', $privacyCenterItems);
		-> With this you can change icons, move items on the privacy center page
- FIX:	Privacy Modal in Popup only possible if bar on bottom
- FIX:	Accept all triggers a click change event now

======
1.2.4
======
- NEW:	Privacy Settings Popup Menu
		See on our Demo Website at bottom right
		This can be enabled in Popup > Show Privacy Settings Modal
- NEW:	Added a Data Retention
		User data can be deleted automatically after X days	when user has not logged in. 
		You can check last logged in "GDPR Requests" > Users
- NEW:	Cookie Consent Log for Logged in Users
		This shows you which users has accepted what cookies
		See GDPR Requests > Users
- NEW:	Filters:
		wordpress_gdpr_privacy_settings
- NEW:	Actions:
		wordpress_gdpr_allow_cookies
		wordpress_gdpr_decline_cookies
		wordpress_gdpr_update_cookie
- FIX:	Added Page Options to WPML Keys

======
1.2.3
======
- NEW:	Inform when no user data found (e.g. after deletion)
- FIX:	After forget me clicked, request status did not changed
- FIX:	User data send not correct email for "email"-only requests
- FIX:	Flamingo records deltetion
- FIX:	Updated ALL translation files and POT

======
1.2.2
======
- FIX:	PHP Notice
- FIX:	Updated IT Translations

======
1.2.1
======
- NEW:	Updated german translations
		Thanks to Frank Rausch
- NEW:	Moved Whitelist option to a new "expert"-Section
- NEW:	Updated Slovakian Translations
- FIX:	Removed a small PHP notice causing JS issues
- FIX:	Updated DA Translations

======
1.2.0
======
- NEW:	We revamped the AJAX Cookie Allowance check
		Instead of multiple AJAX calls it now only calls one
- NEW:	If requests allowed without user exist check
		Data (Form entries, Comments, Orders) can now be removed by Email
- NEW:	Added Gravity Forms support for Export & Deletion
- NEW:	Option to use a Cookie Whitelist
		Read more: https://www.welaunch.io/wordpress-gdpr/documentation/faq/cookie-whitelist/
- NEW:	Added Romanian Translations
		Big Thanks to Leo Diaconu
- NEW:	Added Swedish Translations
		Big Thanks to Mikael Svensson
- NEW:	Update IT Translations
		THANKS to DDS Lab di Diego Gianluigi Di Salvo
- NEW:	Delete Quform entries by Email or User ID
- NEW:	Delete Gravity Form entries by Email or User ID
- NEW:	Delete FlamingoDB entries by Email or User ID
- NEW:	Delete Formidable entries by Email or User ID
- NEW:	Export Quform entries by Email or User ID
- NEW:	Export Gravity Form entries by Email or User ID
- NEW:	Export FlamingoDB entries by Email or User ID
- NEW:	Export Formidable entries by Email or User ID
- FIX:	Updated POT File
- FIX:	Code Refracturing 
- FIX:	Updated WPML Keys

======
1.1.6
======
- NEW:	Added Support for pixelyoursite.com
		If Facebook Integration not allowed our plugin
		sets "pys_disable_by_gdpr" Filter to true
- NEW:	Added Entry export of Formidable & Quform entries
- NEW:	Removed Settings to remove Formidable & Quform entries
		to Integration Settings Section.
		MAKE sure you reenable settings there.
- FIX:	Updated Cookies for deletion of all services
- FIX:	Added Multiple Delete Cookie Functions
- FIX:	Updated Flamingo Enabled Check

======
1.1.5
======
- NEW:	Genernal > Domain text option.
		For GA cookies to be stopped / allowed you need to 
		pass your domain you set in GA there. E.g. ".welaunch.io"
- NEW:	When click on accepted or declines the checkboxes will be
		checked / unchecked without page refreshing
- NEW:	Formidable support 
		Forget me requests can now also delete formidable entries
		See Settings > Forget Me > Formidable

======
1.1.4
======
- FIX:	Adsense Ads will not be removed when no consent given

======
1.1.3
======
- NEW:	Added Support for Adsense
- NEW:	Quform data can now be deleted
- NEW:	Added more option in Forget me options
		about what will be deleted
- FIX:	Updated NL Translations
		Big Thanks to JP Hoey

======
1.1.2
======
- NEW:	Added Piwik Integration
- NEW:	Added Slovakian Translation
- NEW:	Added an option to enable Mailster checkbox only
		This way you can use disable our Checkbox for Mailters,
		but still use the unsubscribe link
- NEW:	Added an option to export data as HTML instead of JSON
- FIX:	Updated POT File (Translations)

======
1.1.1
======
- NEW:	When mailster integration enabled the privacy center link
		to Unsubscribe directly links to mailster unsubscribe page
- FIX:	Cookie policy in Popup not shown

======
1.1.0
======
- NEW:	Added Support for WP 4.9.6
		Some comment & info about WP 4.9.6 & GDPR:
		https://www.welaunch.io/wordpress-gdpr/documentation/faq/wordpress-4-9-6-gdpr/
- NEW:	Added an option to allow all cookies when a user is logged in
- FIX:	Call to undefined function wp_delete_user
- FIX:	Testing WP 4.9.6
- FIX:	Updated Slovenian Translations
- FIX:	Moved Settings to an own Menu item for 4.9.6 compatibility

======
1.0.10
======
- NEW:	Added Hungarian Translations
- NEW:	Added Tag Manger Body Tag option (need for browsers with JS disallowed)
- FIX:	Fixed an issue with non closing a tag

======
1.0.9
======
- NEW:	Added Recaptcha Option
		See settings > General
- NEW:	Added Slowenian Translations
- NEW:	Added FAQ for Quforms
		https://www.welaunch.io/wordpress-gdpr/documentation/faq/quform/
- FIX:	Fixed an issue where Privacy Center was no more available when
		WooCommerce integration activated

======
1.0.8
======
- FIX:	Small Misspelling issue
- FIX:	Updates Translation Template

======
1.0.7
======
- NEW:	Added Flamingo DB Support
		https://www.welaunch.io/wordpress-gdpr/documentation/faq/cf7/
- NEW:	Added Mailster Support
		https://www.welaunch.io/wordpress-gdpr/documentation/faq/mailster/
- NEW:	Added WooCommerce to Privacy Settings
		explaining that 2 cookies are neccessary 
		for order processing
- NEW:	Integrations Tutorials: 
		https://www.welaunch.io/wordpress-gdpr/documentation/topics/integrations/
- NEW:	Filter for Neccessary Cookies: wordpress_gdpr_necessary_cookies
- FIX:	Updated Translations template

======
1.0.6
======
- NEW:	Added Facebook Pixel Code Support
- FIX:	If options disabled still show settings

======
1.0.5
======
- NEW:	Send out Privacy Policy Update Emails
		Settings > Privacy Policy Update
- NEW:	Decline Cookies in Popup
		This will be saved in a cookie
- NEW:	Added Privacy Settings to popup
- NEW:	Requests can be created even when user 
		does not exists in WP. This allows offline
		or other systems data to send manually.
		Settings > FORM > Disable User Exist Check
- NEW:	Added a manually done button to requests
- FIX:	Adjusted all Popup Texts
		Reset this section in plugin settings
- FIX:	Moved Data Breach Email button to settings page
- FIX:	Added Redirect after Action was taken

======
1.0.4
======
- NEW:	Added BuddyPress Support
- NEW:	Added Cloudflare Support

======
1.0.3
======
- NEW:	Close Cookie Popup Button
- NEW:	If logged in as admin -> cookies allowed
		This prevents logged out loop
- NEW:	Redux Framework Cookie blocked

======
1.0.2
======
- NEW:	Added Disclaimer Page
- NEW:	Added Terms and Conditions Page
- FIX:	Integrations will be accepted when cookies allowed
- FIX:	Updated Translations

======
1.0.1
======
- NEW:	Added Imprint Page (necessary for Germany)
- NEW:	Added Machine Translations:
		de_DE
		da_DK
		fr_FR
		it_IT
		nl_NL
		pl_PL
		pt_PT
		es_ES
- FIX:	WPML Keys
- FIX:	Ajax check only if enabled in admin panel

======
1.0.0
======
- Inital release