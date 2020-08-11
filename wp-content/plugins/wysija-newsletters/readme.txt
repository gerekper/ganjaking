=== MailPoet Newsletters (Previous) ===
Contributors: wysija
Tags: newsletter, email, welcome email, post notification, autoresponder, signup, subscription, SMTP
Requires at least: 3.5
Tested up to: 5.5
Stable tag: 2.14
Send newsletters post notifications or autoresponders from WordPress easily, and beautifully. Start to capture subscribers with our widget now.

== Description ==

Our lovely plugin is changing for the better: MailPoet 2 is being replaced by [MailPoet 3](https://wordpress.org/plugins/mailpoet/).

Version 2 will remain available right here on the repository. [Read more the complete FAQ.](https://www.mailpoet.com/faq-mailpoet-version-2/)

= Check out this 2 minute video. =

https://vimeo.com/130151897

= Features =

Please visit [MailPoet version 3 features](https://wordpress.org/plugins/mailpoet/) instead.

= Premium version =

MailPoet Premium offers these nifty extra features:

* Send to more than 2000 subscribers
* A beautiful statistics dashboard to compare your newsletters and subscribers
* Detailed stats for each subscriber and newsletter
* Automated bounce handling that keeps your subscribers' list clean
* Test your SPAM score before you send a newsletter to your subscribers
* Improve deliverability with DKIM signature
* Simple install process
* Priority support

= Support =

This version is no longer officially supported. Paying customer will continue to be supported until further notice.

= Translations in your language =

* Arabic
* Basque
* Catalan
* Chinese
* Croatian
* Czech
* Danish
* Dutch
* French (but of course!)
* German
* Greek
* Hebrew
* Hungarian
* Indonesian
* Italian
* Japanese
* Norwegian
* Persian
* Polish
* Portuguese PT
* Portuguese BR
* Romanian
* Russian
* Serbian
* Slovak
* Slovenian
* Spanish
* Swedish
* Turkish

== Installation ==

There are 3 ways to install this plugin:

= 1. The super easy way =
1. In your Admin, go to menu Plugins > Add
1. Search for `mailpoet`
1. Click to install
1. Activate the plugin
1. A new menu `MailPoet` will appear in your Admin

= 2. The easy way =
1. Download the plugin (.zip file) on the right column of this page
1. In your Admin, go to menu Plugins > Add
1. Select the tab "Upload"
1. Upload the .zip file you just downloaded
1. Activate the plugin
1. A new menu `MailPoet` will appear in your Admin

= 3. The old and reliable way (FTP) =
1. Upload `wysija-newsletters` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A new menu `MailPoet` will appear in your Admin

== Frequently Asked Questions ==

= Got questions? =

Our [support site](https://www.mailpoet.com/support) has plenty of articles and a ticketing system.

[Read more the complete FAQ on version 2.](https://www.mailpoet.com/faq-mailpoet-version-2/)

== Screenshots ==

1. Sample newsletters.
2. The drag & drop editor.
3. Subscriber management.
4. Newsletter statistics.
5. Subscriber statistics (Premium version).
6. Sending method configuration in Settings.
7. Importing subscribers with a CSV.

== Changelog ==

= 2.14 - 2020-07-22 =
* Fixed PHPMail 6.x support for WordPress 5.5.

= 2.13 - 2019-11-05 =
* Added a blacklist for email addresses that should not be receiving any emails;
* Fixed PHP 7.3 support.

= 2.12 - 2019-07-11 =
* Added a blacklist for email addresses that should not be receiving any emails.

= 2.11 - 2019-02-14 =
* Improved: limiting number of "Subscription confirmation" emails sent to prevent abuse.

= 2.10.2 - 2018-10-17 =
* Fixed: stuck "What's new" page so you could still use the rest of the plugin.

= 2.10.1 - 2018-10-16 =
* Added: Discount when upgrading to MailPoet 3. Consider upgrading today!

= 2.9 - 2018-07-24 =
* Added: announcing about newly added image alignment support in MailPoet 3;
* Fixed: missing text domains on some translations;
* Removed: promotion that has ended a while ago.

= 2.8.2 - 2018-03-14 =
* Improved: protection against spam attacks. Thanks, Eslam Mohamed Reda!
* Fixed: support for PHP 5.2 has been restored.

= 2.8.1 - 2017-11-28 =
* Added poll and discounts
* Fixed editor sidebar missing if plugins enqueue the mediaelement script on WP 4.9

= 2.8 - 2017-11-20 =
* Added support for ReCaptcha to protect subscription forms from automated abuse
* Fixed editor sidebar missing if a theme uses wp_enqueue_media() function on WP 4.9

= 2.7.15.1 - 2017-11-02 =
* Improved IP throttling to work independently of subscribers table (for robustness)

= 2.7.14 - 2017-10-23 =
* Removed SWFUpload support
* Fixed path for TinyMCE styles

= 2.7.13 - 2017-09-29 =
* Added throttling of repeated subscriptions from an IP address

= 2.7.12.1 - 2017-09-19 =
* Update messages in regards to official launch of MailPoet version 3

= 2.7.11.3 - 2017-07-21 =
* Fixed Premium version download link

= 2.7.11.1 - 2017-07-17 =
* Improved welcome and update pages

= 2.7.10 - 2017-04-20 =
* Introduced minor changes to the premium page

= 2.7.9 - 2017-04-17 =
* Fixed security issue reported by Craig Smith. Thanks!

= 2.7.8 - 2017-03-03 =
* Fixed sending issues when MailPoet's sending service is selected
* Fixed sending post notifications when tag filters are used
* Fixed the display of incorrect newsletter title in browser preview
* Fixed PHP notices associated with sending on multisite hosts
* Fixed broken DKIM signing

= 2.7.7 - 2017-01-31 =
* Fixed activation issues on PHP 5.2
* Removed PHPMailer library in compliance with the new WordPress security policy. Please report any sending issues!

= 2.7.6 - 2017-01-18 =
* Fixed post categories not being displayed in sent newsletters
* Fixed PHPMailer vulnerability
* Fixed PEAR POP3's usage of static methods
* Fixed direct manipulation of the $wp_filter global
* Fixed BBPress unsubscribe compatibility
* Added ElasticEmail unsubscribe tag

= 2.7.5 - 2016-08-18 =
* Fixed subscription form widget

= 2.7.4 - 2016-08-09 =
* Fixed error appearing during newsletter creation

= 2.7.3 - 2016-08-08 =
* Fixed issue with emoji when saving newsletters
* Prevent encoding of "tel:" URLs
* Fixed SQL injection vulnerability (Thanks to Force Interactive)
* Fixed XSS vulnerability (Thanks to Sipke Mellema from Securify B.V.)
* Fixed PHP warnings on Bounce management
* Escape commma and quote marks during export
* Fixed some editor issues
* Fixed double signed DKIM emails

= 2.7.2 - 2016-06-01 =
* Fixed broken CSS for Premium page
* Fixed Custom Fields not saving on front-end
* Fixed image ratio for specific locations
* Fixed issue with missing rule (onlyNumberSp) in Italian & German translations
* Fixed broken email validation in Dutch language with long extensions
* Fixed undefined property notice on Bounce pages
* Added SparkPost API support
* Fixed security issues. Thanks again to Falk Huber (T-Systems) for reporting them
* Added new menu icon (New MailPoet Branding)
* Fixed sending preview of an email with an empty subject
* Added Ukrainian JS validation language
* Replaced deprecated get_currentuserinfo() with wp_get_current_user()
* Fixed PHP notices that may have appeared during the sending process

= 2.7.1 - 2016-03-15 =
* Fixed security issues. Thanks to Falk Huber for letting us now.

= 2.7 - 2016-01-29 =
* Enabled PHP7 compatibility
* Fixed security issues. Thanks to Immunity and Netsparker (https://www.netsparker.com) for alerting us.
* Fixed an issue with newsletters not saving during the creation process
* Disabled SSL verification on hosts with invalid SSL certificates and PHP 5.6
* Updated SendGrid mailer
* Improved URL validation logic
* Addressed PHP notices that appeared in cron output
* Fixed other minor issues

= 2.6.19 - 2015-10-13 =
* Fixed a URL validation issue when WP's home & site URLs are different. Kudos to Divaldo for pointing it out.

= 2.6.18 - 2015-09-21 =
* Fixed URL validation issue
* Improved newsletter/subscriber search
* Fixed minor issues

= 2.6.17 - 2015-09-04 =
* Enhanced redirection check in email URLs.
* Fixed old-style PHP constructors.
* Fixed newsletter preview bug.
* Fixed import issues on old PHP versions.
* Fixed minor issues.

= 2.6.16 - 2015-06-23 =
* Added HHVM support
* Added MariaDB support
* Fixed import issue where existing subscriber's data was not updated
* Fixed minor issues

= 2.6.15 - 2015-02-17 =
* Fixed hidden signup confirmation when the theme "Twenty Fifteen" is activated
* Fixed import issue on Windows servers, all valid subscribers CSV files can be imported now
* Fixed the filter "Subscribers in no list" in the Subscribers' listing in the backend
* Fixed bug in the frontend subscriptions page with the shortcode [wysija_page] not being replaced
* Fixed rendering issue when sending WordPress Post notifications including multiple levels of nested HTML lists(ul & ol)
* Improved performance in the Subscribers' listing
* Improved scheduled tasks process responses for greater reliability
* Added "WBounce" to our list of compatible plugins in Settings > Add-ons
* Added non-translated strings for the subscribe and unsubscribe admin notifications

= 2.6.14 - 2014-11-26 =
* Fixed all of the RTL issues on delivered newsletters and their web version
* Fixed issue when importing subscribers with new custom fields
* Fixed duplicated Monthly post notifications issue
* Fixed View in Browser version deactivation
* Removed option to switch to Beta

= 2.6.13 - 2014-10-27 =
* Fixed a conflict with the plugin [Magic Action Box](https://wordpress.org/plugins/magic-action-box/)
* Fixed blank export file when exporting subscribers with custom fields
* Fixed the default subscribers ordering in the backend, we now display the most recent first
* Fixed the bug on Bold and Italic commands in our newsletter editor
* Fixed an issue on the new bulk resend confirmation email feature
* Fixed bounce management issue (Premium only), counting until 3 when a mailbox is full

= 2.6.12 - 2014-10-16 =
* Improved security thanks to Dominik Schilling, [Ryan Satterfield](http://planetzuda.com) and [Logical Trust](https://logicaltrust.net/en/)
* Improved performance issue
* Improved email rendering
* Improved our stats dashboard, now they have a few more goodiness attached to them
* Improved Subscribers' Export, better data encoding for Windows servers
* Improved Form Editor, Date fields now accept dates prior to 1970
* Fixed conflict with EditFlow in our Visual Editor
* Fixed rare bug on Windows Server stopping you from sending emails
* Added option to Resend confirmation email in the Subscriber's listing
* Hard at work cleaning up our code and making it better

= 2.6.11 - 2014-08-14 =
* Improved protection against CSRF attacks thanks to Yoshinori Matsumoto.
* Fixed bug on scheduled newsletters edited back and forth and becoming uneditable.
* Fixed when duplicating an email through the stats page of a newsletter, then deleting the duplicate would also delete the original.
* Old code Spring cleaning part 2, getting rid of the junk.

= 2.6.10 - 2014-08-04 =
* Improved protection of themes upload, unsubscribe links, file access and statistics.
* Improved the "Send a test email" function.
* Fixed never ending process while sending previews on certain servers.
* Fixed a few regular expressions for a better rendering in Outlook.
* Fixed memory issue when dropping the "WordPress Post" widget on sites with thousands of taxonomies.
* Fixed wrong subscribers count in the subscribers' listing.
* Old code Spring cleaning, removed unused rusty pieces.

= 2.6.9 - 2014-07-14 =
* Fixed email display issues caused by responsive CSS. We're truly sorry for this.
* New columns on Subscribers page: "Never opened or clicked"
* Removed "Unconfirmed" filter on Subscribers page when signup confirmation is off
* Fixed a few minor bugs on the Statistics page for Premium users. Thanks for your feedbacks!
* Fixed our hair with spray to look like cool kids in the eighties

= 2.6.8 - 2014-07-04 =
* Fixed security issue reported by our dear Dominic. Thank you sir!

= 2.6.7 - 2014-07-01 =
* Added 1 more add-on to our plugin's listing
* Implemented a new Email Rendering Engine, with a lot of bugs fixed for Outlook users and Mobile Users
* Fixed broken "Automatic Latest Content" settings on sites with lots of tags
* Fixed fatal error when sending to more than 1 million total subscribers
* Fixed subscribers page being inaccessible on some Multisites (very rare bug)
* Fixed Outlook 2013 paragraph spacing issue on previous beta
* Fixed security issue reported by [Sucuri](http://sucuri.net/)
* Fixed the HTML button on TinyMCE which was hidden in editor
* Fixed the links popup on TinyMCE for IE11 in editor
* Fixed "automated latest content" (ALC) bugs with multiple Custom Post Types
* Fixed the upload image functionality in our plugin
* Fixed default item selected on filter lists at the Subscribers page for Firefox and Opera users
* Fixed warning message appearing on the subscription form for Admin Users
* Fixed a few typos in the plugin
* Improved the CSV export to expand its compatibility with Excel on Windows
* Improved the performance of the plugin when loading admin assets

= 2.6.6 - 2014-04-30 =
* Fixed the Upload Image functionality in our plugin
* Fixed default item selected on filter lists at the Users Page for Firefox and Opera users
* Fixed warning message appearing on the Subscription form for Admin Users
* Fixed some typos in the plugin
* Improved the performance of the plugin when Loading admin assets

= 2.6.5 - 2014-04-18 =
* Fixed TinyMCE issue with WordPress 3.9, our editor in Step 2 is working again
* Fixed conflicting shortcode between MailPoet's custom fields and Ultimate TinyMCE shortcode

= 2.6.4 - 2014-04-17 =
* Fixed compatibility issue with WordPress 3.9: the TinyMCE editor in WordPress Posts edition page was broken
* Fixed regular expression on Google analytics tracking code (Premium only)
* Fixed the importing method, compatible to more CSV formats
* Hey, we still have an issue with WordPress 3.9, our tinyMCE editor in Step 2 of the newsletter edition is not working properly (buttons are not usable)
* No need to report us that issue, we're already working hard to fix it. Thanks for your patience! :)

= 2.6.3 - 2014-04-16 =
* Fixed filters on Automated Latest Content for Taxonomies and Post Types
* Fixed White Screen on What's New page after updating
* Fixed display bug, image sitting on top of the text editor in the Step2 of newsletter edition
* Added support for `mysqli` of WordPress 3.9
* Replaced The tooltip script "qTip2" in favor of "Bootstrap's Tooltip" (JavaScript Library)

= 2.6.2 - 2014-04-01 =
* Fixed Javascript conflict breaking some of WordPress post editor function (add media upload, etc ...)
* Fixed bugs when using bulk actions within WordPress plugins listing
* Fixed a rendering issue on the welcome page
* Fixed ability to make firstname and last name field required fields on the subscription forms
* Fixed rare issue of WordPress media uploader sending HTTP 500 error when uploading an image
* Improved the performance of the plugin with a better version handling
* Improved the Import/Export tools
* Improved "admin_body_class" to be more consistent
* Improved consistency for settings defaults
* Improved the inner tabs JavaScript on the settings page in premium version
* Improved the JavaScript on the "WordPress post" widget
* Remove badly named functions creating conflicts on step 3 of the newsletter creation process

= 2.6.1 - 2014-03-25 =
* Fixed a conflict with the "Ultimate Shortcode" plugin breaking our subscription forms
* Fixed automatically inserted text in confirmation/subscription pages
* Improved CSV special characters handling in the Subscribers import functionality
* Improved some of our warnings in the backend to make the messages clearer
* Improved MailPoet Statistics Page with styling compatible to WordPress v3.8
* Improved handling Big Databases on daily post notifications
* Added extra information in the Newsletters' statistics page for an efficient summary
* Added German Disclaimer to a new Docs Folder for legal purpose
* Minor changes improving the User Experience in some of our Forms in the backend
* Replaced The autoselection script "Choosen" in favor of "Select2" (JavaScript Library)

= 2.6 - 2014-03-18 =
* This is the juiciest of all releases. Ready?
* a new statistics dashboard in Premium. The beauty of the big picture
* add more fields in your subscription forms, like phone number, address, or whatever you like
* display an archive of your past newsletters in a page with a shortcode. Find it in the settings
* display your posts' author names and categories in the newsletter editor
* insert several posts at once in the editor, instead of one at a time. Time saver
* a dozen new display options when you drop your posts in the newsletter, for the control freaks
* find a list of add-ons in the settings. Plug your MailPoet with your other favorite plugins, like Gravity Forms
* select your own WordPress pages for your confirmation and unsubscribe pages
* use your MailPoet shortcodes in your confirmation emails, like [user:firstname | default:reader]
* MailPoet now abides to German privacy laws. Guten tag Alex!
* the browser version of your newsletter now include the Google Analytics tracking code (Premium)
* right to left language improvements
* our user interface is now styled for 3.8
* needless to say, hundreds of mini improvements
* we hand over to you 6 months of hard work. Enjoy!

= 2.6.beta - 2013-12-19 to 2014-03-18 =
* The latest Beta for v2.6
* Fixed stable update URL to the Core Repository
* Google Analytics URLs now working as expected
* Update warning fixed
* fixed links url not being properly saved in some case when using the google campaign code
* fixed queue processing fatal error, halting the sending process (only in rare case scenarios)
* Automatic latest content widget improved
* added minor visual improvements to fit better WP 3.8 style
* Mail-tester now works also on ssl sites
* Theme installation now works with https protected sites too
* Adapted buttons to the WordPress 3.8 style
* Changed some image icons to use `dashicons`
* Removed notices regarding an unused file
* Removed a bug on FROM email input tooltip
* Removed MySQL bugs on Stats dashboard filters
* Enhanced the Widget AJAX URL method
* Fixed some CSS positioning issue around buttons
* Adapted the notices/warnings style to WordPress 3.8
* Improvements on Bulk actions for Subscribers and Newsletters
* Fixed domain column missing
* Fixed problems with problem author name for automatic newsletters
* Removed notices on preview on Browser
* improved the update plugin process
* better CSS for the add-ons bage
* fixed bug on bulk actions for subscribers and newsletters
* removing some PHP Notices/Warnings
* improved installing premium plugin process
* added newsletter bulk delete option
* fixed a bug throwing an error trying to update "Warning: fopen(..."
* improved RTL on form editor
* added smoother auto update process free and premium plugin happening together
* small corrections on statistics dashboard form editor and newsletter editor
* added better support for Retina displays
* added custom fields functionality in the form editor section
* improved stats dashboard for premium users
* added archive page shortcode
* added select your own confirmation page

= 2.5.9.4 - 2013-12-26 =
* fixed rare bug emails being re-enqueued when a partially sent newsletter was paused and edited from step3
* fixed improved bounce emails detection to handle more case scenarios (premium only)
* added better support for Retina displays
* added facelift and botoxed lips much needed for our best WP 3.8 looks

= 2.5.9.3 - 2013-12-03 =
* fixed another rare case where image goes missing when dragging and dropping a WordPress' post into our visual editor
* fixed post updates taking too long when refreshing automatic newsletters with the "automated latest content" widget
* fixed rare case where image goes missing when dragging and dropping an uploaded image into our visual editor
* fixed birthday cake for our two years anniversary  http://www.mailpoet.com/two-year-anniversary/

= 2.5.9.2 - 2013-11-26 =
* fixed issue automatic newsletter not going out when using Wysija's cron triggered by visitors pageview in Settings > Advanced
* fixed issue wrong separator in export file(CSV or Excel)

= 2.5.9.1 - 2013-11-18 =
* fixed newsletters not being saved in Chrome at visual editor level
* fixed daily/weekly post notifications missing some articles if other newsletters were being sent at the same time
* fixed broken redirection when square brackets are present in your URLs
* fixed rare case where image goes missing when dragging and dropping a WordPress' post into our visual editor
* fixed "View in your Browser" and "Unsubscribe" not being translated in your language in daily/weekly post notifications
* fixed broken links with port specified (e.g.: mysiteurl.com:8888/my-post)
* fixed updating automatically the "automated latest content" widget in automatic newsletter on create/update/delete any post

= 2.5.9 - 2013-10-02 =
* improved multisite bounce process
* improved email validation
* improved IP recording
* fixed issue with "Magic Members" plugin adding their list of users when one of our forms shortcode was in a post/page
* fixed notice in the links tracking controller in the frontend
* fixed cross image in popup was slightly shifted
* fixed network sending method not inheriting the right parameters in MS (From address, etc...)
* fixed issue when trying to delete first list in list selection widget

= 2.5.8 - 2013-09-05 =
* fixed error firing daily and weekly newsletters
* fixed premium mail-tester iframe not working on modern browser
* fixed send_at parameter being updated wrongfully for automatic newsletters

= 2.5.7 - 2013-09-04 =
* fixed missing cron schedules for post notifications and scheduled newsletters
* fixed issue with auto save when switching themes
* fixed issue preventing the deletion of newly added image
* fixed sending method not being override in multisite
* fixed validation messages not translated on some MS sites due to language locale issue
* improved better use of the language definition so that we understand on which language a WPML site is for instance
* improved better use of the language definition (removed WPLANG) so that we understand on which language a WPML site is for instance
* added retina icons for text editor

= 2.5.5 - 2013-08-05 =
* fixed compatibility issue with WordPress 3.6 and latest jQuery
* fixed general reply to email settings not applied to sign up confirmation email
* fixed wild "Security failure during request." in the backend of some server configurations
* fixed count issues on subscribers and list
* fixed failing to send post notification newsletter preview with the tag [newsletter:post_title]
* refactored export and import code
* improved MailPoet's cron in order to respect better the delays between one scheduled task and another

= 2.5.4 - 2013-07-13 =
* fixed broken sending process when DKIM is activated but the openSSL php library goes missing
* fixed missing update procedure of 2.5.2 for bulk confirm
* fixed daily post notification sending sometimes one day old posts
* fixed SQL error on user to subscriber synch
* fixed frontend notice on subscriptions modifications
* fixed delete automatic newsletters
* fixed remove one user from all mailing lists at a time from the admin interface
* added reply-to address in advanced settings

= 2.5.3 - 2013-06-27 =
* added hook to delete MailPoet subscribers when a WP multisite user is deleted
* fixed MailPoet's cron page view auto trigger creating some errors on some servers
* fixed WordPress user auto import as MailPoet subscribers on multisite
* fixed rendering issue 2.5.2


= 2.5.2 - 2013-06-26 =
* fixed confusing text alignment not applied in the visual editor's tiny MCE
* fixed autosave on step 2, the newsletter editor
* fixed dragged images in IE10 in the newsletters' visual editor
* fixed subscribers count not being refreshed after a bulk delete
* fixed bulk confirm option which was missing a subscribed date to be completely working


= 2.5.1 - 2013-06-17 =
* added protection to avoid switching to the beta without wanting
* fixed PHP notices in the newsletter's view in your browser version
* fixed view in your browser link being left aligned
* fixed line height issue on some Outlook versions

= 2.5 - 2013-06-06 =
* added checkbox to select default theme for new newsletters. See Themes tab.
* added bulk select all subscribers, like Gmail with conversations
* added bulk actions to move or remove subscribers from lists
* added bulk action "Confirm unconfirmed subscribers"
* added "undo unsubscribe" in unsubscribed confirmation page
* added option to have the beta version of plugin, see Advanced Settings
* added Styles and Themes tab of visual editor as permissions in Advanced Settings
* improved single automated bounce handling config for all sites in Multisite, for Premium
* improved export feature with semi-colon separated option
* improved sorting of subscribers in stats by open date and time
* fixed the monthly scheduling of automatic newsletter for "last day..."
* fixed duplicating a post notification email into an autoresponder would prevent from sending previews
* fixed new shortcode for issue number was not returning the right number
* fixed send admin notification on subscribe for each list a user subscribes to, not just the first time
* fixed outlook rendering issue fixed images alignment
* fixed tea, with milk and sugar

= 2.4.4 - 2013-04-18 =
* added translations of the "loading..." message in forms.#
* added download link to theme's .zip file in theme detail pages
* added possibility to hide our update page's kitten. It hurt some feelings
* added protection on looping install process resulting in duplication of default list or newsletter
* fixed sending autoresponders twice after importing same csv file

= 2.4.3 - 2013-04-03 =
* improved subscription form rendering and support of unicode/special characters
* improved security on queueing emails
* fixed missing confirmation message when subscribing to forms (introduced in 2.4.2)
* fixed scheduling issue when sending every month on a given day
* fixed form editor issues related to data encoding/decoding
* fixed post notification will not activate on step3 of the newsletter edition
* fixed scheduled emails generating a queueing error on step3 of the newsletter edition
* fixed import into a list associated with a retro-active autoresponder was not put into the queue
* fixed retro-active autoresponder delay calculation

= 2.4.2 - 2013-03-27 =

* fixed issue in form editor. Using quotes in confirmation message prevented Form from being saved.
* fixed autoresponders being automatically queued if modified and saved on step3 of the newsletter edition this resulting in a sql error

= 2.4.1 - 2013-03-26 =

* fixed post notification being queued immediately after changes being saved on step3 related to retroactive autoresponders.

= 2.4 - 2013-03-25 =

* added ability to edit HTML in text blocks of visual editor (beta)
* added a form manager in settings, with a drag and drop interface
* added ability for users to share their usage data with MailPoet's team
* added a dozen newsletter themes
* added image resizing of images uploaded in previous versions
* added autosave on browser back button. No more lost changes
* improved sending status with progress bar
* improved translations
* improved autoresponders: now retroactive and will be sent to newly imported subscribers too
* fixed when sending directly a newsletter which was set as scheduled in step 3
* fixed dozens of small bugs
* impressed by your determination in reading the full change log

= 2.3.5 - 2013-03-15 =

* fixed unsubscribe and subscriptions links lead now to view in browser version if the subscriber doesn't exists in the DB anymore (instead of a white screen)
* fixed error when trying to delete a duplicated list
* fixed view in browser link
* fixed how spammy is your newsletter and Mandrill
* fixed variable type issue leading in some case scenario to a Fatal Error in the frontend subscription widget
* fixed removed autofill value in HTML version of subscription form
* improved memory usage on subscribers import and export processes

= 2.3.4 - 2013-03-07 =

* added default value from WordPress' logged in user in the subscription form
* added dropdown selection of statuses (publish, private, scheduled, draft) for WordPress' Posts to be dropped into the visual editor
* added option to MailPoet's CRON (task scheduler) to deactivate the schedule tasks checks on any page view
* fixed unsubscribe date in the frontend subscriptions management is now translated with date_i18n (thanks Anna :))
* fixed unsubscribe link in preview emails
* fixed subscribers count when double optin is deactivated
* fixed unsubscribe link with Google Analytics

= 2.3.3 - 2013-03-04 =

* added drag and drop private or scheduled posts in the visual editor
* fixed more than one post notification going out monthly, weekly or daily
* fixed warning in MS settings
* fixed translation issues for comments checkbox for instance
* fixed little notice when deleting list
* fixed buddypress multiple checkbox on registration form
* fixed on duplicate of a post notification reset the articles already sent in the original so that it starts from scratch
* fixed import ignoring rows with invalid data to avoid import failure
* fixed missing title and description of widget
* fixed multisite only you'll see once the update screen as a network admin
* improved logging options

= 2.3.2 - 2013-02-20 =

* fixed scheduling issue for automatic newsletters
* fixed message output in case of cron problems
* fixed wordpress images gallery pagination
* fixed occasional internal server error on some users with PHP running as FastCGI

= 2.3.1 - 2013-02-07 =

* added correction of our commit through svn some files were missing
* added "shortcodes" for newsletter. Add more than first and last name, like dates, links Supported in subject line too.
* added custom roles to autoresponders so you can send to more than just the default roles (admin, editor, author, etc.)
* added single sending method for all sites in Multisite. See new "MS" tab in settings for more
* added DKIM optional upgrade to 1024 bits to comply with Gmail (Premium feature)
* added support for Premium behind firewall with no possible requests to MailPoet.com
* fixed images uploaded in MailPoet are now resized to 600px. Next release will include images from media library.
* fixed lightbox (popups) width for right to left languages
* fixed subscription form on WordPress user registration
* fixed load translation error on Windows server
* fixed wrong count for the issue number tag [number] in daily, weekly and monthly post notification
* fixed immediate custom post notification wrongly triggered
* fixed browser view error for rare scenarios
* fixed SendGrid web API not able to send newsletters to subscribers with first name and/or last name
* fixed WordPress' images browser
* fixed breakfast, with a buttered toast and nice latte

= 2.3 - 2013-02-07 =

* svn error please update your version to the latest one

= 2.2.3 - 2013-01-19 =

* fixed weekly post notifications not having all of the articles of the week, but just of the day

= 2.2.2 - 2013-01-18 =

* fixed immediate single post notification not being triggerred

= 2.2.1 - 2013-01-18 =

* fixed translation issue in confirmation page, message was forced in English
* fixed display bug in settings, SMTP fields (port, authentication, secure connection) showing where not needed
* fixed manual bounce processing button not working
* fixed issue number [number] tag not having the right value
* fixed small frontend conflict with jquery 1.9.0 and above
* fixed missing filter in newsletters statistic for the Not Sent status
* fixed post notification could send some past articles in one specific case scenario
* fixed wrong count of subscribers in backend interfaces
* fixed still sending to subscribers manually removed from a list in the backend
* fixed number of WordPress users don't match with the number in our WordPress Users list
* added support for German umlaut in email addresses

= 2.2 - 2013-01-11 =

* added script detector in debug mode to help resolve plugin & theme conflicts
* added checkbox option in WordPress registration form. See Advanced Settings.
* added on auto newsletter duplication reset the [number] tag
* added a safeguard for manually deleted activation email in database
* added support for SendGrid's web API to avoid blocked SMTP ports
* added a sending status load bar for currently sending newsletter
* improved "subscribe in comments" option for better Akismet integration
* improved iframe.css inclusion for MS. All child sites take the main site's styles by default
* renamed list "WordPress Synched" to "WordPress users" for clarity
* fixed "HTML version" which was not working for visitors
* fixed subscription form "HTML version" missing hidden fields in post/page widget
* fixed newsletters themes installation with unsafe paths
* fixed missing page/post title when subscribing without ajax
* fixed encoding issue in HTML and PHP version of the subscription form in the widget
* fixed save issue of subscriber's status in Subscriber's detail page in admin
* fixed over 25 mini bugs
* fixed lunch and went for a well deserved beer

= 2.1.9 - 2012-12-11 =
* added checkbox to comments in post for visitors to optin. Activate in Settings > Advanced
* improved default newsletter into simple 5 min. guide
* improved over a dozen confusing labels and strings
* improved compatibility with domain mapping
* added hook wysija_preview to browser version of newsletter (thx to Matt)
* fixed autoload new posts on scroll in the newsletter WordPress post widget
* fixed missing total click stats in newsletter stats
* fixed saving changes when going back to Step 2 from Step 3
* added sending autoresponders to subscribers added via the admin
* removed 3 messages after installation. Nobody reads them
* removed bulk add to synch list
* removed bulk unsubscribe to all. Too dangerous.
* went for a walk in the park with friends to celebrate this new version

= 2.1.8 - 2012-11-27 =
* added get HTML version of form. See in Widgets.
* improved MailPoet homemade cron, available in Settings > Advanced
* removed validation for first name & last name on subscriber profile
* fixed incompatibility with "Root Relative URLs" plugin
* fixed conflict with plugin "Magic Members"
* fixed crashed on some servers on install
* fixed in newsletters listing, wrong list appearing in automatic newsletter
* fixed disappeared bounce email field in Settings > Advanced for free users
* fixed Internet Explorer issue on WordPress Articles selection widget
* fixed issue on IE8 where a draggable item was not disappearing after being dropped
* fixed WordPress Synched list wrong count when sending
* fixed image not being fetched from post content when inserting a WordPress post
* fixed not sending auto newsletter with event "after a new user is added to your site" when double optin was off
* fixed various plugins conflicting with our subscription form inserted into the content of a post or page

= 2.1.7 - 2012-11-09 =
* added MailPoet custom cron option in Advanced Settings as an alternative to wp-cron
* fixed translation missing for "unsubscribe", "view in your browser" and "manage your subscription" links
* fixed escaping quotes on subject in step 3 send preview
* fixed wrong total of subscribers when sending
* fixed bounced tab appearing empty for free users
* fixed wrong selection in WordPress posts widget after a search(in visual editor)
* fixed security issue with swf uploading module

= 2.1.6 - 2012-11-04 =
* added basic Custom Post Type support in WordPress post widget
* added resend an Activation Email for another list even when already subscribed
* added posts autoload on scroll when adding single post in newsletter visual editor
* fixed PHP Notice: step2 of newsletter creation
* fixed PHP Notice: on debug class
* fixed our debug hijacking WP_DEBUG in the backend (thanks Ryann)
* fixed deprecated in bounce handling
* fixed scrollbar issue in WordPress Post popup on Chrome & Safari
* fixed conflict with Simple Links plugin
* fixed toolbar tabs disappearing in some languages (will be improved)
* fixed bounce error not properly displayed prevented saving settings

= 2.1.5 - 2012-10-16 =
* fixed Notice: Use of undefined constant WYSIJA_DBG - assumed 'WYSIJA_DBG' in [...]/wp-content/plugins/wysija-newsletters/core/model.php on line 842
* fixed bulk add subscriber to list when unsubscribed
* fixed private list removed on edit your subscriber profile
* fixed shortcodes not being properly stripped from post excerpt
* fixed line breaks being stripped from posts
* fixed text alignment issues in Outlook
* fixed font styling issues in email
* fixed auto newsletter for new subscriber when single optin
* fixed new subscriber notification when single optin
* fixed send preview email on automatic post notification newsletter
* fixed not sending followup when updating subscriptions

= 2.1.4 - 2012-09-26 =
* fixed missing "from name" when using Elastic Email
* fixed rare issue where Social bookmarks & Automatic latest posts were not saved
* fixed double scrollbars appearing on article selection popup
* fixed dkim wrong key
* fixed filled up sent on parameter without having sent the newsletter

= 2.1.3 - 2012-09-18 =

* added restAPI for elasticemail when detected in the smtp configuration
* improved install making sure that no crazy plugins will harm our initial setup (symptoms: Too many redirect crash or posting to social networks)
* fixed SQL comments inserted as tables in some weird server...
* fixed error 500 on update procedure of 2.1 when some roles were not existing. (add_cap on none object fatal error)
* improved install process not creating new sql connection, only using wpdb's one.
* fixed synched plugins (Subscribe2 etc...) when there was just the main list
* removed global css and javascript
* fixed issue where the widget would not save
* improved IE9 compatibility
* fixed excerpt function to keep line breaks
* fixed links with #parameters GA incompatibility -> Thanks Adam

= 2.1.2 - 2012-09-05 =

* major speed improvement and cache plugin compatibility
* added utf-8 encoding in iframe loaded subscription form.
* added security check for translated links (dutch translation issue with view in browser link)
* removed _nonce non sense in the visitors subscription forms.
* fixed loading issue in subscription form
* fixed styling issue in subscription form
* fixed accents issue in subscription form
* fixed DKIM activation settings not being saved
* fixed non translated unsubscribe and view in browser links
* fixed warning showing up on some servers configuration when sending a preview of the newsletter
* fixed popups in IE8 and improved overall display
* fixed openssl_error_string function breaking our settings screen on some configurations.
* fixed error with dkim on server without openssl functions
* fixed bounce error with the rule unsubscribe user

= 2.1.1 - 2012-09-02 =

* fixed update 2.1 error : Duplicate column name "is_public" may have caused some big slow down on some servers and some auto post to facebook (deepest apologies).
* fixed Outlook issue where text blocks would not have the proper width

= 2.1 - 2012-08-31 =

* added ability for subscribers to change their email and lists.
* added "View it in your browser" option.
* added advanced access rights with capabilities for subscribers management, newsletter management, settings and subscription widget.
* added new WordPress 3.3 plupload used when possible to use.
* added mail-tester.com integration for Premium (fight against spam).
* added DKIM signature for Premium to improve deliverability.
* added the possibility to preview your newsletter without images in visual editor.
* added background colors for blocks within the visual editor.
* added alternate background colors for automatic latest post widget.
* added possibility to add total number of subscribers in widget with shortcode.
* added widget option "Display label within for Email field".
* improved email rendering and email clients compatibility including the new Outlook 2013
* improved image upload with ssl.
* improved compatibility with access rights plugins like "Advanced Access Manager" or "User Role Editor".
* improved import system with clearer message.
* improved subscription widget, added security if there is no list selected.
* improved Auto newsletter edition, warning added before pausing it.
* improved popups for the visual editor (themes, images, add link,...)
* updated TinyMCE to latest version, the editor now reflects the newsletter styles
* compatibility with [Magic Action Box](http://wordpress.org/extend/plugins/magic-action-box/).
* fixed links style in headings.
* fixed no default value in optin form when JS disabled.
* fixed issue with automatic latest post widget where one article could appear more than once.

= 2.0.9.5 - 2012-08-15 =

* fixed post notification hook when post's status change from publish to draft and back to publish.
* fixed firewall 2 avoid troubles with image uploader automatically
* fixed problem of confirmation page on some servers when pretty links activated on wysijap post. Default is params link now.

= 2.0.9 - 2012-08-03 =

* improved debug mode with different level for different needs
* added logging function to monitor post notification process for instance
* improved send immediately post notification (in some case the trigger was not working... using different WordPress hook now)
* fixed post notification interface (step1 and step3) not compatible with WordPress lower than 3.3
* fixed issue when duplicating sent post notifications. You should not be able to copy a child email and then change it's type like an automatic newsletter etc...
* fixed zip format error when uploading your own theme (this error was happenning on various browsers)

= 2.0.8 - 2012-07-27 =

* added default style for subscription notification which was lost
* fixed php error on subscription form creation
* fixed php error on helper back

= 2.0.7 - 2012-07-21 =

* fixed strict error appearing on servers below php version 5.4
* fixed on export to a csv translate fields and don't get the columns namekeys
* added non translated 'Loading...' string on subscription's frontend

= 2.0.6 - 2012-07-20 =

* fixed unreliable WP_PLUGIN_URL when dealing with https constants now using plugins_url() instead
* fixed automatic newsletter resending itself on unsubscribe
* fixed when unsubscribing and registering to some lists, you will not be re-registered to your previous lists
* fixed issue with small height images not displaying in email
* fixed issue with post excerpt in automatic posts
* improved php 5.4 strictness compatibility

= 2.0.5 - 2012-07-13 =

* added extended check of caching plugin activation
* added security to disallow directory browsing
* added subscription form working now with Quick-cache and Hyper cache(Already working with WP Super Cache && W3 Total Cache)
* added onload attribute on iframe subscription form which seems more reliable
* added independant cron manager wysija_cron.php
* added cleaning the queue of deleted users or deleted emails through phpmyadmin for instance
* added theme menu erasing MailPoet's menu when in the position right below ours

= 2.0.4 - 2012-07-05 =

* added for dummies check that list exists or subscription form widget not editable
* fixed problem with plugin wordpress-https when doing ajax subscription
* fixed issue with scheduled articles not being sent in post notification
* fixed rare issue when inserting a WordPress post would trigger an error
* fixed issue wrong count of ignored emails when importing
* fixed multi forms several send confirmation emails on one subscribing request
* fixed subject title in email template

= 2.0.3 - 2012-06-26 =

* fixed theme activation not working
* fixed google analytics code on iframe subscription forms
* fixed post notification bug with wrong category selected when fetching articles
* fixed issue regarding category selection in auto responder / post notifications
* fixed dollar sign being stripped in post titles
* fixed warning and notices when adding a list
* fixed on some server unsubscribe page or confirmation page redirecting to 404
* improved iframe system works now with short url and multiple forms

= 2.0.2 - 2012-06-21 =

* fixed missing title on widget when cache plugin activated
* fixed update procedure to MailPoet version "2.0" failed! on some MySQL servers
* fixed W3C validation for subscription form with empty action: replace with #wysija
* fixed forbidden iframe subfolder corrected to a home url with params
* improved theme installation with PclZip
* fixed missing previously sent auto newsletter on newsletters page
* fixed broken url for images uploaded in WordPress 3.4
* fixed "nl 2 br" on unsubscribed notification messages for admins
* added meta noindex on iframe forms to avoid polluting Google Analytics
* added validation of lists on subscription form
* fixed issue with image alignment in automatic newsletters
* fixed url & alternative text encoding in header/footer
* fixed images thumbs not displaying in Images tab
* fixed popups' CSS due to WordPress 3.4 update
* fixed issues when creating new lists from segment

= 2.0.1 - 2012-06-16 =

* fixed subscribers not added to the lists on old type of widget

= 2.0 - 2012-06-15 =

* Added post notifications
* Added auto responders
* Added scheduling (send in future)
* allow subscribers to select lists
* embed subscription form outside your WordPress site (find code in the widget)
* Subscription forms compatibility with W3 Total Cache and WP Supercache
* Load social bookmarks from theme automatically
* Several bug fixes and micro improvements
* Ability to send snail mail

= 1.1.5 - 2012-05-21 =

* improved report after importing csv
* fixed Warning: sprintf() /helpers/back.php on some environnements
* fixed roles for creating newsletters or managing subscribers "parent roles can edit as well as child roles if a child role is selected"
* fixed cron MailPoet's frequencies added in a cleaner way to avoid conflict with other plugins
* fixed w3c validation on confirmation and unsubscription page
* improved avoiding duplicates on environment with high sending frequencies
* removed php show errors lost in resolveConflicts

= 1.1.4 - 2012-05-14 =

* added last name to recipient name in header
* fixed automatic redirection for https links in newsletter
* fixed conflict with Advanced Custom Fields (ACF) plugin in the newsletter editor
* fixed conflict with the WpToFacebook plugin
* fixed validation on import of addresses with trim
* fixed dysfunctional unsubscribe link when Google Analytics campaign inserted
* added alphanumeric validation on Google Analytics input
* display clicked links in stats without Google Analytics parameters
* fixed page/post newsletter subscription widget when javascript conflict returns base64 string
* fixed WP users synch when subscriber with same email already exists
* fixed encoded url recorded in click stats
* added sending status In Queue to differentiate with Not Sent
* fixed automatic bounce handling
* added custom roles and permissions

= 1.1.3 - 2012-03-31 =

* fixed unsubscribe link redirection
* fixed rare issue preventing Mac users from uploading images
* added Norwegian translation
* added Slovak translation

= 1.1.2 - 2012-03-26 =

* fixed automatically recreates the subscription page when accidentally deleted
* fixed more accurate message about folder permissions in wp-content/uploads
* fixed possibility to delete synchronisable lists
* fixed pagination on subscribers lists' listing
* fixed google analytics tracking code
* fixed relative path to image in newsletter now forced to absolute path
* fixed widget alignment when labels not within field default value is now within field
* fixed automatic bounce handling error on some server.
* fixed scripts enqueuing in frontend, will print as long as there is a wp_footer function call in your theme
* fixed theme manager returns error on install
* fixed conflict with the SmallBiz theme
* fixed conflict with the Events plugin (wp-events)
* fixed conflict with the Email Users plugin (email-users)
* fixed outlook 2007 rendering issue

= 1.1.1 - 2012-03-13 =

* fixed small IE8 and IE9 compatibility issues
* fixed fatal error for new installation
* fixed MailPoet admin white screen on wordpress due to get_current_screen function
* fixed unsubscribe link disappearing because of qtranslate fix
* fixed old separators just blocked the email wizard
* fixed unsubscribe link disappearing because of default color
* fixed settings panel redirection
* fixed update error message corrected :"An error occurred during the update" sounding like update failed even though it succeeded
* fixed rendering of aligned text
* fixed daily report email information
* fixed export: first line with comma, the rest with semi colon now is all semi colon
* fixed filter by list on subscribers when going on next pages with pagination
* fixed get_avatar during install completely irrelevant
* fixed wordpress post in editor when an article had an image with height 0px
* fixed when domain does not exist, trying to send email, we need to flag it as undelivered after 3 tries and remove it from the queue
* fixed user tags [user:firstname | default:subscriber] left over when sent through queue and on some users
* fixed get_version when wp-admin folder doesn't exist...
* fixed Bulk Unsubscribe from all list "why can't I add him"

= 1.1 - 2012/03/03 =

* support for first and last names
* 14 new themes. First Premium themes
* added social bookmarks widget
* added new divider widget
* added first name and last name feature in subscription form, newsletter content and email subject
* header is now image only and not text/image
* small changes in Styles tab of visual editor
* new full width footer image area (600px)
* added transparency feature to header, footer, newsletter
* newsletter width for content narrowed to 564px
* improved line-height for titles in text editor
* fixed Outlook and Hotmail padding issue with images
* improved speed of editor
* possibility to import automatically and keep in sync lists from all major plugins: MailPress, Satollo, WP-Autoresponder, Tribulant, Subscribe2, etc.
* possibility to change "Unsubscribe" link text in footer
* choose which role can edit subscribers
* preview of newsletter in new window and not in popup
* added possibility to choose between excerpt or full article on inserting WP post
* theme management with API. Themes are now externalized from plugin.
* removed numbered lists from text editor because of inconsistent display, notably Outlook

= 1.0.1 - 2012/01/18 =

* added SMTP TLS support, useful for instance with live.com smtp
* added support for special Danish chars in email subscriptions
* fixed menu position conflict with other themes and plugins
* fixed subscription form works with jquery 1.3, compatible for themes that use it
* fixed issue of drag & drop of WP post not working with php magic quotes
* fixed permissions issue. Only admins could use the plugin despite changing the permissions in Settings > Advanced.
* fixed display of successful subscription in widget displays better in most theme
* fixed synching of WordPress user registering through frontend /wp-login.php?action=register
* fixed redirection unsubscribe link from preview emails
* fixed cross site scripting security threat
* fixed pagination on newsletter statistics's page
* fixed javascript conflict with Tribulant's javascript's includes
* improved detection of errors during installation

= 1.0 - 2011/12/23 =
* Premium upgrade available
* fix image selector width in editor
* fix front stats of email when email preview and show errors all
* fix front stats of email when show errors all
* fix import ONLY subscribed from external plugins such as Tribulant or Satollo
* fix retrieve wp.posts when time is different on mysql server and apache server
* fix changing encoding from utf8 to another was not sending
* newsletter background colour now displays in new Gmail
* less confusing queue sending status
* updated language file (pot) with 20 or so modifications

= 0.9.6 - 2011/12/18 =
* fixed subscribe from a MailPoet confirmation page bug
* fixed campaigns "Column does not exists in model .."
* fixed address and unsubscribe links appearing at bottom of newsletter a second time
* fixed menu submenu no MailPoet but newsletters no js
* fixed bug statistics opened_at not inserted
* fixed bug limit subscribers updated on subscribers delete
* fixed daily cron scandir empty dir
* fixed subscribe from frontend without javascript error
* fixed subscribe IP server validation when trying in local
* fixed CSS issues with WordPress 3.3
* improving interface of email sending in the newsletter's listing
* added delete newsletter option
* added language pot file
* added french translation

= 0.9.2 - 2011/12/12 =
* fixed issue with synched users on multisite(each site synch its users only)
* fixed compatibility issue with wordpress 3.3(thickbox z-index)
* fixed issue with redundant messages after plugin import
* fixed version number display

= 0.9.1 - 2011/12/7 =
* fixed major issue with browser check preventing Safari users from using the plugin
* fixed issue with wp_attachment function affecting WordPress post insertion
* fixed issue when importing subscribers (copy/paste from Gmail)
* fixed issue related to WordPress MU
* minor bugfixes

= 0.9 - 2011/12/3 =
* Hello World.
