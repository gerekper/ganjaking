=== Groovy Menu ===
Contributors: Grooni
Requires at least: 4.9.7
Tested up to: 5.4.2
Stable tag: 2.1.1
License: Themeforest Split Licence
License URI: -
Tags: customizable, responsive, animation, flexible, translation ready, drop down, dropdown, dropdown menu, easy, mega dropdown menu, mega menu, megamenu, navigation, options, presets, shortcodes, widgetized, widgets

Groovy menu is a modern customizable and flexible WordPress Mega Menu Plugin designed for creating mobile friendly menus with a lot of options.

== Description ==
[Groovy Menu](https://codecanyon.net/item/groovy-menu-wordpress-mega-menu-plugin/23049456) | [Demo](http://groovymenu.grooni.com/) | [Documentation](https://grooni.com/docs/groovy-menu/) | [Video tutorials](https://www.youtube.com/channel/UCpbGGAUnqSLwCAoNgm5uAKg)

Groovy Menu is a WordPress Mega Menu Plugin that will allows you easily add an awesome mega menu on your site. Is an easy to customize, just need to upload your logo and fit your own colors, fonts and sizes.

== Installation ==
1. In your admin panel, go to Plugins -> and click the Add New button.
2. Click Upload and Choose File, then select the plugins's ZIP file. Click Install Now.
3. Click Activate to use Groovy menu.

== Copyright ==

Groovy Menu is comprised of two parts.

(1) the PHP code and integrated HTML are licensed under the General Public
License (GPL).

(2) All other parts, but not limited to the CSS code, images, and design are
licensed according to the license purchased from Envato.

Read more about licensing here: http://themeforest.net/licenses

Groovy Menu bundles the following third-party resources:

Font Awesome icons, Copyright Dave Gandy
License: SIL Open Font License v1.10
Source: http://fontawesome.io/

html2canvas maintained by Niklas von Hertzen
License: MIT
Source: https://github.com/niklasvh/html2canvas

imagesLoaded, Copyright David DeSandro
License: MIT
Source: http://imagesloaded.desandro.com

lodash maintained by Lodash Utilities
License: MIT
Source: https://github.com/lodash/lodash

perfect-scrollbar maintained by Hyunje Jun
License: MIT
Source: https://github.com/utatti/perfect-scrollbar

jQuery Select2, Maintained by Kevin Brown (https://github.com/kevin-brown) and Igor Vaynberg (https://github.com/ivaynberg) with the help of contributors (https://github.com/select2/select2/graphs/contributors)
License: MIT
Source: https://select2.github.io/

== Changelog ==

Visit [Changelog](https://grooni.com/docs/groovy-menu/changelog/) from our [Knowledge Base](https://grooni.com/docs/groovy-menu/)

= 2.1.1 =
* Improve: Added the Element (widget) for Elementor builder.

= 2.1.0 =
* Improve: Plugin performance improved for "Appearance > Menus" section.
* Improve: Groovy menu settings in "Appearance > Menus section" has been moved to modal window.
* Improve: Recommended system requirements for value PHP max_input_vars is reduced from 10000 to 1000 (Is a standard settings for a shared hosting).
* Fix: Anchors highlight for centered logo menu style.

= 2.0.16 =
* Fix: Added compatibility with Avada theme through automatic integration.
* Fix: Added compatibility for menu blocks with Fusion Builder (Avada theme).

= 2.0.15 =
* Fix: Resolved conflict сaused by Сomposer autoload (dependency manager for PHP) with some other plugins in some cases.
* Fix: Removed the menu overlapping on content during page editing in Elementor with the "Enable Groovy menu to overlap the first block in the page" option enabled.

= 2.0.14 =
* Improve:  Design of integration section.
* Improve: Added the possibility to set different logo URLs for WPML.
* Fix: Increased priority of the handler of admin nav_menu in WP Dashboard -> Appearance -> Menus. This eliminates conflicts with some plugins.
* Fix: Added the possibility to hide Groovy menu layout from not public post types. Managed in Global setting -> Tools -> Enable displaying the Groovy menu layout into Menu blocks post type.

= 2.0.13 =
* Fix: Fixed conflict with Divi Builder and the gm_menu_block post type.

= 2.0.12 =
* Improve: Added module for Divi Theme Builder.

= 2.0.11 =
* Improve: Added the ability to disable the mobile menu.

= 2.0.10 =
* Fix: Fixed menu item colors for Hover Style 3,4,6 in sticky mode.

= 2.0.9.2 =
* Fix: Fixed minor bugs.

= 2.0.9.1 =
* Fix: Icon position for sidebar type of Groovy Menu.

= 2.0.9 =
* Fix: Fixed fonts issue that appears while editing a preset and displaying a previously saved font in some cases
* Fix: Fixed bug with the export of preset.  Fallback is also provided in case of blocking the downloading of files from the site.
* Fix: Fixed bug with sub-menu icon.
* Improve: Additional characters are allowed in the rename a preset name.

= 2.0.8 =
* Fix: Improved work with caching plugins. A case with multiple saving preset styles has been fixed.
* Fix: Auto integration will be applied only once on the page, immediately after the HTML tag <BODY>.

= 2.0.7 =
* Fix: menu_block with shortcodes did not work properly with bbPress plugin pages.
* Fix: Hide title by "-" symbol.

= 2.0.6 =
* Fix: Fixed fit on the screen of search icons and mini-cart for iOS.
* Fix: Preset preview fix.

= 2.0.5.1 =
* Fix: Fixed php notice: "Undefined variable isCustom".

= 2.0.5 =
* New Feature: Added "Custom" setting to select a "Search form type". Now you can add any custom design created in the "Menu Block", that will be displayed in the search area, including fullscreen mode.
* Improve: Added setting for choosing background color to search screen in fullscreen mode.
* Fix: The search query now is considering the language setting, with installed and active the multilanguage WPML plugin.
* Fix: "Groovy Menu blocks" is rename to "Menu blocks".
* Fix: "Global settings" button not working on the "Integration" section.
* Fix: Not an active text below search icon in the sidemenu.

= 2.0.4 =
* Fix: Fixed bug with the wrong colors at hovering over menu items for a sticky menu.
* Fix: Fixed bug with duplicate, assignment and deleting the presets of the menu.
* Fix: Fixed bug with RTL issue.

= 2.0.3 =
* Fix: Plugin update script is fixed.

= 2.0.2 =
* Fix: Fixed a bug when the option "Top level links with align center must considering logo width" has been ignored.
* Fix: Text size for social icons in the toolbar now also depends on the preset option "Toolbar social icon size".
* Fix: Fixed a bug when Woocommerce mini-cart aren't displayed in the mobile version.

= 2.0.1 =
* Fix: Fixed minimalistic menu bug with centered logo.

= 2.0.0 =
* Improve: All plugin code has been rewritten as Vanilla JS (Pure JS) without using jQuery.
* Improve: Restructuring of plugin files. Now the main components are located in their own modules.
