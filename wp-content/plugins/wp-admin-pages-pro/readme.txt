=== WP Admin Pages PRO ===
Contributors: aanduque
Requires at least: 4.4
Tested up to: 5.4
Requires PHP: 5.6
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create admin pages on your network's subsites with custom HTML, CSS and JavaScript!

== Description ==

WP Admin Pages PRO

Create admin pages on your network's subsites with custom HTML, CSS and JavaScript!

== Installation ==

1. Upload 'wp-admin-pages-pro' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in your WordPress Network Admin

== Changelog ==

Version 1.8.0 - 27/04/2020

* Fixed: Notices being hidden under the top-bar when no margin mode is selected;
* Added: Custom pages can now replace multiple WordPress admin top-level and sub-pages at the same time;
* Added: Admins can now hide admin pages using WP Admin Pages PRO;

Version 1.7.9 - 01/04/2020

* Fixed: Brizy 1.10.118 and up breaking SVG support on admin pages;

Version 1.7.8 - 26/03/2020

* Fixed: Escaping error breaking the Editor page when using French;

Version 1.7.7 - 04/03/2020

* Fixed: Small incompatibility with Brizy Builder;
* Improved: Updated Freemius SDK to 2.3.2;

Version 1.7.6 - 10/02/2020

* Fixed: Admin Page edit button on bottom-right corner not working on Dashboard Widgets;
* Fixed: Small Incompatibility with Astra;
* Fixed: New version of Brizy breaking compatibility;

Version 1.7.5 - 14/12/2019

* Fixed: Added a new edge-case handler to populate menu items on the Replace Page option;
* Fixed: Elementor fonts not working;
* Improved: Updated the Freemius SDK to support hiding sensitive info from the Account page;

Version 1.7.4 - 29/11/2019

* Fixed: Incompatibility with WooCommerce Memberships;
* Fixed: Oxygen Builder tab component not working;
* Fixed: Beaver Themer not working;

Version 1.7.3 - 12/07/2019

* Fixed: Pages disappearing when their parent is converted to another admin page type;
* Improved: Better list table dividers between Admin Page types;
* Improved: Security Review of the entire codebase of the plugin;
* Improved: Note on Separator tab warning when the feature is not available for a given menu/content source type;

Version 1.7.2 - 01/07/2019

* Fixed: Updated the Freemius SDK version to 2.3.0;
* Fixed: Incompatibility issue with Flywheel;

Version 1.7.1 - 27/06/2019

* Fixed: Welcome Widget now gets displayed to all roles;
* Fixed: Screen Option to hide/display the Admin Pages menu is not added if the menu is being hidden via the filter documented on https://docs.wpadminpagespro.com/knowledge-base/hiding-wp-admin-pages-pro-from-your-users/
* Improved: Hiding the Admin Pages menus now also hide the templates created from the Beaver Builder saved templates list on the builder UI;
* Added: Options to bulk activate and deactivate admin pages;
* Added: Option to display pages on the main site as well;
* Added: Option to rename top and sub level menu labels on Replace mode;

Version 1.7.0 - 04/06/2019

* Fix: Issues with Oxygen templates not showing up;
* Added: Support to Widget Creation!

Version 1.6.1 - 22/05/2019

* Fixed: Typos;
* Fixed: Sub-menu pages overriding previous sub-menu items with the same order value;
* Fixed: Duplication now resets the slug of the duplicated page;
* Improved: Updated pt_BR and es_ES po files;
* Improved: List table now lists the name of custom pages as parent pages as well;

Version 1.6.0 - 21/05/2019

* Added: External Links now support iframe loading as well;

Version 1.5.5 - 17/05/2019

* Fixed: Warning message being thrown when a page was deleted or duplicated;
* Fixed: Permission settings not being applied to Admins;

Version 1.5.4 - 08/05/2019

* Fixed: Incompatibilities with Sliced Invoices;
* Fixed: Small issue with the Oxygen;
* Fixed: Placeholder on new admin page title field not going way on key-up;

Version 1.5.3 - 03/05/2019

* Fixed: Incompatibility with Advanced Custom Field option pages;
* Added: Option to add specific users as targets of custom admin pages;

Version 1.5.2 - 30/04/2019

* Fixed: Incompatibilities with newer versions of Brizy;
* Added: Spanish translation added - courtesy of John Rozzo. Thanks, John!
* Added: Beta support to Oxygen Builder;

Version 1.5.1 - 15/04/2019

* Fixed: Issue with placeholders on the Normal and HTML editors;
* Fixed: Admin Pages not showing up on the Tools -> Export;
* Fixed: Loading the scripts and styles only on our own pages;
* Added: Super Admins can now duplicate Admin Pages;

Version 1.5.0 - 29/03/2019

* Fixed: Small bugs caught by Sentry;
* Fixed: Delete button not working on the Edit Admin page screen;
* Improved: Changed the BeaverBuilder button to make clear the Standard BB license is also supported;
* Added: Admins can set the order of the submenus as well;
* Added: Replace page mode now have support to all menu items available;

Version 1.4.0 - 14/02/2019 (stand-alone) & 27/02/2019 (add-on)

* Added: Launched as Stand-alone plugin in https://wpadminpagespro.com
* Added: Option to remove the Admin Pages menu item from the menu after the page creation process is done;
* Added: Option to remove admin notices from admin pages;
* Added: Content processing, so you can place placeholders like {{user:first_name}} and have it be automatically replaced with the user first_name meta field;
* Added: Inline Editor;
* Added: Consolidated content source parent class to make adding new Page Builders/Content Sources easy in the future;
* Added: Support to External URLs;

Version 1.3.0 - 15/01/2019

* Fixed: Admin Pages do not appear on the main-site on multisite environments;
* Improved: Removed WP Ultimo custom post types from the export screen of subsites;
* Added: Top-bar with quick actions for network admins on the create pages;
* Added: HUGE! Elementor Support!
* Added: HUGE! Brizy Support!

Version 1.2.1 - 17/11/2018

* Fixed: Making the plugin compatible with WP Ultimo 1.9.0;

Version 1.2.0 - 10/09/2018

* Added: PHP support added if the WU_APC_ALLOW_PHP_PROCESSING is set to true on wp-config.php. This does not use PHP's eval, but it still can lead to security holes. Use this carefully;
* Improved: Add-on plugin updater;
* Improved: New URL for the updates server;

Version 1.1.2 - 16/08/2018

* Fixed: Small issue with WP Engine;

Version 1.1.1 - 16/08/2018

* Fixed: Permissions not being correctly applied to replace pages;

Version 1.1.0 - 15/08/2018

* Added: Beaver Builder templates are now supported! You can use your favorite page builder to create custom admin pages;
* Added: Now it is possible to replace the content of the WordPress default admin pages as well;
* Added: Now it is possible to append the content created to the top or bottom of default WordPress admin pages as well;

0.0.1 - Initial Release