# Changelog

All notable changes to this project will be documented in this file.

## [3.7.0] - 2022-12-13
### Added:
- Backup Connection. Allows to set a Backup Connection which will send the email, if the Primary Connection fails.
- Smart Routing. Send emails from different additional connections based on your configured conditions.
- Additional Connections. Allows configuring additional mailers that can be used as a Backup Connection or in Smart Routing.

### Changed:
- Improved Action Scheduler data cleanup on plugin uninstall.
- Improved performance for database table's validation checks.

### Fixed:
- Tasks meta database table error.
- Gmail mailer authorization error if the oAuth app already had other non mail scopes attached.
- Email address validation in Setup wizard.
- Removed unneeded composer libraries autoload code.
- Conflict detection for plugin Sendinblue - WooCommerce Email Marketing (v3.0+)

## [3.6.1] - 2022-10-05
### Fixed:
- Setup Wizard steps navigation, when going backwards.

## [3.6.0] - 2022-10-04
### Added:
- Source column to Email Log CSV and XLSX export.
- The `wp_mail` function call backtrace to the Debug Events if the "Debug Email Sending" option is enabled.
- Email Logs Configuration step in the Setup Wizard.
- Plugin's DB tables re-creation process in WP Site Health.
- Debug Events retention period setting.

### Changed:
- Updated the list of conflicting plugins (added Zoho Mail).
- Improved conflicting plugins' admin notices (display multiple at once).
- Switched to the WP Core function `is_email` for verifying email addresses.
- Improved the detection if `wp_mail` function is overwritten.

### Fixed:
- One-time fatal error on the Email Reports page, right after Email Logs were enabled.
- Gmail mailer not using the correct From Email Address in Domain Checker.
- Exported (.csv and .xlsx) email log values could be executed as formulas.
- Email Log `people` DROP index DB error.

## [3.5.2] - 2022-08-17
### Fixed:
- The check if `wp_mail` function is overwritten on Windows servers.

## [3.5.1] - 2022-07-14
### Changed:
- Removed MailPoet from the list of conflicting plugins.

### Fixed:
- PHP warning for undefined variable when using the Default (none) mailer.

## [3.5.0] - 2022-07-12
### Added:
- Alerts for failed emails (Email, Slack, SMS, and Webhook notifications).
- Check if `wp_mail` function is overwritten.
- DB table (`wpmailsmtp_tasks_meta`) cleanup after scheduled actions execution. Keeps DB size small.

### Changed:
- Updated the list of conflicting plugins (added Branda and MailPoet).
- Improved WP Site Health license check.
- Improved Outlook mailer authorization flow error handling.
- Updated Action Scheduler library to 3.4.2.
- Improved Amazon SES (added support for V2 client).

### Fixed:
- SMTP.com mailer email content-encoding.
- Amazon SES long Return-Path header encoding.
- Dashboard widget graph when there is no email logs data.
- Email Log retention period cleanup task DB table check.
- Missing Sendinblue email body WP filter.
- Chart.js library conflicts with other plugins.

## [3.4.0] - 2022-04-27
### Added:
- New transactional mailer: SendLayer integration.
- Support for changing From Email address in Outlook mailer (Send As, Shared Mailboxes, and Groups).
- Support for bigger attachments in Outlook mailer.

### Changed:
- Improved Mailgun API error message extraction.
- Standardized error messages format and improved WP remote request errors extraction.

### Fixed:
- Lite plugin uninstall actions clearing plugin options while Pro version is active.
- Amazon SES identities verification data retrieval for more than 100 identities.
- Hiding unrelated network admin notices on WP Mail SMTP pages.

## [3.3.0] - 2022-02-15
### IMPORTANT
- Support for WordPress versions 5.1.x or lower has been discontinued. If you are using one of those versions, you MUST upgrade WordPress before installing or upgrading to WP Mail SMTP v3.3. Failure to do that will disable WP Mail SMTP functionality.

### Added:
- Email Log: sent status verification AS tasks for Postmark and SparkPost.
- New Amazon AWS SES region: Osaka.
- PHP 8.1 compatibility.

### Changed:
- Email Log: improved sent status verification accuracy with webhooks for: SMTP.com, Sendiblue, Mailgun, Postmark and SparkPost. 
- Updated the list of conflicting plugins (added FluentSMTP and WP HTML Mail).
- Domain verification process for AWS SES mailer (now using DKIM verification instead of the simple TXT record verification).
- Improved debug error message for the Other SMTP mailer in Debug Events.
- Updated Action Scheduler library to 3.4.0.
- Improved Action Scheduler performance.

### Fixed:
- PHP deprecated notices in Sendinblue library (PHP 7.4+).
- Email Log: resend issue with incorrectly stored headers.
- Non-network admin was allowed to change the Debug Event setting, if network-wide setting was enabled.
- DB tables row in Site Health Info section is now private.
- Debug Events' screen options visible on general Tools page.
- Screen Options right alignment.

## [3.2.1] - 2021-11-17
### Fixed:
- PHP 8 compatibility when existing Gmail mailer connection is revoked. 

## [3.2.0] - 2021-11-09
### Added:
- New transactional mailer: SparkPost integration.
- One-click migration from FluentSMTP plugin.
- Plugin constants integration in Setup Wizard.

### Fixed:
- Early plugin deactivation issue with activity log plugins.

## [3.1.0] - 2021-09-28
### Added:
- New transactional mailer: Postmark integration.
- Email Log: added CC and BCC columns to the table.
- Support for string attachments (added via PHPMailer object).

### Changed:
- Improved Weekly Email Summary Setup Wizard control.
- Improved Email Source detection in Debug Events and Email Log for WP Core sent emails.
- Improved uninstall process. It now removes all plugin DB data and tables.

### Fixed:
- Email Source detection in Debug Events and Email Log for file paths with backslashes.
- Amazon SES automatic closest region detection (keycdn API request).
- Added SSL check for Amazon SES and Outlook mailers in the Setup Wizard.
- Blurry image assets in Weekly Email Summary.
- PHP extension mb_strings not polyfilled correctly.
- New user registration email template for Email Controls feature.
- Added missing is_email_sent filters for Sendinblue, Mailgun, and Gmail mailers.
- Debug Events double-entry DB save, because of a bug in is_email_sent method for certain mailers.
- Network subsite switcher in WP Multisite installations with subdomain configuration.

## [3.0.3] - 2021-08-09
### Fixed:
- Weekly Summary Email sending when migration code didn't trigger.

## [3.0.2] - 2021-08-05
### Fixed:
- Fatal PHP error on WP version 5.2 and lower (missing wp_timezone function).

## [3.0.1] - 2021-08-05
### Fixed:
- Email Log and Debug Events Source column detection.

## [3.0.0] - 2021-08-03
### Added:
- Email Reports - Email Log statistics grouped by email subject.
- Weekly Email Summary - email sending statistics sent to your inbox.
- Debug Events - logging all email sending errors and debug events.
- Support for automatic pro plugin updates.
- Quick admin area links.
- New Amazon AWS SES regions: Milan, Cape Town, Bahrain, and GovCloud.
- Email Log: the source of the sent email. Which plugin/theme (or WP Core) sent the email.

### Changed:
- Updated the successful Email Test screen.
- How plugin DB tables are created on WP Multisite installations.
- Included Microsoft 365 in our existing Outlook mailer labels, for easier recognition.
- Updated Action Scheduler library to 3.2.1.

### Fixed:
- Outlook oAuth processing conflicting with other plugins (WP Amelia).
- WP core admin spinner for the dashboard widget.
- Email Log: non-ascii character encoding when link tracking is enabled.
- PHP error when objects implementing `__invoke()` method were used as hook callbacks for admin notices.

## [2.9.1] - 2021-07-05
### Fixed:
- Email Log: mailto links not working correctly when link tracking is enabled.

## [2.9.0] - 2021-06-22
### Added:
- Email Log: store email attachments.
- Email Log: export email logs in EML format.
- Email Log: archive email log table column filters in screen options.
- Email Log: aggregated email log view on Network Admin Email Log page.
- Email Log: resend emails.
- Email Log: open and click tracking.
- Action Scheduler table to the Tools page.
- Dashboard Widget with total emails sent counter (Lite only).

### Changed:
- Notifications design.
- Sodium Compat library loading improved.
- Improved the Zoho Mail OAuth error detection.
- Removed the ability to set dynamic reply-to address for Zoho Mail because they don't support it.

### Fixed:
- Plugin conflict with plugins that populate $_POST data (Ultimate Addons for Elementor, WishList Member, ...).
- Sendinblue WooCommerce Email Marketing plugin conflict admin notice display.
- Email Log: empty email logs caused by sent status verification tasks.
- jQuery deprecated notices.
- Action Scheduler library loading issue.
- Canceled Setup Wizard's OAuth authorization redirecting to the Setup Wizard when connecting via regular plugin settings.
- Meta DB table not existing error notices on a WP Multisite subsite.

## [2.8.0] - 2021-05-04
### Added:
- Email Log: export email logs in CSV and XLSX (Excel) formats.
- Email Log: print email log.
- Email Log: date range filter control.
- Email Log: filter hook for changing required WP capabilities to view and manage Email Logs.
- Email Log: settings constants.
- Filter hooks for the Outlook mailer URLs, enabling GCC High compatibility.
- WP Site Health status check for the sending domain.
- WP Mail SMTP WordPress admin menu position filter hook.

### Changed:
- Updated the single Email Log page style.
- Updated the list of conflicting plugins.
- Moved the Email Test tab to the new WP Mail SMTP > Tools page.

### Fixed:
- Removed the empty admin dashboard menu item for the Setup Wizard.
- The Setup Wizard conflicting issue with Admin 2020 plugin.
- The plugin settings pages not opening when WishList Member plugin was active.
- Empty error message for Outlook mailer.
- Dashboard Widget missing database table error when Email Logging was never enabled.
- Mailgun mailer sent status verification task accuracy.

## [2.7.0] - 2021-03-23
### IMPORTANT
- Support for PHP 5.5 has been discontinued. If you are running one of those versions, you MUST upgrade PHP before installing or upgrading to WP Mail SMTP v2.7. Failure to do that will disable WP Mail SMTP functionality.

### Added:
- Dashboard widget with email sending statistic.
- Email Log: email sent status filters.

### Changed:
- Updated About us plugin page.
- Improved Domain Check Results section in Email Test tab.
- Allow the use of different Gmail aliases as From Email address by disabling the Force From Email setting.
- The Setup Wizard can now be launched via a button in the plugin settings. The Setup Wizard admin dashboard menu item was removed. 

### Fixed:
- WP Multisite subsite admins couldn't remove oAuth connections (in Gmail, Outlook and Zoho mailers).
- Accessible WPMS subsite settings when network-wide setting was enabled.

## [2.6.0] - 2021-02-02
### Added:
- New plugin Setup Wizard.
- One-click plugin upgrade (Lite to Pro).
- New Amazon SES regions: North California, Paris, Stockholm.
- Ability to clear error notices in multisite setups with plugin options editable network-wide.
- SendGrid Invalid API key error message and mitigation steps on the Email Test page.

### Fixed:
- Plugin settings overwriting checkboxes with default values when a network-wide option is enabled on Multisite installation.
- Unused Return Path option removed for the Amazon SES mailer.
- Zoho mailer error when "reply-to" header was set to the Zoho authorized email address.
- Outlook mailer not sending emails with custom email headers missing prepended 'X-'.
- Email Log: Save correct sent date/time (UTC).
- License type text displayed in plugin settings.
- PHP fatal error on undefined Pro mailer object when switching to Lite version.
- PHP warning in Sendinblue email sent verification task.
- PHP 8 support.

## [2.5.3] - 2020-11-23
### Fixed:
- Other SMTP password decryption on network-wide enabled WPMS subsites.

## [2.5.2] - 2020-11-17
### Fixed:
- Outlook oAuth Redirect URI for network-wide enabled WP Multisites.
- WPMS network-wide plugin settings update on network subsites (fixes oAuth token refresh issues).

## [2.5.1] - 2020-10-28
### Fixed:
- The automatic updates issue with Gmail mailer token refresh.
- The 'wp-amil-smtp' typo in a plugin text-domain and a HTML class.

## [2.5.0] - 2020-10-20
### Added:
- Email Log: save and display the email sending errors in email log entries.
- Email Log: button to delete all email log entries at once.
- Email Log: display a proper delivery status for each sent email for these mailers: SMTP.com, Sendinblue, Mailgun.
- Other SMTP mailer password encryption/decryption.

### Changed:
- Gmail mailer suggested steps for `invalid_grant` error on the Settings > Email Test page.
- Gmail mailer redirect URI was changed to fix issues with `mod_security` or redirect blocking plugins/solutions.

### Fixed:
- Plugin options re-saving with unescaped magic quote characters.
- Display correct From Email for the Outlook and Zoho mailers.
- SMTP.com mailer email delivery for certain SMTP servers if attachment's encoded string is too long.

## [2.4.0] - 2020-09-15
### Added:
- Prefix all 3rd-party libraries to avoid compatibility issues with other plugins using different versions of the same libraries.
- Email controls to disable plugin and theme auto-update emails.
- New Amazon AWS SES regions: London, Frankfurt, Ohio, Canada (Central), Mumbai, Tokyo, Seoul, Singapore, Sydney and São Paulo.
- Amazon SES Mailer: support domains verification.

### Changed:
- Sendinblue SDK library version to 6.4.
- Google apiclient library version to 2.7.
- Improve plugin settings input and toggle focus states.
- Amazon AWS SES library. Migrated to the official AWS SDK library.

### Fixed:
- Email controls to disable user and site registration/creation/activation.
- Hide admin bar menu when errors are present and the "Hide Email Delivery Errors" setting is enabled.
- CSS sourcemaps warning messages in browser dev tools.
- Zoho mailer issue with comma in the "from name" field.
- Action Scheduler tasks being registered too early and not getting assigned to the plugin group.
- PHP notices in Amazon SES mailer automatic region detection.
- Admin bar CSS asset loading when the admin bar is not showing.
- License key verification triggered on non license plugin settings input enter event.
- Fatal error on plugin uninstall if `ActionScheduler_QueueRunner::unhook_dispatch_async_request` method does not exist.
- PHP Deprecated notice for `base_convert()` function usage in the Mailgun mailer.

## [2.3.1] - 2020-08-18
### Fixed:
- Fatal error on plugin settings and WP Site Health pages if the plugin mailer option is empty for some reason.

## [2.3.0] - 2020-08-18
### Added:
- Mailer: Zoho Mail integration.
- Email Log: add "View Email" action log items in a table.
- A WP Mail SMTP admin bar menu if there is an email delivery error or notifications.
- WordPress 5.5 compatibility.

### Changed:
- Improve copy button user experience for Gmail and Outlook Authorized redirect URI option.
- Email delivery error admin notices are now displayed only on plugin pages.
- Improve plugin settings UI, by hiding certain options, if the mailer does not support them.
- Pepipost API mailer is no longer available for new installs.

### Fixed:
- Fatal error in Review class if mailer plugin setting is empty.
- WP Site Health check for missing DB tables.
- Email Log: fix empty `$wpdb->actionscheduler_actions` DB table errors, when Action Scheduler is not usable yet.
- PHP errors if Action Scheduler DB tables don't exist when registering plugin tasks.
- PHP error in Action Scheduler on plugin uninstall.

## [2.2.1] - 2020-07-07
### Fixed:
- Fatal error when Gmail mailer has empty Client ID and Client Secret.

## [2.2.0] - 2020-07-07
### Added:
- Network-wide plugin settings for WordPress MultiSite.
- Gmail mailer now supports aliases.
- Support both old PHPMailer v5 (WordPress <=5.4) and PHPMailer v6 (WordPress >=5.5).
- Email Log: display possible DB-related issues as a notice. 

### Changed:
- Pepipost mailer is now using the native API v5 instead of the SendGrid migration API.

### Fixed:
- Incorrect Mailgun Domain Name option was not showing an email delivery error.
- Empty debug errors for the Sendinblue mailer are no more.
- Properly compare From Email option value with a correct default email address from WP core.
- Site Health check for connectivity with wpmailsmtp.com server should always work properly.
- Email Log: fix DB table creation for Database tables with `utf8mb4` collates.

## [2.1.2] - 2020-06-22
### Fixed:
- Verify original From Email address when used in Reply-To header.
- Added missing DB table check in the WordPress Site Health functionality.
- Email Log: fix screen options not saving on WP 5.4.2+.
- Email Log: properly log unsent emails when only one in a batch failed.
- Email Log: fix DB table creation when collate is not set or when MySQL version is lower than 5.6.

## [2.1.1] - 2020-06-08
### Changed:
- Remove current automatic default reply-to address and add WP filter `wp_mail_smtp_processor_set_default_reply_to` for setting default reply-to addresses.
- Improve description for several options with links to an article about how to properly use constants. 

### Fixed:
- PHP parse error connected to Monolog library on PHP versions < 7.x.

## [2.1.0] - 2020-06-04
### Added:
- Async/scheduled tasks management support (e.g. cleaning up emails log table).
- Email Log: log retention period.
- New warning notification for selecting the "Default (none)" mailer and saving the plugin settings.

### Changed:
- Email Log: Change the logs DB table engine to improve compatibility with various MySQL versions and setups.
- Disable valid plugin license key editing to prevent accidental changes.
- Set the original From Email as Reply-To address if it was overwritten by the Force From Email option.
- The Force From Email option is now enabled by default, for new plugin installs.
- Reply-To header is now set when not provided, equals to From Name/Email.

### Fixed:
- Display a non-empty PHPMailer error when some non-SMTP mailers generate errors.
- Display a more accurate message, when the "channel - not found" error is triggered by SMTP.com API.
- Save and display debug errors for the "Other SMTP" mailer. 
- Email Log: mark unsent emails correctly when SMTP error occurs.
- Improve the debug details for the "Invalid address (setFrom)" error in the Email Test tab.
- Improve the debug details for SMTP CA verification fail, Gmail Guzzle requirements, and Gmail invalid grant errors.
- Improve the uninstall cleanup procedure.

## [2.0.1] - 2020-05-07
### Changed:
- Improved description of the "Do Not Send" plugin option.

### Fixed:
- Due to Pepipost API changes we now convert new lines so they are preserved in plain text emails.
- Downgrade internal Guzzle dependency to 6.4 to temporarily fix compatibility issues with WordPress and Guzzle 6.5. Affects Gmail and Outlook mailers.

## [2.0.0] - 2020-04-23
### IMPORTANT
- Support for PHP 5.2-5.4 has been discontinued. If you are running one of those versions, you MUST upgrade PHP before installing or upgrading to WP Mail SMTP v2.0. Failure to do that will disable WP Mail SMTP functionality.

### Added:
- Mailer: SMTP.com integration.

### Changed:
- Auto-download translations on plugin activation.
- Email Log: Check that the DB table exists only after an email was sent.
- Plugin filters that change the FROM Name/Email in emails are now always running last. 

### Fixed:
- Email Log: Always display the logged From email address in a table. 
- Email Log: MS Outlook mailer should log the actually used FROM Email, and not defined in plugin settings.
- `false` value of the `WPMS_SMTP_AUTH`/`WPMS_SMTP_AUTOTLS` constants was not properly handled in UI.
- MS Outlook: do not generate an error on the Email Test page when the mailer is not configured.
- Various minor code and internal links improvements.

## [1.9.0] - 2020-03-23
### Added:
- Email Log: Screen Options to change the displayed number of emails per page.
- Email Log: searchable/clickable emails in From/To columns.
- Site Health: add various Status tests and Info section on Tools > Site Health page. 
- Notify admin if there are unsaved changes in plugin admin area options.

### Fixed:
- Test email now has a proper bottom margin for better look.
- Adjust the way we process MS Outlook connection set up (allow dots in auth codes).
- Email Log: bottom bulk actions do not work.
- Email Log: incorrect plain text emails display (like "Lost Password") in "View Email" popup.
- Email Log: discrepancy between Email Log archive and single Email log From/To values for Gmail mailer.

### Changed:
- Email Log: change the "Preview" button label to more logical "View Email".
- Update "About us" plugin page with relevant information.
- Settings: save default WordPress FROM Email address when incorrect FROM Email address is saved by a user.

## [1.8.1] - 2019-12-13
### Fixed:
- Revert Guzzle version to 6.4.1 because Sendinblue and Gmail mailers may experience issues under certain circumstances while sending emails (not all sites are affected).
- Make compatible the WordPress PhpMailer class inline attachments management with the Sendgrid API.

## [1.8.0] - 2019-12-12
### Added:
- New recommended mailer: Pepipost.
- "Suggest a Mailer" link in a list of mailers to send us your ideas about new ones.

### Fixed:
- Sendgrid: Content ID for attachments missing.

### Changed: 
- Timeout to HTTP requests (pepipost, sendgrid, mailgun), same as max_execution_time, to prevent fails when sending emails with big attachments.

## [1.7.1] - 2019-11-11
### Fixed:
- Compatibility with WordPress 5.3.
- `get_default_email()` always returns empty value when the server incorrectly configured.
- [Pro] Incorrect 'From Email' in the email log table for Gmail/Outlook mailers.

## [1.7.0] - 2019-10-24
### Added:
- [Pro] Email Log: search in emails for email addresses, headers and content.
- Add a new constant `WPMS_DO_NOT_SEND` to block email sending.
- [Pro] Safety check for Email Log: DB table should exist.
### Changed:
- [Pro] Email Log: emails with empty subjects should be valid.
### Fixed:
- Default email (wordpress@example.com) rewriting in CLI mode.
- Incorrect conflicts detection with certain plugins.

## [1.6.2] - 2019-09-02
### Fixed:
- Race condition when loading with certain plugins, that send emails very early. Makes email delivery more reliable.

## [1.6.1] - 2019-08-21
### Fixed:
- [Pro] Correctly run DB migration for new users, needed for Email Log.

## [1.6.0] - 2019-08-21
### Added:
- New transactional mailer: Sendinblue.
- Educate users to use transactional mailers for better deliverability.
- New option and filter to disable admin area delivery error notices.
- [Pro] Retrieve Lite translations when Pro is installed to fully translate the plugin.
- [Pro] New geolocation endpoint to determine the nearest Amazon SES region.
### Fixed:
- [Pro] Correctly support old and unmaintained versions of MySQL 5.1+.
### Changed:
- Hide private API key saved in the DB for API based mailers using `input[type=password]`.
- Update links to various docs, pointing now to https://wpmailsmtp.com.

## [1.5.3] - 2019-08-06
### Fixed:
- Outlook mailer: make sure that the refreshed token is always used when sending emails.

## [1.5.2] - 2019-07-18
### Fixed:
- "Redirect URI mismatch" error for "Gmail" mailer when trying to re-authorize an account that was initially created with version < v1.5.0.
### Changed: 
- Make "Authentication" setting in "Other SMTP" mailer ON by default for new users.
- Mailers docs links now point to wpmailsmtp.com own site.

## [1.5.1] - 2019-07-12
### Fixed:
- Duplicated emails sent to the first recipient in a loop (and others not receiving their emails).

## [1.5.0] - 2019-07-09
### Added:
- Email Log.
- Email Control.
- New mailer - AmazonSES.
- New mailer - Microsoft Office 365/Outlook.
- Support plugin auto-update with a valid license key.
- Loсo plugin support.
- "About us" admin area page.
- Display in debug output a possible conflicting plugin existence.
- Lots of actions and filters to improve flexibility of the plugin.
### Changed:
- Plugin menu is now top level.
- Hide secrets/API keys in page DOM in plugin admin area.
- Do not save constant values into the database when plugin settings are saved.
- Lots of i18n improvements to support translation for both free and paid version of the plugin.
- Gmail mailer - allow to change From Name email header.
- Gmail mailer - display email used to create a connection.
- WordPress 4.9 is the minimum WordPress version we support.
### Fixed:
- X-Mailer header should be present in all emails.
- PHP notices when migrating under certain circumstances from 0.x version of the plugin.
- Options::get_group() now supports values set via constants.

## [1.4.2] - 2019-03-23
### Changed:
- Tested up to WordPress 5.1.x.
- Removed TGMPA library.

## [1.4.1] - 2018-12-03
### Fixed: 
- Correctly process backslashes in SMTP passwords defined via constants.

### Changed: 
- Allow to send a Test Email when Default (none) mailer is selected in plugin settings.

## [1.4.0] - 2018-11-29
### Added:
- New option: Do Not Send - block emails from being sent.
- New option: Send HTML or plain text emails when doing an Email Test.
- New option: Mailgun region selection - US and EU (US is default to preserve compatibility).
  
### Fixed:
- Compatibility with WordPress 3.6+.
- Compatibility with WordPress 5.0.
- Constants usage is much more reliable now, works correctly on Multisite. Constants are global accross the whole network.
- Preserve multipart emails when using Sendgrid/Mailgun mailers (were converted to HTML-only).
- Security hardening.

### Changed:
- Prefill Email Test page From field with currently logged in user email.
- Update libraries: google/apiclient-services, google/auth, phpseclib/phpseclib and their dependecies.
- Display in debug output cURL version if Gmail mailing failed.
- Display in debug output OpenSSL version if it exists if Gmail/SMTP mailing failed.
- Display plugin version in dashboard error notice when emailing failed.
- Do not allow to send Test Email if mailer not configured properly.
- Notify in plugin admin area that Gmail doesn't allow to redefine From Name/Email etc.
- List all constants with descriptions in plugin main file: wp_mail_smtp.php. 
- TGMPA: change descriptions from "Required" to "Recommended" (labels were incorrect).

## [1.3.3] - 2018-07-05
### Fixed:
- Compatibility with other plugins, that are using Google Service or Google Client classes.

### Changed:
- Optimize code loading.

## [1.3.2] - 2018-06-29
### Fixed: 
- Make sure that other plugins/themes are not conflicting with our TGMPA library.

## [1.3.1] - 2018-06-29
### Fixed: 
- Other SMTP: Clear new Debug messages about failed email delivery on next successful email sending.
- Introduce conditional autoloader to workaround Gmail PHP 5.5 requirement and its library compatibility issues vs PHP 5.3+ minimum viable plugin version.

## [1.3.0] - 2018-06-28
### Added:
- New option: force From Email rewrite regardless of the current value.
- New option: force From Name rewrite regardless of the current value.
- New option: remove all plugin data on plugin uninstall (when user deletes it).
- Notify site admins in wp-admin area with a notice about last failed email delivery. Cleans up on successful delivery.
- Notify site admins in wp-admin area with a notice about possible compatibility issues with other SMTP and email delivery plugins.
- Improve User Debug Experience when doing Email Test - display helpful description and steps to fix the issue.
- New users: provide default SMTP Port value for new users based on Encryption selection.
- New users: notify about not configured plugin settings.
- New users: Recommend free WPForms Lite plugin for those who don't have it.
- SendGrid/Mailgun: provide support for multipart/alternative types of emails.
- Gmail: new button to remove connection and to connect a new Google account.

### Fixed:
- Support plugin installation into /mu-plugins/ directory.
- SendGrid: required text/plain part of email being the first one - fixes plain text emails not having links.
- SendGrid and Mailgun: improperly sending plain text emails in html format.
- SMTP Debug output was empty in some cases.
- Compatibility with lots of other plugins that use Google Analytics library of different versions.
- "client_id is empty" is no more a problem, should be fixed.

### Changed:
- For SendGrid and Mailgun allow using custom defined attachments names if present. Fallback to file name.
- Gmail: switch to a wider scope to prevent possible issues in certain circumstances.
- Remove whitespaces start/end of keys, secrets etc.
- Improved helpful description tests of various options.
- Improved plugin autoloading functionality.

## [1.2.5] - 2018-02-05
### Fixed:
- `Return path` can't be turned off.
- `Authentication` sometimes can't be turned off.
- `Auto TLS` sometimes can't be turned off.
- BCC support for Gmail was broken.
- Debug output improved to handle SELinux and grsecurity.
- Strip slashes from plugin settings (useful for `From Name` option).
- Change the way sanitization is done to prevent accidental removal of useful data.
- Plugin activation will not overwrite settings back to defaults.
- Properly set `Auto TLS` option on plugin activation.
- Providers autoloading improved for certain Windows-based installs.
- Use the proper path to load translations from plugin's `/languages` directory.

### Changed:
- Do not autoload on each page request plugin settings from WordPress options table.
- Do not autoload Pepipost classes unless it's saved as active mailer in settings.

## [1.2.4] - 2018-01-28
### Fixed:
- Improved escaping in debug reporting.

## [1.2.3] - 2018-01-22
### Fixed:
- Gmail tokens were reset after clicking Save Settings.
- Slight typo in Gmail success message.

## [1.2.2] - 2017-12-27
### Fixed:
- Correctly handle Mailgun debug message for an incorrect api key.
- Fatal error for Gmail and SMTP mailers with Nginx web-server (without Apache at all).

### Changed:
- Update X-Mailer emails header to show the real sender with a mailer and plugin version.

## [1.2.1] - 2017-12-21
### Fixed:
- Failed SMTP connections generate fatal errors.

## [1.2.0] - 2017-12-21
### Fixed:
- Decrease the factual minimum WordPress version from 3.9 to 3.6.

### Changed:
- Improve debug output for all mail providers.

## [1.1.0] - 2017-12-18
### Added:
- New option "Auto TLS" for SMTP mailer. Default is enabled. Migration routine for all sites.

### Changed:
- Improve debug output - clear styles and context-aware content.
- Better exceptions handling for Google authentication process.
- Do not sanitize passwords, api keys etc - as they may contain special characters in certain order and sanitization will break those values.
- Improve wording of some helpful texts inside plugin admin area.

### Fixed:
- Do not include certain files in dependency libraries that are not used by Google mailer. This should stop flagging plugin by Wordfence and VaultPress.
- Constants usage is working now, to define the SMTP password, for example.
- Notice for default mailer.

## [1.0.2] - 2017-12-12
### Fixed
- PHPMailer using incorrect SMTPSecure value.

## [1.0.1] - 2017-12-12
### Fixed
- Global POST processing conflict.

## [1.0.0] - 2017-12-12
### Added
- Automatic migration tool to move options from older storage format to a new one.
- Added Gmail & G Suite email provider integration - without your email and password.
- Added SendGrid email provider integration - using the API key only.
- Added Mailgun email provider integration - using the API key and configured domain only.
- New compatibility mode - for PHP 5.2 old plugin will be loaded, for PHP 5.3 and higher - new version of admin area and new functionality.

### Changed
- The new look of the admin area.
- SMTP password field now has "password" type.
- SMTP password field does not display real password at all when using constants in `wp-config.php` to define it.
- Escape properly all translations.
- More helpful test email content (with a mailer name).

## [0.11.2] - 2017-11-28
### Added
- Setting to hide announcement feed.

### Changed
- Announcement feed data.

## [0.11.1] - 2017-10-30
### Changed
- Older PHP compatibility fix.

## [0.11] - 2017-10-30
### Added
- Composer support.
- PHPCS support.
- Build system based on `gulp`.
- Helper description to Return Path option.
- Filter `wp_mail_smtp_admin_test_email_smtp_debug` to increase the debug message verbosity.
- PHP 5.2 notice.
- Announcement feed.

### Changed
- Localization fixes, proper locale name.
- Code style improvements and optimizations for both HTML and PHP.
- Inputs for emails now have a proper type `email`, instead of a generic `text`.
- Turn off `$phpmailer->SMTPAutoTLS` when `No encryption` option is set to prevent error while sending emails.
- Hide Pepipost for those who are not using it.
- WP CLI support improved.
