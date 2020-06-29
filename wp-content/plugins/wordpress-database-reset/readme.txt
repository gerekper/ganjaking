=== WP Database Reset ===
Contributors: WebFactory, wpreset, underconstructionpage, googlemapswidget
Tags: database, reset, restore, database reset, wp reset, developer, development
Requires at least: 4.2
Requires PHP: 5.2
Tested up to: 5.4
Stable tag: 3.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Skip reinstalling WP to reset it & reset the WordPress database back to its original state with 1-click.

== Description ==

The WordPress Database Reset plugin allows you to **reset the database** (all tables or the ones you choose) back to its default settings without having to go through the WordPress 5 minute installation or having to modify any files.

> Need more reset tools? To individually reset plugins, themes, transients or media? Or perhaps database snapshots so you can restore your WP site with one click if you made a mistake? Then check out our free sister plugin - <a href="https://wordpress.org/plugins/wp-reset/">WP Reset</a>.

**Features**

* Extremely fast one click process to reset the WordPress database tables
* Choose to reset the entire database or specific database tables
* Secure and super simple to use
* Safe - it's not possible to accidentally click reset
* Prefer the command line? Reset the database in one command
* Excellent for theme and plugin developers who need to clean the database of any unnecessary content

**Command Line**

Once activated, you can use the WordPress Database Reset plugin with [WordPress CLI](http://wp-cli.org/). List of WP-CLI commands:

Reset all tables:

`wp reset database`

Specify a list of tables to reset:

`wp reset database --tables='users, posts, comments, options'`

The current theme and plugins will be reactivated by default. You can disable them like so:

`wp reset database --no-reactivate`

List table names:

`wp reset list`


**Support**

Create a new post in the [WordPress Database Reset support forum](https://wordpress.org/support/plugin/wordpress-database-reset).

**Want to help out?**

* Provide new <a href="https://translate.wordpress.org/projects/wp-plugins/wordpress-database-reset">language translations</a>
* Want to help others that might be having issues? [Answer questions on the support forum](https://wordpress.org/support/plugin/wordpress-database-reset).
* Rate the plugin - <a href="https://wordpress.org/support/plugin/wordpress-database-reset/reviews/#new-post">rate it</a>


WP Database Reset was originally developed in October 2011 by <a href="https://github.com/chrisberthe">Chris Berthe</a>. Please do not send him any support questions. If you need assistance the <a href="https://wordpress.org/support/plugin/wordpress-database-reset/">official forum</a> is the best and fastest way to get it.


== Screenshots ==
1. The WP Database Reset plugin page

== Changelog ==

= 3.15 =
* 2020/01/14
* security fixes - thanks to Chloe from Wordfence
* 80k installations; 761,300 downloads

= 3.1 =
* 2019/08/13
* WebFactory took over development
* minor improvements in GUI and messaging
* fixed a nasty bug related to admin user ID not being 1
* 70k installations; 665,500 downloads

= 3.0.2 =
* Fix for plugin page not showing up in tools menu (on some hosting providers)
* Update how session tokens were being restored
* Remove unnecessary nonce
* Bump 'requires at least' to version 4.2
* Change 'theme_data' to 'theme_plugin_data'

= 3.0.1 =
* Fix plugin disabled after update, thanks to Ulrich Pogson
* Update the pot file

= 3.0.0 =
* Completely re-written from scratch
* Add extended WP_CLI command class
* Clean up admin interface
* Remove unnecessary help tabs
* Submit button is now deactivated until user inputs security code
* Add PayPal donation button
* Remove outdated localization files
* Update the text domain to match slug for translate.wordpress.org

= 2.3.2 =
* Add option to keep active theme, thanks to Ulrich Pogson
* Adhere to WordPress PHP coding syntax standards
* Delete the user session and recreate it
* Separate the backup_tables method into two new methods
* Reset only WP tables and not custom tables
* French language updates, thanks to Fx Benard
* Fix for undefined variable: backup_tables

= 2.3.1 =
* Fixed bug where reactivate plugins div was not displaying on 'options' table select

= 2.3 =
* Removed deprecated function $wpdb->escape(), replaced with esc_sql()
* Add German translation, thanks to Ulrich Pogson
* Updated screenshot-1.png
* Renamed default localization file
* Fixed broken if conditional during code clean up for version 2.2

= 2.2 =
* Fixed scripts and styles to only load on plugin page
* Formatted code to meet WordPress syntax standards

= 2.1 =
* Replaced 3.3 deprecated get_userdatabylogin() with get_user_by()
* Updated deprecated add_contextual_help() with add_help_tab()
* Small change in condition check for backup tables
* Removed custom _rand_string() with core wp_generate_password()
* Added Portuguese translation - thanks to Fernando Lopes

= 2.0 =
* Added functionality to be able to select which tables you want to reset, rather than having to reset the entire database.
* Added bsmSelect for the multiple select.
* Modified screenshot-1.png.
* Fixed redirect bug
* 'Reactivate current plugins after reset' only shows if the options table is selected from the dropdown.

= 1.4 =
* Made quite a few changes to the translation files
* Renamed French translation file for plugin format, not theme format
* Optimized (until potential version 2.0)

= 1.3 =
* Replaced reactivation option for all currently active plugins (not just this plugin)
* Updated language files

= 1.2 =
* Added capability to manually select whether or not plugin should be reactivated upon reset
* Modified class name to avoid potential conflicts with WordPress core
* Modified wp_mail override
* Removed deprecated user level for WordPress 3.0+
* Fixed small bug where if admin user did not have admin capabilities, it would tell the user they did

= 1.0 =
* First version
* 2011-10-04
