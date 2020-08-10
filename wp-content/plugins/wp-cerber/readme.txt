=== Cerber Security, Anti-spam & Malware Scan ===
Contributors: gioni
Tags: security, malware scanner, antispam, firewall, limit login attempts, custom login url, login, recaptcha, captcha, activity, log, logging, whitelist, blacklist, access list
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SQ5EC8WQP654Q&source=url
Requires at least: 4.9
Requires PHP: 5.6
Tested up to: 5.5
Stable tag: 8.6.6
License: GPLv2

Protection against hacker attacks and bots. Malware scanner & integrity checker. User activity log. Antispam reCAPTCHA. Limit login attempts.

== Description ==

Defends WordPress against hacker attacks, spam, trojans and malware.
Mitigates brute force attacks by limiting the number of login attempts through the login form, XML-RPC / REST API requests or using auth cookies.
Tracks user and bad actors activity with flexible email, mobile and desktop notifications.
Stops spam by using a specialized Cerber's anti-spam engine and Google reCAPTCHA to protect registration, contact and comments forms.
Advanced malware scanner, integrity checker and file monitor.
Hardening WordPress with a set of flexible security rules and sophisticated security algorithms.
Restricts access with Black and White IP Access Lists.

**Features you will love**

* Limit login attempts when logging in by IP address or entire subnet.
* Monitors logins made by login forms, XML-RPC requests or auth cookies.
* Permit or restrict access by [White IP Access list and Black IP Access List](https://wpcerber.com/using-ip-access-lists-to-protect-wordpress/) with a single IP, IP range or subnet.
* Create **Custom login URL** ([rename wp-login.php](https://wpcerber.com/how-to-rename-wp-login-php/)).
* Cerber anti-spam engine for protecting contact and registration forms.
* Automatically detects and moves spam comments to trash or denies them completely.
* [Manage multiple WP Cerber instances from one dashboard](https://wpcerber.com/manage-multiple-websites/).
* [Two-Factor Authentication for WordPress](https://wpcerber.com/two-factor-authentication-for-wordpress/).
* Logs users, bots, hacker and other suspicious activities.
* Security scanner verifies the integrity of WordPress files, plugins and themes.
* Monitors file changes and new files with email notifications and reports.
* [Mobile and email notifications with a set of flexible filters](https://wpcerber.com/wordpress-notifications-made-easy/).
* Advanced users' sessions manager
* Protects wp-login.php, wp-signup.php and wp-register.php from attacks.
* Hides wp-admin (dashboard) if a visitor isn't logged in.
* Immediately blocks an intruder IP when attempting to log in with non-existent or prohibited username.
* Restrict user registration or login with a username matching REGEX patterns.
* [Restrict access to WP REST API with your own role-based security rules](https://wpcerber.com/restrict-access-to-wordpress-rest-api/).
* Block access to WordPress REST API completely.
* Block access to XML-RPC (block access to XML-RPC including Pingbacks and Trackbacks).
* Disable feeds (block access to the RSS, Atom and RDF feeds).
* Restrict access to XML-RPC, REST API and feeds by **White IP Access list** by an IP address or an IP range.
* [Authorized users only mode](https://wpcerber.com/only-logged-in-wordpress-users/)
* [Block a user account](https://wpcerber.com/how-to-block-wordpress-user/).
* Disable automatic redirection to the hidden login page.
* **Stop user enumeration** (blocks access to author pages and prevents user data leaks via REST API).
* Proactively **blocks IP subnet class C**.
* Anti-spam: **reCAPTCHA** to protect WordPress login, register and comment forms.
* [reCAPTCHA for WooCommerce & WordPress forms](https://wpcerber.com/how-to-setup-recaptcha/).
* Invisible reCAPTCHA for WordPress comments forms.
* A special Citadel mode for **massive brute force attacks**.
* [Play nice with fail2ban](https://wpcerber.com/how-to-protect-wordpress-with-fail2ban/): write failed attempts to the syslog or a custom log file.
* Filter out and inspect activities by IP address, user, username or a particular activity.
* Filter out activities and export them to a CSV file.
* Reporting: get weekly reports to specified email addresses.
* Limit login attempts works on a site/server behind a reverse proxy.
* [Be notified via mobile push notifications](https://wpcerber.com/wordpress-mobile-and-browser-notifications-pushbullet/).
* Trigger and action for the [jetFlow.io automation plugin](http://jetflow.io).
* Protection against (DoS) attacks (CVE-2018-6389).

= Limit login attempts done right =

By default, WordPress allows unlimited login attempts through the login form, XML-RPC or by sending special cookies. This allows passwords to be cracked with relative ease via brute force attack.

WP Cerber blocks intruders by IP or subnet from making further attempts after a specified limit on retries is reached, making brute force attacks or distributed brute force attacks from botnets impossible.

You will be able to create a **Black IP Access List** or **White IP Access List** to block or allow logins from a particular IP address, IP address range or a subnet any class (A,B,C).

Moreover, you can create your Custom login page and forget about automatic attacks to the default wp-login.php, which takes your attention and consumes a lot of server resources. If an attacker tries to access wp-login.php they will be blocked and get a 404 Error response.

= Malware scanner =

Cerber Security Scanner is a sophisticated and extremely powerful tool that thoroughly scans every folder and inspects every file on a website for traces of malware, trojans, backdoors, changed and new files.

[Read more about the malware scanner](https://wpcerber.com/wordpress-security-scanner/).

= Integrity checker =

The scanner checks if all WordPress folders and files match what exist in the official WordPress core repository, compares your plugins and themes with what are in the official WordPress repository and alerts you to any changes. As with scanning free plugins and themes, the scanner scans and verifies commercial plugins and themes that are installed manually.

= Scheduled Scans With Automatic File Recovery =

Cerber Security Scanner allows you to configure a schedule for automated recurring scanning easily. Once the schedule is configured the scanner automatically scans the website, deletes malware and recovers modified and infected WordPress files. After every scan, you can get an optional email report with the results of the scan.

[Read more about the scheduled scans](https://wpcerber.com/automated-recurring-malware-scans/).

= Two-Factor Authentication =

Two-Factor Authentication (2FA) provides an additional layer of security requiring a second factor of identification beyond just a username and password. When 2FA is enabled on a website, it requires a user to provide an additional verification code when signing into the website. This verification code is generated automatically and sent to the user by email.

= Log, filter out and export activities =

WP Cerber tracks time, IP addresses and usernames for successful and failed login attempts, logins, logouts, password changes, blocked IP and actions taken by itself. You can export them to a CSV file.

= Limit login attempts reinvented =

You can **hide WordPress dashboard** (/wp-admin/) when a user isn't logged in. If a user isn't logged in and they attempt to access the dashboard by requesting /wp-admin/, WP Cerber will return a 404 Error.

Massive botnet brute force attack? That's no longer a problem. **Citadel mode** will automatically be activated for awhile and prevent your site from making further attempts to log in with any username.

= Cerber anti-spam engine =

Anti-spam and anti-bot protection for contact, registration, comments and other forms.
WP Cerber anti-spam and bot detection engine now protects all forms on a website. No reCAPTCHA is needed.
It’s compatible with virtually any form you have. Tested with Caldera Forms, Gravity Forms, Contact Form 7, Ninja Forms, Formidable Forms, Fast Secure Contact Form, Contact Form by WPForms.

= Anti-spam protection: invisible reCAPTCHA for WooCommerce =

* WooCommerce login form
* WooCommerce register form
* WooCommerce lost password form

= Anti-spam protection: invisible reCAPTCHA for WordPress =

* WordPress login form
* WordPress register form
* WordPress lost password form
* WordPress comment form

= Integration with Cloudflare =

A [special Cloudflare add-on for WP Cerber](https://wpcerber.com/cloudflare-add-on-wp-cerber/) keeps in sync the list of blocked IP addresses with Cloudflare IP Access Rules.

**Stay in compliance with GDPR**

How to get full control of personal data to be in compliance with data privacy laws such as GDPR in Europe or CCPA in California.

* [Personal data export feature](https://wpcerber.com/export-personal-data/)
* [Personal data erase feature](https://wpcerber.com/delete-personal-data/)
* [How WP Cerber processes browser cookies](https://wpcerber.com/browser-cookies-set-by-wp-cerber/)

**Documentation & Tutorials**

* [Configuring Two-Factor Authentication](https://wpcerber.com/two-factor-authentication-for-wordpress/)
* [How to set up notifications](https://wpcerber.com/wordpress-notifications-made-easy/)
* [Push notifications with Pushbullet](https://wpcerber.com/wordpress-mobile-and-browser-notifications-pushbullet/)
* [How to set up invisible reCAPTCHA for WooCommerce](https://wpcerber.com/how-to-setup-recaptcha/)
* [Changing default plugin messages](https://wpcerber.com/wordpress-hooks/)
* [2FA alternatives to the Clef plugin](https://wpcerber.com/two-factor-authentication-plugins-for-wordpress/)
* [Why reCAPTCHA does not protect WordPress from bots and brute-force attacks](https://wpcerber.com/why-recaptcha-does-not-protect-wordpress/)

**Translations**

* Czech, thanks to [Hrohh](https://profiles.wordpress.org/hrohh/)
* Deutsche, thanks to mario, Mike and [Daniel](http://detacu.de)
* Dutch, thanks to Jos Knippen and [Bernardo](https://twitter.com/bernardohulsman)
* Français, thanks to [hardesfred](https://profiles.wordpress.org/hardesfred/)
* Norwegian (Bokmål), thanks to [Eirik Vorland](https://www.facebook.com/KjellDaSensei)
* Portuguese (Portugal), thanks to Helderk
* Portuguese (Brazil), thanks to [Felipe Turcheti](http://felipeturcheti.com)
* Spanish, thanks to Ismael Murias and [leemon](https://profiles.wordpress.org/leemon/)
* Український, thanks to [Nadia](https://profiles.wordpress.org/webbistro)
* Русский, thanks to [Yui](https://profiles.wordpress.org/fierevere/)
* Italian, thanks to [Francesco Venuti](http://www.algostream.it/)
* Swedish, thanks to Fredrik Näslund

Thanks to [POEditor.com](https://poeditor.com) for helping to translate this project.

There are some semi-similar security plugins you can check out: Login LockDown, Login Security Solution,
BruteProtect, Ajax Login & Register, Lockdown WP Admin, Loginizer,
BulletProof Security, SiteGuard WP Plugin, All In One WP Security & Firewall, Brute Force Login Protection

**Another reliable plugins from the trusted author**

* [Plugin Inspector reveals issues with installed plugins](https://wordpress.org/plugins/plugin-inspector/)

Checks plugins for deprecated WordPress functions, known security vulnerabilities, and some unsafe PHP functions

* [Translate sites with Google Translate Widget](https://wordpress.org/plugins/goo-translate-widget/)

Make your website instantly available in 90+ languages with Google Translate Widget. Add the power of Google automatic translations with one click.

== Installation ==

Installing the WP Cerber Security plugin is the same as other WordPress plugins.

1. Install the plugin through Plugins > Add New > Upload or unzip plugin package into wp-content/plugins/.
2. Activate the WP Cerber through the Plugins > Installed Plugins menu in the WordPress admin dashboard.
3. The plugin is now active and has started protecting your WordPress with default settings.
4. Make sure, that you've got a notification letter to your site admin email.
5. It's advised to enable Standard mode for the "Load security engine" setting.
5. Read carefully: [Getting Started Guide](https://wpcerber.com/getting-started/)

**Important notes**

1. Before enabling invisible reCAPTCHA, you must obtain separate keys for the invisible version. [How to enable reCAPTCHA](https://wpcerber.com/how-to-setup-recaptcha/).
2. If you want to test out plugin's features, do this on another computer (or incognito browser window) and remove computer IP address or network from the White Access List. Cerber is smart enough to recognize "the boss".
3. If you've set up the Custom login URL and you use some caching plugin like **W3 Total Cache** or **WP Super Cache**, you have to add the new Custom login URL to the list of pages not to cache.
4. [Read this if your website is under CloudFlare](https://wpcerber.com/cloudflare-and-wordpress-cerber/)
5. If you use the Jetpack plugin or another plugin that needs to connect to wordpress.com, you need to unlock XML-RPC. To do that go to the Hardening tab, uncheck Disable XML-RPC, and click the Save changes button.

The following steps are optional but they allow you to reinforce the protection of your WordPress.

1. Fine tune **Limit login attempts** settings making them more restrictive according to your needs
2. Configure your **Custom login URL** and remember it (the plugin will send you an email with it).
3. Once you have configured Custom login URL, check 'Immediately block IP after any request to wp-login.php' and 'Block direct access to wp-login.php and return HTTP 404 Not Found Error'. Don't use wp-admin to log in to your WordPress dashboard anymore.
4. If your WordPress has a few experienced users, check 'Immediately block IP when attempting to log in with a non-existent username'.
5. Specify the list of prohibited usernames (logins) that legit users will never use. They will not be permitted to log in or register.
6. Configure mobile and browser notifications via Pushbullet.
7. Obtain keys and enable invisible reCAPTCHA for password reset and registration forms (WooCommerce supported too).


== Frequently Asked Questions ==

= Can I use the plugin with CloudFlare? =

Yes.  [WP Cerber settings for CloudFlare](https://wpcerber.com/cloudflare-and-wordpress-cerber/).

= Is WP Cerber Security compatible with WordPress multisite mode? =

Yes. All settings apply to all sites in the network simultaneously. You have to activate the plugin in the Network Admin area on the Plugins page. Just click on the Network Activate link.

= Is WP Cerber Security compatible with bbPress? =

Yes. [Compatibility notes](https://wpcerber.com/compatibility/).

= Is WP Cerber Security compatible with WooCommerce? =

Completely.

= Is reCAPTCHA for WooCommerce free feature? =

Yes. [How to set up reCAPTCHA for WooCommerce](https://wpcerber.com/how-to-setup-recaptcha/).

= Are there any incompatible plugins? =

The following plugins can cause some issues: Ultimate Member, WPBruiser {no- Captcha anti-Spam}, Plugin Organizer, WP-SpamShield.
The Cerber Security plugin won't be updated to fix any issue or conflict related to them, you should decide and stop using one or all of them.
Read more: [https://wpcerber.com/compatibility/](https://wpcerber.com/compatibility/).

= Can I change login URL (rename wp-login.php)? =

Yes, easily. [How to rename wp-login.php](https://wpcerber.com/how-to-rename-wp-login-php/)

= Can I hide the wp-admin folder? =

Yes, easily. [How to hide wp-admin and wp-login.php from possible attacks](https://wpcerber.com/how-to-hide-wp-admin-and-wp-login-php-from-possible-attacks/)

= Can I rename the wp-admin folder? =

Nope. It's not possible and not recommended for compatibility reasons.

= Can I hide the fact I use WordPress? =

No. We strongly encourage you not to use any plugin that renames wp-admin folder to protect a website.
 Beware of all plugins that hide WordPress folders or other parts of a website and claim this as a security feature.
 They are not capable to protect your website. Don't be silly, hiding some stuff doesn't make your site more secure.

= Can WP Cerber Security work together with the Limit Login Attempts plugin? =

Nope. WP Cerber is a drop in replacement for that outdated plugin.

= Can WP Cerber Security protect my site from DDoS attacks? =

Nope. The plugin protects your site from Brute force attacks or distributed Brute force attacks. By default WordPress allows unlimited login attempts either through the login form or by sending special cookies. This allows passwords to be cracked with relative ease via a brute force attack. To prevent from such a bad situation use WP Cerber.

= Is there any WordPress plugin to protect my site from DDoS attacks? =

Nope. This hard task cannot be done by using a plugin. That may be done by using special hardware from your hosting provider.

= What is the goal of the Citadel mode? =

Citadel mode is intended to block massive bot (botnet) attacks and also a slow brute force attack. The last type of attack has a large range of intruder IPs with a small number of attempts to log in per each.

= How to turn off the Citadel mode completely? =

Set Threshold fields to 0 or leave them empty.

= What is the goal of using Fail2Ban? =

With Fail2Ban you can protect site on the OS level with iptables firewall. See details here: [https://wpcerber.com/how-to-protect-wordpress-with-fail2ban/](https://wpcerber.com/how-to-protect-wordpress-with-fail2ban/)

= Do I need to use Fail2Ban to get the plugin working? =

No, you don't. It is optional.

= Is WP Cerber Security compatible with other security plugins like WordFence, iThemes Security, Sucuri, NinjaFirewall, All In One WP Security & Firewall, BulletProof Security? =

The plugin has been tested with WordFence and no issues have been noticed. Other plugins also should be compatible.

= Can I use this plugin on the WP Engine hosting? =

Yes! WP Cerber Security is not on the list of disallowed plugins.

= Is the plugin compatible with Cloudflare? =

Yes, read more: https://wpcerber.com/cloudflare-and-wordpress-cerber/

= Does the plugin works on websites with SSL(HTTPS) =

Absolutely!

= It seems that old activity records are not removing from the activity log =

That means that scheduled tasks are not executed on your site. In other words, WordPress cron is not working the right way.
Try to add the following line to your wp-config.php file:

define( 'ALTERNATE_WP_CRON', true );

= I'm unable to log in / I'm locked out of my site / How to get access (log in) to the dashboard? =

There is a special version of the plugin called **WP Cerber Reset**. This version performs only one task. It resets all WP Cerber settings to their initial values (excluding Access Lists) and then deactivates itself.

To get access to your dashboard you need to copy the WP Cerber Reset folder to the plugins folder. Follow these simple steps.

1. Download the wp-cerber-reset.zip archive to your computer using this link: [https://wpcerber.com/downloads/wp-cerber-reset.zip](https://wpcerber.com/downloads/wp-cerber-reset.zip)
2. Unpack wp-cerber folder from the archive.
3. Upload the wp-cerber folder to the **plugins** folder of your site using any FTP client or a file manager from your hosting control panel. If you see a question about overwriting files, click Yes.
4. Log in to your site as usually. Now WP Cerber is disabled completely.
5. Reinstall the WP Cerber plugin again. You need to do that, because **WP Cerber Reset** cannot work as a normal plugin.

== Screenshots ==

1. The Dashboard: Recently recorded important security events and recently locked out IP addresses.
2. WordPress activity log with filtering, export to CSV and powerful notifications. You can see what's going on right now, when an IP reaches the limit of login attempts and when it was blocked.
3. Activity log filtered by login and specific type of activity. Export it or click Subscribe to be notified with each event.
4. Detailed information about an IP address with WHOIS information.
5. These settings allows you to customize the plugin according to your needs.
6. White and Black IP access lists allow you to restrict access from a particular IP address, network or IP range.
7. Hardening WordPress: disable REST API, XML-RPC and stop user enumeration.
8. Powerful email, mobile and browser notifications for WordPress events.
9. Stop spammer: visible/invisible reCAPTCHA for WooCommerce and WordPress forms - no spam comments anymore.
10. You can export and import security settings and IP Access Lists on the Tools screen.
11. Beautiful widget for the WP dashboard to keep an eye on things. Get quick analytic with trends over last 24 hours.
12. WP Cerber adds four new columns on the WordPress Users screen: Date of registration, Date of last login, Number of failed login attempts and Number of comments. To get more information just click on the appropriate link.


== Changelog ==

= 8.6.6 =
* New: On the user sessions page, you can now search sessions by a user name, email, and the IP address from which a user has logged in.
* New: You can specify locations (URL Paths) to exclude requests from logging. They can be either exact matches or regular expressions (REGEX).
* New: You can exclude requests from logging based on the value of the User-Agent (UA) header.
* New: A new, minimal logging mode. When it is set, only HTTP requests related to known activities are logged.
* Improved: The layout of the Live Traffic log has been improved: now all events that are logged during a particular request are shown as an event list sorted in reverse order.
* Improved: The user sessions page has been optimized for performance and compatibility and now works blazingly fast.
* Improved: If your website is behind a proxy, IP addresses of user sessions now are detected more precisely.
* Improved: When you configure the request whitelist in the Traffic Inspector settings, you can now specify rules with or without trailing slash.
* Improved: A new version of [Cloudflare add-on for WP Cerber](https://wpcerber.com/cloudflare-add-on-wp-cerber/) is available: the performance of the add-on has been optimized.
* [Read more](https://wpcerber.com/wp-cerber-security-8-6-6/)

= 8.6.5 =
* New: File system analytics. It's generated based on the results of the last full integrity scan.
* New: Logging user deletions. The user’s display name and roles are temporarily stored until all log entries related to the user are deleted.
* New: Faster export with a new date format for CSV log export.
* New: Ability to disable adding the website administrator's IP address to the White IP Access List upon WP Cerber activation.
* Improved: Handling the creation of new users by WooCommerce and membership plugins.
* Improved: Handling user registrations with prohibited emails.
* Improved: Handling secure Cerber‘s cookies on websites with SSL encryption enabled.
* Improved: The performance of the integrity checker and malware scanner on huge websites with a large number of files.
* Fixed: Loading the default plugin settings has no effect. Now it’s fixed and moved from the admin sidebar to the Tools admin page.
* [Read more](https://wpcerber.com/wp-cerber-security-8-6-5/)

= 8.6.3 =
* New: Ability to load IP access list's entries in the CSV format (bulk load).
* Update: A new malware scanner setting allows you to permit the scanner to change permissions of folders and files when required.
* Fixed: The access list IPv4 wildcard *.*.*.* doesn’t work (has no effect).
* Fixed: If the anti-spam query whitelist contains more than one entry, they do not work as expected.
* Fixed: Several settings fields are not properly escaped.
* [Read more](https://wpcerber.com/wp-cerber-security-8-6-3/)

= 8.6 =
* New: [An integration with the Cloudflare cloud-based firewall. It’s implemented as a special WP Cerber add-on.](https://wpcerber.com/cloudflare-add-on-wp-cerber/)
* Update: The malware scanner has got improvements to the monitoring of new and modified files feature.
* Update: Additional search fields for the Activity log. They enable you to find a specific request by its Request ID (RID) or/and to search for a string in the request URL.
* Update: The minimum supported PHP version is 5.6.
* [Read more](https://wpcerber.com/wp-cerber-security-8-6/)

= 8.5.9 =
* New: On the Live Traffic log, now you can find requests with software errors if they occurred.
* Update: The code of WP Cerber has been updated and tested to fully support and be compatible with PHP 7.4.
* Update: The layout of the list of slave websites on the Cerber.Hub's main page has been improved to display the list more accurately on narrow and mobile screens.
* Update: If a slave website has the professional version of WP Cerber, it has a PRO sign in the "WP Cerber" column. The license expiration date is shown when you hover the mouse over the sign.
* Fixed: A bug with displaying long file names in the Security Scanner Quarantine that makes unavailable deleting or restoring quarantined files manually.
* Fixed: A bug that requires installing a valid license key on a Cerber.Hub master website to permit configuring settings on slave websites remotely, which is not intended behavior.
* [Read more](https://wpcerber.com/wp-cerber-security-8-5-9/)

= 8.5.8 =
* New: A personal data export and erase features which can be used through the WordPress personal data export and erase tool. This feature helps your organization to be in compliance with data privacy laws such as GDPR in Europe or CCPA in California
* Update: The performance of the algorithm that handles exporting rows from the Activity log and the Live Traffic log to a CSV file has been improved enabling export larger datasets
* Update: When you block a user you can add an optional admin note now
* Fixed: If a user is blocked, it’s not possible to update the user message
* Fixed: Depending on the logging settings the "Details" links on the Live Traffic log are not displayed in some rows
* [Read more](https://wpcerber.com/wp-cerber-security-8-5-8/)

= 8.5.6 =
* New: Ability to separately set the number of days of keeping log records in the database for authenticated (logged in) website users and non-authenticated (not logged in) visitors.
* New: You can completely turn off the Citadel mode feature in the Main Settings
* Update: When you upload a ZIP archive on the integrity scanner page it processes nested ZIP archives now and writes errors to the diagnostic log if it's enabled
* Update: The appearance of the Activity log has got small visual improvements
* Update: If the number of days to keep log records is not set or set to zero, the plugin uses the default setting instead. Previously you can set it to zero and keep log records infinitely.
* Fixed: The blacklisting buttons on the Activity tab do not work showing "Incorrect IP address or IP range".
* Fixed: PHP Notice: Trying to get property "ID" of non-object in cerber-load.php on line 1131

= 8.5.5 =
* IP Access Lists now support IPv6 networks, ranges, and wildcards. Add as many IPv6 entries to the access lists as you need. We've developed an extraordinarily fast ACL engine to process them.
* The algorithm of handling consecutive IP address lockouts has been improved: the reason for an existing lockout is updated and its duration is recalculated in real-time now.
* Traffic inspection algorithms were optimized to reduce false positives and make algorithms more human-friendly.
* Improved compatibility with WooCommerce: the password reset and login forms are not blocked anymore if a user’s IP gets locked out due to using a non-existing username by mistake, using a prohibited username, or if a user has exceeded the number of allowed login attempts.
* Improved compatibility with WordPress scheduled cron tasks if a website runs on a server with PHP-FPM (FastCGI Process Manager)
* Very long URLs on the Live Traffic page are now displayed in full when you click the "Details" link in a row.
* The [Cerber.Hub multi-site manager](https://wpcerber.com/manage-multiple-websites/): the server column on the slave websites list page now contains a link to quickly filter out websites on the same server.
* The [Cerber.Hub multi-site manager](https://wpcerber.com/manage-multiple-websites/): now it remembers the filtered list of slave websites while you’re switching between them and the master.
* Fixed: If the Custom login URL is enabled on a subfolder WordPress installation, the user redirection after logout generates the HTTP 404 error page.
* Fixed: Very long HTTP referrers and request URLs are displayed in a truncated form on the Live Traffic page due to CSS bug.
* Fixed: If the Data Shield security feature is active, the password reset page on WordPress 5.3 doesn’t work properly showing "Your password reset link appears to be invalid. Please request a new link below."
* [Read more](https://wpcerber.com/wp-cerber-security-8-5-5/)

= 8.5.3 =
* New: The malware scanner and integrity checker window has got a new filter that enables you to filter out and navigate to specific issues quickly.
* New: Cerber.Hub: new columns and filters have been added to the list of slave websites. The new columns display server IP addresses, hostnames, and countries where servers are located.
* Bug fixed: Depending on the number of items in the access lists, the IP address 0.0.0.0 can be erroneously marked as whitelisted or blacklisted.
* Bug fixed in Cerber.Hub: if a WordPress plugin is installed on several slave websites and the plugin needs to be updated on some of the slave websites, the plugin is shown as needs to be updated on all the slave websites.
* [Read more](https://wpcerber.com/wp-cerber-security-8-5-3/)

= 8.5 =
* New: Data Shield module for advanced protection of user data and vital settings in the website database. Available in the PRO version.
* Improvement: Compatibility with WooCommerce significantly improved.
* Update: Strict filtering for the Custom login URL setting.
* Update: Chinese (Taiwan) translation has been added. Thanks to Sid Lo.
* Bug fixed: Custom login URL doesn't work after updating WordPress to 5.2.3.
* Bug fixed: User Policies tabs are not switchable if a user role was declared with a hyphen instead of the underscore.
* Bug fixed: A PHP warning while adding a network to the Black IP Access List from the Activity tab.
* Bug fixed: An anti-spam false positive: some WordPress DB updates can't be completed.
* [Read more](https://wpcerber.com/wp-cerber-security-8-5/)

= 8.4 =
* New: More flexible role-based GEO access policies.
* New: A logged in users' sessions manager.
* Update: Access to users’ data via WordPress REST API is always granted for administrator accounts now
* Improvement: The custom login page feature has been updated to eliminate possible conflicts with themes and other plugins.
* Improvement: Improved compatibility with operating systems that natively doesn’t support the PHP GLOB_BRACE constant.

= 8.3 =
* New: Two-Factor Authentication.
* New: Block registrations with unwanted (banned) email domains.
* New: Block access to the WordPress Dashboard on a per-role basis.
* New: Redirect after login/logout on a per-role basis.
* Update: The Users tab has been renamed to Global and now is under the new User Policies admin menu.
* Fixed: Switching to the English language in Cerber’s admin interface has no effect.
* Fixed: Multiple notifications about a new version of the plugin in the WordPress dashboard.
* [Read more](https://wpcerber.com/wp-cerber-security-8-3/)

= 8.2 =
* New: Automatic recovery of infected files. When [the malware scanner](https://wpcerber.com/wordpress-security-scanner/) detects changes in the core WordPress files and plugins, it automatically recovers them.
* New: A set of quick navigation buttons on the Activity page. They allow you to filter out log records quickly.
* New: A unique Session ID (SID) is displayed on the Forbidden 403 Page now.
* New: The advanced search on the Live Traffic page has got a set of new fields.
* New: To make a website comply with GDPR, a cookie prefix can be set.
* Update: The lockout notification settings are moved to the Notifications tab.
* Update: The list of files to be scanned in Quick mode now also includes files with these extensions:  phtm, phtml, phps, php2, php3, php4, php5, php6, php7.
* [Read more](https://wpcerber.com/wp-cerber-security-8-2/)

= 8.1 =
* New: In a single click you can get a list of active plugins and available updates on a slave website.
* New: Notification about a newer versions of Cerber and WordPres available ot install on a slave.
* New: On a master website, you can select what language to use when a slave admin page is being displayed.
* Improvement: Long URLs on the Live Traffic page now are shortened and displayed more neatly.
* Improvement: The plugin uninstallation process has been improved and now cleans up the database completely.
* Improvement: Multiple translations have been updated. Thanks to Maxime, Jos Knippen, Fredrik Näslund, Francesco.
* Fixed: The "Add to the Black List" button on the Activity log page doesn't work.
* Fixed: When the "All suspicious activity" button is clicked on the Dashboard admin page, the "Subscribe" link on the Activity page doesn't work correctly.
* Fixed: When you open an email report, the link to the list of deleted files during a malware scan doesn't work as expected.
* [Read more](https://wpcerber.com/wp-cerber-security-8-1/)

= 8.0 =
* New: [Manage multiple WP Cerber instances from one dashboard](https://wpcerber.com/manage-multiple-websites/).
* New: A new bulk action to block multiple WordPress users at a time.
* Improvement: The performance of the export feature has been improved significantly.
* Improvement: Multiple code optimizations improve overall plugin performance.

= 7.9.7 =
* New: [Authorized users only mode](https://wpcerber.com/only-logged-in-wordpress-users/).
* New: [An ability to block a user account](https://wpcerber.com/how-to-block-wordpress-user/).
* New: [Role-based access to WordPress REST API](https://wpcerber.com/restrict-access-to-wordpress-rest-api/).
* Update: Added ability to search and filter a user on the Activity page.
* Update: A new, separate setting for preventing user enumeration via WordPress REST API.
* Update: A new Changelog section on the Tools page.
* Update: Improved handling scheduled maintenance tasks on a multi-site WordPress installation.
* Fixed: Several HTML markup errors on plugin admin pages.
* [Read more](https://wpcerber.com/wp-cerber-security-7-9-7/)

= 7.9.3 =
* New: New settings for [the Traffic Inspector firewall](https://wpcerber.com/traffic-inspector-in-a-nutshell/) allow you to fine-tune its behavior. You can enable less or more restrictive firewall rules.
* Update: Troubleshooting of possible issues with scheduled maintenance tasks has been improved.
* Update: To make troubleshooting easier the plugin logs not only a lockout event but also logs and displays the reason for the lockout.
* Update: Compatibility with ManageWP and Gravity Forms has been improved.
* Update: The layout of the Activity and Live Traffic pages has been improved.
* Bug fixed: [The malware scanner](https://wpcerber.com/wordpress-security-scanner/) wrongly prevents PHP files with few specific names in one particular location from being deleted after a manual scan or during [the automatic malware removal](https://wpcerber.com/automatic-malware-removal-wordpress/).
* Bug fixed: The number of email notifications might be incorrectly limited to one email per hour.
* [Read more](https://wpcerber.com/wp-cerber-security-7-9-3/)

= 7.9 =
* New: The plugin monitors suspicious requests that cause 4xx and 5xx HTTP errors and blocks IP addresses that aggressively generate such requests.
* New: A set of WordPress navigation menu links. Login, logout, and register menu items can be automatically generated and shown in any WordPress menu or a widget.
* New: Software error logging. A handy feature that logs PHP errors and shows them on Live Traffic page.
* New: A new export feature for Traffic Inspector. It allows exporting all log entries or a filtered set from the log of HTTP requests.
* Update: Multiple improvements to Traffic Inspector firewall algorithms. In short, the detection of obfuscated malicious SQL queries and injections has been  improved.
* Update: Improved handling of malformed requests to wp-cron.php.
* Fix: The number of email notifications per hour can exceed the configured limit.
* [Read more](https://wpcerber.com/wp-cerber-security-7-9/)

= 7.8.5 =
* New: A new set of heuristics algorithms for detecting obfuscated malicious JavaScript code.
* New: A new file filter on the Quarantine page lets to filter out quarantined files by the date of the scan.
* New: The performance of [the malware scanner](https://wpcerber.com/wordpress-security-scanner/) has been improved. Now the scanner deletes all files in the website session and temporary folders permanently before the scan.
* Update: If the plugin is unable to detect the remote IP address, it uses 0.0.0.0 as an IP.
* Update: The anti-spam engine will never block the localhost IP
* Update: Performance improvements for database queries related to the process of user authentication.
* Update: Improved handling the plugin settings in a buggy or misconfigured hosting environment that could cause the plugin to reset settings to their default values.
* Update: Translations have been updated. Thanks to Francesco, Jos Knippen, Fredrik Näslund, Slobodan Ljubic and MARCELHAP.
* Fix: Fixed an issue with saving settings on the Hardening tab: "Unable to get access to the file…"
* [Read more](https://wpcerber.com/wp-cerber-security-7-8-5/)

= 7.8 =
* New: An ignore list for the malware scanner.
* New: Disabling execution of PHP scripts in the WordPress media folder helps to prevent offenders from exploiting security flaws.
* New: Disabling PHP error displaying as a setting is useful for misconfigured servers.
* New: English for the plugin admin interface. Enable it if you prefer to have untranslated, original admin interface.
* New: Diagnostic logging for the malware scanner. Specify a particular location of the log file by using the CERBER_DIAG_DIR constant.
* Update: The performance of malware scanning on a slow web server with thousands of issues and tens of thousands of files has been improved.
* Update: PHP 5.3 is not supported anymore. The plugin can be activated and run only on PHP 5.4 or higher.
* Fix: If a malicious file is detected on a slow shared hosting, the file can be shown twice in the results of the scan.
* Fix: A possible issue with the short PHP syntax on old PHP versions in /wp-content/plugins/wp-cerber/common.php on line 1970
* [Read more](https://wpcerber.com/wp-cerber-security-7-8/)

= 7.7 =
* New: [Automatic cleanup of malware and suspicious files](https://wpcerber.com/automatic-malware-removal-wordpress/). This powerful feature is available in the PRO version and automatically deletes trojans, viruses, backdoors, and other malware. Cerber Security Professional scans the website on an hourly basis and removes malware immediately.
* Update: Algorithms of the malware scanner have been improved to detect obfuscated malware code more precisely for all types of files.
* Update: Email reports for [scheduled malware scans](https://wpcerber.com/automated-recurring-malware-scans/) have been extended with useful performance numbers and a list of automatically deleted malicious files if you’ve enabled automatic malware removal and some files have been deleted.
* Fix: A possible issue with uploading large JSON and CSV files. When Traffic Inspector scans uploaded files for malware payload, some JSON and CSV files might be erroneously identified as containing a malicious payload.
* Fix: A possible Divi theme forms incompatibility. If you use the Divi theme (by Elegant Themes), you can come across a problem with submitting some forms.
* [Read more](https://wpcerber.com/wp-cerber-security-7-7/)

= 7.6 =
* New: The quarantine has got a separate admin page in the WordPress dashboard which allows viewing deleted files, restoring or deleting them.
* New: Now [the malware scanner and integrity checker](https://wpcerber.com/wordpress-security-scanner/) supports multisite WordPress installations.
* Fix: Once an address IP has been locked out after reaching the limit to the number of attempts to log in the "We’re sorry, you are not allowed to proceed" forbidden page is being displayed instead of the normal user message "You have exceeded the number of allowed login attempts".
* Fix: PHP Notice: Only variables should be passed by reference in cerber-load.php on line 5377
* [Read more](https://wpcerber.com/wp-cerber-security-7-6/)

= 7.5 =
* New: Firewall algorithms have been improved and now inspect the contents of all files that are being tried to upload on a website.
* New: The traffic logger can save headers, cookies and the $_SERVER variable for every HTTP request.
* New: The scanner now scans installed plugins for known vulnerabilities. If you have enabled scheduled automatic scans you will be notified in a email report.
* Update: A set of new malware signatures amd patterns have been added to detect malware submitted through a contact form as well as any HTTP request fields.
* Update: Now the plugin inspects user sign ups (user registrations) on multisite WordPress installations and BuddyPress.
* Update: The search for user activity, as well as enabling activity notifications, is improved.
* [Read more](https://wpcerber.com/wp-cerber-security-7-5/)

= 7.2 =
* New: Monitoring new and changed files.
* New: Detecting malicious redirections and directives in .htaccess files.
* New: [Automated hourly and daily scheduled scans with flexible email reports](https://wpcerber.com/automated-recurring-malware-scans/).
* Update: Added a protection from logging wrong time stamps on some misconfigured web servers.
* Fix: Unexpected warning messages in the WordPress dashboard.
* Fix: Some file status links on the scanner results page may not work.

= 7.0 =
* Cerber Security Scanner: [integrity checker, malware detector and malware removal tool](https://wpcerber.com/wordpress-security-scanner/).
* New: a new setting for Traffic Inspector: Use White IP Access List.
* Update: the redirection from /wp-admin/ to the login page is not blocked for a user that has been logged in once before.
* Bug fixed: the limit to the number of new user registrations is calculated the way that allows one additional registration within a given period of time.
* [Read more](https://wpcerber.com/wp-cerber-security-7-0/)

== Other Notes ==

1. If you want to test out plugin's features, do this from another computer and remove that computer's network from the White Access List. Cerber is smart enough to recognize "the boss".
2. If you've set up the Custom login URL and you use some caching plugin like **W3 Total Cache** or **WP Super Cache**, you have to add a new Custom login URL to the list of pages not to cache.
3. [Read this if your website is under CloudFlare](https://wpcerber.com/cloudflare-and-wordpress-cerber/)

**Deutsche**
Schützt vor Ort gegen Brute-Force-Attacken. Umfassende Kontrolle der Benutzeraktivität. Beschränken Sie die Anzahl der Anmeldeversuche durch die Login-Formular, XML-RPC-Anfragen oder mit Auth-Cookies. Beschränken Sie den Zugriff mit Schwarz-Weiß-Zugriffsliste Zugriffsliste. Track Benutzer und Einbruch Aktivität.

**Français**
Protège site contre les attaques par force brute. Un contrôle complet de l'activité de l'utilisateur. Limiter le nombre de tentatives de connexion à travers les demandes formulaire de connexion, XML-RPC ou en utilisant auth cookies. Restreindre l'accès à la liste noire accès et blanc Liste d'accès. L'utilisateur de la piste et l'activité anti-intrusion.

**Український**
Захищає сайт від атак перебором. Обмежте кількість спроб входу через запити ввійти форми, XML-RPC або за допомогою авторизації в печиво. Обмежити доступ з чорний список доступу і список білий доступу. Користувач трек і охоронної діяльності.

**What does "Cerber" mean?**

Cerber is derived from the name Cerberus. In Greek and Roman mythology, Cerberus is a multi-headed dog with a serpent's tail, a mane of snakes, and a lion's claws. Nobody can bypass this angry dog. Now you can order WP Cerber to guard the entrance to your site too.


