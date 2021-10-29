=== Mute Screamer ===
Contributors: ampt
Tags: phpids, intrusion detection, security, ids, wordpress phpids, xss, sql injection, csrf
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: trunk

PHPIDS for Wordpress

== Description ==

[Mute Screamer](http://ampt.github.com/mute-screamer/) uses [PHPIDS](http://phpids.org/) to detect attacks on your Wordpress site and react in a way that you choose.

Requires PHP 5.2 or higher.

Features:

* View attack logs. Go to Dashboard -> Intrusions
* Send alert emails
* Configure PHPIDS exceptions, html and json fields
* Display a warning page
* Log users out of WP Admin
* Auto updates of default_filter.xml and Converter.php from phpids.org
* Auto update shows a diff of changes to be updated
* Ban client when attack is over the ban threshold
* Ban client when attack exceeds the repeat attack limit
* Display ban template and message

Translations:

* Spanish by David Perez - [Closemarketing Diseño Web](http://www.closemarketing.es/)


== Installation ==

Use automatic installer or:

1. Copy the mute-screamer folder into /wp-content/plugins
2. Activate Mute Screamer via the plugins page in the WordPress admin
3. Go to Settings -> Mute Screamer to configure

== Screenshots ==

1. Attack logs
2. Auto update diff

== Changelog ==

= 1.0.7 =

* Spanish translsation

= 1.0.6 =

* Latest PHPIDS rules and converter
* Change the update feed

= 1.0.5 =

* Latest PHPIDS rules and converter
* Fix update fetch error

= 1.0.4 =

* Latest PHPIDS rules and converter
* Update default exceptions
* Option to disable automatic updates for default_filter.xml and Convertor.php
* Bulk exclude intrusions
* Update to PHPIDS 0.7
* Option to disable intrusion logging
* WordPress 3.3 compatibility updates
* Add unit tests

= 1.0.3 =

* Latest PHPIDS rules and converter
* Fix email logger tmp file cleanup
* Twenty Eleven 500 template
* Add password fields to default exceptions
* Minor bug fixes

= 1.0.2 =

* Fix PHPIDS feed

= 1.0.1 =

* Fix PHPIDS updater
* Latest PHPIDS rules and converter
* Display correct update count in adminbar
* Fix intrusion search escaping
* Fix diff table rendering issues
* Fix upload_dir undefined index
* Additional output escaping

= 1.0.0 =

* Latest rules and converter
* Add comments field to default exceptions
* Fix local file path leakage
* Gracefully handle requirements
* Fix bulk actions on intrusions page
* i18n support
* Add delete & exclude actions to intrusions page
* Add contextual help
* Email throttling
* Update 500 error template
* Update phpids update url
* ip banning
* Fix admin footer in update process
* Fix redirect loop
* Run PHPIDS on wp-login.php
* Don't run the update routine on plugin (de)activation

= 0.59 =

* Update to PHPIDS 0.6.5

= 0.58 =

* Automatic updates of default_filter.xml and Converter.php from phpids.org
* Show a diff of changes during the auto update process
* Remove source column from intrusions list

= 0.32 =

* Fix where Mute Screamer would only allow bulk actions to run on Mute Screamer's intrusions page

= 0.31 =

* Requires PHP 5.2+
* Better IP detection
* Updated default exceptions
* Option to disable IDS in WP Admin
* Latest PHPIDS rules and converter
* Display a custom warning page to the user
* Bug: strip slashes of value before insertion into the database

= 0.22 =

* Fix missing fields in database logger

= 0.2 =

* Use wpdb instead of PDO for database logging
* Show new instrusions count badge in dashboard menu

= 0.17 =

* Fix search results for intrusions
* Fix empty exception fields

= 0.16 =

* Fix default exceptions list
* Merge existing options on activation

= 0.1 =

* Initial release
