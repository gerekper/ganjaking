=== Advanced WordPress Reset ===
Contributors: symptote
Donate Link: http://www.sigmaplugin.com/donation
Tags: database, reset database, reset, clean, restore
Requires at least: 4.0
Tested up to: 5.5
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Reset and restore your WordPress database back to its first original status, just like if you make a fresh installation.

== Description ==

Notice: If you are looking for cleaning up your database, use instead our plugin: <a href="https://wordpress.org/plugins/advanced-database-cleaner" target="_blank">Advanced Database Cleaner</a>

'Advanced WordPress reset' plugin will reset you WordPress Database back to its first original status in order to make a fresh installation without going through WordPress traditional installation. This plugin will help you saving time especially if you are a developer and you have to install WordPress from scratch every time.

= Main Features =
* Make a new installation without going through the 5 minutes WordPress installation
* Resets the database without deleting or modifying any of your files (all your WordPress, plugins and themes files are kept)
* Deletes all database customizations made by plugins and themes
* Deletes all content including post, pages, options, etc.
* Detects the Admin user and recreate it with its saved password
* Keeps the blog name after the reset
* Quick and convenient

= Multisite Support =
* Actually the plugin does not support Multisite installation. We will add it as soon as possible.

== Installation ==

This section describes how to install the plugin and get it working.

= Single site installation =
* After extraction, upload the Plugin to your `/wp-content/plugins/` directory
* Go to "Dashboard" &raquo; "Plugins" and choose 'Activate'
* The plugin page can be accessed via "Dashboard" &raquo; "Tools" &raquo; "Advanced WP reset"

== Screenshots ==

1. Admin page of the plugin

== Changelog ==

= 1.1.1 - 17/09/2020 =
- Tweak: enhancing the JavaScript code
- Tweak: we are now using SweetAlert for all popup boxes
- Tweak: enhancing some blocks of code
- Tested with WordPress 5.5

= 1.1.0 =
* Some changes to CSS style
* Changing a direct text to _e() for localization
* Test the plugin with WP 5.1

= 1.0.1 =
* The plugin is now Reactivated after the reset
* Adding "Successful Reset" message

= 1.0.0 =
* First release: Hello world!

== Frequently Asked Questions ==

= What does mean "reset my database"? =
This option will reset your WordPress database back to its first original status, just like if you make a new installation. That is to say, a clean installation without any content or customizations

= Is it safe to reset my database? =
Yes, it is safe since you have no important content to lose. If there are any issues, we will support you :)

= Is there any files that will be deleted after the reset? =
No. All files are kept as they are. The plugin does not delete or modify any of your files.

= Is there any plugins or themes that will be deleted after the reset? =
No. All your plugins and themes will be kept. However you will lose any settings in database of those plugins/themes.

= Is this plugin compatible with multisite? =
No, it is not compatible with multisite. We will try to fix this compatibility as soon as possible.

= Is this plugin compatible with SharDB, HyperDB or Multi-DB? =
Actually the plugin is not supposed to be compatible with SharDB, HyperDB or Multi-DB.