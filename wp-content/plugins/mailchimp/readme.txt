=== MailChimp List Subscribe Form ===
Contributors: MailChimp
Tags: mailchimp, email, newsletter, signup, marketing, plugin, widget
Requires at least: 2.8
Tested up to: 4.5
Stable tag: 1.5.7

== Description ==

Use the MailChimp List Subscribe plugin to quickly add a MailChimp signup form widget to your WordPress 2.8 or higher site. 

After installation, you’ll log in with your API key, select your MailChimp list, choose merge fields and groups, and add the widget to your site.  Typically, installation and setup will take about 5-10 minutes, and absolutely everything can be done via the WordPress Setting GUI, with no file editing at all.

WordPress.com compatibility is limited to Business tier users only. [How to add a signup form if you have a WordPress.com site](https://kb.mailchimp.com/lists/signup-forms/ways-to-add-a-signup-form-in-wordpress).

== Installation ==

This section describes how to install the plugin and get started using it.

= Version 2.8+ =
1. Unzip our archive and upload the entire mailchimp directory to your `/wp-content/plugins/ directory`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Navigate to **Settings** click **MailChimp Setup**.
4. Enter your MailChimp API Key and let the plugin verify it.
5. Select the list where you want to send new MailChimp subscribers.
6. Optional: turn **MonkeyRewards** on or off.
7. Optional: Turn **Merge Fields** and **Groups** on or off. Navigate to **Appearance**, and click **Widgets**. Drag the MailChimp Widget into one of your Widget Areas.


= Advanced =
If you have a custom coded sidebar or bells and whistles that prevent enabling widgets  through the WordPress GUI, complete these steps instead.

WordPress v2.8 or higher: 
` [mailchimpsf_form] `

If you are adding it inside a php code block, pop this in:

` mailchimpSF_signup_form(); `

Or, if you are dropping it in between a bunch of HTML, use this:

`<?php mailchimpSF_signup_form(); ?>`

Where ever you want it to show up.

Note: in some environments you will need to install the Exec_PHP plugin to use that method of display. It can be found here: http://wordpress.org/extend/plugins/exec-php/

== Upgrading ==

If you are upgrading to version 1.2.1 and you used the widget in your sidebar previously, all you need to do is drag the `MailChimp Widget` back into the sidebar, visit the MailChimp settings page (which will have maintained your prior settings), click the "Update List" button, and you're done!

== Internationalization (i18n) ==
Currently we have the plugin configured so it can be easily translated and the following languages supported:

* bg_BG - Bulgarian in Bulgaria (thanks to [SiteGround](http://www.siteground.com/wordpress-hosting.htm) for contributing)
* cs_CZ - Czech in the Czech Republic (thanks to [Peter Kahoun](http://kahi.cz/) for contributing)
* da_DK - Danish in Denmark (thanks to Jan Lund for contributing)
* de_DE - German in Germany (thanks to Michael Jaekel for contributing)
* el_GR - Modern Greek in Greece (thanks to Ιωάννης Δημοφέρλιας (John Dimoferlias) for contributing)
* en_US - English in the U.S.
* es_CL - Spanish in Chile (thanks to Tomás Nader for contributing)
* es_ES - Spanish in Spain (thanks to [Claudia Mansilla](http://cricava.com/) for contributing)
* et_ET - Estonian in Estonia (thanks to [Helen Urbanik](http://www.motomaania.ee/) for contributing)
* fr_FR - French in France (thanks to [Maxime Toulliou](http://www.maximetoulliou.com/) for contributing)
* he_IL - Hebrew in Israel (thanks to [שגיב בית](http://www.sagive.co.il) for contributing)
* hu_HU - Hungarian in Hungary (thanks to Okostobi for contributing)
* it_IT - Italian in Italy (thanks to [Stefan Des](http://www.stefandes.com) for contributing)
* ko_KR - Korean (thanks to 백선기 (SK Baek)  for contributing)
* nb_NO - Norwegian (thanks to [Alexander Roterud aka Defrag](http://www.tigerpews.com) for contributing)
* nl_BE - Dutch (thanks to [Filip Stas](http://suddenelfilio.net/) for contributing)
* pt_BR - Portuguese in Brazil (thanks to Maria Manoela Porto for contributing)
* pt_PT - Portuguese in Portugal (thanks to [Tiago Faria](http://xroot.org) for contributing)
* ro_RO - Romanian in Romania (thanks to Alexandru Armin Roșu for contributing)
* ru_RU - Russian in the Russian Federation (thanks to [Илья](http://fatcow.com) for contributing)
* sv_SE - Swedish in Sweden (thanks to [Sebastian Johnsson](http://www.agiley.se/) for contributing)
* tr_TR - Turkish in Turkey (thanks to [Hakan E.](http://kazancexpert.com/) for contributing)

If your language is not listed above, feel free to create a translation. Here are the basic steps:

1. Copy "mailchimp_i18n-en_US.po" to "mailchimp_i18n-LANG_COUNTRY.po" - fill in LANG and COUNTRY with whatever you use for WPLANG in wp-config.php
2. Grab a transalation editor. [POedit](http://www.poedit.net/) works for us
3. Translate each line - if you need some context, just open up mailchimp.php and search for the line number or text
4. [Fork](http://help.github.com/fork-a-repo/) the [repository on github](https://github.com/crowdfavorite/wp-mailchimp)
5. [Clone](http://help.github.com/remotes/#clone) the _develop_ branch
6. Add the appropriately named files to the /po/ directory and edit the /readme.txt to include how you'd like to be attributed
7. Make a [pull request](http://help.github.com/send-pull-requests/)

== Screenshots ==

1. Entering your MailChimp login info
2. Selecting your MailChimp list
3. Configuring your Signup Form display format (optional)
4. Configuring extra fields on your Signup Form (optional)
5. An example Signup Form Widget

== Upgrade Notice ==

= 1.5.5 =
If you are updating from v1.4.x, you will need to re-authorize with an API key.

= 1.5 =
Updates the MailChimp API version, adds double/single opt-in toggle.

= 1.4.2 =
add customized wp_nonces functions for post-back behavior to fix 4.0 callbacks

= 1.4.1 =
Fix for checkbox weirdness on 3.8

= 1.4 =
Added Developer Mode "Kitchen Sink" to aid in styling without having to authenticate a MailChimp account.

= 1.3 =
Now using OAuth flow within plugin for user authentication

Admin UI refresh

= 1.2.11 =
Merged pull request from https://github.com/headshift/wp-mailchimp adding additional translation strings.

= 1.2.10 =
Fixed submission error when apostrophes are present

= 1.2.8=
Fixes bug where entire phone numbers were only being deposited in the area code portion

= 1.2.6 =
Fixes major bug with "Settings" link on Plugins screen.

= 1.2.5 =
Added support for multiple interest groups, field formatting based on type and date picker.

== Changelog ==
= 1.5.7 =
* Fix undefined variable notice.
* Fix HTML submission message.

= 1.5.6 =
* Fixes short array notation which caused a fatal error in older PHP versions.

= 1.5.5 =
* Fix timeout error on activation.

= 1.5.4 =
* Set optional value for API wrapper.

= 1.5.3 =
* Fix PHP7 compatibility issue
* Cut down on size of API requests for users with large lists.
* Fix CSS issue on removing MailChimp style.

= 1.5.2 =
* General bugfixes for merge fields.
* When reinitializing, update merge field values.

= 1.5.1 =
* Bugfix for fatal error in MailChimp lib

= 1.5 =
* Upgrade to MailChimp API v3.0
* Remove OAuth2 middle layer and use MailChimp API keys
* Include double/single opt-in toggle.

= 1.4.1 =
* Update styles to be compatible with upcoming 3.8 wp-admin changes

= 1.4 =
* Developer Mode "Kitchen Sink" takes over plugin for local development
* Developer Mode has filters of changable content
* Fix bug related to required US phone validation

= 1.3 =
* Now using OAuth flow for user authentication
* Admin UI refresh

= 1.2.14 =
* Add link to edit profile within error when duplicate signup occurs

= 1.2.13 =
* Fixed bug preventing address fields from submitting correctly.

= 1.2.12 =
* Update spanish language files (es_ES and es_MX)

= 1.2.9 =
* Fixed bug where multiple checkbox type interest groups were returning an invalid error
* Fixed bug where assets were not enqueueing properly if the plugin directory was not set to 'mailchimp'. Now supports any directory name.

= 1.2.8 =
* Fixed bug where entire phone numbers were only being deposited in the area code portion

= 1.2.7 =
* CSS should now always load correctly on the front end
* Adding Hebrew and Romanian language support
* Updating translation contribution instructions
* Tested version is now 3.3.1

= 1.2.6 =
* Fixed bug with "Settings" link appearing on all plugins (props Jason Lane)
* Resolved issue with unnecessary calls to the MailChimp API during upgrade check
* Resolved PHP warning when there weren't any interest groups

= 1.2.5 =
* Field formatting based on type
* Support for multiple interest groups (a data upgrade procedure must be run by visiting the WordPress dashboard)
* Added jQuery datepicker option to be used with dates.
* Added a handful of new translations
* Fixing various PHP notices and deprecated functions (props Jeffry Ghazally)

= 1.2.4 =
* Version bump for proper listing on wordpress.org

= 1.2.3 =
* Change mailchimpSF_where_am_i() to use plugins_url() in place of WP_PLUGIN_URL to take SSL into account when delivering assets (props John LeBlanc)
* Update MCAPI wrapper to bring back PHP4 support (note: PHP 5.2 to be required starting with WordPress 3.2)

= 1.2.2 =
* Change MCAPI wrapper to use a more unique class name, v1.3 of the API, and a much lighter client library

= 1.2.1 =
* Fixed internationalization path bug.
* Fixed instances where i18n functions weren't necessary in admin.
* Added more strings to be translated.

= 1.2 =
* Recommended Upgrade, please see "Upgrading" section of readme.
* Security and various other improvements

